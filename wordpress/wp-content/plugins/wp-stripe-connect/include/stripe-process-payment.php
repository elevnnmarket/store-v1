<?php
/**
 * Stripe process payment.
 */
global $woocommerce, $wpdb;

require WK_MARKETPLACE_DIR.'includes/class-mp-commission.php';
require WK_MARKETPLACE_DIR.'includes/class-mp-transaction.php';

$commission = new MP_Commission();

$transaction = new MP_Transaction();

$token = $this->get_post('stripe_response_token');

$order_id = $odr_id;

$order = new WC_Order($order_id);

$expmonth = $this->get_post('expmonth');

if ($expmonth < 10) {
    $expmonth = '0'.$expmonth;
}

if (null !== $this->get_post('expyear')) {
    $expyear = substr($this->get_post('expyear'), -2);
}

// Send request and get response from server.
$stripe_payment_type = $this->stripe_payment_mode;

if ('yes' === $stripe_payment_type) {
    \Stripe\Stripe::setApiKey($this->stripe_test_secret_key);
} else {
    \Stripe\Stripe::setApiKey($this->stripe_live_secret_key);
}

try {

	$customer = \Stripe\Customer::create(array(
		'email' =>  $order->get_billing_email(),
		'source' => $token,
	));

	if($customer->id){
		
		$customer_id = $customer->id;
		
		$admin_total_amt = 0;
	
		$seller_price = array();
	
		$data_sel = array();
	
		$sel_ids = $commission->get_sellers_in_order($order_id);	
	
		$vendor_total_amt = 0;
		
		foreach ($sel_ids as $selid) {
			$current_user = get_user_by('ID', $selid);
			$role_name = $current_user->roles;
			$payment_method = get_user_meta($selid, 'mp_seller_payment_method', true);
			if (in_array('wk_marketplace_seller', $role_name, true) && !in_array('administrator', $role_name, true) && 'wk_stripe_connect' == $payment_method ) {
				$sel_data = $commission->get_seller_final_order_info($order_id, $selid);
				$data_sel[$selid][] = $sel_data;
			}
		}
		foreach ($data_sel as $sellerid => $data) {
			$seller_total = 0;
			$adm_comisn = 0;
			$stripe_id = get_user_meta($sellerid, 'stripe_user_id', true);
	
			foreach ($data as $orddata) {
				$seller_total = $seller_total + $orddata['total_seller_amount'];
				$adm_comisn = $adm_comisn + $orddata['total_commission'];
			}
	
			$seller_price[] = array(
				'seller_id' => $sellerid,
				'access_token' => $stripe_id,
				'total_seller_price' => $seller_total + $adm_comisn,
				'admin_commision' => $adm_comisn,
			);
			$vendor_total_amt = $vendor_total_amt + $seller_total + $adm_comisn;
		}

		$admin_total_amt = $order->get_total() - $vendor_total_amt;
	
		$stripe_email = $order->get_billing_email();
	
		$stripe_currency = $order->get_currency();

		$error = '';

		if (!empty($admin_total_amt) || !empty($seller_price)) {
			
			$order_main_transsaction = '';

			$transfer = array();
			
			if (!empty($seller_price)) {
				foreach ($seller_price as $seller_transactions) {
					$sel_id = $seller_transactions['seller_id'];
					try{
						$transfer[$sel_id] = \Stripe\Charge::create(
							array(
								'amount' => $seller_transactions['total_seller_price'] * 100,
								'currency' => $stripe_currency,
								'customer' => $customer->id,
								'application_fee' => $seller_transactions['admin_commision']* 100, 
								'destination' => $seller_transactions['access_token'],
							)
						);
					}catch( Exception $err ) {
						$transfer[$sel_id] = array(
							'error' => true,
							'seller' => $seller_transactions['seller_id'],
							'sel_amount' =>$seller_transactions['total_seller_price'],
						);
					}
				}
				if (!empty($transfer)) {

					$sel_list = $commission->get_sellers_in_order($order_id);

					foreach ($sel_list as $value) {
						$s_user = get_user_by('ID', $value);
	
						$user_r = $s_user->roles;
	
						if (!in_array('administrator', $user_r, true)) {
							if (isset($transfer[$value])) {
								$tran_obj = $transfer[$value];
								
								if( $transfer[$value]['error'] ){

									$admin_total_amt = $admin_total_amt + $transfer[$value]['sel_amount'];

								}else{

									if ( $value ) {
										$pay_data = $wpdb->get_results("Select meta_value from {$wpdb->prefix}mporders_meta where seller_id = $value and order_id = $order_id and meta_key = 'paid_status' ");
										$amount = 0;
										$paid_status = '';
	
										if (!empty($pay_data)) {
											$paid_status = $pay_data[0]->meta_value;
										}
										if (empty($paid_status)) {
											$result = $commission->update_seller_commission($value, $order_id);
	
											$amount = $result;
	
											if ($amount) {
												$wpdb->insert(
													$wpdb->prefix.'mporders_meta',
													array(
														'seller_id' => $value,
	
														'order_id' => $order_id,
	
														'meta_key' => 'paid_status',
	
														'meta_value' => 'paid',
													)
												);
	
												if ($amount > 0) {
													$transaction->generate($value, $order_id, $amount, $transfer[$value]->id);
												}
	
												$order_main_transsaction = $transfer[$value]->id;
											}
										}
									}
								}
							}
						}
					}
				}
			}
			if( $admin_total_amt != 0 ){

				$charge_data = array(
					'amount' => $admin_total_amt * 100,
					'currency' => $stripe_currency,
					'customer' => $customer->id,
					'description' => sprintf('#%s, %s', $order_id, $stripe_email),
				);
				
				$response_charge = \Stripe\Charge::create($charge_data);

				$order_main_transsaction = $response_charge->id;

			}

			if(!empty($order_main_transsaction)){

				update_post_meta($order_id, '_transaction_id', $order_main_transsaction[0]);
				
				$order->payment_complete();
			}

			if ($error) {
				$order->add_order_note(__('Stripe payment failed. Payment declined.', 'marketplace-stripe'));

				wc_add_notice(__('Sorry, the transaction was declined.', 'marketplace-stripe'), 'error');
			} else {
				$order->add_order_note(__(' Stripe payment completed. ', 'marketplace-stripe'));

				return array(
					'redirect' => $this->get_return_url($order),
					'result' => 'success',
				);
			}

		} else {
			$html_error_time_transaction = __('Stripe Failure Transaction.', 'marketplace-stripe');

			$order->add_order_note(__('Stripe payment failed. Payment declined. Please Check your Admin settings', 'marketplace-stripe'));

			wc_add_notice(__('Sorry, the transaction was declined. Please Check your Admin settings', 'marketplace-stripe'), 'error');
		}
	}
} catch (\Stripe\Error $e) {
    $html_error_transaction = $e->getMessage().__('it is error', 'marketplace-stripe');
    $error_message = $e->getMessage().__('it is error', 'marketplace-stripe');
}
