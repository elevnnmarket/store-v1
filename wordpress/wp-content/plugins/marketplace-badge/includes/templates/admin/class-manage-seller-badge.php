<?php

/**
 * Menu manage seller badge.
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('MP_Sellers_Badge_Manager')) :

    if (!class_exists('WP_List_Table')) {
        require_once ABSPATH.'wp-admin/includes/class-wp-list-table.php';
    }
    /**
     * Class MP_Sellers_Badge_Manager.
     */
    class MP_Sellers_Badge_Manager extends WP_List_Table
    {
        /**
         * Class constructor.
         */
        public function __construct()
        {
            parent::__construct(array(
                'singular' => 'singular_form',
                'plural' => 'plural_form',
                'ajax' => true,
            ));
        }

        /**
         * Set data into array.
         */
        private function table_data()
        {
            $getseller = new MP_Get_Seller_Badge();
            if (isset($_POST['s'])) {
                $search = $_POST['s'];

                return $getseller->get_mp_seller_badge_list($search);
            } else {
                return $getseller->get_mp_seller_badge_list();
            }
        }

        /**
         * Sort table.
         */
        public function sort_table()
        {
            $sortcolumn = array(
                'id' => array('id', false),
                'thumbnail' => array('thumbnail', false),
                'name' => array('name', false),
                'email' => array('email', false),
                'status' => array('status', false),
            );

            return $sortcolumn;
        }

        /**
         * Sort order.
         *
         * @param int $a a
         * @param int $b b
         */
        public function unsort_reorder($a, $b)
        {
            $orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'id';
            $order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
            $result = strcmp($a[$orderby], $b[$orderby]);

            return ($order === 'asc') ? $result : -$result;
        }

        /**
         * Default columns.
         */
        public function get_columns()
        {
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'id' => __('Seller Id', 'wk-seller-badge'),
                'thumbnail' => __('Badge(s)', 'wk-seller-badge'),
                'name' => __('Seller Name', 'wk-seller-badge'),
                'email' => __('Email', 'wk-seller-badge'),
                'status' => __('Status', 'wk-seller-badge'),
            );

            return $columns;
        }

        /**
         * Bulk action.
         */
        public function get_bulk_actions()
        {
            $action = array(
                'assign' => __('Assign Badge', 'wk-seller-badge'),
                'remove' => __('Remove Badge', 'wk-seller-badge'),
            );

            return $action;
        }

        /**
         * Default columns.
         *
         * @param array  $item        item array
         * @param string $column_name column name
         */
        public function column_default($item, $column_name)
        {
            switch ($column_name) {
                case 'id':
                case 'name':
                case 'email':
                case 'status':
                case 'thumbnail':
                    return $item[$column_name];
                default:
                    return print_r($item, true);
            }
        }

        /**
         * Column check box.
         *
         * @param array $item item array
         */
        public function column_cb($item)
        {
            return sprintf(
                '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                'id',
                $item['id']
            );
        }

        /**
         * Column Thumbnail.
         *
         * @param array $item item array
         */
        public function column_thumbnail($item)
        {
            $dat = '';
            if ($item['thumbnail'][0] == __('No Badge Assign', 'wk-seller-badge')) {
                $dat = __('No Badge Assign', 'wk-seller-badge');
            } else {
                foreach ($item['thumbnail'] as $key) {
                    $dat .= '<br><img src="'.wp_get_attachment_url($key).'" width="80" /> ';
                }
            }

            return $dat;
        }

        /**
         * Column id.
         *
         * @param array $item column array
         */
        public function column_id($item)
        {
            $actions = array();

            return sprintf('%1$s %2$s', $item['id'], $this->row_actions($actions));
        }

        /**
         * Extra tab nav.
         *
         * @param staring $which which
         */
        public function extra_tablenav($which)
        {
            global $wpdb;
            $nonce = wp_create_nonce();
            if ($which == 'top') {
                ?>
				<div class="alignleft actions">
				<select name="badge-filter" >
					<option value=""><?php esc_html_e('Select Badge', 'wk-seller-badge'); ?></option>
						<?php
                        $badge = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mpbadge where status = '1'");
                foreach ($badge as $key) {
                    ?>
						<option value="<?php echo $key->id; ?>"><?php echo $key->b_name; ?></option>
						<?php
                } ?>
				</select>
				<input type="hidden" name="_wpnonce" value="<?php echo $nonce; ?>">
				<?php
                submit_button(__('Apply', 'wk-seller-badge'), 'large', 'assign-badges', false); ?>
				</div>
				<?php
            }
        }

        /**
         * Add badge.
         */
        public function add_bg()
        {
            $b_assign = new MP_AssignBadges();
            if (isset($_POST['id']) && isset($_POST['badge-filter'])) {
                if (isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {
                    $nonce = filter_input(INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING);
                    if (!wp_verify_nonce($_POST['_wpnonce'])) {
                        wp_die(__('Nope! Security check failed!', 'wk-seller-badge'));
                    }
                }
                if (!$_POST['id']) {
                    return;
                }
                if ($this->current_action() == 'assign') {
                    $b_assign->assign($_POST['badge-filter'], $_POST['id'], 'assign');
                }
                if ($this->current_action() == 'remove') {
                    $b_assign->assign($_POST['badge-filter'], $_POST['id'], 'remove');
                }
            }
            if (!empty($b_assign->get_sucess())) {
                ?>
				<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
					<p >
						<strong >
							<?php echo $b_assign->get_sucess(); ?>
						</strong>
					</p>
				</div>
				<?php
            }
            if (!empty($b_assign->get_error())) {
                ?>
				<div  class="error">
					<p>
						<strong >
							<?php echo $b_assign->get_error(); ?>
						</strong>
					</p>
				</div>
					<?php
            }
        }

        /**
         * Prepare item.
         */
        public function prepare_items()
        {
            $this->add_bg();
            $data = $this->table_data();
            $column = $this->get_columns();
            $hidden = array();
            $sorttable = $this->sort_table();

            $this->_column_headers = array($column, $hidden, $sorttable);
            $this->process_bulk_action();
            usort($data, array(&$this, 'unsort_reorder'));

            $perpage = $this->get_items_per_page('marketplace_seller_badge_page_manage_seller_badges_per_page', 5);
            $current_page = $this->get_pagenum();
            $totalitems = count($data);

            $data = array_slice($data, (($current_page - 1) * $perpage), $perpage);

            $this->set_pagination_args(array(
                'total_items' => $totalitems,
                'per_page' => $perpage,
            ));
            $this->items = $data;
        }
    }
endif;
?>
