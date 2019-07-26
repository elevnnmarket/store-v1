<?php
/**
 * Functions.php
 *
 * @package  Theme_Customisations
 * @author   WooThemes
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/* GTM HEAD */
add_action('wp_head', 'gtm_container');
function gtm_container(){
?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-M6LQC75');</script>
<!-- End Google Tag Manager -->
<?php
};
/*GTM BODY TAG*/
add_action('wp_footer','tag_manager2', 20);
function tag_manager2(){
?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M6LQC75"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php 
}
;
/* Fontfamily */
add_action('wp_head', 'new_font_family');
function new_font_family(){
?>
<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
<?php
};
/*Added Login link to top header*/
add_action( 'storefront_header', 'add_login_link', 11 );
  function add_login_link() {
  if ( is_user_logged_in() ) {
    echo '<div class=login-top-link>
            <a href=https://elevnn.com/my-account>My Account</a>
          </div>';} 
  else {
    echo '<div class=login-top-link>
            <a href=https://elevnn.com/my-account>Login</a>
          </div>';}
  }
  ;
/*Custom Header hook changes
 * Swapped search and cart
 * */
add_action('storefront_before_header', 'remove_storefront_product_search');
  function remove_storefront_product_search(){
    remove_action( 'storefront_header', 'storefront_product_search', 10);
  }
add_action( 'storefront_header', 'storefront_product_search', 30 );
add_action('storefront_before_header', 'remove_storefront_header_cart');
  function remove_storefront_header_cart(){
    remove_action( 'storefront_header', 'storefront_header_cart', 30);
  }
add_action( 'storefront_header', 'storefront_header_cart', 12 );
?>
<?php 
;