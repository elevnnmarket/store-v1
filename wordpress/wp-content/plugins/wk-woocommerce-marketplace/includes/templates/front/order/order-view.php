<?php

global $wpdb, $commission;

$order = '';

$order_id = get_query_var('order_id');

if (is_admin() && isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['oid']) && !empty($_GET['oid'])) {
    $order_id = filter_input(INPUT_GET, 'oid', FILTER_SANITIZE_NUMBER_INT);
}

$user_id = get_current_user_id();

if (isset($_POST['mp-submit-status'])) {
    mp_order_update_status($_POST);
}

try {
    $order = new WC_Order($order_id);

    $order_detail_by_order_id = array();

    $get_item = $order->get_items();

    $cur_symbol = get_woocommerce_currency_symbol($order->get_currency());

    $order_detail_by_order_id = array();

    foreach ($get_item as $key => $value) {
        $product_id = $value->get_product_id();
        $variable_id = $value->get_variation_id();
        $product_total_price = $value->get_data()['total'];
        $qty = $value->get_data()['quantity'];
        $post = get_post($product_id);

        if ($post->post_author == $user_id) {
            $order_detail_by_order_id[$product_id][] = array(
                'product_name' => $value['name'],
                'qty' => $qty,
                'variable_id' => $variable_id,
                'item_key' => $key,
                'product_total_price' => $product_total_price,
            );
        }
    }

    $shipping_method = $order->get_shipping_method();

    $payment_method = $order->get_payment_method_title();

    $total_payment = 0; ?> <div class="woocommerce-account">
		<?php

    apply_filters('mp_get_wc_account_menu', 'marketplace');

    if (!empty($order_detail_by_order_id)):

    ?>

		<div class="woocommerce-MyAccount-content mp-order-view wrap">

			<div id="order_data_details">

				<?php do_action('wkmp_before_seller_print_invoice_button', $order); ?>

				<a href="<?php echo esc_url(site_url().'/seller/invoice/'.base64_encode($order_id)); ?>" target="_blank" class="button print-invoice"><?php echo esc_html__('Print Invoice', 'marketplace'); ?></a>

				<?php do_action('wkmp_after_seller_print_invoice_button', $order); ?>

				<h3><?php echo esc_html__('Order', 'marketplace').' #'.$order_id; ?></h3>

				<div class="wkmp_order_data_detail">
					<table class="widefat">
						<thead>
							<tr>
								<th class="product-name"><b><?php echo esc_html_e('Product', 'marketplace'); ?></b></th>
								<th class="product-total"><b><?php echo esc_html_e('Total', 'marketplace'); ?></b></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($order_detail_by_order_id as $product_id => $details) {
								for ($i = 0; $i < count($details); ++$i) {
									$mp_ord_data = $commission->get_seller_final_order_info($order_id, $user_id);

									$total_payment = round(floatval($mp_ord_data['total_seller_amount']), 2);
									if ($details[$i]['variable_id'] == 0) {
										?>
										<tr class="order_item alt-table-row">
											<td class="product-name toptable">
												<a target="_blank" href="<?php echo esc_url(get_permalink($product_id)); ?>"><?php echo $details[$i]['product_name']; ?></a>
												<strong class="product-quantity">× <?php echo $details[$i]['qty']; ?></strong>
												<?php do_action('wk_mp_append_order_meta_data', $product_id, $details, $order_id); ?>
											</td>
											<td class="product-total toptable">
												<?php echo $cur_symbol.$mp_ord_data['product_total']; ?>
											</td>
										</tr>
										<?php
            						} else {
										$product = new WC_Product($product_id);
										$attribute = $product->get_attributes();

										$attribute_name = '';

										$variation = new WC_Product_Variation($details[$i]['variable_id']);
										$aaa = $variation->get_variation_attributes(); ?>
										<tr class="order_item alt-table-row">
											<td class="product-name toptable">
												<a target="_blank" href="<?php echo esc_url(get_permalink($product_id)); ?>"><?php echo $details[$i]['product_name']; ?></a>
												<strong class="product-quantity">× <?php echo $details[$i]['qty']; ?></strong>
												<dl class="variation">
													<?php
													foreach ($attribute as $key => $value) {
														$attribute_name = $value['name'];
														$attribute_prop = strtoupper($aaa['attribute_'.strtolower($attribute_name)]); ?>
														<dt class="variation-size"><?php echo $attribute_name.' : '.$attribute_prop; ?></dt>
														<?php
													} ?>
												</dl>
											</td>
											<td class="product-total toptable">
												<?php echo $cur_symbol.$details[0]['product_total_price']; ?>
											</td>
										</tr>
										<?php
									}
								}
							}

							$sel_rwd_note = '';
							if (!empty($mp_ord_data['reward_data'])) {
								if (!empty($mp_ord_data['reward_data']['seller'])) {
									$sel_rwd_note = ' '.round($mp_ord_data['reward_data']['seller'], 2).'( '.__('Reward', 'marketplace').' )';
								}
							}

							$sel_walt_note = '';
							if (!empty($mp_ord_data['wallet_data'])) {
								if (!empty($mp_ord_data['wallet_data']['seller'])) {
									$sel_walt_note = ' '.round($mp_ord_data['wallet_data']['seller'], 2).'( '.__('Wallet', 'marketplace').' )';
								}
							}

							if ($mp_ord_data['product_total'] != $mp_ord_data['total_seller_amount']) {
								$tip = $total_payment;
								$tip .= ' = ';
								$tip .= ($mp_ord_data['product_total']);
								if ($mp_ord_data['shipping'] > 0) {
									$tip .= ' + ';

									$tip .= ($mp_ord_data['shipping']).' ( '.__('Shipping', 'marketplace').' ) ';
								}
								if (!empty($sel_rwd_note)) {
									$tip .= ' - ';
									$tip .= $sel_rwd_note;
								}
								if (!empty($sel_walt_note)) {
									$tip .= ' - ';
									$tip .= $sel_walt_note;
								}
								$tip .= ' ';
							}

							$shipping_cost = $mp_ord_data['shipping']; ?>
						</tbody>
						<tfoot>
							<?php if (!empty($shipping_method) & $shipping_cost != 0): ?>
								<tr>
									<th scope="row"><b><?php echo esc_html_e('Shipping', 'marketplace'); ?>:</b></th>
									<td class="toptable"><?php echo $cur_symbol.($shipping_cost ? $shipping_cost : 0); ?><i>  via <?php echo $shipping_method; ?></i></td>
								</tr>
							<?php endif; ?>
							<?php if (!empty($payment_method)): ?>
								<tr>
									<th scope="row"><b><?php echo esc_html_e('Payment Method', 'marketplace'); ?>:</b></th>
									<td class="toptable"><?php echo $payment_method; ?></td>
								</tr>
							<?php endif; ?>
							<?php if (!empty($mp_ord_data['total_commission']) && $mp_ord_data['total_commission'] > 0): ?>
							<tr class="alt-table-row">
								<th scope="row"><b><?php echo esc_html_e('Admin Commission', 'marketplace'); ?>:</b></th>
								<td class="toptable">
									<span class="amount"><?php echo $cur_symbol.$mp_ord_data['total_commission']; ?></span>
								</td>
							</tr>
							<?php endif; ?>
							<tr class="alt-table-row">
								<th scope="row"><b><?php echo esc_html_e('Total', 'marketplace'); ?>:</b></th>
								<td class="toptable">
									<span class="amount"><?php echo $cur_symbol.$total_payment; ?></span>
									<?php
									if (!empty($tip)) {
									?>
										<span class="dashicons dashicons-editor-help" title="<?php echo $tip; ?>"></span><?php
    								} ?>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
			<header><h3><?php echo esc_html_e('Customer details', 'marketplace'); ?></h3></header>
			<table class="shop_table shop_table_responsive customer_details widefat">
				<tbody>
					<tr>
						<th><b><?php echo esc_html_e('Email', 'marketplace'); ?>:</b></th>
						<td data-title="Email" class="toptable"><?php echo $order->get_billing_email(); ?></td>
					</tr>
					<tr class="alt-table-row">
						<th><b><?php echo esc_html_e('Telephone', 'marketplace'); ?>:</b></th>
						<td data-title="Telephone" class="toptable"><?php echo $order->get_billing_phone(); ?></td>
					</tr>
				</tbody>
			</table>
			<div class="col2-set addresses">
				<div class="col-1">
					<header class="title">
						<h3><?php echo esc_html_e('Billing Address', 'marketplace'); ?></h3>
					</header>
					<address>
						<?php echo wp_kses_post($order->get_formatted_billing_address(esc_html__('N/A', 'woocommerce'))); ?>
					</address>
				</div><!-- /.col-1 -->
				<div class="col-2">
					<header class="title">
						<h3><?php echo esc_html_e('Shipping Address', 'marketplace'); ?></h3>
					</header>
					<address>
						<?php echo wp_kses_post($order->get_formatted_shipping_address(esc_html__('N/A', 'woocommerce'))); ?>
					</address>
				</div><!-- /.col-2 -->
			</div>

			<!-- Order status form  -->
			<?php

    $order_status = '';
    $query_result = '';

    if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s;', $wpdb->prefix.'mpseller_orders')) === $wpdb->prefix.'mpseller_orders') {
        $query = $wpdb->prepare("SELECT order_status from {$wpdb->prefix}mpseller_orders where order_id = '%d' and seller_id = '%d'", $order_id, $user_id);
        $query_result = $wpdb->get_results($query);
    }

    if ($query_result) {
        $order_status = $query_result[0]->order_status;
    }
    if (!$order_status) {
        $order_status = get_post_field('post_status', $order_id);
    } ?>

			<div class="mp-status-manage-class">
				<header class="title">
					<h3><?php esc_html_e('Order Status', 'marketplace'); ?></h3>
				</header>

				<?php if ($order_status != 'wc-completed'): ?>
					<form method="POST">
						<table class="shop_table shop_table_responsive customer_details widefat">
							<tbody>
								<tr>
									<td><label for="mp-status">Status:</label></td>
									<td>
										<select name="mp-order-status" id="mp-status" class="mp-select">
											<?php
											foreach (wc_get_order_statuses() as $key => $value) {
        									?>
												<option value="<?php echo $key; ?>" <?php if ($order_status == $key) { echo 'selected'; } ?>><?php echo $value; ?></option>
												<?php
    										} ?>
										</select>
									</td>
								</tr>
								<tr>
									<?php
									wp_nonce_field('mp_order_status_nonce_action', 'mp_order_status_nonce');
									echo "<input type='hidden' name='mp-order-id' value={$order_id} />";
									echo "<input type='hidden' name='mp-seller-id' value={$user_id} />";
									echo "<input type='hidden' name='mp-old-order-status' value={$order_status} />"; ?>
									<td><input type="submit" name="mp-submit-status" class="button" value="<?php echo esc_html__('Save', 'marketplace'); ?>" /></td>
								</tr>
							</tbody>
						</table>
					</form>
					<?php else: ?>
						<p><?php echo esc_html__('Status: Order status is completed.', 'marketplace'); ?></p>
					<?php endif; ?>
			</div>
			<?php

			$args = array(
				'post_id' => $order_id,
				'orderby' => 'comment_ID',
				'order' => 'DESC',
				'approve' => 'approve',
				'type' => 'order_note',
			);

			remove_filter('comments_clauses', array('WC_Comments', 'exclude_order_comments'), 10, 1);

			$notes = get_comments($args);

			add_filter('comments_clauses', array('WC_Comments', 'exclude_order_comments'), 10, 1);

			echo '<div class="mp-order-notes">'; ?><h3><?php esc_html_e('Order Notes', 'marketplace'); ?> </h3> <?php

			echo '<ul class="order_notes">';

			if ($notes) {
				foreach ($notes as $note) {
					?>
					<li>
						<div class="note_content">
							<?php echo wpautop(wptexturize(wp_kses_post($note->comment_content))); ?>
						</div>
						<p class="meta">
							<abbr class="exact-date" title="<?php echo $note->comment_date; ?>"><?php printf(__('added on %1$s at %2$s', 'marketplace'), date_i18n(wc_date_format(), strtotime($note->comment_date)), date_i18n(wc_time_format(), strtotime($note->comment_date))); ?></abbr>
						<?php
						if (__('WooCommerce', 'marketplace') !== $note->comment_author):
							/* translators: %s: note author */
							printf(' '.__('by %s', 'marketplace'), $note->comment_author);
						endif; ?>
						</p>
					</li>
				<?php
				}
    		} else {
        		echo '<li>'.esc_html__('There are no notes yet.', 'woocommerce').'</li>';
    		}

			echo '</ul>';

			echo '</div>'; ?>

			</div>

		<?php else: ?>

			<h1><?php echo esc_html__('Cheat\'n huh ???', 'marketplace'); ?></h1>
			<p><?php echo esc_html__('Sorry, You can\'t access other seller\'s orders.', 'marketplace'); ?></p>

		<?php endif; ?> 
	</div> 
		<?php
} catch (Exception $e) {
	if (is_admin()) {
	?>
		<div class="wrap">
			<div class="notice notice-error">
				<p><?php echo $e->getMessage(); ?></p>
			</div>
		</div>
	<?php
	} else {
		wc_print_notice($e->getMessage(), 'error');
	}
}
