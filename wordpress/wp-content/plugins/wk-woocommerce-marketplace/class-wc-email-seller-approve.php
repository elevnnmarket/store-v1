<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Seller_Approval' ) ) :

/**
 * Seller Approve Email.
 */
class Seller_Approval extends WC_Email {
		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id               = 'seller_approval';
			$this->title            = __( 'Seller Approval', 'marketplace' );
			$this->heading          = __( 'Seller Approval', 'marketplace' );
			$this->subject          = '[' . get_option( 'blogname' ) . ']' . __( ' Account Approved', 'marketplace' );
			$this->template_html    = 'emails/seller-approval.php';
			$this->template_plain   = 'emails/plain/seller-approval.php';
			$this->footer           = __( 'Thanks for choosing marketplace.', 'marketplace' );
			$this->template_base    = plugin_dir_path(__FILE__) . 'woocommerce/templates/';

			add_action( 'woocommerce_approve_seller_notification', array( $this, 'trigger' ) );

			// Call parent constructor
			parent::__construct();

			// Other settings
			$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
		}

	/**
	 * Trigger.
	 *
	 * @param int $order_id
	 */
	public function trigger( $user_id ) {

		if( !empty( $user_id ) ) {
			$this->recipient = get_userdata( $user_id )->user_email;
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * Get content html.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html($this->template_html, array(
			'user_email'	=> $this->get_recipient(),
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => true,
			'plain_text'    => false,
			'email'			=> $this
		), '', $this->template_base);
	}

	/**
	 * Get content plain.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html($this->template_plain, array(
			'user_email'	=> $this->get_recipient(),
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => true,
			'plain_text'    => true,
			'email'			=> $this
		), '', $this->template_base);
	}

}

endif;

return new Seller_Approval();
