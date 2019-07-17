<?php
/**
 * Plugin Name: Marketplace conflict fixing
 * Plugin URI: https://store.webkul.com/Wordpress-Woocommerce-Marketplace.html
 * Description: WordPress WooCommerce Marketplace conflict fixing is use to fix the conflict issue with other plugins or theme.
 * Version: 1.0.0
 * Author: Webkul
 * Author URI: http://webkul.com
 * License: GNU/GPL for more info see license.txt included with plugin
 * License URI: https://store.webkul.com/license.html
 * Text Domain: wkmp-cf
 * WC requires at least: 3.0.0
 * WC tested up to: 3.6.x.
 *
 **/

/*---------------------------------------------------------------------------------------------*/
defined('ABSPATH') || exit;

define('MPCF_VERSION', '1.0.0');

define('MPCF_SCRIPT_VERSION', '1.0.0');

define('MPCF_PLUGIN_FILE', __FILE__);

define('MARKETPLACE_CF_VERSION', MPCF_VERSION);

define('MPCF_CF', plugin_dir_url(__FILE__));

define('MPCF_CF_DIR', plugin_dir_path(__FILE__));

if (!function_exists('mpcf_init_install')) {
    function mpcf_init_install()
    {
        if (!class_exists('Marketplace')) {
            add_action('admin_notices', 'mpcf_missing_notice');
        } else {
            add_action('wp','mpcf_enqueue_script');
        }
    }
    add_action('plugins_loaded', 'mpcf_init_install', 99);
}

/**
 * Function to show message if woocommerce is not installed.
 */
function mpcf_missing_notice()
{
    echo '<div class="error"><p>' . sprintf(esc_html__('Marketplace conflict fixing depends on the last version of %s or later to work!', 'wkmp-cf'), '<a href="https://store.webkul.com/Wordpress-Woocommerce-Marketplace.html" target="_blank">' . esc_html__('Marketplace', 'wkmp-cf') . '</a>') . '</p></div>';
}

function mpcf_enqueue_script(){
  global $wpdb;

  $page_name = $wpdb->get_var( "SELECT post_name FROM $wpdb->posts WHERE post_name ='" . get_option( 'wkmp_seller_page_title' ) . "'" );

  if ( ( isset( get_queried_object()->post_name ) && get_queried_object()->post_name == $page_name) || isset( get_queried_object()->post_name ) && get_queried_object()->post_name == 'my-account' ) {
     wp_enqueue_style('mpcf-style', MPCF_CF . 'assets/css/style.css');
  }
}
