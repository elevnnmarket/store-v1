<?php
/**
 * File for ask to admin template.
 *
 * @package  wk-woocommerce-marketplace/includes/template/front/my-account/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;

$error = array();

if ( isset( $_POST['ask_to_admin'] ) ) { // Input var okay.
	if ( isset( $_POST['ask_to_admin_nonce'] ) && ! empty( $_POST['ask_to_admin_nonce'] ) ) { // Input var okay.
		if ( wp_verify_nonce( sanitize_key( $_POST['ask_to_admin_nonce'] ), 'ask_to_admin_nonce_action' ) ) { // Input var okay.
			$error = admin_mailer();
		} else {
			$error['nonce-error'] = __( 'Security check failed, nonce verification failed!', 'marketplace' );
		}
	} else {
		$error['nonce-error'] = __( 'Security check failed, nonce empty!', 'marketplace' );
	}
	if ( $error ) {
		foreach ( $error as $key => $value ) {
			if ( is_admin() ) {
				?>
				<div class="wrap">
					<div class="notice notice-error">
						<p><?php echo esc_html( $value ); ?></p>
					</div>
				</div>
				<?php
			} else {
				wc_print_notice( $value, 'error' );
			}
		}
	}
}

if ( ! is_admin() ) :

?>
<div class="woocommerce-account">
	<?php

	apply_filters( 'mp_get_wc_account_menu', 'marketplace' );

?>

<div class="woocommerce-MyAccount-content">

<?php endif; ?>

	<!-- Form -->
	<div id="ask-data">
		<form id="ask-form" method="post" action="">
			<p>
				<label class="label" for="query_user_sub"><b><?php echo esc_html__( 'Subject', 'marketplace' ); ?></b><span class="required"> *</span></label>
				<input id='query_user_sub' class="wkmp-querysubject regular-text" type="text" name="subject">
				<span  id="askesub_error" class="error-class"></span>
			</p>
			<p>
				<label class="label" for="userquery"><b><?php echo esc_html__( 'Message', 'marketplace' ); ?><span class="required"> *</span></b></label>
				<textarea id="userquery" rows="4" class="wkmp-queryquestion regular-text" name="message"></textarea>
				<span  id="askquest_error" class="error-class"></span>
			</p>
			<div class="">
				<?php wp_nonce_field( 'ask_to_admin_nonce_action', 'ask_to_admin_nonce' ); ?>
				<input id="askToAdminBtn" type="submit" name="ask_to_admin" value="<?php echo esc_html__( 'Ask', 'marketplace' ); ?>" class="button button-primary">
				<!-- <span style="clear:both;"></span> -->
			</div>
		</form>
	</div>

	<?php

	$user_id = get_current_user_id();

	$query_result = $wpdb->get_results( $wpdb->prepare( " SELECT * FROM {$wpdb->prefix}mpseller_asktoadmin where seller_id = %d", $user_id ) );

	if ( $query_result ) :

		if ( ! is_admin() ) :
			?>

			<!-- History table -->
			<div class="mp-asktoadmin-history" id="main_container">

				<table class="mp-asktoadmin-history-table">

					<thead>
						<tr>
							<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo esc_html__( 'Date', 'marketplace' ); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
							<th><?php echo esc_html__( 'Subject', 'marketplace' ); ?></th>
							<th><?php echo esc_html__( 'Message', 'marketplace' ); ?></th>
						</tr>
					</thead>

					<tbody>
						<?php foreach ( $query_result as $key => $value ) : ?>
							<tr>
								<td><?php echo date( 'd-M-Y', strtotime( $value->create_date ) ); ?></td>
								<td><?php echo esc_html( $value->subject ); ?></td>
								<td><?php echo esc_html( $value->message ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>

				</table>

			</div>

<?php
if ( ! is_admin() ) :
	?>

</div>

</div>

<?php endif; ?>

<?php

endif;

endif;
