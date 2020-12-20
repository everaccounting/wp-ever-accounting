<?php

namespace EverAccounting\Core;


use EverAccounting\Abstracts\Singleton;
use EverAccounting\Models\Invoice;

class Emails extends Singleton {

	/**
	 * Emails constructor.
	 */
	public function __construct() {
		//invoice
		add_action( 'eacccounting_insert_invoice', array( __CLASS__, 'send_new_invoice_notification' ) );
		//add_action( 'eaccounting_email_invoice_details', array( __CLASS__, 'invoice_details' ), 10, 2 );
		add_action( 'eaccounting_email_invoice_items', array( __CLASS__, 'invoice_items' ), 10, 2 );
		add_action( 'eaccounting_email_invoice_customer_details', array( __CLASS__, 'invoice_customer_details' ), 10, 2 );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param $sent_to_admin
	 * @param $invoice
	 */
	public static function invoice_items( $invoice, $sent_to_admin ) {
		$args = compact( 'invoice', 'sent_to_admin' );
		eaccounting_get_template( 'emails/invoice-details.php', $args );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param         $sent_to_admin
	 * @param Invoice $invoice
	 */
	public static function invoice_customer_details( $invoice, $sent_to_admin ) {
		$fields = apply_filters(
			'eaccounting_invoice_customer_details',
			array(
				__( 'Name', 'wp-ever-accounting' )     => $invoice->get_name(),
				__( 'Address', 'wp-ever-accounting' )  => $invoice->get_address(),
				__( 'Postcode', 'wp-ever-accounting' ) => $invoice->get_postcode(),
				__( 'Country', 'wp-ever-accounting' )  => $invoice->get_country_nicename(),
				__( 'Phone', 'wp-ever-accounting' )    => $invoice->get_phone(),
				__( 'Email', 'wp-ever-accounting' )    => $invoice->get_email(),
			),
			$invoice,
			$sent_to_admin
		);
		$args   = compact( 'invoice', 'sent_to_admin', 'fields' );
		eaccounting_get_template( 'emails/customer-details.php', $args );
	}

	/**
	 * Replace invoice tags from message.
	 *
	 * @param $invoice
	 * @since 1.1.0
	 */
	public static function get_invoice_placeholders( $invoice ) {
		$placeholders = array(
			'{invoice_number}'    => $invoice->get_invoice_number(),
			'{name}'              => $invoice->get_name(),
			'{invoice_total}'     => eaccounting_price( $invoice->get_total(), $invoice->get_currency_code() ),
			'{invoice_admin_url}' => add_query_arg(
				array(
					'page'       => 'ea-sales',
					'tab'        => 'invoices',
					'action'     => 'view',
					'invoice_id' => $invoice->get_id(),
				),
				admin_url( 'admin.php' )
			),
		);
		return apply_filters( 'eaccounting_invoice_placeholders', $placeholders, $invoice );
	}
	/**
	 * @since 1.1.0
	 *
	 * @param Invoice $invoice
	 *
	 * @return bool
	 */
	public static function send_customer_invoice( $invoice ) {
		if ( 'yes' !== eaccounting()->settings->get( 'email_customer_invoice_active' ) ) {
			return false;
		}

		$subject       = eaccounting()->settings->get( 'email_customer_invoice_subject', '[{site_title}] Customer Invoice #{invoice_number}' );
		$heading       = eaccounting()->settings->get( 'email_customer_invoice_heading', 'Customer Invoice #{invoice_number}' );
		$customer_info = $invoice->get_customer_info();
		$recipient     = sanitize_email( $customer_info['email'] );
		if ( empty( $recipient ) ) {
			return false;
		}

		$message = eaccounting_get_template_html(
			'emails/email-invoice.php',
			array(
				'invoice'       => $invoice,
				'message_body'  => eaccounting()->settings->get( 'email_customer_invoice_body' ),
				'sent_to_admin' => true,
			)
		);

		return eaccounting()
			->mailer()
			->add_placeholders( self::get_invoice_placeholders( $invoice ) )
			->set_prop( 'heading', $heading )
			->send( $recipient, $subject, $message );
	}

	public static function send_customer_note( $invoice, $note ) {

	}


	/**
	 * @since 1.1.0
	 *
	 * @param Invoice $invoice
	 */
	public static function send_new_invoice_notification( $invoice ) {
		if ( 'yes' !== eaccounting()->settings->get( 'email_new_invoice_active' ) ) {
			return false;
		}
		$subject     = eaccounting()->settings->get( 'email_new_invoice_subject' );
		$heading     = eaccounting()->settings->get( 'email_new_invoice_heading' );
		$admin_email = eaccounting()->settings->get( 'admin_email', get_option( 'admin_email' ) );

		$message = eaccounting_get_template_html(
			'emails/email-invoice.php',
			array(
				'invoice'       => $invoice,
				'message_body'  => eaccounting()->settings->get( 'email_new_invoice_body' ),
				'sent_to_admin' => true,
			)
		);

		return eaccounting()
			->mailer()
			->add_placeholders(
				array(
					'{invoice_number}'    => $invoice->get_invoice_number(),
					'{name}'              => $invoice->get_name(),
					'{invoice_total}'     => $invoice->get_formatted_total(),
					'{invoice_admin_url}' => add_query_arg(
						array(
							'page'       => 'ea-sales',
							'tab'        => 'invoices',
							'action'     => 'view',
							'invoice_id' => $invoice->get_id(),
						),
						admin_url( 'admin.php' )
					),
				)
			)
			->set_prop( 'heading', $heading )
			->send( $admin_email, $subject, $message );
	}
}
