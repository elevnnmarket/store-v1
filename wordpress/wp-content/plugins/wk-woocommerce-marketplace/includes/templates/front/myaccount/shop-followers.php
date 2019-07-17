<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?> <div class="woocommerce-account">
	<?php

	apply_filters( 'mp_get_wc_account_menu', 'marketplace' );

?>

<div class="favourite-seller woocommerce-MyAccount-content">

		<div id="notify-customer" class="" >
			<div class="mp-modal-wrapper">

				<div class="mp-modal-dialog">

						<div class="mp-modal-content">

							<form action="" method="post" id="snotifier">

									<div class="mp-modal-header">

											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

											<h4 class="mp-modal-title"><?php echo esc_html__( 'Confirmation', 'marketplace' ); ?></h4>

									</div>

									<div class="mp-modal-body">

											<div class="form-group">

									<label for="subject"><?php echo esc_html__( 'Subject: *', 'marketplace' ); ?></label>

									<input type="text" name="customer_subject" class="form-control customer_subject" aria-describedby="subject" placeholder="<?php echo esc_html( 'Enter Subject', 'marketplace' )?>">

							</div>

							<div class="form-group">

									<label for="message"><?php echo esc_html__( 'Message: *', 'marketplace' ); ?></label>

									<textarea name="customer_message" class="form-control customer_message" aria-describedby="message" placeholder="<?php echo esc_html( 'Enter Message', 'marketplace' )?>" rows="4"></textarea>

								<input type="hidden" name="seller_id" value="<?php echo get_current_user_id(); ?>">

							</div>

									</div>

									<div class="mp-modal-footer">

								<div class="final-result"></div>

											<div class="reaction">

												<button type="button" id="wk-cancel-mail"><?php echo esc_html__( 'Close', 'marketplace' ); ?></button>

												<button type="submit" id="wk-send-mail"><?php echo esc_html__( 'Send Mail', 'marketplace' ); ?></button>

											</div>


									</div>
						</form>
						</div>

				</div>

			</div>

		</div>

		<?php

			$current_user = get_current_user_id();

			$customer_list = get_users( array(
				'meta_key'   =>'favourite_seller',
				'meta_value' => $current_user,
			));
			if ( ! empty( $customer_list ) ) :
		?>
				<div class="filter-data">
					<div class="mail-to-follower">
						<button type="button"><?php echo esc_html__( 'Send Notification', 'marketplace' ); ?></button>
					</div>
					<div class="action-delete">
						<button type="button"><?php echo esc_html__( 'Delete Follower', 'marketplace' ); ?></button>
					</div>
				</div>
			<table class="shop-fol">
				<thead>

					<tr>
						<th style="position:relative">
							<div class="select-all-box">
									<div class="icheckbox_square-blue">
												<input type="checkbox" class="mass-action-checkbox">
												<ins class="iCheck-helper"></ins>
										</div>
							</div>
							</th>
						<th class=""><?php esc_html_e( 'Customer Name', 'marketplace' ); ?></th>
						<th class=""><?php esc_html_e( 'Customer Email', 'marketplace' ); ?></th>
						<th class=""><?php esc_html_e( 'Action', 'marketplace' ); ?></th>
					</tr>

				</thead>

				<tbody>

				<?php

				foreach ( $customer_list as $ckey => $cvalue ) {
					$user_id = $cvalue->data->ID;
					$customer_country = get_user_meta( $user_id, 'shipping_country', true );
					?>
						<tr data-id="<?php echo $user_id; ?>">
							<td>
								<div class=icheckbox_square-blue>
										<input type=checkbox class="mass-action-checkbox">
										<ins class=iCheck-helper></ins>
								</div>
							</td>
					<?php
							echo '<td>' . $cvalue->data->display_name . '</td>';
							echo "<td class='c-mail' data-cmail=" . $cvalue->data->user_email . '>' . $cvalue->data->user_email . '</td>';
							echo "<td><span class='remove-icon' data-customer-id='$user_id' data-seller-id=" . $current_user . '></span></td>';
							echo '</tr>';
				}

				?>

				</tbody>

			</table>

			<?php

		else :

				echo '<strong>';
				echo esc_html__( 'No Followers Available.', 'marketplace' );
				echo '</strong>';

		endif;

			?>

	</div>

</div>
