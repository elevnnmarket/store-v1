<?php
/**
 * Plugin Name: Stripe Connect
 * Plugin URI: http://store.webkul.com/Wordpress-Plugins.html
 * Description: WordPress WooCommerce Marketplace Stripe-Connect plugin.
 * Version: 1.1.1
 * Author: Webkul
 * Author URI: http://webkul.com
 * Domain Path: plugins/woocommerce-marketplace-stripe-connect
 * Network: true
 * License: GNU/GPL for more info see license.txt included with plugin
 * License URI: http://www.gnu.org/licenseses/gpl-2.0.html
 * Text Domain: marketplace-stripe
 *
 * @package wp-stripe-connect.
 **/

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wk_stripe_plugin_action_links' );

define( 'WK_MP_STRIPE', plugin_dir_url( __FILE__ ) );

define( 'WK_MP_STRIPE_DIR', plugin_dir_path( __FILE__ ) );
/**
 * Add stripe link.
 *
 * @param  array $links payment links.
 * @return array payment links.
 */
function wk_stripe_plugin_action_links( $links ) {
	$links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=stripe-connect' ) ) . '">Settings</a>';
	return $links;
}

add_action( 'plugins_loaded', 'woocommerce_stripe_connect_init', 0 );

/**
 * Plugin Initialize
 *
 * @return [type] [description]
 */
function woocommerce_stripe_connect_init() {

	load_plugin_textdomain( 'marketplace-stripe', false, '/languages' );

	require WK_MP_STRIPE_DIR . 'include/class-wc-stripe-connect.php';

	/**
	 * Encrypt method.
	 *
	 * @param string $pure_string pure string.
	 * @param string $encryption_key encripted string.
	 */
	function encrypt( $pure_string, $encryption_key ) {
		$iv_size          = mcrypt_get_iv_size( MCRYPT_BLOWFISH, MCRYPT_MODE_ECB );
		$iv               = mcrypt_create_iv( $iv_size, MCRYPT_RAND );
		$encrypted_string = mcrypt_encrypt( MCRYPT_BLOWFISH, $encryption_key, utf8_encode( $pure_string ), MCRYPT_MODE_ECB, $iv );
		return $encrypted_string;
	}

	/**
	 * Decrypt method.
	 *
	 * @param string $encrypted_string encryped string.
	 * @param string $encryption_key   encripted key.
	 */
	function decrypt( $encrypted_string, $encryption_key ) {
		$iv_size          = mcrypt_get_iv_size( MCRYPT_BLOWFISH, MCRYPT_MODE_ECB );
		$iv               = mcrypt_create_iv( $iv_size, MCRYPT_RAND );
		$decrypted_string = mcrypt_decrypt( MCRYPT_BLOWFISH, $encryption_key, $encrypted_string, MCRYPT_MODE_ECB, $iv );
		return $decrypted_string;
	}

	/**
	 * Add the gateway to woocommerce
	 *
	 * @param array $methods payment methord array.
	 */
	function add_stripe_connect_gateway( $methods ) {
		$methods[] = 'WC_Stripe_Connect';
		return $methods;
	}

	add_filter( 'woocommerce_payment_gateways', 'add_stripe_connect_gateway' );

}

add_action( 'wp_footer', 'add_stripe_connect_script' );

/**
 * Add stripe form.
 */
function add_stripe_connect_script() {
	global $wpdb;
	$stripe_connect      = new WC_Stripe_Connect();
	$stripe_payment_type = $stripe_connect->stripe_payment_mode;
	if ( $stripe_payment_type == 'yes' ) {
		$client_id = $stripe_connect->stripe_test_client_id;
	} else {
		$client_id = $stripe_connect->stripe_live_client_id;
	}
	$stripe_connect_img = WK_MP_STRIPE . '/assets/images/stripe.png';

	$stripeconnecturl = 'https://connect.stripe.com/oauth/authorize?response_type=code&client_id=' . $client_id . '&stripe_landing=register&scope=read_write';
	?>
	<script type="text/javascript">
	(function(wk){

	wk(document).ready(function(){
		var payment_method=wk(this).val();
		jQuery("#mp_seller_payment_stripe_method").attr("href","<?php echo ( $stripeconnecturl ); ?>");
		jQuery("#mp_seller_payment_stripe_method img").attr("src","<?php echo ( $stripe_connect_img ); ?>");

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
				var stripe_user_id="<?php echo get_user_meta( get_current_user_ID(), 'stripe_user_id', true ); ?>";
				wk('#wkmppaydesci').val(stripe_user_id);
			}
		}
	});
	})(jQuery);
	</script><?php
}
add_action( 'plugins_loaded', 'get_auth_stripe', 10 );

/**
 * Authenticating stripe details.
 */
function get_auth_stripe() {
	$error_warning   = '';
	$client_id       = '';
	$success_message = '';
	$code            = '';

	if ( isset( $_GET['code'] ) ) {
		$code                = $_GET['code'];
		$client_secret       = '';
		$stripe_connect      = new WC_Stripe_Connect();
		$stripe_payment_type = $stripe_connect->stripe_payment_mode;
		if ( $stripe_payment_type == 'yes' ) {
			$client_secret = $stripe_connect->stripe_test_secret_key;
			$client_id     = $stripe_connect->stripe_test_client_id;
		} else {
			$client_secret = $stripe_connect->stripe_live_secret_key;
			$client_id     = $stripe_connect->stripe_live_client_id;
		}

		$token_request_body = array(
			'grant_type'    => 'authorization_code',
			'client_id'     => $client_id,
			'code'          => $code,
			'client_secret' => $client_secret,
		);

		$req = curl_init( 'https://connect.stripe.com/oauth/token' );
		curl_setopt( $req, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $req, CURLOPT_POST, true );
		curl_setopt( $req, CURLOPT_POSTFIELDS, http_build_query( $token_request_body ) );
		curl_setopt( $req, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $req, CURLOPT_SSL_VERIFYHOST, 2 );
		curl_setopt( $req, CURLOPT_VERBOSE, true );
		$respcode = curl_getinfo($req, CURLINFO_HTTP_CODE);
		$resp     = json_decode(curl_exec($req), true);
		if ( isset( $resp['error'] ) ) {
			$error_warning = $resp['error'] . ':' . $resp['error_description'];
		} else {
			$access_token = $resp['access_token'];
			add_stripe_connect( $resp );
			$success_message = 'Connected Successfully';
		}
		curl_close( $req );
	} elseif ( isset( $_GET['error'] ) ) {
		$error_warning = $_GET['error_description'];
	} else {
		$authorize_request_body = array(
			'response_type' => $code,
			'scope'         => 'read_write',
			'client_id'     => $client_id,
		);
	}

	if ( '' !== $error_warning && ! defined( 'DOING_AJAX' ) ) {

		echo '<input type="hidden" id="stripe_response_error" value="' . esc_html( $error_warning ) . '">';
	} elseif ( '' !== $success_message && ! defined( 'DOING_AJAX' ) ) {

		echo '<input type="hidden" id="stripe_response_success" value="' . esc_html( $success_message ) . '">';
	}
}

/**
 * Add Stripe info.
 *
 * @param array $resp api response.
 */
function add_stripe_connect( $resp ) {
	$token_type             = $resp['token_type'];
	$stripe_publishable_key = $resp['stripe_publishable_key'];
	$livemode               = $resp['livemode'];
	$stripe_user_id         = $resp['stripe_user_id'];
	$refresh_token          = $resp['refresh_token'];
	$access_token           = $resp['access_token'];
	$user_id                = get_current_user_ID();
	update_user_meta( $user_id, 'mp_seller_payment_method', 'Credit Card (Stripe Connect)', true );
	update_user_meta( $user_id, 'stripe_token_type', $token_type, true );
	update_user_meta( $user_id, 'stripe_publishable_key', $stripe_publishable_key, true );
	update_user_meta( $user_id, 'stripe_livemode', $livemode, true );
	update_user_meta( $user_id, 'stripe_user_id', $stripe_user_id, true );
	update_user_meta( $user_id, 'stripe_refresh_token', $refresh_token, true );
	update_user_meta( $user_id, 'stripe_access_token', $access_token, true );
}
?>
