<?php

namespace EverAccounting\Admin\Settings;

/**
 * Class Emails.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin\Settings
 */
class Emails extends Page {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct( 'emails', __( 'Emails', 'wp-ever-accounting' ) );
	}

	/**
	 * Get default section settings.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_default_section_settings() {
		return array(
			// new payment email section.
			array(
				'type'  => 'title',
				'title' => __( 'New Payment [Customer]', 'wp-ever-accounting' ),
				'desc'  => __( 'Email sent to the customer when a new payment is received.', 'wp-ever-accounting' ),
				'id'    => 'new_payment_email',
			),
			// enable disalbe.
			array(
				'title'   => __( 'Enable/Disable', 'wp-ever-accounting' ),
				'type'    => 'checkbox',
				'id'      => 'new_payment_email_enabled',
				'default' => 'yes',
				'desc'    => __( 'Enable this email notification.', 'wp-ever-accounting' ),
			),
			// subject.
			array(
				'title'   => __( 'Subject', 'wp-ever-accounting' ),
				'type'    => 'text',
				'id'      => 'new_payment_email_subject',
				'default' => __( 'New Payment Received', 'wp-ever-accounting' ),
				'desc'    => __( 'Enter the subject for this email.', 'wp-ever-accounting' ),
			),
			// heading.
			array(
				'title'   => __( 'Heading', 'wp-ever-accounting' ),
				'type'    => 'text',
				'id'      => 'new_payment_email_heading',
				'default' => __( 'New Payment Received', 'wp-ever-accounting' ),
				'desc'    => __( 'Enter the heading for this email.', 'wp-ever-accounting' ),
			),
			// email body.
			array(
				'title'   => __( 'Content', 'wp-ever-accounting' ),
				'type'    => 'wp_editor',
				'id'      => 'new_payment_email_content',
				'default' => __( 'Hello {customer_name},<br><br>A new payment has been received for the invoice {invoice_number}.<br><br>Amount: {amount}<br>Date: {date}<br><br>Thank you for your payment.<br><br>Regards,<br>{site_name}', 'wp-ever-accounting' ),
				'desc'    => __( 'Available template tags:', 'wp-ever-accounting' )
				             . '<br>{customer_name} - ' . __( 'Customer name.', 'wp-ever-accounting' )
				             . '<br>{invoice_number} - ' . __( 'Invoice number.', 'wp-ever-accounting' )
				             . '<br>{amount} - ' . __( 'Payment amount.', 'wp-ever-accounting' )
				             . '<br>{date} - ' . __( 'Payment date.', 'wp-ever-accounting' )
				             . '<br>{site_name} - ' . __( 'Site name.', 'wp-ever-accounting' ),
			),
			// end.
			array(
				'type' => 'sectionend',
				'id'   => 'new_payment_email',
			),
		);
	}
}
