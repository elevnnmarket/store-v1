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
						$transaction_id = $trans['transaction_id'];
						$date           = get_date_from_gmt( $trans['transaction_date'] );
						$amount         = wc_price( $trans['amount'] );
						$action         = '<a href="' . site_url( get_option( 'wkmp_seller_page_title' ) . '/transaction/view/' ) . $trans['id'] . '" class="button">' . esc_html__( 'View', 'marketplace' ) . '</a>';
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
				} else {

					echo '<tr> <td colspan=4> ' . esc_html__( 'No Transaction Avaliable', 'marketplace' ) . '  </td> </tr>';
				}
				?>
			</tbody>
		</table>
	</div>
</div>
