<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'mp_price' ) ) {
	function mp_price( $value ) {
		if ( ! function_exists( 'woocommerce_price' ) || 'WC_IS_MIS_WC_ACITVE' === false ) {
			return apply_filters( 'mp_currency_symbol', '&#36;', 'USD' ) . $value;
		} else {
				return wc_price( $value );
		}
	}
}

function product_list() {
	?> <div class="woocommerce-account">
	<?php
	apply_filters( 'mp_get_wc_account_menu', 'marketplace' );
	?>
	<div class="woocommerce-MyAccount-content">
		<div id="main_container">

			<?php

			global $wpdb, $wp_query;

			$user_id = get_current_user_id();

			$wpmp_pid = '';

			$mainpage = get_query_var( 'main_page' );

			$p_id = get_query_var( 'pid' );

			$action = get_query_var( 'action' );

			if ( ! empty( $p_id ) ) {
				$wpmp_pid = $p_id;
			}
			$product_auth = $wpdb->prepare( "SELECT post_author from $wpdb->posts where ID = %d", $wpmp_pid );
			$product_auth = $wpdb->get_var( $product_auth );

			if ( ! empty( $mainpage ) && ! empty( $action ) ) {

				if ( ! isset( $_GET['_mp_delete_nonce']) || ! wp_verify_nonce( $_GET['_mp_delete_nonce'], 'marketplace-product-delete-nonce-action' ) ) {
					wc_add_notice( __( 'Security check failed!!!', 'marketplace' ), 'error' );
					wp_redirect( get_permalink() . 'product-list' );
					exit;
				} else {
					if ( 'product-list' === $mainpage && 'delete' === $action && intval( $product_auth ) === $user_id ) {
						$delete_product_name = get_the_title( $wpmp_pid );

						if ( delete_post_meta( $wpmp_pid, '_sku' ) ) {
							delete_post_meta( $wpmp_pid, '_regular_price' );
							delete_post_meta( $wpmp_pid, '_sale_price' );
							delete_post_meta( $wpmp_pid, '_price' );
							delete_post_meta( $wpmp_pid, '_sale_price_dates_from' );
							delete_post_meta( $wpmp_pid, '_sale_price_dates_to' );
							delete_post_meta( $wpmp_pid, '_downloadable' );
							delete_post_meta( $wpmp_pid, '_virtual' );

							$delete_check = wp_delete_post( $wpmp_pid );
							if ( $delete_check ) {
								wc_add_notice( $delete_product_name . __( ' deleted successfully.', 'marketplace' ), 'success' );
								wp_redirect( get_permalink() . 'product-list' );
								exit;
							}
						}
					}
				}
			}

			$product_query = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_type = 'product' and ( post_status = 'draft' or post_status = 'publish' ) and post_author = '%d' ORDER BY ID DESC", $user_id );

			$product = $wpdb->get_results( $product_query );

			?>

			<div class="mp-product-table-actions">
				<button id="triggerBulkDelete" class="button"><?php echo __( 'Delete', 'marketplace' ); ?></button>&nbsp;&nbsp;
				<a href="<?php echo get_permalink(); ?>add-product" class="button add-product"><?php echo __( 'Add Product', 'marketplace' ); ?></a>
			</div>

			<table class="productlist">

				<thead>
					<tr>
						<th><input type="checkbox" id="allDelete" /></th>
						<th><?php esc_html_e( 'Product Name', 'marketplace' ); ?></th>
						<th><?php esc_html_e( 'Stock', 'marketplace' ); ?></th>
						<th><?php esc_html_e( 'Product Status', 'marketplace' ); ?></th>
						<th><?php esc_html_e( 'Price', 'marketplace' ); ?></th>
						<th><?php esc_html_e( 'Image', 'marketplace' ); ?></th>
						<th><?php esc_html_e( 'Action', 'marketplace' ); ?></th>
					</tr>
				</thead>

				<tbody>
					<?php
					$page_name_query = $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s and post_type = 'page' ", get_option( 'wkmp_seller_page_title' ) );

					$page_name = $wpdb->get_var( $page_name_query );

					$wpmp_obj2 = new MP_Form_Handler();

					foreach ( $product as $pro ) {
						$prod = wc_get_product( $pro->ID );
						$symbol = get_woocommerce_currency_symbol();
						$product_price = $prod->get_price_html();
						$product_stock = get_post_meta( $pro->ID, '_stock_status', true );
						$stock_remain = get_post_meta( $pro->ID, '_stock', true );
						$product_image = $wpmp_obj2->get_product_image( $pro->ID, '_thumbnail_id' );

						if ( $prod->is_type( 'variable' ) ) {
							$symbol = get_woocommerce_currency_symbol();
							$product_price = '';

							if ( ! empty( get_post_meta( $pro->ID, '_min_variation_price', true ) ) && ! empty( get_post_meta( $pro->ID, '_max_variation_price', true ) ) ) {
								$product_price = $symbol . get_post_meta( $pro->ID, '_min_variation_price', true ) . ' - ' . $symbol . get_post_meta( $pro->ID, '_max_variation_price', true );
							}
						}
						?>

						<tr>
							<td><?php echo '<input type="checkbox" class="deleteProductRow" value="' . $pro->ID . '"  /> '; ?></td>
							<td>
								<a href="<?php echo get_permalink( $pro->ID ); ?>"><?php echo $pro->post_title; ?></a>
							</td>

							<td>
								<?php echo ( isset( $product_stock ) && ! empty( $product_stock ) ) ? $product_stock : '-'; ?>
							</td>

							<td>
								<?php echo $pro->post_status;?>
							</td>

							<td>
								<?php
								if ( '' !== $product_price ) {
										echo $product_price;
								} else {
										echo '-';
								}
								?>
							</td>

							<td>
								<img class="wkmp_productlist_img" alt="<?php echo $pro->post_title; ?>" title="<?php echo $pro->post_title; ?>" src="<?php
									if ( '' !== $product_image ) {
										echo content_url() . '/uploads/' . $product_image;
									} else {
										echo WK_MARKETPLACE . 'assets/images/placeholder.png';
									}
								?>" width="50" height="50">
							</td>

							<td>
								<a id="editprod" class="mp-action" href="<?php echo home_url( get_option( 'wkmp_seller_page_title' ) . '/product/edit/' . $pro->ID );?>"><?php echo __( 'edit', 'marketplace' ); ?></a>
								<a id="delprod" class="mp-action" href="<?php echo wp_nonce_url( home_url( get_option( 'wkmp_seller_page_title' ) . '/product-list/delete/' . $pro->ID ), 'marketplace-product-delete-nonce-action', '_mp_delete_nonce' ); ?>" class="ask" onclick="return confirm('Are you sure you want to delete this item?');"><?php echo __( 'delete', 'marketplace' ); ?></a>
							</td>

						</tr>

						<?php
					}

					?>

				</tbody>

			</table>

		</div>

	</div>

	</div>

	<?php
}
