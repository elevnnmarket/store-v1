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
// User roles communication
function elevnn_save_role( $user_id, $role ) {

  // Site 1
  // Change value if needed
  $prefix_1 = 'first_';
  
  // Site 2 prefix
  // Change value if needed
  $prefix_2 = 'second_';
  
  $caps = get_user_meta( $user_id, $prefix_1 . 'capabilities', true );
  $level = get_user_meta( $user_id, $prefix_1 . 'user_level', true );

  if ( $caps ){
    update_user_meta( $user_id, $prefix_2 . 'capabilities', $caps );
  }

  if ( $level ){
    update_user_meta( $user_id, $prefix_2 . 'user_level', $level );
  }
}

add_action( 'set_user_role', 'elevnn_save_role', 10, 2 );

remove_action( 'wp_footer', 'add_stripe_connect_script');

add_action( 'wp_footer', 'add_stripe_connect_script_elevnn');
function add_stripe_connect_script_elevnn()
{
  // wp_enqueue_script ( 'stripe_connect_control' , plugins_url ( 'stripe-connect/assets/js/stripe_connect_control.js' ) );
  $stripe_connect=new WC_Stripe_Connect();
  // if(strchr(get_permalink(),'?'))
  //   $icon='&';
  //   else
  //   $icon='?';
  // $redirect_uri=get_permalink().$icon.'page=pedit';
  $stripe_payment_type=$stripe_connect->stripe_payment_mode;
  if($stripe_payment_type=='yes'){
  /*Stripe::setApiKey($stripe_connect->stripe_test_secret_key);*/
  $client_id= $stripe_connect->stripe_test_client_id;
  }
  else
  {
  /*Stripe::setApiKey($stripe_connect->stripe_live_secret_key);*/
  $client_id=$stripe_connect->stripe_live_client_id;
  }
  /*$client_id=$stripe_connect->stripe_platform_client_id;*/
  $stripe_connect_img=plugins_url().'/wp-stripe-connect/assets/images/stripe.png';
  $stripeConnectUrl='https://connect.stripe.com/express/oauth/authorize?redirect_uri=https://elevnn.com/seller/profile/edit/&client_id='.$client_id.'&stripe_user[business_type]=company';
  ?>
  <script type="text/javascript">
  (function(wk){

  wk(document).ready(function(){
  var payment_method=wk(this).val();
  jQuery("#mp_seller_payment_stripe_method").attr("href","<?php echo $stripeConnectUrl; ?>");
  jQuery("#mp_seller_payment_stripe_method img").attr("src","<?php echo $stripe_connect_img; ?>");


  if(wk('body').find('#mp_seller_payment_stripe_method').length==1){
  var error_warning=wk('#stripe_response_error').val();
  var success_message=wk('#stripe_response_success').val();
  var a=wk('#mp_seller_payment_method :selected').val();
  if(wk.type(error_warning)!='undefined'){
  var html ='<div class="stripe_show_error" style="color:red;">';
  html +=error_warning;
  html +='</div>';
  wk('.wk-success-check').append(html);
  }
  if(wk.type(success_message)!='undefined'){
  wk('#stripe_connect_button').parent().remove();
  var html ='<div class="stripe_show_success" style="color:green;">';
  html +=wk.trim(success_message);
  html +='</div>';
  wk('.wk-success-check').append(html);
  var stripe_user_id="<?php echo get_user_meta(get_current_user_ID(), 'stripe_user_id', true); ?>";
  wk('#wkmppaydesci').val(stripe_user_id);

  }
  }
  });
  })(jQuery);
  </script><?php
}

?>
<?php 
;