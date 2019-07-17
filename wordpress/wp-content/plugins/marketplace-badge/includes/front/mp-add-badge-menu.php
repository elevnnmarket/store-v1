<?php
/**
 * Add badge menu.
 *
 * @package marketplace-badge.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Call badge function.
 */
function call_badges() {

	$sellerurl = urldecode( get_query_var( 'info' ) );

	$user = get_users( array(
		'meta_key'   => 'shop_address',
		'meta_value' => $sellerurl,
	) );
	if ( ! empty( $user ) ) {

		foreach ( $user as $value ) {
			$sellerid = $value->ID;
		}
		global $current_user, $wpdb, $wp_query;
		$current_user = wp_get_current_user();
		$seller_info  = $wpdb->get_var( "SELECT user_id FROM " . $wpdb->prefix . "mpsellerinfo WHERE user_id = '" . $current_user->ID . "' and seller_value='seller'" );
		$pagename     = $wpdb->get_var( "SELECT post_name FROM $wpdb->posts WHERE post_name ='" . get_option( 'wkmp_seller_page_title' ) . "'" );
		$main_page    = get_query_var( 'main_page' );
		global $wp;
		$my_account = get_post( get_option( 'woocommerce_myaccount_page_id' ) );
		if ( ! empty( $pagename ) ) {
			if ( $main_page == 'badge-form'  && ( $current_user->ID || $seller_info > 0 ) ) {
				add_shortcode( 'marketplace', 'badges_' );
			} elseif ( $main_page == 'store' ) {

			}
		}
	}
}

/**
 * Template file load.
 */
function badges_() {
	require_once WK_MP_DIR . 'includes/templates/front/view-seller-badges.php';
}

add_action( 'wp_head', 'call_badges' );
