<?php
/**
 * Assign badge class.
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('MP_AssignBadges')) :
    /**
     * Class MP_AssignBadges.
     */
    class MP_AssignBadges
    {
        /**
         * Error variable.
         *
         * @var
         */
        public $error;

        /**
         * Success variable.
         *
         * @var
         */
        public $sucess;

        /**
         * Class constructor.
         */
        public function __construct()
        {
            $this->error = '';
            $this->sucess = '';
        }

        /**
         * Function to return badge names from id.
         *
         * @param int $id badge id
         */
        public function get_badge_name($id)
        {
            global $wpdb;
            $result = $wpdb->get_results("SELECT b_name FROM {$wpdb->prefix}mpbadge where id = '$id'");
            if ($result) {
                foreach ($result as $key) {
                    return $key->b_name;
                }
            }

            return;
        }

        /**
         * Change Status of badges in bulk.
         *
         * @param string $status status
         * @param int    $id     id
         */
        public function bulk_badge_status_update($status, $id)
        {
            global $wpdb;
            $tablename = $wpdb->prefix.'mpbadge';
            foreach ($id as $key) {
                $result = $wpdb->update($tablename, array('status' => $status), array('id' => $key));
                if ($result) {
                    $this->sucess = __('Action Successful', 'wk-seller-badge');
                } else {
                    $this->error = __('Action failed', 'wk-seller-badge');
                }
            }
        }

        /**
         * Change status of single badge.
         *
         * @param string $status status
         * @param int    $id     id
         */
        public function badge_status_update($status, $id)
        {
            global $wpdb;
            $tablename = $wpdb->prefix.'mpbadge';
            $result = $wpdb->update($tablename, array('status' => $status), array('id' => $id));
            if ($result) {
                $this->sucess = __('Badge', 'wk-seller-badge').' '.$this->get_badge_name($id).' '.__('status changed', 'wk-seller-badge').' ';
            }
        }

        /**
         * Return the value of error if exist.
         */
        public function get_error()
        {
            return $this->error;
        }

        /**
         * Return the sucess value if operration is sucessful.
         */
        public function get_sucess()
        {
            return $this->sucess;
        }

        /**
         * Get seller user name.
         *
         * @param int $user_id user id
         */
        public function get_display_name($user_id)
        {
            if (!$user = get_userdata($user_id)) {
                return false;
            }

            return $user->data->display_name;
        }

        /**
         * Assign and remove Badges.
         */
        public function assign($b_id, $u_id, $action)
        {
            if (empty($b_id)) {
                $this->error = __('Select the badge you want to assign', 'wk-seller-badge');

                return;
            }

            if (empty($u_id) && empty($action)) {
                return;
            }
            global $wpdb;
            $tablename = $wpdb->prefix.'mpbadge';
            $tablename2 = $wpdb->prefix.'mpbadge_assign';

            if ($action == 'remove') {
                foreach ($u_id as $key) {
                    if ($wpdb->query("DELETE  FROM {$wpdb->prefix}mpbadge_assign WHERE u_id = '".$key."' and b_id = '".$b_id."'") == false) {
                        $this->error .= $this->get_display_name($key).' '.__('do not have', 'wk-seller-badge').' '.$this->get_badge_name($b_id).' '.__('badge', 'wk-seller-badge').' <br>';
                    } else {
                        $this->sucess .= __('Sucessfully removed', 'wk-seller-badge').' '.$this->get_badge_name($b_id).' '.__('Badge of', 'wk-seller-badge').'  '.$this->get_display_name($key).'  <br>';
                    }
                }
            } elseif ($action == 'assign') {
                foreach ($u_id as $key) {
                    $result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}mpbadge_assign where u_id = '$key' and b_id = '$b_id'");
                    if ($result) {
                        $this->error .= $this->get_badge_name($b_id).' '.__('Badge is already Assigned to', 'wk-seller-badge').' '.$this->get_display_name($key).' <br>';
                    } else {
                        if ($wpdb->insert($tablename2, array(
                            'b_id' => $b_id,
                            'u_id' => $key,
                        )) == false) {
                            $this->error .= __('failed to assigned', 'wk-seller-badge').' '.$this->get_badge_name($b_id).' '.__('Badge to', 'wk-seller-badge').' '.$this->get_display_name($key).' <br>';
                        } else {
                            $this->sucess .= __('successfully assigned', 'wk-seller-badge').' '.$this->get_badge_name($b_id).' '.__('badge to', 'wk-seller-badge').' '.$this->get_display_name($key).' <br>';
                        }
                    }
                }
            }
        }
    }
endif;

return new MP_AssignBadges();
