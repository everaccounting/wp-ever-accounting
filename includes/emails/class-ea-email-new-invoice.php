<?php

namespace EverAccounting\Emails;

use EverAccounting\Abstracts\Email;

defined( 'ABSPATH' ) || exit();

class Email_New_Invoice extends Email {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id             = 'new_invoice';
		$this->title          = __( 'New Invoice', 'wp-ever-accounting' );
		$this->description    = __( 'New payment emails are sent to chosen recipient(s) when a new payment is received.', 'wp-ever-accounting' );
		$this->template_html  = 'emails/admin-new-payment.php';
		$this->template_plain = 'emails/plain/admin-new-payment.php';
		$this->placeholders   = array(
			'{payment_date}' => '',
			'{payment_id}'   => '',
		);

		// Triggers for this email.
		add_action( 'eaccounting_insert_invoice', array( $this, 'trigger' ), 10, 2 );

		// Call parent constructor.
		parent::__construct();

		// Other settings.
		$this->recipient = get_option( 'recipient', get_option( 'admin_email' ) );
	}

	/**
	 * Get email subject.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_subject() {
		return __( '[{site_title}]: New payment #{payment_id}', 'wp-ever-accounting' );
	}

	/**
	 * Get email heading.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_heading() {
		return __( 'New Order: #{payment_id}', 'wp-ever-accounting' );
	}

	/**
	 * Trigger the sending of this email.
	 *
	 * @param $payment_id
	 */
	public function trigger( $payment_id ) {

		if ( ! empty( $payment_id ) ) {
			$payment = eaccounting_get_transaction( $payment_id );
		}

		if ( $payment && $payment->exists() ) {
			$this->object                         = $payment;
			$this->placeholders['{payment_date}'] = eaccounting_format_datetime( $this->object->get_payment_date() );
			$this->placeholders['{payment_id}']   = $this->object->get_id();
		}
		if ( $this->is_enabled() && $this->get_recipient() ) {
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

	}

	/**
	 * Get content html.
	 *
	 * @return string
	 */
	public function get_content_html() {
		return eaccounting_get_template_html(
			$this->template_html,
			array(
				'payment'            => $this->object,
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'sent_to_admin'      => true,
				'plain_text'         => false,
				'email'              => $this,
			)
		);
	}

	/**
	 * Get content plain.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return eaccounting_get_template_html(
			$this->template_plain,
			array(
				'payment'            => $this->object,
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'sent_to_admin'      => true,
				'plain_text'         => true,
				'email'              => $this,
			)
		);
	}

	/**
	 * Default content to show below main email content.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_default_additional_content() {
		return __( 'Congratulations on the sale.', 'wp-ever-accounting' );
	}

}
