<?php

/**
 * MP stripe class.
 */
if (!class_exists('WC_Payment_Gateway')) {
    return;
}

require_once WK_MP_STRIPE_DIR . 'assets/lib/stripe/init.php';

/**
 * Stripe Connect Gateway Class.
 */
class WC_Stripe_Connect extends WC_Payment_Gateway
{
    /**
     * Class stripe.
     *
     * @var int
     */
    protected static $count = 0;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        // Register plugin information.
        $this->id = 'stripe-connect';
        $this->has_fields = true;
        $this->supports = array(
            'products',
            'subscriptions',
            'subscription_cancellation',
            'subscription_suspension',
            'subscription_reactivation',
            'subscription_date_changes',
        );

        // Create plugin fields and settings.
        $this->init_form_fields();
        $this->init_settings();

        // Get setting values.
        foreach ($this->settings as $key => $val) {
            $this->$key = $val;
        }

        $this->icon = WK_MP_STRIPE . 'payment-stripe.png';

        add_action('woocommerce_receipt_stripe', array($this, 'receipt_page'));

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));

        add_action('wp_enqueue_scripts', array($this, 'add_stripe_scripts'));

        add_action('woocommerce_review_order_before_submit', array($this, 'stripe_woocommerce_before_submit'));

        add_action('marketplace_payment_gateway', array($this, 'stripe_payment_details'));
	}
	
    /**
     * Check if SSL is enabled and notify the user.
     */
    public function stripe_payment_ssl_check()
    {
        if (get_option('no' == 'woocommerce_force_ssl_checkout') && 'yes' == $this->enabled) {
            echo '<div class="error"><p>' . sprintf(esc_html__('Stripe Connect is enabled and the <a href="%s">force SSL option</a> is disabled; your checkout is not secure! Please enable SSL and ensure your server has a valid SSL certificate.', 'marketplace-stripe'), esc_url(admin_url('admin.php?page=woocommerce'))) . '</p></div>';
        }
    }

    /**
     * Initialize Gateway Settings Form Fields.
     */
    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'marketplace-stripe'),
                'label' => __('Enable Stripe Connect', 'marketplace-stripe'),
                'type' => 'checkbox',
                'description' => '',
                'default' => 'no',
            ),
            'title' => array(
                'title' => __('Title', 'marketplace-stripe'),
                'type' => 'text',
                // 'custom_attributes' => array('readonly' => 'readonly'),
                'description' => __('This controls the title which the user sees during checkout.', 'marketplace-stripe'),
                'default' => __('Stripe Connect', 'marketplace-stripe'),
            ),
            'description' => array(
                'title' => __('Description', 'marketplace-stripe'),
				'type' => 'textarea',
				'css' => 'width:410px!important',
                'description' => __('This controls the description which the user sees during checkout.', 'marketplace-stripe'),
                'default' => 'Pay with your card via Stripe Connect.',
            ),
            'stripe_payment_mode' => array(
                'title' => __('Test/Live', 'marketplace-stripe'),
                'label' => __('Stripe payment Mode Enable "Test" Disable "Live"', 'marketplace-stripe'),
                'type' => 'checkbox',
                'description' => '',
                'default' => 'yes',
            ),
            'stripe_test_client_id' => array(
                'title' => __('Stripe Connect Test client_id', 'marketplace-stripe'),
                'label' => __('Test client_id', 'marketplace-stripe'),
                'type' => 'text',
                'description' => __('Enter Stripe Connect Test Client Id ', 'marketplace-stripe'),
                'default' => '',
            ),
            'stripe_live_client_id' => array(
                'title' => __('Stripe Connect Live client_id', 'marketplace-stripe'),
                'label' => __('Live client_id', 'marketplace-stripe'),
                'type' => 'text',
                'description' => __('Enter Stripe Connect Live Client Id ', 'marketplace-stripe'),
                'default' => '',
            ),
            'stripe_test_secret_key' => array(
                'title' => __('Stripe Test Secret Key', 'marketplace-stripe'),
                'type' => 'text',
                'description' => __('This is the API Stripe Test Secret Key generated within the Stripe Payment gateway.', 'marketplace-stripe'),
                'default' => '',
            ),
            'stripe_test_publishable_key' => array(
                'title' => __('Stripe Test Publishable Key', 'marketplace-stripe'),
                'type' => 'text',
                'description' => __('This is the API Stripe Test Publishable Key generated within the Stripe Payment gateway.', 'marketplace-stripe'),
                'default' => '',
            ),
            'stripe_live_secret_key' => array(
                'title' => __('Stripe Live Secret Key', 'marketplace-stripe'),
                'type' => 'text',
                'description' => __('This is the API Stripe Live Secret Key generated within the Stripe Payment gateway.', 'marketplace-stripe'),
                'default' => '',
            ),
            'stripe_live_publishable_key' => array(
                'title' => __('Stripe Live Publishable Key', 'marketplace-stripe'),
                'type' => 'text',
                'description' => __('This is the API Stripe Live Publishable Key generated within the Stripe Payment gateway.', 'marketplace-stripe'),
                'default' => '',
            ),
            'salemethod' => array(
                'title' => __('Sale Method', 'marketplace-stripe'),
                'type' => 'select',
                'description' => __('Select which sale method to use. Authorize Only will authorize the customers card for the purchase amount only.  Authorize &amp; Capture will authorize the customer\'s card and collect funds.', 'marketplace-stripe'),
                'options' => array(
                    'sale' => __('Authorize &amp; Capture', 'marketplace-stripe'),
                    'auth' => __('Authorize Only', 'marketplace-stripe'),
                ),
                'default' => __('Authorize &amp; Capture', 'marketplace-stripe'),
            ),
            'cardtypes' => array(
                'title' => __('Accepted Cards', 'marketplace-stripe'),
                'type' => 'multiselect',
                'description' => __('Select which card types to accept.', 'marketplace-stripe'),
                'default' => '',
                'options' => array(
                    'MasterCard' => 'MasterCard',
                    'Visa' => 'Visa',
                    'Discover' => 'Discover',
                    'American Express' => 'American Express',
                ),
            ),
            'cvv' => array(
                'title' => __('CVV', 'marketplace-stripe'),
                'type' => 'checkbox',
                'label' => __('Require customer to enter credit card CVV code', 'marketplace-stripe'),
                'description' => '',
                'default' => 'yes',
            ),
        );
    }

    /**
     * UI - Admin Panel Options.
     */
    public function admin_options()
    {
        ?>
		<h3>
		<?php esc_html_e('Stripe Payment', 'marketplace-stripe');?>
		</h3>
		<p>
			<?php
				esc_html_e('The Stripe Payment Gateway is simple and powerful.  The plugin works by adding credit card fields on the checkout page, and then sending the details to Stripe Payment for verification.', 'marketplace-stripe') . '<a href="https://stripe.com/">' . esc_html_e('Click here to get paid like the pros.', 'marketplace-stripe');
			?>
		</p>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				jQuery('#woocommerce_stripe-connect_cardtypes').select2();
			});
		</script>
		<table class="form-table">
			<?php $this->generate_settings_html();?>
		</table>
		<?php
	}

    /**
     * UI - Payment page fields for Stripe Payment.
     */
    public function payment_fields()
    {
        if ($this->description) {
            ?>
			<p><?php echo $this->description; ?></p>
            <?php
		}
		?>
        <fieldset>
        <?php
        $user = get_current_user_ID();

        if ($this->user_has_stored_data($user)) {
            ?>
            <fieldset>
                <input type="radio" name="stripe-use-stored-payment-info" id="stripe-use-stored-payment-info-yes" value="yes" checked="checked" onclick="document.getElementById('stripe-new-info').style.display='none'; document.getElementById('stripe-stored-info').style.display='block'"; />
                <label for="stripe-use-stored-payment-info-yes" style="display: inline;"><?php esc_html_e('Use a stored card', 'marketplace-stripe');?></label>
                <div id="stripe-stored-info" style="padding: 10px 0 0 40px; clear: both;">
                <?php
				$i = 0;
				$method = $this->get_payment_method($i);
				while (null != $method) {
					?>
					<p>
						<input type="radio" name="stripe-payment-method" id="<?php echo $i; ?>" value="<?php echo $i; ?>" /> &nbsp;
						<?php echo $method->cc_number; ?> (
						<?php
						$exp = $method->cc_exp;
						echo substr($exp, 0, 2) . '/' . substr($exp, -2);
						?> )
						<br/>
					</p>
				<?php
					$method = $this->get_payment_method(++$i);
				}
				?>
			</fieldset>
			<fieldset>
				<p>
					<input type="radio" name="stripe-use-stored-payment-info" id="stripe-use-stored-payment-info-no" value="no" onclick="document.getElementById('stripe-stored-info').style.display='none'; document.getElementById('stripe-new-info').style.display='block'"; />
					<label for="stripe-use-stored-payment-info-no"  style="display: inline;">
						<?php esc_html_e('Use a new payment method', 'marketplace-stripe');?>
					</label>
				</p>
				<div id="stripe-new-info" style="display:none">
			</fieldset>
		<?php
		} else {
		?>
			<fieldset>
				<div id="stripe-new-info">
				<?php
		}?>

			<!-- Credit card number -->
				<p class="form-row form-row-first">
					<label for="ccnum"><?php echo esc_html__('Card number', 'marketplace-stripe'); ?> <span class="required">*</span></label>
					<input type="text" name="ccnum" data-stripe="number" class="input-text" id="ccnum" maxlength="16" />
				</p>
				<!-- Credit card type -->
				<p class="form-row form-row-last">
					<label for="cardtype"><?php echo esc_html__('Card type', 'marketplace-stripe'); ?> <span class="required">*</span></label>
					<select name="cardtype" id="cardtype" class="woocommerce-select" style="padding: 5px;">
						<?php

						if (is_array($this->cardtypes) && !empty($this->cardtypes)) {
							foreach ($this->cardtypes as $type) {
							?>
							<option value="<?php echo $type; ?>"><?php esc_html_e($type, 'marketplace-stripe');?></option>
							<?php
							}
						} else {
						?>
							<option value="-1"><?php esc_html_e('No card type found', 'marketplace-stripe');?></option>
						<?php
						}
						?>
					</select>
				</p>
				<div class="clear"></div>
				<!-- Credit card expiration -->
					<p class="form-row form-row-first">
						<label for="cc-expire-month"><?php esc_html_e('Expiration date', 'marketplace-stripe');?> <span class="required">*</span></label>
						<select id="expmonth" name="expmonth" class="woocommerce-select woocommerce-cc-month" style="padding: 5px;width:48%;">
							<option value=""><?php esc_html_e('Month', 'marketplace-stripe');?></option>
							<?php

							$months = array();

							for ($i = 1; $i <= 12; ++$i) {
								$timestamp = mktime(0, 0, 0, $i, 1);

								$months[date('n', $timestamp)] = date('F', $timestamp);
							}
							foreach ($months as $num => $name) {
								printf('<option value="%u">%s</option>', $num, $name);
							}?>
						</select>
						<input type="hidden" size="2"  data-stripe="exp-month" id="stripe-data-exp-month">
						<select id="expyear" name="expyear" class="woocommerce-select wooco;mmerce-cc-year" style="padding: 5px;width:48%;">
							<option value=""><?php _e('Year', 'marketplace-stripe');?></option>
							<?php
							$years = array();
							for ($i = date('y'); $i <= date('y') + 15; ++$i) {
								printf('<option value="20%u">20%u</option>', $i, $i);
							}
							?>
						</select>
						<input type="hidden" size="4" data-stripe="exp-year" id="stripe-data-exp-year"/>
					</p>
					<?php
				// Credit card security code.
				if ($this->cvv == 'yes') {
					?>
					<p class="form-row form-row-last">
						<label for="cvv"><?php esc_html_e('Card security code', 'marketplace-stripe');?> <span class="required">*</span></label>
						<input  type="text" name="cvv" class="input-text" id="cvv" maxlength="4" data-stripe="cvc" style="width:100%!important" />
						<span style="width:100%!important" class="help"><?php esc_html_e('3 or 4 digits usually found on the signature strip.', 'marketplace-stripe');?></span>
					</p>
					<?php
				}?>
				<input type="hidden" id="stripe_response_token" name="stripe_response_token" value="token">
				<input type="hidden" id="stripe_response_error" name="stripe_response_error" value="">
			</fieldset>
		</fieldset>
	<?php
}

    /**
     * Process the payment and return the result.
     *
     * @param int $odr_id order id
     */
    public function process_payment($odr_id)
    {
        $result = require_once WK_MP_STRIPE_DIR . 'include/stripe-process-payment.php';

		WC()->cart->empty_cart();

        return $result;
    }

    /**
     * Stripe woocommerce before submit.
     */
    public function stripe_woocommerce_before_submit()
    {
        $stripe_payment_type = $this->stripe_payment_mode;
        $stripe_publishable_key = '';
        if ($stripe_payment_type == 'yes') {
            $stripe_publishable_key = $this->stripe_test_publishable_key;
        } else {
            $stripe_publishable_key = $this->stripe_live_publishable_key;
        }
        $expmonth = $this->get_post('expmonth');
        if ($expmonth < 10) {
            $expmonth = '0' . $expmonth;
        }
        if ($this->get_post('expyear') != null) {
            $expyear = substr($this->get_post('expyear'), -2);
        }?>
		<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
		<script type="text/javascript">
			(function(wk){
				// if(wk('.payment_box payment_method_stripe-connect').css('display') == 'block' ){
					wk('.woocommerce-checkout').on('submit',function(e){
						if(wk('#stripe_response_token').val()=='token'){
							e.preventDefault();
							e.stopImmediatePropagation();
							var expmon=wk('#expmonth option:selected').val();
							if(expmon<10){
								expmon='0'+expmon;
							}
							expyr=wk('#expyear option:selected').val();
							if(wk.type(expyr)!='undefined'){
								expyr=expyr.substr(-2);
							}
							wk('#stripe-data-exp-month').val(expmon);
							wk('#stripe-data-exp-year').val(expyr);

							Stripe.setPublishableKey('<?php echo $stripe_publishable_key; ?>');
							var form = wk('.woocommerce-checkout');

							tok = Stripe.card.createToken({
								number: wk('#ccnum').val(),
								cvc: wk('#cvv').val(),
								exp_month: wk('#stripe-data-exp-month').val(),
								exp_year: wk('#stripe-data-exp-year').val()
							}, stripeResponseHandler);
						}
					});
				// }
				function stripeResponseHandler(status, response) {
					if(response.error){
						var error_message=response.error.message;
						wk('#stripe_response_token').val('token');
						wk('#stripe_response_error').val(error_message);
					}else{
						var token = response.id;
						wk('#stripe_response_token').val(token);
						wk('#stripe_response_error').val('');
						if(wk('#stripe_response_token').val()!='token' || wk('#stripe_response_token').val()!=''){
							wk('.woocommerce-checkout').submit();
						}
					}
				}
			})(jQuery);
        </script>
        <?php
}

    /**
     * Get details of a payment method for the current user from the Customer Vault.
     *
     * @param int $payment_method_number payment number
     */
    public function get_payment_method($payment_method_number)
    {
        if ($payment_method_number < 0) {
            die(esc_html__('Invalid payment method', 'marketplace-stripe') . ': ' . $payment_method_number);
        }

        $user = wp_get_current_user();

        $customer_vault_ids = get_user_meta($user->ID, 'customer_vault_ids', true);

        if ($payment_method_number >= count($customer_vault_ids)) {
            return null;
        }

        $query = array(
            'username' => $this->username,
            'password' => $this->password,
            'report_type' => 'customer_vault',
        );

        $id = $customer_vault_ids[$payment_method_number];
        if (substr($id, 0, 1) !== '_') {
            $query['customer_vault_id'] = $id;
        } else {
            $query['customer_vault_id'] = $user->user_login;
            $query['billing_id'] = substr($id, 1);
            $query['ver'] = 2;
        }
        $response = wp_remote_post(QUERY_URL, array(
            'body' => $query,
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'cookies' => array(),
            'ssl_verify' => false,
        ));

        // Do we have an error?
        if (is_wp_error($response)) {
            return null;
        }

        // Check for empty response, which means method does not exist.
        if (trim(strip_tags($response['body'])) == '') {
            return null;
        }

        // Format result.
        $content = simplexml_load_string($response['body'])->customer_vault->customer;

        if (substr($id, 0, 1) === '_') {
            $content = $content->billing;
        }

        return $content;
    }

    /**
     * Check if a user's stored billing records have been converted to Single Billing. If not, do it now.
     *
     * @param int $user_login [description]
     * @param int $user_id    [description]
     */
    public function check_payment_method_conversion($user_login, $user_id)
    {
        if (!$this->user_has_stored_data($user_id) && $this->get_mb_payment_methods($user_login) != null) {
            $this->convert_mb_payment_methods($user_login, $user_id);
        }
    }

    /**
     * Convert any Multiple Billing records stored by the user into Single Billing records.
     *
     * @param int $user_login [description]
     * @param int $user_id    [description]
     */
    public function convert_mb_payment_methods($user_login, $user_id)
    {
        $mb_methods = $this->get_mb_payment_methods($user_login);
        foreach ($mb_methods->billing as $method) {
            $customer_vault_ids[] = '_' . ((string) $method['id']);
        }
        // Store the payment method number/customer vault ID translation table in the user's metadata.
        add_user_meta($user_id, 'customer_vault_ids', $customer_vault_ids);

        // Update subscriptions to reference the new records.
        if (class_exists('WC_Subscriptions_Manager')) {
            $payment_method_numbers = array_flip($customer_vault_ids);
            foreach ((array) (WC_Subscriptions_Manager::get_users_subscriptions($user_id)) as $subscription) {
                update_post_meta($subscription['order_id'], 'payment_method_number', $payment_method_numbers['_' . get_post_meta($subscription['order_id'], 'billing_id', true)]);
                delete_post_meta($subscription['order_id'], 'billing_id');
            }
        }
    }

    /**
     * Get the user's Multiple Billing records from the Customer Vault.
     *
     * @param int $user_login user login
     */
    public function get_mb_payment_methods($user_login)
    {
        if ($user_login == null) {
            return null;
        }

        $query = array(
            'username' => $this->username,
            'password' => $this->password,
            'report_type' => 'customer_vault',
            'customer_vault_id' => $user_login,
            'ver' => '2',
        );

        $content = wp_remote_post(QUERY_URL, array(
            'body' => $query,
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'cookies' => array(),
            'ssl_verify' => false,
        ));

        if (trim(strip_tags($content['body'])) == '') {
            return null;
        }

        return simplexml_load_string($content['body'])->customer_vault->customer;
    }

    /**
     * Check if the user has any billing records in the Customer Vault.
     *
     * @param int $user_id user id
     */
    public function user_has_stored_data($user_id)
    {
        return get_user_meta($user_id, 'customer_vault_ids', true) != null;
    }

    /**
     * Update a stored billing record with new CC number and expiration.
     *
     * @param string $payment_method payment method
     * @param int    $ccnumber       card number
     * @param string $ccexp          expiry date
     */
    public function update_payment_method($payment_method, $ccnumber, $ccexp)
    {
        global $woocommerce;
        $user = get_current_user_id();

        $customer_vault_ids = get_user_meta($user, 'customer_vault_ids', true);

        $id = $customer_vault_ids[$payment_method];

        if (substr($id, 0, 1) == '_') {
            // Copy all fields from the Multiple Billing record.
            $mb_method = $this->get_payment_method($payment_method);
            $stripe_request = (array) $mb_method[0];
            // Make sure values are strings.
            foreach ($stripe_request as $key => $val) {
                $stripe_request[$key] = "$val";
            }
            // Add a new record with the updated details.
            $stripe_request['customer_vault'] = 'add_customer';
            $new_customer_vault_id = $this->random_key();
            $stripe_request['customer_vault_id'] = $new_customer_vault_id;
        } else {
            // Update existing record.
            $stripe_request['customer_vault'] = 'update_customer';
            $stripe_request['customer_vault_id'] = $id;
        }

        $stripe_request['username'] = $this->username;
        $stripe_request['password'] = $this->password;
        // Overwrite updated fields.
        $stripe_request['cc_number'] = $ccnumber;
        $stripe_request['cc_exp'] = $ccexp;

        $response = $this->post_and_get_response($stripe_request);

        if ($response['response'] == 1) {
            if (substr($id, 0, 1) === '_') {
                // Update references.
                $customer_vault_ids[$payment_method] = $new_customer_vault_id;
                update_user_meta($user, 'customer_vault_ids', $customer_vault_ids);
            }
            $woocommerce->add_message(esc_html__('Successfully updated your information!', 'marketplace-stripe'));
        } else {
            wc_add_notice(esc_html__('Sorry, there was an error: ', 'marketplace-stripe') . $response['responsetext'], 'error');
        }
        $woocommerce->show_messages();
    }

    /**
     * Delete a stored billing method.
     *
     * @param string $payment_method payment method
     */
    public function delete_payment_method($payment_method)
    {
        global $woocommerce;
        $user = get_current_user_id();

        $customer_vault_ids = get_user_meta($user, 'customer_vault_ids', true);

        $id = $customer_vault_ids[$payment_method];
        // If method is Single Billing, actually delete the record.
        if (substr($id, 0, 1) !== '_') {
            $stripe_request = array(
                'username' => $this->username,
                'password' => $this->password,
                'customer_vault' => 'delete_customer',
                'customer_vault_id' => $id,
            );

            $response = $this->post_and_get_response($stripe_request);

            if ($response['response'] != 1) {
                wc_add_notice(esc_html__('Sorry, there was an error: ', 'marketplace-stripe') . $response['responsetext'], 'error');
                $woocommerce->show_messages();

                return;
            }
        }

        $last_method = count($customer_vault_ids) - 1;

        // Update subscription references.
        if (class_exists('WC_Subscriptions_Manager')) {
            foreach ((array) (WC_Subscriptions_Manager::get_users_subscriptions($user)) as $subscription) {
                $subscription_payment_method = get_post_meta($subscription['order_id'], 'payment_method_number', true);
                if ($subscription_payment_method == $payment_method) {
                    delete_post_meta($subscription['order_id'], 'payment_method_number');
                    WC_Subscriptions_Manager::cancel_subscription($user, WC_Subscriptions_Manager::get_subscription_key($subscription['order_id']));
                } elseif ($subscription_payment_method == $last_method && $subscription['status'] != 'cancelled') {
                    update_post_meta($subscription['order_id'], 'payment_method_number', $payment_method);
                }
            }
        }

        // Delete the reference by replacing it with the last method in the array.
        if ($payment_method < $last_method) {
            $customer_vault_ids[$payment_method] = $customer_vault_ids[$last_method];
        }
        unset($customer_vault_ids[$last_method]);
        // Check for saving payment info without having or creating an account.
        update_user_meta($user, 'customer_vault_ids', $customer_vault_ids);

        $woocommerce->add_message(esc_html__('Successfully deleted your information!', 'marketplace-stripe'));
        $woocommerce->show_messages();
    }

    /**
     * Check payment details for valid format.
     */
    public function validate_fields()
    {
        if ('yes' === $this->get_post('stripe-use-stored-payment-info')) {
            return true;
        }

        global $woocommerce;

        if ((!is_user_logged_in()) && (!$this->get_post('createaccount'))) {
            wc_add_notice(__('Sorry, you need to create an account in order for us to save your payment information.', 'marketplace-stripe'), 'error');
            return false;
        }

        $cardType = $this->get_post('cardtype');
        $cardNumber = $this->get_post('ccnum');
        $cardCSC = $this->get_post('cvv');
        $cardExpirationMonth = $this->get_post('expmonth');
        $cardExpirationYear = $this->get_post('expyear');

		// Check card number.
        if (empty($cardNumber) || !ctype_digit($cardNumber)) {
            wc_add_notice(__('Card number is invalid.', 'marketplace-stripe'), 'error');
            return false;
        }

        if ($this->cvv == 'yes') {
            // Check security code.
            if (!ctype_digit($cardCSC)) {
                wc_add_notice(__('Card security code is invalid (only digits are allowed).', 'marketplace-stripe'), 'error');
                return false;
            }
            if ((strlen($cardCSC) != 3 && in_array($cardType, array('Visa', 'MasterCard', 'Discover'))) || (strlen($cardCSC) != 4 && 'American Express' == $cardType)) {
                wc_add_notice(__('Card security code is invalid (wrong length).', 'marketplace-stripe'), 'error');
                return false;
            }
        }

        // Check expiration data.
        $currentYear = date('Y');
        $currentMon = date('M');
        if (!ctype_digit($cardExpirationMonth) || !ctype_digit($cardExpirationYear) || $cardExpirationMonth > 12 || $cardExpirationMonth < 1 || $cardExpirationYear < $currentYear || $cardExpirationYear > $currentYear + 20 || ($currentYear == $cardExpirationMonth && $cardExpirationMonth < $currentMon)) {
            wc_add_notice(__('Card expiration date is invalid', 'marketplace-stripe'), 'error');
            return false;
        }

        // Strip spaces and dashes.
        $cardNumber = str_replace(array(' ', '-'), '', $cardNumber);

        return true;
    }

    /**
     * Add ability to view and edit payment details on the My Account page.(The WooCommerce 'force ssl' option also secures the My Account page, so we don't need to do that.).
     */
    public function add_payment_method_options()
    {
		global $woocommerce;

        $user = get_current_user_id();
        if (!$this->user_has_stored_data($user)) {
            return;
        }

        if ($this->get_post('delete') != null) {
            $method_to_delete = $this->get_post('delete');
            $response = $this->delete_payment_method($method_to_delete);
        } elseif ($this->get_post('update') != null) {
            $method_to_update = $this->get_post('update');
            $ccnumber = $this->get_post('edit-cc-number-' . $method_to_update);
            if (empty($ccnumber) || !ctype_digit($ccnumber)) {
                wc_add_notice(__('Card number is invalid.', 'marketplace-stripe'), 'error');
                $woocommerce->show_messages();
            } else {
                $ccexp = $this->get_post('edit-cc-exp-' . $method_to_update);
                $expmonth = substr($ccexp, 0, 2);
                $expyear = substr($ccexp, -2);
                $currentYear = substr(date('Y'), -2);
                if (empty($ccexp) || !ctype_digit(str_replace('/', '', $ccexp)) || $expmonth > 12 || $expmonth < 1 || $expyear < $currentYear || $expyear > $currentYear + 20) {
                    wc_print_notice(__('Card expiration date is invalid', 'marketplace-stripe'), 'error');
                    $woocommerce->show_messages();
                } else {
                    $response = $this->update_payment_method($method_to_update, $ccnumber, $ccexp);
                }
            }
        }?>

		<h2><?php esc_html_e('Saved Payment Methods', 'marketplace-stripe');?></h2>
		<p><?php esc_html_e('This information is stored to save time at the checkout and to pay for subscriptions.', 'marketplace-stripe');?></p>

		<?php
		$i = 0;
        $current_method = $this->get_payment_method($i);

        while ($current_method != null) {
            if ($method_to_delete === $i && $response['response'] == 1) {
                $method_to_delete = null;
                continue;
            }?>

			<header class="title">

				<h3>
					<?php esc_html_e('Payment Method', 'marketplace-stripe');?> <?php echo $i + 1; ?>
				</h3>
				<p>

					<button style="float:right" class="button" id="unlock-delete-button-<?php echo $i; ?>"><?php esc_html_e('Delete', 'marketplace-stripe');?></button>

					<button style="float:right; display:none" class="button" id="cancel-delete-button-<?php echo $i; ?>"><?php esc_html_e('No', 'marketplace-stripe');?></button>
					<form action="<?php echo get_permalink(woocommerce_get_page_id('myaccount')); ?>" method="post" style="float:right" >
						<input type="submit" value="<?php esc_html_e('Yes', 'marketplace-stripe');?>" class="button alt" id="delete-button-<?php echo $i; ?>" style="display:none">
						<input type="hidden" name="delete" value="<?php echo $i; ?>">
					</form>
					<span id="delete-confirm-msg-<?php echo $i; ?>" style="float:left_; display:none"><?php esc_html_e('Are you sure? (Subscriptions purchased with this card will be canceled.)', 'marketplace-stripe');?>&nbsp;</span>

					<button style="float:right" class="button" id="edit-button-<?php echo $i; ?>" ><?php esc_html_e('Edit', 'marketplace-stripe');?></button>
					<button style="float:right; display:none" class="button" id="cancel-button-<?php echo $i; ?>" ><?php esc_html_e('Cancel', 'marketplace-stripe');?></button>

					<form action="<?php echo get_permalink(woocommerce_get_page_id('myaccount')); ?>" method="post" >

						<input type="submit" value="<?php esc_html_e('Save', 'marketplace-stripe');?>" class="button alt" id="save-button-<?php echo $i; ?>" style="float:right; display:none" >

						<span style="float:left"><?php esc_html_e('Credit card', 'marketplace-stripe');?>:&nbsp;</span>
						<input type="text" style="display:none" id="edit-cc-number-<?php echo $i; ?>" name="edit-cc-number-<?php echo $i; ?>" maxlength="16" />
						<span id="cc-number-<?php echo $i; ?>">
							<?php echo ($method_to_update === $i && $response['response'] == 1) ? ('<b>' . $ccnumber . '</b>') : $current_method->cc_number; ?>
						</span>
						<br/>

						<span style="float:left"><?php esc_html_e('Expiration', 'marketplace-stripe');?>:&nbsp;</span>
						<input type="text" style="float:left; display:none" id="edit-cc-exp-<?php echo $i; ?>" name="edit-cc-exp-<?php echo $i; ?>" maxlength="5" value="MM/YY" />
						<span id="cc-exp-<?php echo $i; ?>">
							<?php echo ($method_to_update === $i && $response['response'] == 1) ? ('<b>' . $ccexp . '</b>') : substr($current_method->cc_exp, 0, 2) . '/' . substr($current_method->cc_exp, -2); ?>
						</span>

						<input type="hidden" name="update" value="<?php echo $i; ?>">

					</form>

				</p>

			</header>
			<?php

            $current_method = $this->get_payment_method(++$i);
        }
    }

    /**
     * Thank you page description.
     *
     * @param object $order wc order object
     */
    public function receipt_page($order)
    {
        echo '<p>' . esc_html__('Thank you for your order.', 'marketplace-stripe') . '</p>';
    }

    /**
     * Include jQuery and our scripts.
     */
    public function add_stripe_scripts()
    {
        if (!$this->user_has_stored_data(get_current_user_ID())) {
            return;
        }

        wp_enqueue_script('jquery');
        wp_enqueue_script('edit_billing_details', WK_MP_STRIPE . 'js/edit_billing_details.js', array('jquery'), 1.0);

        if ($this->cvv == 'yes') {
            wp_enqueue_script('check_cvv', WK_MP_STRIPE . 'js/check_cvv.js', array('jquery'), 1.0);
        }
    }

    /**
     * Get the current user's login name.
     */
    private function get_user_login()
    {
        global $user_login;
        wp_get_current_user();

        return $user_login;
    }

    /**
     * Get post data if set.
     *
     * @param string $name name
     */
    private function get_post($name)
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }
        return null;
    }

    /**
     * Stripe payment details.
     */
    public function stripe_payment_details()
    {
        ++self::$count;

        if (self::$count == 1) {
            $paymet_gateways = WC()->payment_gateways->payment_gateways();
            foreach ($paymet_gateways as $payment) {
                if ($payment->id == 'stripe-connect') {
                    if ($payment->enabled == 'yes') :
                    ?>
					<fieldset style="border:1px solid #ddd;padding: 10px;">
						<legend style="padding:0;margin:0;"><?php echo $payment->get_title(); ?></legend>
						<div class="social-seller-input">
							<?php
							if (!get_user_meta(get_current_user_ID(), 'stripe_user_id', true)):
                    		?>
							<label><?php esc_html_e('Click here to connect to Stripe account', 'marketplace-stripe');?></label>
							<?php
							endif;
							$stripe_desc = '';
							if (get_user_meta(get_current_user_ID(), 'stripe_user_id', true)) {
								$stripe_desc = get_user_meta(get_current_user_ID(), 'stripe_user_id', true);
							}?>
							<div class="wk-success-check">
								<a href="" id="mp_seller_payment_stripe_method"><img src="" id="stripe_connect_button" width="190" height="33"></a>
							</div>
							<input type="hidden" name="mp_seller_payment_stripe_method" value="wk_stripe_connect">
						</div>
						<div class="mp_seller_paymet_method_description">
							<label for="stripe_user_id" id="mp_payment_description" ><?php echo esc_html_e('Payment Description Or Payment Id', 'marketplace-stripe'); ?></label>
							<div class="social-seller-input">
								<input type="text" value="<?php echo isset($stripe_desc) ? $stripe_desc : ''; ?>" id="stripe_user_id" name="stripe_user_id">
							</div>
						</div>
					</fieldset>
					<?php
					endif;
                }
            }
        }
    }
}
