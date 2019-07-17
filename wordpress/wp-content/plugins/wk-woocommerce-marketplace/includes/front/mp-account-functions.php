<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 *  Add seller menu items in my account menu
 *
 *  @param array $items items array.
 *  @return menu item array with seller options if seller
 */
function mp_seller_menu_items_my_account( $items ) {
	global $wpdb;

	$user_id = get_current_user_id();

	$new_items = array();

	$shop_address = get_user_meta( $user_id, 'shop_address', true );

	$page_name = $wpdb->get_var( "SELECT post_name FROM $wpdb->posts WHERE post_name ='" . get_option( 'wkmp_seller_page_title' ) . "'" );

	$seller_info = $wpdb->get_var("SELECT user_id FROM {$wpdb->prefix}mpsellerinfo WHERE user_id = '" . $user_id . "' and seller_value='seller'");

	if ( $seller_info > 0 ) {
		$new_items[ '../' . $page_name . '/dashboard' ]                      = esc_html__( 'Marketplace', 'marketplace' );
		$new_items[ '../' . $page_name . '/product-list' ]                   = esc_html__( 'Products', 'marketplace' );
		$new_items[ '../' . $page_name . '/order-history' ]                  = esc_html__( 'Order History', 'marketplace' );
		$new_items[ '../' . $page_name . '/transaction' ]                    = esc_html__( 'Transaction', 'marketplace' );
		$new_items[ '../' . $page_name . '/' . $shop_address . '/shipping' ] = esc_html__( 'Shipping', 'marketplace' );
		$new_items[ '../' . $page_name . '/profile/edit' ]                   = esc_html__( 'Seller Profile', 'marketplace' );
		$new_items[ '../' . $page_name . '/notification' ]                   = esc_html__( 'Notifications', 'marketplace' );
		$new_items[ '../' . $page_name . '/shop-follower' ]                  = esc_html__( 'Shop Followers', 'marketplace' );
		$new_items                               = apply_filters( 'mp_woocommerce_account_menu_options', $new_items );
		$new_items[ '../' . $page_name . '/to' ] = __( 'Ask To Admin', 'marketplace' );

		if ( get_option( 'wkmp_enable_seller_seperate_dashboard' ) ) {
			$new_items['../seperate-dashboard'] = esc_html__( 'Admin Dashboard', 'marketplace' );
		} else {
			?>
			<style>
				.woocommerce-MyAccount-navigation ul li.woocommerce-MyAccount-navigation-link--sellerto {
				margin-bottom: 40px
				}
			</style>
			<?php
		}
	}

	$new_items += $items;

	return $new_items;
}

/**
 * Mp_return_wc_account_menu
 *  My account menu for seller pages
 */
function mp_return_wc_account_menu() {
	?>
	<nav class="woocommerce-MyAccount-navigation">
		<ul>
			<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
				<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>">
					<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>
	<?php
}

/**
 * Account menu shpping style
 */
function mp_shipping_icon_style() {
	global $wpdb;

	$user_id = get_current_user_id();

	$shop_address = get_user_meta( $user_id, 'shop_address', true );

	$page_name = $wpdb->get_var( "SELECT post_name FROM $wpdb->posts WHERE post_name ='" . get_option( 'wkmp_seller_page_title' ) . "'" );

	$seller_info = $wpdb->get_var( "SELECT user_id FROM {$wpdb->prefix}mpsellerinfo WHERE user_id = '" . intval( $user_id ) . "' and seller_value='seller'" );

	$total_count = mp_seller_panel_notification_count();

	if ( $seller_info > 0 ) {
		?>
		<style type="text/css" media="screen">
		.woocommerce-MyAccount-navigation ul li.woocommerce-MyAccount-navigation-link--<?php echo $page_name . $shop_address; ?>shipping a:before {
			content: "\e95a";
			font-family: 'Webkul Rango';
			font-size: 20px;
			font-weight: normal;
			text-align: center;
		}

		.woocommerce-MyAccount-navigation ul li.woocommerce-MyAccount-navigation-link--sellernotification a:after {
			content: "<?php echo $total_count; ?>";
			display: inline-block;
			margin-left: 5px;
			background-color: #96588a;
			color: #fff;
			padding: 0 6px;
			border-radius: 3px;
			line-height: normal;
			vertical-align: middle;
		}
		</style>
		<?php
	}

}

/**
 *  Add active class to current menu for seller pages
 *
 * @param string $classes classes.
 * @param string $endpoint endpoints.
 */
function mp_add_menu_active_class( $classes, $endpoint ) {
	global $wpdb, $wp;

	$page_name = $wpdb->get_var( "SELECT post_name FROM $wpdb->posts WHERE post_name ='" . get_option( 'wkmp_seller_page_title' ) . "'" );

	if ( is_page( $page_name ) ) {

		$actual_link = ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . '://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]';

		$current = str_replace( home_url() . '/', '', $actual_link );

		$endpoint = str_replace( '../', '', $endpoint );

		if ( strpos( untrailingslashit( $current ), $endpoint ) !== false || ( ( get_query_var( 'main_page' ) == 'product' || get_query_var( 'main_page' ) == 'add-product' ) && strpos( $endpoint, 'product-list' ) !== false ) ) {
			$classes[] = 'is-active';
			$count     = 0;
		}

		if ( 'dashboard' === $endpoint && ( $key = array_search( 'is-active', $classes, true ) ) !== false ) {
			unset( $classes[ $key ] );
		}
	}
	return $classes;
}

function mp_seller_panel_notification_count() {
	global $wpdb;

	$user_id = get_current_user_id();

	$page_name = $wpdb->get_var( "SELECT post_name FROM $wpdb->posts WHERE post_name ='" . get_option( 'wkmp_seller_page_title' ) . "'" );

	$total = $wpdb->get_results( "Select * from {$wpdb->prefix}mp_notifications where read_flag = '0' and author_id = '$user_id' ", ARRAY_A );

	$total_count = 0;

	if ( $total ) {
		foreach ( $total as $key => $value ) {
			if ( in_array( $user_id, explode( ',', $value['author_id'] ), true ) ) {
					$total_count++;
			}
		}
	}

	return $total_count;
}
