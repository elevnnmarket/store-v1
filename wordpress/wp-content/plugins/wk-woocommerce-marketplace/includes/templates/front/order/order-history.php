<div class="woocommerce-account">
<?php apply_filters('mp_get_wc_account_menu', 'marketplace'); ?>

<div id="main_container" class="woocommerce-MyAccount-content">
    <table class="orderhistory">
        <thead>
			<tr>
				<th width="20%"><?php echo esc_html__('Order', 'marketplace'); ?></th>
				<th width="20%"><?php echo esc_html__('Status', 'marketplace'); ?></th>
				<th width="20%"><?php echo esc_html__('Date', 'marketplace'); ?></th>
				<th width="20%"><?php echo esc_html__('Total', 'marketplace'); ?></th>
				<th width="20%"><?php echo esc_html__('View Order', 'marketplace'); ?></th>
			</tr>
		</thead>
		<tbody class="">
		<?php

global $wpdb, $commission;

$wpmp_obj5 = new MP_Form_Handler();

$user_id = get_current_user_id();

$page_id = $wpmp_obj5->get_page_id(get_option('wkmp_seller_page_title'));

$page_name = $wpdb->get_var("SELECT post_name FROM $wpdb->posts WHERE post_name ='".get_option('wkmp_seller_page_title')."'");

$order_detail = $wpdb->get_results("select DISTINCT woitems.order_id from {$wpdb->prefix}woocommerce_order_itemmeta woi join {$wpdb->prefix}woocommerce_order_items woitems on woitems.order_item_id=woi.order_item_id join {$wpdb->prefix}posts post on woi.meta_value=post.ID where woi.meta_key='_product_id' and post.ID=woi.meta_value and post.post_author='".$user_id."' order by woitems.order_id DESC");

$order_detail = apply_filters('mp_vendor_split_orders', $order_detail, $user_id);

$all_order_details = array();
$order_id_list = array();
foreach ($order_detail as $order_dtl) {
    $order_status = $query_result = '';
    $order_id = $order_dtl->order_id;
    $order_id = apply_filters('mp_set_parent_order', $order_id);
    $order_id_list[] = $order_id;
    $order = new WC_Order($order_id);
    $cur_symbol = get_woocommerce_currency_symbol($order->get_currency());
    $get_item = $order->get_items();

    if ($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s;', $wpdb->prefix.'mpseller_orders')) === $wpdb->prefix.'mpseller_orders') {
        $order_id = apply_filters('mp_set_parent_order', $order_id);
        $query = $wpdb->prepare("SELECT order_status from {$wpdb->prefix}mpseller_orders where order_id = '%d' and seller_id = '%d'", $order_id, $user_id);
        $query_result = $wpdb->get_results($query);
    }

    if ($query_result) {
        $order_status = $query_result[0]->order_status;
    }
    if (!$order_status) {
        $order_status = get_post_field('post_status', $order_id);
    }

    $status_array = wc_get_order_statuses();

    foreach ($get_item as $key => $value) {
        $product_id = $value->get_product_id();
        $variable_id = $value->get_variation_id();
        $post = get_post($product_id, ARRAY_A);
        if ($post['post_author'] == $user_id) {
            $price_id = $product_id;
            $type = 'simple';
            $qty = $value->get_quantity();
            $product = new WC_Product($price_id);
            if ($variable_id != 0) {
                $price_id = $variable_id;
                $type = 'variable';
                $product = new WC_Product_Variation($price_id);
            }
            $product_price = $product->get_price();
            $display_status = isset($status_array[$order_status]) ? $status_array[$order_status] : '-';

            $all_order_details[$order_id][] = array(
                'order_date' => date_format($order->get_date_created(), 'Y-m-d H:i:s'),
                'order_status' => $display_status,
                'product_price' => $product_price,
                'qty' => $qty,
            );
        }
    }
}
$order_by_table = array();
for ($counter = 0; $counter < count($order_id_list); ++$counter) {
    $order_id = $order_id_list[$counter];
    foreach ($all_order_details as $key => $value) {
        if ($order_id == $key) {
            $order = new WC_Order($order_id);
            $cur_symbol = get_woocommerce_currency_symbol($order->get_currency());
            foreach ($value as $index => $val) {
                $qty = $val['qty'];
                $total_price = $val['product_price'];
                $status = $val['order_status'];
                $date = $val['order_date'];

                if (isset($order_by_table[$key])) {
                    $total_price = $order_by_table[$key]['total_price'] + $total_price;
                    $total_qty = $order_by_table[$key]['total_qty'] + $qty;

                    $order_by_table[$key] = array(
                        'symbol' => $cur_symbol,
                        'status' => $status,
                        'date' => $date,
                        'total_price' => $total_price,
                        'total_qty' => $total_qty,
                    );
                } else {
                    $order_by_table[$key] = array(
                        'symbol' => $cur_symbol,
                        'status' => $status,
                        'date' => $date,
                        'total_price' => $total_price,
                        'total_qty' => $qty,
                    );
                }

                $ord_info = $commission->get_seller_order_info($key, $user_id);

                $order_by_table[$key]['total_qty'] = $ord_info['total_qty'];
                $order_by_table[$key]['total_price'] = $ord_info['total_sel_amt'] + $ord_info['ship_data'];
            }
        }
    }
}
foreach ($order_by_table as $key => $value) {
    ?>
			<tr>
				<td width="20%"><?php echo '#'.$key; ?></td>
				<td width="20%"><?php echo ucfirst($value['status']); ?></td>
				<td width="20%"><?php echo $value['date']; ?></td>
				<td width="20%"><?php echo $value['symbol'].$value['total_price'].' for '.$value['total_qty'].' items'; ?></td>
				<td width="20%"><a href="<?php echo esc_url(home_url($page_name.'/order-history/'.$key)); ?>" class="button"><?php echo esc_html_e('View', 'marketplace'); ?></a></td>
			</tr>
		<?php
}?>
		</tbody>
	</table>
	</div>
</div>
