<?php
/**
 * Seller profile.
 *
 * @package marketplace-badge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MP_Seller_Profile' ) ) :
	/**
	 * Class MP_Seller_Profile
	 */
	class MP_Seller_Profile {

		/**
		 * Class constructor.
		 */
		public function __construct() {
			add_filter( 'mp_woocommerce_account_menu_options', array( $this, 'user_badge' ) );
		}

		/**
		 * To add the menu of badges in seller Dashboard
		 *
		 * @param array $items item array.
		 */
		public function user_badge( $items ) {
			global $wpdb;
			$user_id      = get_current_user_id();
			$new_items    = array();
			$shop_address = get_user_meta( $user_id, 'shop_address', true );
			$page_name    = $wpdb->get_var( "SELECT post_name FROM $wpdb->posts WHERE post_name ='" . get_option( 'wkmp_seller_page_title' ) . "'" );
			$seller_info  = $wpdb->get_var( "SELECT user_id FROM {$wpdb->prefix}mpsellerinfo WHERE user_id = '" . $user_id . "' and seller_value='seller'" );
			$new_items[ '../' . $page_name . '/badge-form' ] = __( 'Badges', 'wk-seller-badge' );

			$new_items = $items + $new_items;
			return $new_items;
		}
	}
endif;
