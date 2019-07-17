<?php
/**
 * Save badge data.
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('MP_SAVE_BADGES')) :
    /**
     * Class MP_Seller_Profile.
     */
    class MP_SAVE_BADGES
    {
        public $error;
        public $sucess;
        public $wpdb = '';
        public $b_name;
        public $b_des;
        public $rank;
        public $image;
        public $status;
        public $tablename;
        public $id;

        /**
         * Class construtor.
         */
        public function __construct()
        {
            $this->error = '';
            $this->sucess = '';
            global $wpdb;
            $this->wpdb = $wpdb;
            $this->tablename = $wpdb->prefix.'mpbadge';
        }

        /**
         * Check for validation of input data.
         *
         * @param array $POST post data
         */
        public function check($POST)
        {
            $c = 1;

            if (isset($POST['_wpnonce']) && !empty($POST['_wpnonce'])) {
                $nonce = filter_input(INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING);

                if (!wp_verify_nonce($POST['_wpnonce'])) {
                    wp_die(esc_html__('Nope! Security check failed!', 'wk-seller-badge'));
                    $c = 0;
                }
            }

            $this->b_name = trim(filter_var($POST['b_name'], FILTER_SANITIZE_STRING));
            $this->b_des = trim(filter_var($POST['b_des'], FILTER_SANITIZE_STRING));
            $this->rank = trim(filter_var($POST['rank'], FILTER_SANITIZE_NUMBER_INT));
            $this->image = $POST['upload-img-id'];
            $this->status = $POST['status'];
            $this->id = $POST['id'];
            if (empty($this->b_name)) {
                $c = 0;
            } elseif (preg_match('/[^a-z A-Z\d]/', $this->b_name) || preg_match('/\d/', $this->b_name)) {
                $c = -2;

                $this->error .= __('Invalid Badge Name', 'wk-seller-badge');
            }
            if (empty($this->b_des)) {
                $c = 0;
            }
            if (empty($this->rank)) {
                $c = 0;
            } else {
                if ($this->rank < 0 || filter_var($this->rank, FILTER_VALIDATE_INT) === false) {
                    $c = -1;

                    $this->error .= __('Invalid Rank', 'wk-seller-badge');
                }
            }
            if (empty($this->image)) {
                $c = 0;
            }
            if ($c != 1) {
                $this->error .= __(' Please fill all the field correctly', 'wk-seller-badge');
            }

            return $c;
        }

        /**
         * Update Badge.
         */
        public function update_badge()
        {
            $result = $this->wpdb->update($this->tablename, array(
                'b_name' => $this->b_name,
                'b_des' => $this->b_des,
                'rank' => intval($this->rank),
                'tumbnail' => intval($this->image),
                'status' => $this->status,
            ), array('id' => $this->id), array('%s', '%s', '%d', '%d', '%d'), array('%d'));

            if ($result > 0) {
                $this->sucess = __('Badge successfully Updated', 'wk-seller-badge');
            } else {
                $this->error = __('Please enter details to update the badge.', 'wk-seller-badge');
            }
            $this->wpdb->flush();
        }

        /**
         * Adding new Badge.
         */
        public function add_new_badge()
        {
            $val_ar = array(
                'b_name' => $this->b_name,
                'b_des' => $this->b_des,
                'rank' => intval($this->rank),
                'tumbnail' => intval($this->image),
                'status' => $this->status,
            );
            $s_ar = array(
                '%s',
                '%s',
                '%d',
                '%d',
                '%d',
            );
            $add_rpt = $this->wpdb->insert($this->tablename, $val_ar, $s_ar);

            if ($add_rpt == false) {
                $this->error = __('Unable to add Badge', 'wk-seller-badge');
            } else {
                $this->sucess = __('Badge successfully added', 'wk-seller-badge');
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
    }
endif;
