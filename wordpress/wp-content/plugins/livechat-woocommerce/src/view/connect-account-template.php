<?php
/**
 * Connecting account template.
 * @category Admin pages
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="woocommerce-livechat-container">
    <div class="woocommerce-livechat-column-left">
        <div class="login-box-header">
            <img src="<?php echo plugins_url('livechat-woocommerce').'/src/media/livechat-woocommerce@2x.png'; ?>" alt="LiveChat + WooCommerce" class="logo">
        </div>
        <div id="useExistingAccount">
            <p class="login-with-livechat"><br>
                <iframe id="login-with-livechat" src="https://addons.livechatinc.com/sign-in-with-livechat/woocommerce/?linkLabel=Connect+with+LiveChat&popupRoute=signup%2Fcredentials&a=woocommerce&utm_source=woocommerce.com&utm_medium=integration&utm_campaign=woocommerce_plugin&name=<?php echo urlencode($username) ;?>&email=<?php echo urlencode($user_email) ;?>" > </iframe>
            </p>
            <form id="licenseForm" action="" method="post">
                <input type="hidden" name="licenseEmail" id="licenseEmail">
                <input type="hidden" name="licenseNumber" id="licenseNumber">
            </form>
        </div>
    </div>
    <div class="woocommerce-livechat-column-right">
        <p><img src="<?php echo plugins_url('livechat-woocommerce').'/src/media/livechat-app.png'; ?>" alt="LiveChat apps" class="livechat-app"></p>
        <p class="apps-link"><?php _e('Check out our apps for', 'livechat-woocommerce'); ?> <a href="https://www.livechatinc.com/applications/?utm_source=woocommerce.com&utm_medium=integration&utm_campaign=woocommerce_plugin" target="_blank" class="a-important"><?php _e('desktop or mobile!', 'livechat-woocommerce'); ?></a></p>
    </div>
</div>
