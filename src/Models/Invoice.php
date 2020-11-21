<?php
/**
 * Handle the invoice object.
 *
 * @package     EverAccounting\Models
 * @class       Currency
 * @version     1.1.0
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Repositories\Invoices;

defined( 'ABSPATH' ) || exit;

/**
 * Class Invoice
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Invoice extends ResourceModel {

	/**
	 * Get the Invoice if ID is passed, otherwise the invoice is new and empty.
	 *
	 * @param int|object|Invoice $data object to read.
	 *
	 * @since 1.1.0
	 *
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data, Invoices::instance() );
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return the invoice number.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_invoice_number( $context = 'edit' ) {
		return $this->get_prop( 'invoice_number', $context );
	}

	/**
	 * Return the order number.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_order_number( $context = 'edit' ) {
		return $this->get_prop( 'order_number', $context );
	}

	/**
	 * Return the status.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_status( $context = 'edit' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Return the invoiced at.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_invoiced_at( $context = 'edit' ) {
		return $this->get_prop( 'invoiced_at', $context );
	}

	/**
	 * Return the due at.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_due_at( $context = 'edit' ) {
		return $this->get_prop( 'due_at', $context );
	}


	/**
	 * Return the subtotal
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_subtotal( $context = 'edit' ) {
		return $this->get_prop( 'subtotal', $context );
	}

	/**
	 * Return the discount.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_discount( $context = 'edit' ) {
		return $this->get_prop( 'discount', $context );
	}

	/**
	 * Return the tax.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_tax( $context = 'edit' ) {
		return $this->get_prop( 'tax', $context );
	}

	/**
	 * Return the shipping.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_shipping( $context = 'edit' ) {
		return $this->get_prop( 'shipping', $context );
	}

	/**
	 * Return the total.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_total( $context = 'edit' ) {
		return $this->get_prop( 'total', $context );
	}

	/**
	 * Return the currency code.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_currency_code( $context = 'edit' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/**
	 * Return the currency rate.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_currency_rate( $context = 'edit' ) {
		return $this->get_prop( 'currency_rate', $context );
	}

	/**
	 * Return the category id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_category_id( $context = 'edit' ) {
		return $this->get_prop( 'category_id', $context );
	}

	/**
	 * Return the contact id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_contact_id( $context = 'edit' ) {
		return $this->get_prop( 'contact_id', $context );
	}

	/**
	 * Return the contact name.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_contact_name( $context = 'edit' ) {
		return $this->get_prop( 'contact_name', $context );
	}

	/**
	 * Return the contact email.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_contact_email( $context = 'edit' ) {
		return $this->get_prop( 'contact_email', $context );
	}

	/**
	 * Return the contact tax number.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_contact_tax_number( $context = 'edit' ) {
		return $this->get_prop( 'contact_tax_number', $context );
	}

	/**
	 * Return the contact phone.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_contact_phone( $context = 'edit' ) {
		return $this->get_prop( 'contact_phone', $context );
	}

	/**
	 * Return the contact address.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_contact_address( $context = 'edit' ) {
		return $this->get_prop( 'contact_address', $context );
	}

	/**
	 * Return the note.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_note( $context = 'edit' ) {
		return $this->get_prop( 'note', $context );
	}

	/**
	 * Return the footer.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_footer( $context = 'edit' ) {
		return $this->get_prop( 'footer', $context );
	}

	/**
	 * Return the attachment.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_attachment( $context = 'edit' ) {
		return $this->get_prop( 'attachment', $context );
	}

	/**
	 * Return the parent id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_parent_id( $context = 'edit' ) {
		return $this->get_prop( 'parent_id', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * set the invoice number.
	 *
	 * @param string $invoice_number .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_invoice_number( $invoice_number ) {
		$this->set_prop( 'invoice_number', eaccounting_clean( $invoice_number ) );
	}

	/**
	 * set the order number.
	 *
	 * @param string $invoice_number .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_order_number( $order_number ) {
		$this->set_prop( 'order_number', eaccounting_clean( $order_number ) );
	}

	/**
	 * set the status.
	 *
	 * @param string $invoice_number .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_status( $status ) {
		$this->set_prop( 'status', eaccounting_clean( $status ) );
	}

	/**
	 * set the invoiced_at.
	 *
	 * @param string $invoiced_at .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_invoiced_at( $invoiced_at ) {
		$this->set_prop( 'invoiced_at', eaccounting_string_to_datetime( $invoiced_at ) );
	}

	/**
	 * set the due at.
	 *
	 * @param string $due_at .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_due_at( $due_at ) {
		$this->set_prop( 'due_at', eaccounting_string_to_datetime( $due_at ) );
	}

	/**
	 * set the subtotal.
	 *
	 * @param DOUBLE $subtotal .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_subtotal( $subtotal ) {
		$this->set_prop( 'subtotal', eaccounting_sanitize_price( $subtotal ) );
	}

	/**
	 * set the discount.
	 *
	 * @param DOUBLE $discount .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_discount( $discount ) {
		$this->set_prop( 'discount', eaccounting_sanitize_price( $discount ) );
	}

	/**
	 * set the tax.
	 *
	 * @param DOUBLE $tax .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_tax( $tax ) {
		$this->set_prop( 'tax', eaccounting_sanitize_price( $tax ) );
	}

	/**
	 * set the shipping.
	 *
	 * @param DOUBLE $shipping .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_shipping( $shipping ) {
		$this->set_prop( 'shipping', eaccounting_sanitize_price( $shipping ) );
	}

	/**
	 * set the total.
	 *
	 * @param DOUBLE $total .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_total( $total ) {
		$this->set_prop( 'total', eaccounting_sanitize_price( $total ) );
	}

	/**
	 * set the currency code.
	 *
	 * @param string $currency_code .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_currency_code( $currency_code ) {
		if ( eaccounting_get_currency_code( $currency_code ) ) {
			$this->set_prop( 'currency_code', eaccounting_clean( $currency_code ) );
		}
	}

	/**
	 * set the currency rate.
	 *
	 * @param double $currency_rate .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_currency_rate( $currency_rate ) {
		$this->set_prop( 'currency_rate', eaccounting_clean( $currency_rate ) );
	}

	/**
	 * set the category id.
	 *
	 * @param int $category_id .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_category_id( $category_id ) {
		$this->set_prop( 'category_id', absint( $category_id ) );
	}

	/**
	 * set the contact_id.
	 *
	 * @param int $contact_id .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_contact_id( $contact_id ) {
		$this->set_prop( 'contact_id', absint( $contact_id ) );
	}

	/**
	 * set the contact name.
	 *
	 * @param string $contact_name .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_contact_name( $contact_name ) {
		$this->set_prop( 'contact_name', eaccounting_clean( $contact_name ) );
	}

	/**
	 * set the contact_email.
	 *
	 * @param string $contact_email .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_contact_email( $contact_email ) {
		$this->set_prop( 'contact_email', sanitize_email( $contact_email ) );
	}

	/**
	 * set the contact tax number.
	 *
	 * @param string $contact_tax_number .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_contact_tax_number( $contact_tax_number ) {
		$this->set_prop( 'contact_tax_number', eaccounting_clean( $contact_tax_number ) );
	}

	/**
	 * set the contact phone.
	 *
	 * @param string $contact_phone .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_contact_phone( $contact_phone ) {
		$this->set_prop( 'contact_phone', eaccounting_clean( $contact_phone ) );
	}

	/**
	 * set the contact address.
	 *
	 * @param string $contact_address .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_contact_address( $contact_address ) {
		$this->set_prop( 'contact_address', eaccounting_sanitize_textarea( $contact_address ) );
	}

	/**
	 * set the note.
	 *
	 * @param string $note .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_note( $note ) {
		$this->set_prop( 'note', eaccounting_sanitize_textarea( $note ) );
	}

	/**
	 * set the footer.
	 *
	 * @param string $footer .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_footer( $footer ) {
		$this->set_prop( 'footer', eaccounting_sanitize_textarea( $footer ) );
	}

	/**
	 * set the attachment.
	 *
	 * @param string $attachment .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_attachment( $attachment ) {
		$this->set_prop( 'attachment', eaccounting_sanitize_textarea( $attachment ) );
	}

	/**
	 * set the parent id.
	 *
	 * @param int $parent_id .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_parent_id( $parent_id ) {
		$this->set_prop( 'parent_id', absint( $parent_id ) );
	}
}
