<?php
/**
 * Plugin Name: Stripe Connect
 * Plugin URI: http://store.webkul.com/Wordpress-Plugins.html
 * Description: WordPress WooCommerce Marketplace Stripe-Connect plugin.
 * Version: 1.1.2
 * Author: Webkul
 * Author URI: http://webkul.com
 * Network: true
 * License: GNU/GPL for more info see license.txt included with plugin
 * License URI: https://store.webkul.com/license.html
 * Text Domain: marketplace-stripe
 *
 **/
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'wk_stripe_plugin_action_links');

define('WK_MP_STRIPE', plugin_dir_url(__FILE__));

define('WK_MP_STRIPE_DIR', plugin_dir_path(__FILE__));

define('MP_STRIPE_PLUGIN_FILE', __FILE__);
/**
 * Add stripe link.
 *
 * @param array $links payment links
 *
 * @return array payment links
 */
function wk_stripe_plugin_action_links($links)
{
    $links[] = '<a href="'.esc_url(admin_url('admin.php?page=wc-settings&tab=checkout&section=stripe-connect')).'">'.__('Settings', 'marketplace-stripe').'</a>';

    return $links;
}

add_action('plugins_loaded', 'woocommerce_stripe_connect_init', 0);

/**
 * Returns bool for dependency availability.
 *
 * @return bool
 */
function stripe_dependencies_satisfied()
{
    $woocommerce_minimum_met = class_exists('WooCommerce');
    if (!$woocommerce_minimum_met) {
        return false;
    }
    $marketplace_minimum_met = class_exists('Marketplace');
    if (!$marketplace_minimum_met) {
        return false;
    }

    return true;
}

/**
 * Plugin Initialize.
 *
 * @return [type] [description]
 */
function woocommerce_stripe_connect_init()
{
	if (stripe_dependencies_satisfied()) {

		load_plugin_textdomain('marketplace-stripe', false, '/languages');
	
		require WK_MP_STRIPE_DIR.'include/class-wc-stripe-connect.php';
	
		/**
		 * Encrypt method.
		 *
		 * @param string $pure_string    pure string
		 * @param string $encryption_key encripted string
		 */
		function encrypt($pure_string, $encryption_key)
		{
			$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
			$encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, $encryption_key, utf8_encode($pure_string), MCRYPT_MODE_ECB, $iv);
	
			return $encrypted_string;
		}
	
		/**
		 * Decrypt method.
		 *
		 * @param string $encrypted_string encryped string
		 * @param string $encryption_key   encripted key
		 */
		function decrypt($encrypted_string, $encryption_key)
		{
			$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
			$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
			$decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $encryption_key, $encrypted_string, MCRYPT_MODE_ECB, $iv);
	
			return $decrypted_string;
		}
	
		/**
		 * Add the gateway to woocommerce.
		 *
		 * @param array $methods payment methord array
		 */
		function add_stripe_connect_gateway($methods)
		{
			$methods[] = 'WC_Stripe_Connect';
	
			return $methods;
		}
	
		add_filter('woocommerce_payment_gateways', 'add_stripe_connect_gateway');
		
	} else {
		add_action('admin_notices', 'mpstripe_add_admin_error_notice');

		return;
	}
}

/**
 * Add admin notice.
 */
function mpstripe_add_admin_error_notice()
{
    deactivate_plugins(plugin_basename(MP_STRIPE_PLUGIN_FILE), true);
    $message = sprintf(
        esc_html__('The Marketplace Stripe Connect plugin for WooCommerce requires <a href="%s">WooCommerce</a> and <a href="%s">Marketplace</a> to be installed and active.', 'marketplace-stripe'),
        'https://wordpress.org/plugins/woocommerce/',
        'https://codecanyon.net/item/wordpress-woocommerce-marketplace-plugin/19214408'
    );
    printf('<div class="error"><p>%s</p></div>', $message);
}

add_action('wp_footer', 'add_stripe_connect_script');

/**
 * Save seller details.
 * 
 * @param array $postdata post request data.
 * @param int   $user_id current user id.
 */
function mp_save_seller_stripe_details( $postdata, $user_id ){
	
	global $wpdb;

	$stripe_val = array();
	if (isset($postdata['mp_seller_payment_stripe_method'])) {
		$stripe_val = $postdata['mp_seller_payment_stripe_method'];
	}
	if (isset($postdata['stripe_user_id'])) {
		update_user_meta($user_id, 'stripe_user_id', $postdata['stripe_user_id']);
		update_user_meta($user_id, 'mp_seller_payment_method', 'wk_stripe_connect');
	}

	if (isset($postdata['mp_seller_payment_stripe_method']) && isset($postdata['stripe_user_id'])) {
		$payment_methods = $wpdb->get_results("select seller_payment_method from {$wpdb->prefix}mpcommision where seller_id='" . $user_id . "'");
		$payment_methods_s = maybe_unserialize($payment_methods);

		if (!empty($payment_methods_s)) {
			foreach ($payment_methods_s[0] as $key => $value) {
				$value = maybe_unserialize($value);
			}
		}
		if (!empty($value)) {
			$payment_methods = array_merge($value, $stripe_val);
		} else {
			$payment_methods = $stripe_val;
		}

		$payment_methods = maybe_serialize($payment_methods);

		$wpdb->get_results("update {$wpdb->prefix}mpcommision  set seller_payment_method='" . $payment_methods . "',payment_id_desc='" . $postdata['wkmppaydesci'] . "' where seller_id='" . $user_id . "'");

		$page_name = $wpdb->get_var("SELECT post_name FROM $wpdb->posts WHERE post_name ='" . get_option('wkmp_seller_page_title') . "'");

		wp_safe_redirect(site_url('/' . $page_name . '/profile/edit'));
		die;
	}
}

add_action('mp_save_seller_profile_details', 'mp_save_seller_stripe_details', 10, 2);

/**
 * Add stripe form.
 */
function add_stripe_connect_script()
{
    global $wpdb;
    $stripe_connect = new WC_Stripe_Connect();
    $stripe_payment_type = $stripe_connect->stripe_payment_mode;
    if ($stripe_payment_type == 'yes') {
        $client_id = $stripe_connect->stripe_test_client_id;
    } else {
        $client_id = $stripe_connect->stripe_live_client_id;
    }
    $stripe_connect_img = WK_MP_STRIPE.'/assets/images/stripe.png';

    $stripeconnecturl = 'https://connect.stripe.com/express/oauth/authorize?redirect_uri=https://elevnn.com/seller/profile/edit/&client_id='.$client_id.'&stripe_user[business_type]=company'; ?>

	<script type="text/javascript">
	(function(wk){

	wk(document).ready(function(){
		var payment_method=wk(this).val();
		jQuery("#mp_seller_payment_stripe_method").attr("href","<?php echo $stripeconnecturl; ?>");
		jQuery("#mp_seller_payment_stripe_method img").attr("src","<?php echo $stripe_connect_img; ?>");

		if ( wk('body').find('#mp_seller_payment_stripe_method').length == 1 ) {
			var error_warning=wk('#stripe_response_error').val();
			var success_message=wk('#stripe_response_success').val();
			var a = wk('#mp_seller_payment_method :selected').val();
			if ( wk.type( error_warning ) != 'undefined' ) {
				var html ='<div class="stripe_show_error" style="color:red;">';
				html +=error_warning;
				html +='</div>';
				wk('.wk-success-check').append(html);
			}
			if ( wk.type(success_message)!='undefined' ) {
				wk('#stripe_connect_button').parent().remove();
				var html ='<div class="stripe_show_success" style="color:green;">';
				html +=wk.trim(success_message);
				html +='</div>';
				wk('.wk-success-check').append(html);
				var stripe_user_id="<?php echo get_user_meta(get_current_user_ID(), 'stripe_user_id', true); ?>";
				wk('#wkmppaydesci').val(stripe_user_id);
			}
		}
	});
	})(jQuery);
	</script>

	<?php
}
add_action('wp_head', 'get_auth_stripe', 10);

/**
 * Authenticating stripe details.
 */
function get_auth_stripe()
{
    $error_warning = '';
    $client_id = '';
    $success_message = '';
    $code = '';

    if (isset($_GET['code'])) {
        $code = $_GET['code'];
        $client_secret = '';
        $stripe_connect = new WC_Stripe_Connect();
        $stripe_payment_type = $stripe_connect->stripe_payment_mode;
        if ($stripe_payment_type == 'yes') {
            $client_secret = $stripe_connect->stripe_test_secret_key;
            $client_id = $stripe_connect->stripe_test_client_id;
        } else {
            $client_secret = $stripe_connect->stripe_live_secret_key;
            $client_id = $stripe_connect->stripe_live_client_id;
        }

        $token_request_body = array(
            'grant_type' => 'authorization_code',
            'client_id' => $client_id,
            'code' => $code,
            'client_secret' => $client_secret,
        );

        $req = curl_init('https://connect.stripe.com/oauth/token');
        curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($req, CURLOPT_POST, true);
        curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));
        curl_setopt($req, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($req, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($req, CURLOPT_VERBOSE, true);
        $respcode = curl_getinfo($req, CURLINFO_HTTP_CODE);
		$resp = json_decode(curl_exec($req), true);
        if (isset($resp['error'])) {
            $error_warning = $resp['error'].':'.$resp['error_description'];
        } else {
            $access_token = $resp['access_token'];
            add_stripe_connect($resp);
            $success_message = 'Connected Successfully';
        }
        curl_close($req);
    } elseif (isset($_GET['error'])) {
        $error_warning = $_GET['error_description'];
    } else {
        $authorize_request_body = array(
            'response_type' => $code,
            'scope' => 'read_write',
            'client_id' => $client_id,
        );
    }

    if ('' !== $error_warning && !defined('DOING_AJAX')) {
        echo '<input type="hidden" id="stripe_response_error" value="'.esc_html($error_warning).'">';
    } elseif ('' !== $success_message && !defined('DOING_AJAX')) {
        echo '<input type="hidden" id="stripe_response_success" value="'.esc_html($success_message).'">';
    }
}

/**
 * Add Stripe info.
 *
 * @param array $resp api response
 */
function add_stripe_connect($resp)
{
    $token_type = $resp['token_type'];
    $stripe_publishable_key = $resp['stripe_publishable_key'];
    $livemode = $resp['livemode'];
	$stripe_user_id = $resp['stripe_user_id'];
    $refresh_token = $resp['refresh_token'];
    $access_token = $resp['access_token'];
    $user_id = get_current_user_ID();
    update_user_meta($user_id, 'mp_seller_payment_method', 'wk_stripe_connect');
    update_user_meta($user_id, 'stripe_token_type', $token_type);
    update_user_meta($user_id, 'stripe_publishable_key', $stripe_publishable_key);
    update_user_meta($user_id, 'stripe_livemode', $livemode);
    update_user_meta($user_id, 'stripe_user_id', $stripe_user_id);
    update_user_meta($user_id, 'stripe_refresh_token', $refresh_token);
    update_user_meta($user_id, 'stripe_access_token', $access_token);
}

?>
