<?php
/**
 * Plugin Name: Marketplace Sellers Badges
 * Author: Webkul
 * Description: Marketplace Seller Badge Plugin will provide functionality to add and assign Badges to MarketPlace Seller
 * Version: 1.0.1
 * Author: Webkul
 * Text Domain: wk-seller-badge
 * Author URI: http://webkul.com
 * Domain Path: /languages/.
 */

/*
 *if direct access then exit
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!defined('WK_MP')) {
    define('WK_MP', plugin_dir_url(__FILE__));
}
if (!defined('WK_MP_DIR')) {
    define('WK_MP_DIR', plugin_dir_path(__FILE__));
}
if (!defined('WK_MP_BADGE_DIR')) {
    define('WK_MP_BADGE_DIR', __FILE__);
}

if (!class_exists('Markeplace_Badges')) :
    /**
     * Class Markeplace_Badges.
     */
    class Markeplace_Badges
    {
        /**
         * Class variabale.
         *
         * @var Handler to handle the files and function
         */
        protected $handler;

        /**
         * Class construtor.
         */
        public function __construct()
        {
            $this->includes();
            $this->handler = new MP_Badges_Handler();
        }

        /**
         * Includes files.
         */
        public function includes()
        {
            if (is_admin()) {
                require_once 'includes/class-mp-badges-install.php';
                require_once 'includes/admin/class-get-seller.php';
                require_once 'includes/admin/class-mp-assign-badge.php';
                require_once 'includes/templates/admin/class-mp-bage-list.php';
                require_once 'includes/templates/admin/class-manage-seller-badge.php';
            }
            require_once 'includes/class-mp-badges-handler.php';
        }
    }

endif;

add_action('plugins_loaded', 'mp_badges');

/**
 * Returns bool for dependency availability.
 *
 * @return bool
 */
function mp_badge_dependencies_satisfied()
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
 * Initiate class.
 */
function mp_badges()
{
    if (mp_badge_dependencies_satisfied()) {
        load_plugin_textdomain('wk-seller-badge', false, dirname(plugin_basename(__FILE__)).'/langua g es/');

        new Markeplace_Badges();
    } else {
        add_action('admin_notices', 'mp_seller_badge_add_admin_error_notice');

        return;
    }
}
/**
 * Add admin notice.
 */
function mp_seller_badge_add_admin_error_notice()
{
    deactivate_plugins(plugin_basename(WK_MP_BADGE_DIR), true);
    $message = sprintf(
        __('The Marketplace Seller Badge plugin for WooCommerce requires <a href="%s">WooCommerce</a> and <a href="%s">Marketplace</a> to be installed and active.', 'wk-mp-brs'),
        'https://wordpress.org/plugins/woocommerce/',
        'https://codecanyon.net/item/wordpress-woocommerce-marketplace-plugin/19214408'
    );
    printf('<div class="error"><p>%s</p></div>', $message);
}
