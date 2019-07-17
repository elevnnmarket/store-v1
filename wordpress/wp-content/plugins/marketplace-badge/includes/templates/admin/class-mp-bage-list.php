<?php

/**
 * Badge list.
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH.'wp-admin/includes/class-wp-list-table.php';
}

if (!class_exists('MP_Manage_Badge')) :
    /**
     * Class MP_Manage_Badge.
     */
    class MP_Manage_Badge extends WP_List_Table
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
         * Table data.
         */
        private function table_data()
        {
            $data = array();
            global $wpdb;
            if (isset($_GET['s'])) {
                $search = $_GET['s'];
                $result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mpbadge where 	b_name  LIKE '%$search%'");
            } else {
                $result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mpbadge");
            }
            foreach ($result as $print) {
                if ($print->status == 1) {
                    $status = 'Disable';
                } else {
                    $status = 'Enable';
                }
                $data[] = array(
                    'id' => $print->id,
                    'Thumbnail' => $print->tumbnail,
                    'Badge Name' => $print->b_name,
                    'Badge Description' => $print->b_des,
                    'rank' => $print->rank,
                    'status' => $status,
                );
            }

            return $data;
        }

        /**
         * Sort table data.
         */
        public function sort_table()
        {
            $sortcolumn = array(
                'id' => array('id', false),
                'Thumbnail' => array('Thumbnail', false),
                'Badge Name' => array('Badge Name', false),
                'Badge Description' => array('Badge Description', false),
                'rank' => array('rank', false),
                'status' => array('status', false),
            );

            return $sortcolumn;
        }

        /**
         * Sort data.
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
         * Prepare item.
         */
        public function prepare_items()
        {
            $this->process_bulk_action();
            $data = $this->table_data();
            $column = $this->get_columns();
            $hidden = array();
            $sorttable = $this->sort_table();

            $this->_column_headers = array($column, $hidden, $sorttable);
            usort($data, array(&$this, 'unsort_reorder'));

            $perpage = $this->get_items_per_page('toplevel_page_mp_badges_per_page', 5);
            $currentpage = $this->get_pagenum();
            $totalitems = count($data);

            $data = array_slice($data, (($currentpage - 1) * $perpage), $perpage);
            $this->set_pagination_args(array(
                'total_items' => $totalitems,
                'per_page' => $perpage,
            ));
            $this->items = $data;
        }

        /**
         * Get default column.
         */
        public function get_columns()
        {
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'id' => __('Id', 'wk-seller-badge'),
                'Thumbnail' => __('Badge(s)', 'wk-seller-badge'),
                'Badge Name' => __('Badge Name', 'wk-seller-badge'),
                'Badge Description' => __('Badge Description', 'wk-seller-badge'),
                'rank' => __('Rank', 'wk-seller-badge'),
                'status' => __('Status', 'wk-seller-badge'),
            );

            return $columns;
        }

        /**
         * Add bulk action.
         */
        public function get_bulk_actions()
        {
            $action = array(
                'delete' => __('Delete', 'wk-seller-badge'),
                'enable' => __('Enable', 'wk-seller-badge'),
                'disable' => __('Disable', 'wk-seller-badge'),
            );

            return $action;
        }

        /**
         * Process bulk action.
         */
        public function process_bulk_action()
        {
            $change_status = new MP_AssignBadges();
            if (!empty($_GET['action'])) {
                if (isset($_GET['id']) && !empty($_GET['id'])) {
                    switch ($_GET['action']) {
                        case 'Disable':
                            $change_status->badge_status_update(0, $_GET['id']);
                            break;
                        case 'Enable':
                            $change_status->badge_status_update(1, $_GET['id']);
                            break;
                        default:
                            break;
                    }

                    switch ($this->current_action()) {
                        case 'enable':
                            $change_status->bulk_badge_status_update(1, $_GET['id']);
                            break;
                        case 'disable':
                            $change_status->bulk_badge_status_update(0, $_GET['id']);
                            break;
                        default:
                            break;
                    }

                    if (!empty($change_status->get_sucess())) {
                        ?>
<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
    <p>
        <strong>
            <?php echo $change_status->get_sucess(); ?>
        </strong>
    </p>
</div>
<?php
                    }
                    global $wpdb;
                    $tablename = $wpdb->prefix;
                    if ('delete' == $this->current_action()) {
                        if (empty($_GET['id'])) {
                            $msg = __('no item selected', 'wk-seller-badge');
                        }

                        if ('delete' == $this->current_action() && is_array($_GET['id'])) {
                            $i = 0;
                            foreach ($_GET['id'] as $id) {
                                ++$i;
                                $result1 = $wpdb->query('DELETE FROM '.$tablename.'mpbadge WHERE id = " '.$id.'"');
                                $result2 = $wpdb->query('DELETE FROM '.$tablename.'mpbadge_assign WHERE b_id = " '.$id.'"');
                            }
                            $msg = __('Deleted', 'wk-seller-badge').' '.$i.'   '.__('Badge(s) Sucessfully', 'wk-seller-badge');
                        } elseif ($_GET['action'] == 'delete') {
                            $wpdb->query('DELETE FROM '.$tablename.'mpbadge WHERE id="'.$_GET['id '].'"');
                            $wpdb->query('DELETE FROM '.$tablename.'mpbadge_assign  WHERE b_id = "'.$_GET['id'].'"');
                            $msg = __('Deleted Badge Sucessfully', 'wk-seller-badge');
                        } ?>
<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
    <p>
        <strong>
            <?php if (isset($msg)) {
                            echo $msg;
                        } ?>
        </strong>
    </p>
</div>
<?php
                    }
                } else {
                    ?>
<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible">
    <p>
        <strong>
            <?php esc_html_e('Please select a badge first', 'wk- seller-badge'); ?>
        </strong>
    </p>
</div>
<?php
                }
            }
        }

        /**
         * Default column.
         *
         * @param array  $item        item array
         * @param string $column_name col name
         *
         * @return [type] [description]
         */
        public function column_default($item, $column_name)
        {
            switch ($column_name) {
        case 'id':
        case 'Badge Name':
        case 'Badge Description':
        case 'rank':
        case 'status':
            return $item[$column_name];
        case 'Thumbnail':
            the_post_thumbnail($item[$column_name]);
            break;
        default:
            return print_r($item, true);
    }
        }

        /**
         * Column checkbox.
         *
         * @param array $item item array
         */
        public function column_cb($item)
        {
            return sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', 'id', $item['id']);
        }

        /**
         * Column status.
         *
         * @param array $item item array
         *
         * @return [type] [description]
         */
        public function column_status($item)
        {
            $dat = '';
            if ($item['status'] == 'Disable') {
                $dat = "<a class='wk_badge_button_enable'  href='?page=m p -bad ges&action=  ".$item['status'].' & id='.$item['id']."'>  ".$item['status'].'</a>';
            } else {
                $dat = "<a class='wk_badge_button_disable'  href='?page=m p -bad ges&action=  ".$item['status'].' & id='.$item['id']."'>  ".$item['status'].'</a>';
            }

            return $dat;
        }

        /**
         * Column thumbnail.
         *
         * @param array $item item array
         */
        public function column_Thumbnail($item)
        {
            return sprintf('<img src="%s" width="80" />', wp_get_attachment_url($item['Thumbnail']));
        }

        /**
         * Column id.
         *
         * @param array $item item array
         */
        public function column_id($item)
        {
            $actions = array(
        'edit' => sprintf('<a href="?page=adding-badges&action=%s&id=%s">'.__('Edit', ' wk-seller-badge').'</a>', 'edit', $item['id']),
        'trash' => sprintf('<a href="?page=%s&a c ti o n=%s&id=% s ">'.__('Delete', 'wk-seller-badge').'</a>', $_REQUEST['page'], 'delete', $item['id']),
    );

            return sprintf('%1$s %2$s', $item['id'], $this->row_actions($actions));
        }
    }
endif;

?> 