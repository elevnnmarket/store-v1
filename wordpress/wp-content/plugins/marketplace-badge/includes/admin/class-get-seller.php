<?php

/**
 * If direct access then exit.
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('MP_Get_Seller_Badge')) :
    /**
     * Class MP_Get_Seller_Badge.
     */
    class MP_Get_Seller_Badge
    {
        /**
         * Get badge details.
         *
         * @param int $badge_id
         */
        public function mp_get_badge_details($badge_id)
        {
            global $wpdb;

            $bdg_info = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mpbadge where id='$badge_id'");

            if ($bdg_info) {
                $b_info = array(
                    'b_name' => $bdg_info[0]->b_name,
                    'b_des' => $bdg_info[0]->b_des,
                    'rank' => intval($bdg_info[0]->rank),
                    'tumbnail' => $bdg_info[0]->tumbnail,
                    'status' => $bdg_info[0]->status,
                );

                return $b_info;
            } else {
                return false;
            }
        }

        /**
         * Get selle badge list.
         *
         * @param string $search serch string
         */
        public function get_mp_seller_badge_list($search = '')
        {
            global $wpdb;
            $data = array();

            if (!empty($search)) {
                $users = new WP_User_Query(array(
                    'search' => '*'.esc_attr($search).'*',
                    'search_columns' => array(
                        'user_login',
                        'user_email',
                    ),
                ));
                $users = $users->get_results();
            } else {
                $users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}users");
            }
            foreach ($users as $print) {
                $user_meta = get_userdata($print->ID);
                /* Get User Role*/
                $user_roles = $user_meta->roles;
                /*Extract Seller whose role is Market Place Seller*/
                if (in_array('wk_marketplace_seller', $user_roles, true)) {
                    $img = array(); // array to store no of badges
                    /* To Get Which badges is assinged to the Marketplace Seller*/
                    $buser = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mpbadge_assign where u_id='$print->ID'");

                    $i = 0;
                    /*Get Badge Thumbnail On the base of it's ID*/
                    foreach ($buser as $key) {
                        $badge = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mpbadge where id='$key->b_id'");
                        foreach ($badge as $key3) {
                            ++$i;
                            $img[] = $key3->tumbnail;
                        }
                    }
                    if ($i == 0) {
                        $img[] = __('No Badge Assign', 'wk-seller-badge');
                    }

                    $data[] = array(
                        'thumbnail' => $img,
                        'id' => $print->ID,
                        'name' => $print->display_name,
                        'email' => $print->user_email,
                        'status' => __('Approve', 'wk-seller-badge'),
                    );
                }
            }

            return $data;
        }
    }
    new MP_Get_Seller_Badge();
endif;
