<?php
/**
 * Connect notice template.
 *
 * @category Admin pages
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="notice notice-info" id="lc-connect-notice">
    <p>
      <?php _e('Please connect your LiveChat account to start chatting with your customers.', 'livechat-woocommerce'); ?> <a href="admin.php?page=wc-livechat">
        <?php _e('Connect', 'livechat-woocommerce'); ?> &rarr;
        </a>
    </p>
</div>
