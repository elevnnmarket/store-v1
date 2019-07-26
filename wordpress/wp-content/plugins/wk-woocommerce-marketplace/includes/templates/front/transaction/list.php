<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


?>
<div class="woocommerce-account">
	<?php apply_filters( 'mp_get_wc_account_menu', 'marketplace' ); ?>
	<div id="main_container" class="wk_transaction woocommerce-MyAccount-content">
		<table class="transactionhistory">

			<thead>
				<tr>
					<th width="20%"><?php echo esc_html__( 'Tranaction Id', 'marketplace' ); ?></th>
					<th width="20%"><?php echo esc_html__( 'Date', 'marketplace' ); ?></th>
					<th width="20%"><?php echo esc_html__( 'Amount', 'marketplace' ); ?></th>
					<th width="20%"><?php echo esc_html__( 'Action', 'marketplace' ); ?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				if ( ! empty( $transactions ) && is_array( $transactions ) ) {
					foreach ( $transactions as $trans ) {
						$order = wc_get_order($trans['order_id']);
						if($order){
							$ord_currncy = $order->get_currency();
						}else{
							$ord_currncy = get_woocommerce_currency();
						}
						$transaction_id = $trans['transaction_id'];
						$date           = $trans['transaction_date'];
						$amount         = wc_price( $trans['amount'], array('currency'=>$ord_currncy) );
						$action         = '<a href="' . site_url( 'seller/transaction/view/' ) . $trans['id'] . '" class="button">' . __( 'View', 'marketplace' ) . '</a>';
				?>
						<tr>
							<td>
								<?php echo $transaction_id; ?>
							</td>
							<td>
								<?php echo $date; ?>
							</td>
							<td>
								<?php echo $amount; ?>
							</td>
							<td>
								<?php echo $action; ?>
							</td>
						</tr>
				<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>
</div>
