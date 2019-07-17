<?php
/**
 * Admin head block template. Set up base JS URL's (for ajax requests) includes CSS, fonts and LiveChat script.
 * @category Admin pages
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

header( "Content-type: text/javascript; charset: UTF-8" );

?>
var WcLcUrls = {
    setSettings: '<?php echo $set_settings_url ?>'
};
