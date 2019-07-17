<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Adding the id var so that WP recognizes it.
 *
 * @param array $vars .
 */
function wp_insertcustom_vars( $vars ) {
		$vars[] = 'main_page';
		$vars[] = 'pagename';
		$vars[] = 'pid';
		$vars[] = 'sid';
		$vars[] = 'action';
		$vars[] = 'info';
		$vars[] = 'shop_name';
		$vars[] = 'order_id';
		$vars[] = 'ship';
		$vars[] = 'zone_id';
		$vars[] = 'pagenum';
		$vars[] = 'ship_page';
		return $vars;
}

/**
 * Function insert rules.
 *
 * @param array $rules rules.
 */
function wp_insertcustom_rules( $rules ) {

		global $wpdb, $wp_query;
		$page_name = $wpdb->get_var( "SELECT post_name FROM $wpdb->posts WHERE post_name ='" . get_option( 'wkmp_seller_page_title' ) . "'" );

		$my_account = get_post( get_option( 'woocommerce_myaccount_page_id' ) );
		$my_account = $my_account->post_name;

		$newrules = array();
		$newrules = array(
			'(.+)/(.+)/shipping/edit/([0-9]+)/?'          => 'index.php?pagename=$matches[1]&main_page=$matches[2]&ship=shipping&action=edit&zone_id=$matches[3]',
			'(.+)/(.+)/shipping/add/?'                    => 'index.php?pagename=$matches[1]&main_page=$matches[2]&ship=shipping&action=add',
			'(.+)/(.+)/edit/([0-9]+)/?'                   => 'index.php?pagename=$matches[1]&main_page=$matches[2]&action=edit&pid=$matches[3]',
			'(.+)/(.+)/view/([0-9]+)/?'                   => 'index.php?pagename=$matches[1]&main_page=$matches[2]&action=view&pid=$matches[3]',
			$page_name . '/(.+)/shipping/?'               => 'index.php?pagename='.$page_name.'&main_page=$matches[1]&ship_page=shipping',
			'(.+)/invoice/(.+)/?'                         => 'index.php?pagename=$matches[1]&main_page=invoice&order_id=$matches[2]',
			'(.+)/(.+)/delete/([0-9]+)/?'                 => 'index.php?pagename=$matches[1]&main_page=$matches[2]&action=delete&pid=$matches[3]',
			'(.+)/order-history/([0-9]+)/?'               => 'index.php?pagename=$matches[1]&main_page=order-history&order_id=$matches[2]',
			$page_name . '/([-a-z]+)/(.+)/page/([0-9]+)?' => 'index.php?pagename=' . $page_name . '&main_page=$matches[1]&info=$matches[2]&action=page&pagenum=$matches[3]',
			$page_name . '/([-a-z]+)/(.+)/?'              => 'index.php?pagename=' . $page_name . '&main_page=$matches[1]&info=$matches[2]',
			'(.+)/seller-product/(.+)/?'                  => 'index.php?pagename=$matches[1]&main_page=seller-product&info=$matches[2]',
			$my_account . '/(.+)/(.+)?'                   => 'index.php?pagename=' . $my_account . '&$matches[1]=$matches[2]&$matches[2]',
			$my_account . '/(.+)/?'                       => 'index.php?pagename=' . $my_account . '&$matches[1]',
			'(.+)/(.+)/?'                                 => 'index.php?pagename=$matches[1]&main_page=$matches[2]',
		);

		return $newrules + $rules;
}
