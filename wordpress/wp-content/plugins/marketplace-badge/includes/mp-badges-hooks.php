<?php
/**
 * Seller badge hooks.
 *
 * @package marketplace-badge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*To add Menu */
add_action( 'admin_menu', array( $this, 'create_menu' ) );

/*To enable scripts in admin section */
add_action( 'admin_enqueue_scripts', array( $this, 'admin_style' ) );

/*To add Shortcode menu in seller profile*/
add_action( 'init', array( $this, 'seller_profile' ) );

/* To enable admin script  */
add_action( 'admin_enqueue_scripts', array( $this, 'admin_script' ) );

/*To add Title */
add_filter( 'the_title', array( $this, 'mp_hide_page_title' ) );

/**To add front end script */
add_filter( 'wp_enqueue_scripts', array( $this, 'front_script' ) );

/*To display Badges at Market Place seller shop */
add_action( 'mkt_before_seller_preview_products', array( $this, 'mp_display_badge' ) );
/*To add Screen Options */
add_filter( 'set-screen-option', 'apply_screen_options', 10, 3 );
