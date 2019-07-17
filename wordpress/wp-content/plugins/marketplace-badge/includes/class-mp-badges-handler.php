<?php
/**
 * Handles actions.
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('MP_Badges_Handler')) :
    /**
     * Class MP_Badges_Handler.
     */
    class MP_Badges_Handler
    {
        /**
         * Page title dispaly.
         *
         * @var
         */
        protected $page_title_display = 1;

        /**
         * Class constructor.
         */
        public function __construct()
        {
            require_once 'mp-badges-hooks.php';
        }

        /**
         * Admin style.
         */
        public function admin_style()
        {
            wp_enqueue_style('adminstyles', WK_MP.'assets/css/admin.css');
        }

        /**
         * Loads admin side scripts.
         */
        public function admin_script()
        {
            wp_enqueue_script('admin_image_js', WK_MP.'assets/js/admin.js');
        }

        /**
         * Loads front script.
         */
        public function front_script()
        {
            wp_enqueue_script('front_js', WK_MP.'assets/js/front-js.js');
        }

        /**
         * Hide page title.
         *
         * @param string $title page title
         */
        public function mp_hide_page_title($title)
        {
            global $wpdb, $wp_query;
            $page_name = $wpdb->get_var("SELECT post_name FROM $wpdb->posts WHERE post_name ='".get_option('wkmp_seller_page_title')."'");
            if (in_the_loop() && is_page($page_name) && $this->page_title_display == 1) {
                $this->page_title_display = 0;
                if ((null !== get_query_var('ship_page') && get_query_var('ship_page') == 'shipping') || (null !== get_query_var('ship') && get_query_var('ship') == 'shipping')) {
                    return __('Shipping Zone', 'marketplace');
                }
                if (null !== get_query_var('main_page')) {
                    $main_page = get_query_var('main_page');
                    switch ($main_page) {
                        case 'badge-form':
                            return __('Seller Badges', 'wk-seller-badge');
                        default:
                            return '';
                    }
                }
            }

            return $title;
        }

        /**
         * To add the Badge Manager menu.
         */
        public function create_menu()
        {
            $hook = add_menu_page(__('Marketplace Seller Badge ', 'wk-seller-badge'), __('Marketplace Seller Badge', 'wk-seller-badge'), 'manage_options', 'mp-badges', array($this, 'mp_badge_manager'), 'dashicons-awards');

            add_submenu_page('mp-badges', __('Badge Manager', 'wk-seller-badge'), __('Badge Manager', 'wk-seller-badge'), 'manage_options', 'mp-badges', array($this, 'mp_badge_manager'));

            add_submenu_page('mp-badges', __('Add New Badge', 'wk-seller-badge'), __('Add New Badge', 'wk-seller-badge'), 'manage_options', 'adding-badges', array($this, 'add_badges'));

            $hook1 = add_submenu_page('mp-badges', __('Manage Seller Badge', 'wk-seller-badge'), __('Seller Badge Manager', 'wk-seller-badge'), 'manage_options', 'manage-seller-badges', array($this, 'manage_seller_badges'));

            add_action('load-'.$hook, array($this, 'screen_options_badges'));
            add_action('load-'.$hook1, array($this, 'screen_options_manage_seller_badge'));
        }

        /**
         * Badge manage option.
         */
        public function mp_badge_manager()
        {
            $mp_manage_badge = new MP_Manage_Badge();
            echo '</pre><div class="wrap"><h1 class="wp-heading-inline">'.esc_html__('Badge List', 'wk-seller-badge').' </h1>';
            echo '<a href="?page=adding-badges" class="page-title-action">'.esc_html__('Add New', 'wk-seller-badge').'</a>'; ?>
<form method="GET">
    <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>">
    <br>
    <?php
    $mp_manage_badge->prepare_items();
            $mp_manage_badge->search_box(__('search', 'wk-seller-badge'), 'search_id');
            $mp_manage_badge->display();
            echo '</form></div>';
        }

        /**
         * Add badge option.
         */
        public function add_badges()
        {
            require_once 'admin/class-mp-save-badges.php';
            require_once 'templates/admin/add-Badges.php';
        }

        /**
         * Manage seller badge.
         */
        public function manage_seller_badges()
        {
            require_once 'admin/class-mp-assign-badge.php';
            require_once 'admin/class-get-seller.php';
            $mp_seller_badge_manager = new MP_Sellers_Badge_Manager();
            echo '</pre><div class="wrap"><h1 class="wp-heading-inline">'.esc_html__('Seller Badge Manager', 'wk-seller-badge').'</h1>';
            $mp_seller_badge_manager->prepare_items(); ?>
    <form method="POST">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>">
        <?php
        $mp_seller_badge_manager->search_box(__('search', 'wk-seller-badge'), 'search_id');
            $mp_seller_badge_manager->display();
            echo '</form>';
        }

        /**
         * Display Badges on Seller Dashboard.
         */
        public function seller_profile()
        {
            global $wp;
            $wp->add_query_var('main_page');
            require_once 'front/class-mp-seller-profile.php';
            $mp_seller_profile = new MP_Seller_Profile();
            require_once 'front/mp-add-badge-menu.php';
        }

        /**
         * To Display the Badges on seller shop.
         */
        public function mp_display_badge()
        {
            $sellerurl = urldecode(get_query_var('info'));

            $user = get_users(array(
                'meta_key' => 'shop_address',
                'meta_value' => $sellerurl,
            ));
            if (!empty($user)) {
                foreach ($user as $value) {
                    $sellerid = $value->ID;
                }
            }

            global $wpdb;
            $seller_user_data = get_user_by('ID', $sellerid);
            $mp_seller = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mpbadge_assign where u_id='$sellerid'");
            if (!$mp_seller) {
                // echo '<h3>NO Badge assigned</h3>';
                return;
            }

            foreach ($mp_seller as $key) {
                $badge = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mpbadge where id='$key->b_id' and status ='1'");

                $i = 0;

                foreach ($badge as $key3) {
                    ++$i;
                }
            }
            if ($i == 0) {
                // echo '<h4>No badge Assigned</h4>';
            } else {
                ?>

        <h4><?php esc_html_e('Seller badge(s)', 'wk-seller-badge'); ?></h4>
        <div id="mp-badge">

            <?php
            foreach ($mp_seller as $key) {
                $badge = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mpbadge where id='$key->b_id' and status ='1'");

                foreach ($badge as $key3) {
                    ?>
            <img class="mp-badge-icon" title='<?php echo $key3->b_name.' '.esc_html__('Badge', 'wk-seller-badge').' &#10; '.$key3->b_des; ?>' src='<?php echo wp_get_attachment_url($key3->tumbnail); ?>' style="width:100px; display: inline-block;">
            <?php
                }
            } ?>
        </div>
        <?php
            }
        }

        /**
         * Screen option badge.
         */
        public function screen_options_badges()
        {
            $option = 'per_page';
            $args = array(
                'label' => __('Badges Per Page', 'wk-seller-badge'),
                'default' => 5,
                'options' => 'badge_per_page',
            );
            add_screen_option($option, $args);
        }

        /**
         * Screen options.
         */
        public function screen_options_manage_seller_badge()
        {
            $option = 'per_page';
            $args = array(
                'label' => __('Seller Per Page', 'wk-seller-badge'),
                'default' => 5,
                'options' => 'sellers_per_page',
            );
            add_screen_option($option, $args);
        }
    }
endif;

/**
 * Add screen options.
 *
 * @param bool   $status status
 * @param string $option option value
 * @param string $value  option value
 */
function apply_screen_options($status, $option, $value)
{
    if ('toplevel_page_mp_badges_per_page' == $option) {
        return $value;
    }

    if ('marketplace_seller_badge_page_manage_seller_badges_per_page' == $option) {
        return $value;
    }
}
