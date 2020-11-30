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
use EverAccounting\Core\Repositories;
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
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'invoice';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	public $cache_group = 'eaccounting_invoice';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $data = array(
		'items'              => array(),
		'histories'          => array(),
		'invoice_number'     => '',
		'order_number'       => '',
		'status'             => 'draft',
		'invoiced_at'        => null,
		'due_at'             => null,
		'subtotal'           => 0.00,
		'discount'           => 0.00,
		'tax'                => 0.00,
		'shipping'           => 0.00,
		'total'              => 0.00,
		'currency_code'      => null,
		'currency_rate'      => null,
		'category_id'        => null,
		'contact_id'         => null,
		'contact_name'       => null,
		'contact_email'      => null,
		'contact_tax_number' => null,
		'contact_phone'      => null,
		'contact_address'    => '',
		'note'               => '',
		'footer'             => '',
		'attachment'         => null,
		'parent_id'          => null,
		'creator_id'         => null,
		'date_created'       => null,
	);

	/**
	 * Get the account if ID is passed, otherwise the account is new and empty.
	 *
	 * @since 1.1.0
	 *
	 * @param int|object|Account $data object to read.
	 *
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		if ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( is_numeric( $data ) ) {
			$this->set_id( $data );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} elseif ( is_array( $data ) ) {
			$this->set_props( $data );
		} else {
			$this->set_object_read( true );
		}

		//Load repository
		$this->repository = Repositories::load( $this->object_type );

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}
	}
	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return the invoice number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_invoice_number( $context = 'edit' ) {
		return $this->get_prop( 'invoice_number', $context );
	}

	/**
	 * Return the order number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_order_number( $context = 'edit' ) {
		return $this->get_prop( 'order_number', $context );
	}

	/**
	 * Return the status.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_status( $context = 'edit' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Return the invoiced at.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_invoiced_at( $context = 'edit' ) {
		return $this->get_prop( 'invoiced_at', $context );
	}

	/**
	 * Return the due at.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_due_at( $context = 'edit' ) {
		return $this->get_prop( 'due_at', $context );
	}


	/**
	 * Return the subtotal
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_subtotal( $context = 'edit' ) {
		return $this->get_prop( 'subtotal', $context );
	}

	/**
	 * Return the discount.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_discount( $context = 'edit' ) {
		return $this->get_prop( 'discount', $context );
	}

	/**
	 * Return the tax.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_tax( $context = 'edit' ) {
		return $this->get_prop( 'tax', $context );
	}

	/**
	 * Return the shipping.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_shipping( $context = 'edit' ) {
		return $this->get_prop( 'shipping', $context );
	}

	/**
	 * Return the total.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_total( $context = 'edit' ) {
		return $this->get_prop( 'total', $context );
	}

	/**
	 * Return the currency code.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_currency_code( $context = 'edit' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/**
	 * Return the currency rate.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_currency_rate( $context = 'edit' ) {
		return $this->get_prop( 'currency_rate', $context );
	}

	/**
	 * Return the category id.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_category_id( $context = 'edit' ) {
		return $this->get_prop( 'category_id', $context );
	}

	/**
	 * Return the contact id.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_contact_id( $context = 'edit' ) {
		return $this->get_prop( 'contact_id', $context );
	}

	/**
	 * Return the contact name.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_contact_name( $context = 'edit' ) {
		return $this->get_prop( 'contact_name', $context );
	}

	/**
	 * Return the contact email.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_contact_email( $context = 'edit' ) {
		return $this->get_prop( 'contact_email', $context );
	}

	/**
	 * Return the contact tax number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_contact_tax_number( $context = 'edit' ) {
		return $this->get_prop( 'contact_tax_number', $context );
	}

	/**
	 * Return the contact phone.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_contact_phone( $context = 'edit' ) {
		return $this->get_prop( 'contact_phone', $context );
	}

	/**
	 * Return the contact address.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_contact_address( $context = 'edit' ) {
		return $this->get_prop( 'contact_address', $context );
	}

	/**
	 * Return the note.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_note( $context = 'edit' ) {
		return $this->get_prop( 'note', $context );
	}

	/**
	 * Return the footer.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_footer( $context = 'edit' ) {
		return $this->get_prop( 'footer', $context );
	}

	/**
	 * Return the attachment.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_attachment( $context = 'edit' ) {
		return $this->get_prop( 'attachment', $context );
	}

	/**
	 * Return the parent id.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
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
	 * @since  1.1.0
	 *
	 * @param string $invoice_number .
	 *
	 */
	public function set_invoice_number( $invoice_number ) {
		$this->set_prop( 'invoice_number', eaccounting_clean( $invoice_number ) );
	}

	/**
	 * set the order number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $invoice_number .
	 *
	 */
	public function set_order_number( $order_number ) {
		$this->set_prop( 'order_number', eaccounting_clean( $order_number ) );
	}

	/**
	 * set the status.
	 *
	 * @since  1.1.0
	 *
	 * @param string $invoice_number .
	 *
	 */
	public function set_status( $status ) {
		$this->set_prop( 'status', eaccounting_clean( $status ) );
	}

	/**
	 * set the invoiced_at.
	 *
	 * @since  1.1.0
	 *
	 * @param string $invoiced_at .
	 *
	 */
	public function set_invoiced_at( $invoiced_at ) {
		$this->set_prop( 'invoiced_at', eaccounting_string_to_datetime( $invoiced_at ) );
	}

	/**
	 * set the due at.
	 *
	 * @since  1.1.0
	 *
	 * @param string $due_at .
	 *
	 */
	public function set_due_at( $due_at ) {
		$this->set_prop( 'due_at', eaccounting_string_to_datetime( $due_at ) );
	}

	/**
	 * set the subtotal.
	 *
	 * @since  1.1.0
	 *
	 * @param DOUBLE $subtotal .
	 *
	 */
	public function set_subtotal( $subtotal ) {
		$this->set_prop( 'subtotal', eaccounting_sanitize_price( $subtotal ) );
	}

	/**
	 * set the discount.
	 *
	 * @since  1.1.0
	 *
	 * @param DOUBLE $discount .
	 *
	 */
	public function set_discount( $discount ) {
		$this->set_prop( 'discount', eaccounting_sanitize_price( $discount ) );
	}

	/**
	 * set the tax.
	 *
	 * @since  1.1.0
	 *
	 * @param DOUBLE $tax .
	 *
	 */
	public function set_tax( $tax ) {
		$this->set_prop( 'tax', eaccounting_sanitize_price( $tax ) );
	}

	/**
	 * set the shipping.
	 *
	 * @since  1.1.0
	 *
	 * @param DOUBLE $shipping .
	 *
	 */
	public function set_shipping( $shipping ) {
		$this->set_prop( 'shipping', eaccounting_sanitize_price( $shipping ) );
	}

	/**
	 * set the total.
	 *
	 * @since  1.1.0
	 *
	 * @param DOUBLE $total .
	 *
	 */
	public function set_total( $total ) {
		$this->set_prop( 'total', eaccounting_sanitize_price( $total ) );
	}

	/**
	 * set the currency code.
	 *
	 * @since  1.1.0
	 *
	 * @param string $currency_code .
	 *
	 */
	public function set_currency_code( $currency_code ) {
		if ( eaccounting_get_currency_data( $currency_code ) ) {
			$this->set_prop( 'currency_code', eaccounting_clean( $currency_code ) );
		}
	}

	/**
	 * set the currency rate.
	 *
	 * @since  1.1.0
	 *
	 * @param double $currency_rate .
	 *
	 */
	public function set_currency_rate( $currency_rate ) {
		$this->set_prop( 'currency_rate', eaccounting_clean( $currency_rate ) );
	}

	/**
	 * set the category id.
	 *
	 * @since  1.1.0
	 *
	 * @param int $category_id .
	 *
	 */
	public function set_category_id( $category_id ) {
		$this->set_prop( 'category_id', absint( $category_id ) );
	}

	/**
	 * set the contact_id.
	 *
	 * @since  1.1.0
	 *
	 * @param int $contact_id .
	 *
	 */
	public function set_contact_id( $contact_id ) {
		$this->set_prop( 'contact_id', absint( $contact_id ) );
	}

	/**
	 * set the contact name.
	 *
	 * @since  1.1.0
	 *
	 * @param string $contact_name .
	 *
	 */
	public function set_contact_name( $contact_name ) {
		$this->set_prop( 'contact_name', eaccounting_clean( $contact_name ) );
	}

	/**
	 * set the contact_email.
	 *
	 * @since  1.1.0
	 *
	 * @param string $contact_email .
	 *
	 */
	public function set_contact_email( $contact_email ) {
		$this->set_prop( 'contact_email', sanitize_email( $contact_email ) );
	}

	/**
	 * set the contact tax number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $contact_tax_number .
	 *
	 */
	public function set_contact_tax_number( $contact_tax_number ) {
		$this->set_prop( 'contact_tax_number', eaccounting_clean( $contact_tax_number ) );
	}

	/**
	 * set the contact phone.
	 *
	 * @since  1.1.0
	 *
	 * @param string $contact_phone .
	 *
	 */
	public function set_contact_phone( $contact_phone ) {
		$this->set_prop( 'contact_phone', eaccounting_clean( $contact_phone ) );
	}

	/**
	 * set the contact address.
	 *
	 * @since  1.1.0
	 *
	 * @param string $contact_address .
	 *
	 */
	public function set_contact_address( $contact_address ) {
		$this->set_prop( 'contact_address', eaccounting_sanitize_textarea( $contact_address ) );
	}

	/**
	 * set the note.
	 *
	 * @since  1.1.0
	 *
	 * @param string $note .
	 *
	 */
	public function set_note( $note ) {
		$this->set_prop( 'note', eaccounting_sanitize_textarea( $note ) );
	}

	/**
	 * set the footer.
	 *
	 * @since  1.1.0
	 *
	 * @param string $footer .
	 *
	 */
	public function set_footer( $footer ) {
		$this->set_prop( 'footer', eaccounting_sanitize_textarea( $footer ) );
	}

	/**
	 * set the attachment.
	 *
	 * @since  1.1.0
	 *
	 * @param string $attachment .
	 *
	 */
	public function set_attachment( $attachment ) {
		$this->set_prop( 'attachment', eaccounting_sanitize_textarea( $attachment ) );
	}

	/**
	 * set the parent id.
	 *
	 * @since  1.1.0
	 *
	 * @param int $parent_id .
	 *
	 */
	public function set_parent_id( $parent_id ) {
		$this->set_prop( 'parent_id', absint( $parent_id ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Additional methods
	|--------------------------------------------------------------------------
	|
	| Does extra thing as helper functions.
	|
	*/
	/**
	 * Set the invoice items.
	 *
	 * @since 1.1.0
	 *
	 * @param InvoiceItem[] $value items.
	 */
	public function set_items( $value ) {

		// Remove existing items.
		$this->set_prop( 'items', array() );

		// Ensure that we have an array.
		if ( ! is_array( $value ) ) {
			return;
		}

		foreach ( $value as $item ) {
			$this->add_item( $item );
		}

	}

	/**
	 * Get the invoice items.
	 *
	 * @since 1.1.0
	 * @param  string $context View or edit context.
	 * @return InvoiceItem[]
	 */
	public function get_items( $context = 'view' ) {
		return $this->get_prop( 'items', $context );
	}

	/**
	 * Retrieves a specific item.
	 *
	 * @since 1.0.19
	 */
	public function get_item( $item_id ) {
		$items   = $this->get_items();
		$item_id = (int) $item_id;

		return ( ! empty( $item_id ) && isset( $items[ $item_id ] ) ) ? $items[ $item_id ] : null;
	}

	/**
	 * Removes a specific item.
	 *
	 * @since 1.1.0
	 */
	public function remove_item( $item_id ) {
		$items   = $this->get_items();
		$item_id = (int) $item_id;

		if ( isset( $items[ $item_id ] ) ) {
			unset( $items[ $item_id ] );
			$this->set_prop( 'items', $items );
		}
	}

	/**
	 * Adds an item to the invoice.
	 *
	 * @param InvoiceItem|array $item
	 *
	 * @return \WP_Error|Bool
	 */
	public function add_item( $item ) {

		if ( is_array( $item ) ) {
			$id   = wp_parse_args( $item, array( 'id' => null ) );
			$item = new InvoiceItem( $id );

			$item->set_props( (array) $item );
		}

		if ( is_numeric( $item ) ) {
			$item = new InvoiceItem( $item );
		}

		// Invoice id.
		$item->set_invoice_id( $this->get_id() );

		// Retrieve all items.
		$items                          = $this->get_items();
		$items[ (int) $item->get_id() ] = $item;

		$this->set_prop( 'items', $items );

		return true;
	}


	public function email_invoice() {

	}

	public function mark_sent() {

	}

	public function mark_cancelled() {

	}

	public function mark_paid() {

	}

	public function duplicate() {

	}
}
