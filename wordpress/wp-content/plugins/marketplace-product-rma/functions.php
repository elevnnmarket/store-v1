<?php
/**
 * Plugin Name: Marketplace Product Return RMA
 * Plugin URI: http://store.webkul.com
 * Description: Marketplace Product Return RMA module allows you to organize a system for customers to request a return without any efforts. RMA is very useful for product return and order return. With the help of this module, a customer can return the products, have them exchanged or refunded within the admin specified time limit.
 * Version: 1.1.0
 * Author: Webkul
 * Author URI: https://webkul.com
 * Domain Path: plugins/wp-woocommerce-product-return-rma
 * License: GNU/GPL for more info see license.txt included with plugin
 * License URI: https://www.gnu.org/licenses/gpl-2.0.en.html
 * Text Domain: marketplace-rma
**/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

! defined( 'MP_RMA_PATH' ) && define( 'MP_RMA_PATH', plugin_dir_path(__FILE__) );
! defined( 'MP_RMA_URL' ) && define( 'MP_RMA_URL', plugin_dir_url(__FILE__) );

if( ! function_exists( 'mp_rma_install' ) )
{

    function mp_rma_install()
    {

    	if ( !class_exists( 'Marketplace' ) )
    	{

            add_action('admin_notices', 'mp_rma_install_marketplace_admin_notice');

        } else
        {

        		new MP_Woo_RMA();

            do_action( 'mp_rma_init' );

            $wk_obj = new MP_RMA_Install();

            $wk_obj->mp_rma_activation();

        }

    }

    add_action( 'plugins_loaded', 'mp_rma_install', 11 );

}

function mp_rma_install_marketplace_admin_notice()
{

		?>
    <div class="error">
        <p><?php _ex( 'Woocommerce Product Return RMA is enabled but not effective. It requires <a href="https://codecanyon.net/item/wordpress-woocommerce-marketplace-plugin/19214408" target="_blank">Marketplace Plugin</a> in order to work.', 'Alert Message: Marketplace requires', 'marketplace-rma' ); ?></p>
    </div>
    <?php

}

if (!class_exists('MP_Woo_RMA')) {

	/**
	* 	Seller rma: Main Class
	*/
	class MP_Woo_RMA
	{

  		function __construct()
  		{

				  ob_start();

			    add_action( 'mp_rma_init', array( $this, 'mp_rma_init' ) );

				  add_action( 'init', array( $this, 'mp_rma_add_endpoints' ) );

  		}

			/**
			* Register new endpoint to use inside My Account page.
			*
			*/
			public function mp_rma_add_endpoints()
			{
				add_rewrite_endpoint( 'rma', EP_ROOT | EP_PAGES );
			}

  		function mp_rma_init()
  		{

					require_once( sprintf('%s/includes/class-mp-rma-ajax-functions.php', dirname(__FILE__) ) );
  			  require_once( sprintf('%s/includes/class-mp-rma.php', dirname(__FILE__) ) );

          new MP_Wk_RMA();

  			  add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'mp_rma_plugin_settings_link' ) );

  			  add_action( 'admin_enqueue_scripts', array( $this, 'mp_rma_admin_scripts' ) );

  			  add_action( 'wp_enqueue_scripts', array( $this, 'mp_rma_front_scripts' ) );

  		}

  		function mp_rma_plugin_settings_link( $links )
  		{

    			$url = 'https://wordpressdemo.webkul.com';

    			$settings_link = '<a href="'.$url.'" target="_blank" style="color:green;">' . __( 'More Add-ons', 'wk_mu' ) . '</a>';

    			$links[] = $settings_link;

    			return $links;

  		}

  		function mp_rma_admin_scripts()
  		{

					wp_enqueue_media();

    			wp_enqueue_script( 'mp_rma_admin_js', MP_RMA_URL. 'assets/js/plugin-admin.js', array( 'jquery' ) );

    			wp_enqueue_style( 'mp_rma_admin_css', MP_RMA_URL.'assets/css/style.css');

  		}

  		function mp_rma_front_scripts()
  		{

    			wp_enqueue_script( 'mp_rma_front_js', MP_RMA_URL. 'assets/js/plugin.js', array( 'jquery' ) );

    			wp_enqueue_style( 'mp_rma_front_css', MP_RMA_URL.'assets/css/style.css');

					wp_localize_script( 'mp_rma_front_js', 'mp_rma_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'ajax_nonce' => wp_create_nonce('rma_ajax_nonce') ));

  		}

	}

}
