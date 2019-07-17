<?php
/**
 * Stripe process payment.
 *
 * @package wp-stripe-connect
 */

global $woocommerce, $wpdb;

require WK_MARKETPLACE_DIR . 'includes/class-mp-commission.php';
require WK_MARKETPLACE_DIR . 'includes/class-mp-transaction.php';

$commission = new MP_Commission();

$transaction = new MP_Transaction();

$token = $this->get_post( 'stripe_response_token' );

$order_id = $odr_id;

$expmonth = $this->get_post( 'expmonth' );

if ( $expmonth < 10 ) {
	$expmonth = '0' . $expmonth;
}

if ( null !== $this->get_post( 'expyear' ) ) {
	$expyear = substr( $this->get_post( 'expyear' ), -2 );
}

// Send request and get response from server.
$stripe_payment_type = $this->stripe_payment_mode;

if ( 'yes' === $stripe_payment_type ) {
	\Stripe\Stripe::setApiKey( $this->stripe_test_secret_key );
} else {
	\Stripe\Stripe::setApiKey( $this->stripe_live_secret_key );
}

try {
	$admin_total_amt = 0;

	$seller_price = array();

	$data_sel = array();

	$sel_ids = $commission->get_sellers_in_order( $order_id );

	$order = new WC_Order( $order_id );

	$admin_total_amt = $order->get_total();

	foreach ( $sel_ids as $selid ) {
		$current_user   = get_user_by( 'ID', $selid );
		$role_name      = $current_user->roles;
		$payment_method = get_user_meta( $selid, 'mp_seller_payment_method', true );
		if ( in_array( 'wk_marketplace_seller', $role_name, true ) && 'Credit Card (Stripe Connect)' == $payment_method ) {
			$sel_data             = $commission->get_seller_final_order_info( $order_id, $selid );
			$data_sel[ $selid ][] = $sel_data;
		}
	}

	foreach ( $data_sel as $sellerid => $data ) {

		$seller_total = 0;
		$adm_comisn   = 0;
		$stripe_id    = get_user_meta( $sellerid, 'stripe_user_id', true );

		foreach ( $data as $orddata ) {

			$seller_total = $seller_total + $orddata['total_seller_amount'];
			$adm_comisn   = $adm_comisn + $orddata['total_commission'];
		}

		$seller_price[] = array(
			'seller_id'          => $sellerid,
			'access_token'       => $stripe_id,
			'total_seller_price' => $seller_total,
			'admin_commision'    => $adm_comisn,
		);

	}

	$stripe_card_num = $this->get_post( 'ccnum' );

	$stripe_cvc = $this->get_post( 'cvv' );

	$stripe_exp_month = $expmonth;

	$stripe_exp_year = $expyear;

	$stripe_email = $order->get_billing_email();

	$error = $this->get_post( 'stripe_response_error' );

	if ( $error ) {
		$order->add_order_note( __( 'Stripe payment failed. Payment declined.', 'marketplace-stripe' ) );

		wc_add_notice( $error, 'error' );
	}

	$stripe_currency = $order->get_currency();

	if ( ! empty( $admin_total_amt ) || ! empty( $seller_price ) ) {

		$charge_data = array(
			'amount'         => $admin_total_amt * 100,
			'currency'       => $stripe_currency,
			'source'         => $token,
			'description'    => sprintf( '#%s, %s', $order_id, $stripe_email ),
			'transfer_group' => 'ord_' . $order_id,
		);

		$response_charge = \Stripe\Charge::create( $charge_data );

		$error = '';

		if ( $response_charge ) {

			if ( $response_charge->paid != true ) {

				$error = __( 'Stripe payment failed. Payment Declined !', 'marketplace-stripe' );
			}

			if ( ! $error && 'succeeded' == $response_charge->status ) {

				update_post_meta( $order_id, '_transaction_id', $response_charge->id );

				$order->update_status( 'processing' );

				$transfer = array();

				if ( ! empty( $seller_price ) ) {

					foreach ( $seller_price as $seller_transactions ) {

						$sel_id = $seller_transactions['seller_id'];

						$transfer[ $sel_id ] = \Stripe\Transfer::create( array(
							'amount'         => $seller_transactions['total_seller_price'] * 100,
							'currency'       => $stripe_currency,
							'destination'    => $seller_transactions['access_token'],
							'transfer_group' => 'ord_' . $order_id,
						));
					}
				}
				if ( ! empty( $transfer ) ) {

					$sel_list = $commission->get_sellers_in_order( $order_id );

					foreach ( $sel_list as $value ) {

						$s_user = get_user_by( 'ID', $value );

						$user_r = $s_user->roles;

						if ( ! in_array( 'administrator', $user_r, true ) ) {

							if ( isset( $transfer[ $value ] ) ) {

								if ( ! empty( $order_id ) && ! empty( $value ) ) {

									$pay_data = $wpdb->get_results( "Select meta_value from {$wpdb->prefix}mporders_meta where seller_id = $value and order_id = $order_id and meta_key = 'paid_status' " );

									$paid_status = '';

									if ( ! empty( $pay_data ) ) {

										$paid_status = $pay_data[0]->meta_value;

									}
									if ( empty( $paid_status ) ) {

										$result = $commission->update_seller_commission( $value, $order_id );

										$amount += $result;

										if ( $amount ) {

											$wpdb->insert( $wpdb->prefix . 'mporders_meta', array(

												'seller_id' => $value,

												'order_id' => $order_id,

												'meta_key' => 'paid_status',

												'meta_value' => 'paid',

											) );

											if ( $amount > 0 ) {

												$transaction->generate( $value, $order_id, $amount, $transfer[ $value ]->id );
											}
										}
									}
								}
							}
						}
					}
				}
			}
			if ( $error ) {
				$order->add_order_note( __( 'Stripe payment failed. Payment declined.', 'marketplace-stripe' ) );

				wc_add_notice( __( 'Sorry, the transaction was declined.', 'marketplace-stripe' ), 'error' );
			} else {
				$order->add_order_note( __( ' Stripe payment completed. ', 'marketplace-stripe' ) );

				$order->payment_complete();

				return array(
					'redirect' => $this->get_return_url( $order ),
					'result'   => 'success',
				);
			}
		} else {

			$html_error_time_transaction = __( 'Stripe Failure Transaction.', 'marketplace-stripe' );

			$order->add_order_note( __( 'Stripe payment failed. Payment declined. Please Check your Admin settings', 'marketplace-stripe' ) );

			wc_add_notice( __( 'Sorry, the transaction was declined. Please Check your Admin settings', 'marketplace-stripe' ), 'error' );
		}
	}
} catch ( \Stripe\Error $e ) {

	$html_error_transaction = $e->getMessage() . __( 'it is error', 'marketplace-stripe' );
	$error_message          = $e->getMessage() . __( 'it is error', 'marketplace-stripe' );
}
