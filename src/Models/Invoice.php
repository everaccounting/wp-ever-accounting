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
	public $cache_group = 'ea_invoices';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $data = array(
		'invoice_number' => '',
		'order_number'   => '',
		'status'         => 'pending',
		'issue_date'     => null,
		'due_date'       => null,
		'payment_date'   => null,
		'category_id'    => null,
		'customer_id'    => null,
		'name'           => '',
		'phone'          => '',
		'email'          => '',
		'tax_number'     => '',
		'postcode'       => '',
		'address'        => '',
		'country'        => '',
		'subtotal'       => 0.00,
		'total_discount' => 0.00,
		'total_tax'      => 0.00,
		'total_vat'      => 0.00,
		'total_shipping' => 0.00,
		'total'          => 0.00,
		'note'           => '',
		'footer'         => '',
		'attachment_id'  => null,
		'currency_code'  => null,
		'currency_rate'  => null,
		'key'            => null,
		'parent_id'      => null,
		'creator_id'     => null,
		'date_created'   => null,
	);

	/**
	 * Temporarily stores discount
	 *
	 * @since 1.1.0
	 * @var float
	 */
	protected $discount = 0;

	/**
	 * Order items will be stored here, sometimes before they persist in the DB.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $line_items = array();

	/**
	 * Order items that need deleting are stored here.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $line_items_to_delete = array();

	/**
	 * Get the invoice if ID is passed, otherwise the account is new and empty.
	 *
	 * @since 1.1.0
	 *
	 * @param int|object|Invoice $invoice object to read.
	 *
	 */
	public function __construct( $invoice = 0 ) {
		parent::__construct( $invoice );

		if ( $invoice instanceof self ) {
			$this->set_id( $invoice->get_id() );
		} elseif ( is_numeric( $invoice ) ) {
			$this->set_id( $invoice );
		} elseif ( ! empty( $invoice->id ) ) {
			$this->set_id( $invoice->id );
		} elseif ( is_array( $invoice ) ) {
			$this->set_props( $invoice );
		} elseif ( is_string( $invoice ) && $invoice_id = self::get_by( $invoice, 'key' ) ) { // phpcs: ignore
			$this->set_id( $invoice_id );
		} elseif ( is_string( $invoice ) && $invoice_id = self::get_by( $invoice, 'invoice_number' ) ) { // phpcs: ignore
			$this->set_id( $invoice_id );
		} else {
			$this->set_object_read( true );
		}

		//Load repository
		$this->repository = Repositories::load( 'invoices' );

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}
	}

	/**
	 * Get invoice ID based on field type.
	 *
	 * @since 1.1.0
	 *
	 * @param string $field
	 * @param        $value
	 *
	 * @return int|mixed
	 */
	public static function get_id_by( $value, $field = 'key' ) {
		global $wpdb;
		// Valid fields.
		$fields = array( 'key', 'invoice_number', 'order_number' );
		// Ensure a field has been passed.
		if ( empty( $field ) || ! in_array( $field, $fields, true ) ) {
			return 0;
		}

		// Ensure valid invoice types.
		if ( empty( $type ) || ! array_key_exists( $type, eaccounting_get_invoice_types() ) ) {
			return 0;
		}

		// Maybe retrieve from the cache.
		$invoice_id = wp_cache_get( "$field-$value", 'ea_invoices' );
		if ( false !== $invoice_id ) {
			return $invoice_id;
		}

		// Fetch from the db.
		$table      = $wpdb->prefix . 'ea_invoices';
		$invoice_id = (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT `id` FROM $table WHERE `$field`=%s  AND type=%s LIMIT 1", $value, self::TYPE )
		);

		// Update the cache with our data
		wp_cache_set( "$field-$value", $invoice_id, 'ea_invoices' );

		return $invoice_id;
	}

	/*
	|--------------------------------------------------------------------------
	| Object Specific data methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * All available invoice statuses.
	 *
	 * @when an invoice is created status is pending
	 * @when sent to customer is sent
	 * @when partially paid is partial
	 * @when Full amount paid is paid
	 * @when due date passed but not paid is overdue.
	 *
	 * @since 1.0.1
	 * @return array
	 */
	public static function get_statuses() {
		return array(
			'pending'   => __( 'Pending', 'wp-ever-accounting' ),
			'sent'      => __( 'Sent', 'wp-ever-accounting' ),
			'partial'   => __( 'Partial', 'wp-ever-accounting' ),
			'paid'      => __( 'Paid', 'wp-ever-accounting' ),
			'overdue'   => __( 'Overdue', 'wp-ever-accounting' ),
			'cancelled' => __( 'Cancelled', 'wp-ever-accounting' ),
			'refunded'  => __( 'Refunded', 'wp-ever-accounting' ),
		);
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
	 * Get invoice status nice name.
	 *
	 * @since 1.1.0
	 * @return mixed|string
	 */
	public function get_status_nicename() {
		return isset( $this->get_statuses()[ $this->get_status() ] ) ? $this->get_statuses()[ $this->get_status() ] : $this->get_status();
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
	public function get_issue_date( $context = 'edit' ) {
		return $this->get_prop( 'issue_date', $context );
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
	public function get_due_date( $context = 'edit' ) {
		return $this->get_prop( 'due_date', $context );
	}

	/**
	 * Return the completed at.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_payment_date( $context = 'edit' ) {
		return $this->get_prop( 'payment_date', $context );
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
	public function get_customer_id( $context = 'edit' ) {
		return $this->get_prop( 'customer_id', $context );
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
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Return the customer phone.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_phone( $context = 'edit' ) {
		return $this->get_prop( 'phone', $context );
	}

	/**
	 * Return the customer email.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_email( $context = 'edit' ) {
		return $this->get_prop( 'email', $context );
	}

	/**
	 * Return the customer tax number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_tax_number( $context = 'edit' ) {
		return $this->get_prop( 'tax_number', $context );
	}

	/**
	 * Return the customer postcode.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_postcode( $context = 'edit' ) {
		return $this->get_prop( 'postcode', $context );
	}

	/**
	 * Return the customer address.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_address( $context = 'edit' ) {
		return $this->get_prop( 'address', $context );
	}

	/**
	 * Return the customer country.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_country( $context = 'edit' ) {
		return $this->get_prop( 'country', $context );
	}

	/**
	 * Get country nicename.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_country_nicename() {
		$countries = eaccounting_get_countries();

		return isset( $countries[ $this->get_country() ] ) ? $countries[ $this->get_country() ] : $this->get_country();
	}

	/**
	 * Returns the contact info.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context View or edit context.
	 *
	 * @return array
	 */
	public function get_customer_info( $context = 'view' ) {

		return array(
			'id'         => $this->get_customer_id(),
			'name'       => $this->get_name( $context ),
			'phone'      => $this->get_phone( $context ),
			'email'      => $this->get_email( $context ),
			'tax_number' => $this->get_tax_number( $context ),
			'postcode'   => $this->get_postcode( $context ),
			'address'    => $this->get_address( $context ),
			'country'    => $this->get_country( $context ),
		);
	}

	/**
	 * Get the invoice subtotal.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 */
	public function get_subtotal( $context = 'view' ) {
		return (float) $this->get_prop( 'subtotal', $context );
	}

	/**
	 * Get formatted subtotal.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_formatted_subtotal() {
		return eaccounting_format_price( $this->get_subtotal(), $this->get_currency_code() );
	}

	/**
	 * Get the invoice discount total.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 */
	public function get_total_discount( $context = 'view' ) {
		return (float) $this->get_prop( 'total_discount', $context );
	}

	/**
	 * Get formatted subtotal.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_formatted_total_discount() {
		return eaccounting_format_price( $this->get_total_discount(), $this->get_currency_code() );
	}

	/**
	 * Get the invoice tax total.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 */
	public function get_total_tax( $context = 'view' ) {
		return (float) $this->get_prop( 'total_tax', $context );
	}

	/**
	 * Get formatted subtotal.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_formatted_total_tax() {
		return eaccounting_format_price( $this->get_total_tax(), $this->get_currency_code() );
	}

	/**
	 * Get the invoice vat total.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 */
	public function get_total_vat( $context = 'view' ) {
		return (float) $this->get_prop( 'total_vat', $context );
	}

	/**
	 * Get formatted subtotal.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_formatted_total_vat() {
		return eaccounting_format_price( $this->get_total_vat(), $this->get_currency_code() );
	}

	/**
	 * Get the invoice shipping total.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 */
	public function get_total_shipping( $context = 'edit' ) {
		return (float) $this->get_prop( 'total_shipping', $context );
	}

	/**
	 * Get formatted total.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_formatted_total_shipping() {
		return eaccounting_format_price( $this->get_total(), $this->get_currency_code() );
	}

	/**
	 * Get the invoice total.
	 *
	 * @since 1.1.0
	 * @return float
	 */
	public function get_total( $context = 'edit' ) {
		$total = (float) $this->get_prop( 'total', $context );
		if ( 0 > $total ) {
			$total = 0;
		}

		return $total;
	}

	/**
	 * Get formatted total.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_formatted_total() {
		return eaccounting_format_price( $this->get_total(), $this->get_currency_code() );
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
	public function get_attachment_id( $context = 'edit' ) {
		return $this->get_prop( 'attachment_id', $context );
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
	 * Return the invoice key.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_key( $context = 'edit' ) {
		return $this->get_prop( 'key', $context );
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

	/**
	 * Get the invoice items.
	 *
	 * @since 1.1.0
	 *
	 * @param bool $force
	 *
	 * @return LineItem[]
	 */
	public function get_line_items() {
		if ( $this->exists() && empty( $this->line_items ) ) {
			$line_items = $this->repository->get_line_items( $this );
			foreach ( $line_items as $item_id => $line_item ) {
				if ( ! array_key_exists( $item_id, $this->line_items_to_delete ) ) {
					$this->line_items[ $item_id ] = $line_item;
				}
			}
		}

		return $this->line_items;
	}

	/**
	 * Get item ids.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_line_item_ids() {
		$ids = array();
		foreach ( $this->get_line_items() as $item ) {
			$ids[] = $item->get_item_id();
		}

		return $ids;
	}

	/**
	 * Get the invoice items.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function get_taxes() {
		if ( empty( $this->get_line_items() ) && $this->exists() ) {
			$this->taxes = $this->repository->read_taxes( $this );
		}

		return array();
	}

	/**
	 * Get payments.
	 *
	 * @since 1.1.0
	 * @return Income[]
	 */
	public function get_payments() {
		if ( $this->exists() ) {
			return eaccounting_get_incomes(
				array(
					'document_id' => $this->get_id(),
					'type'        => 'income',
				)
			);
		}

		return array();
	}

	/**
	 * Get total paid
	 *
	 * @since 1.1.0
	 * @return float|int|string
	 */
	public function get_total_paid() {
		$total_paid = 0;
		foreach ( $this->get_payments() as $payment ) {
			var_dump( $payment->get_amount() );
			$total_paid += (float) eaccounting_price_convert_between( $payment->get_amount(), $payment->get_currency_code(), $payment->get_currency_rate(), $this->get_currency_code(), $this->get_currency_rate() );
		}

		return $total_paid;
	}

	/**
	 * @since 1.1.0
	 * @return string
	 */
	public function get_formatted_total_paid() {
		return eaccounting_format_price( $this->get_total_paid(), $this->get_currency_code() );
	}

	/**
	 * Get total due.
	 *
	 * @since 1.1.0
	 * @return float|int
	 */
	public function get_total_due() {
		return abs( $this->get_total() - $this->get_total_paid() );
	}

	/**
	 * Get formatted total due.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_formatted_total_due() {
		return eaccounting_format_price( $this->get_total_due(), $this->get_currency_code() );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * set the number.
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
	 * set the number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $order_number .
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
	 * @param string $status .
	 * @param string $note
	 * @param bool   $by_user
	 *
	 * @return array
	 */
	public function set_status( $status, $note = '', $by_user = false ) {
		$old_status = $this->get_status();
		$statuses   = $this->get_statuses();
		$this->set_prop( 'status', eaccounting_clean( $status ) );

		if ( isset( $statuses['draft'] ) ) {
			unset( $statuses['draft'] );
		}

		// If setting the status, ensure it's set to a valid status.
		if ( true === $this->object_read ) {

			// Only allow valid new status.
			if ( ! array_key_exists( $status, $statuses ) ) {
				$status = 'draft';
			}
		}

		if ( true === $this->object_read && $old_status !== $status ) {
			$this->status_transition = array(
				'from' => ! empty( $this->status_transition['from'] ) ? $this->status_transition['from'] : $old_status,
				'to'   => $status,
				'note' => $note,
			);

			$this->maybe_set_payment_date();
		}

		return array(
			'from' => $old_status,
			'to'   => $status,
		);
	}

	/**
	 * Set date when the invoice was created.
	 *
	 * @since 1.1.0
	 *
	 * @param string $date Value to set.
	 */
	public function set_issue_date( $date ) {
		$this->set_date_prop( 'issue_date', $date );
	}

	/**
	 * set the due at.
	 *
	 * @since  1.1.0
	 *
	 * @param string $due_date .
	 *
	 */
	public function set_due_date( $due_date ) {
		$this->set_date_prop( 'due_date', $due_date );
	}

	/**
	 * set the completed at.
	 *
	 * @since  1.1.0
	 *
	 * @param string $payment_date .
	 *
	 */
	public function set_payment_date( $payment_date ) {
		$this->set_date_prop( 'payment_date', $payment_date );
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
	 * set the customer_id.
	 *
	 * @since  1.1.0
	 *
	 * @param int $customer_id .
	 *
	 */
	public function set_customer_id( $customer_id ) {
		$this->set_prop( 'customer_id', absint( $customer_id ) );
	}

	/**
	 * set the contact name.
	 *
	 * @since  1.1.0
	 *
	 * @param string $contact_name .
	 *
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', eaccounting_clean( $name ) );
	}

	/**
	 * set the contact phone.
	 *
	 * @since  1.1.0
	 *
	 * @param string $phone .
	 *
	 */
	public function set_phone( $phone ) {
		$this->set_prop( 'phone', eaccounting_clean( $phone ) );
	}

	/**
	 * set the email.
	 *
	 * @since  1.1.0
	 *
	 * @param string $email .
	 *
	 */
	public function set_email( $email ) {
		$this->set_prop( 'email', sanitize_email( $email ) );
	}

	/**
	 * set the contact tax number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $tax_number .
	 *
	 */
	public function set_tax_number( $tax_number ) {
		$this->set_prop( 'tax_number', eaccounting_clean( $tax_number ) );
	}

	/**
	 * set the contact postcode.
	 *
	 * @since  1.1.0
	 *
	 * @param string $postcode .
	 *
	 */
	public function set_postcode( $postcode ) {
		$this->set_prop( 'postcode', eaccounting_clean( $postcode ) );
	}

	/**
	 * set the contact address.
	 *
	 * @since  1.1.0
	 *
	 * @param string $address .
	 *
	 */
	public function set_address( $address ) {
		$this->set_prop( 'address', eaccounting_sanitize_textarea( $address ) );
	}

	/**
	 * set the contact country.
	 *
	 * @since  1.1.0
	 *
	 * @param string $country .
	 *
	 */
	public function set_country( $country ) {
		if ( ! empty( $country ) && array_key_exists( $country, eaccounting_get_countries() ) ) {
			$this->set_prop( 'country', eaccounting_clean( $country ) );
		}
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
		$this->set_prop( 'subtotal', floatval( $subtotal ) );
	}

	/**
	 * set the discount.
	 *
	 * @since  1.1.0
	 *
	 * @param DOUBLE $discount .
	 *
	 */
	public function set_total_discount( $discount ) {
		$this->set_prop( 'total_discount', floatval( $discount ) );
	}

	/**
	 * set the tax.
	 *
	 * @since  1.1.0
	 *
	 * @param DOUBLE $tax .
	 *
	 */
	public function set_total_tax( $tax ) {
		$this->set_prop( 'total_tax', floatval( $tax ) );
	}

	/**
	 * set the vat.
	 *
	 * @since  1.1.0
	 *
	 * @param DOUBLE $vat .
	 *
	 */
	public function set_total_vat( $vat ) {
		$this->set_prop( 'total_vat', floatval( $vat ) );
	}

	/**
	 * set the shipping.
	 *
	 * @since  1.1.0
	 *
	 * @param DOUBLE $shipping .
	 *
	 */
	public function set_total_shipping( $shipping ) {
		$this->set_prop( 'total_shipping', floatval( $shipping ) );
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
		$this->set_prop( 'total', floatval( $total ) );
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
	public function set_attachment_id( $attachment ) {
		$this->set_prop( 'attachment_id', absint( $attachment ) );
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
		$this->set_prop( 'currency_rate', floatval( $currency_rate ) );
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

	/**
	 * Set the invoice key.
	 *
	 * @since 1.1.0
	 *
	 * @param string $value New key.
	 */
	public function set_key( $value ) {
		$key = strtolower( eaccounting_clean( $value ) );
		$this->set_prop( 'key', substr( $key, 0, 30 ) );
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
		$this->discount = floatval( $discount );
	}


	/**
	 * Set the invoice items.
	 *
	 * @since 1.1.0
	 *
	 * @param array|LineItem[] $items items.
	 */
	public function set_line_items( $items, $append = false ) {
		// Remove existing items.
		$old_item_ids = $this->get_line_item_ids();

		// Ensure that we have an array.
		if ( ! is_array( $items ) ) {
			return;
		}
		$new_item_ids = array();
		foreach ( $items as $item ) {
			$new_item_ids[] = $this->add_line_item( $item );
		}

		if ( ! $append ) {
			$remove_item_ids = array_diff( $old_item_ids, $new_item_ids );
			foreach ( $remove_item_ids as $remove_item_id ) {
				$this->remove_item( $remove_item_id );
			}
		}
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
	 * Adds an item to the invoice.
	 *
	 * @param array $item
	 *
	 * @return \WP_Error|Bool
	 */
	public function add_line_item( $args ) {
		$args = wp_parse_args( $args, array( 'item_id' => null ) );
		if ( empty( $args['item_id'] ) ) {
			return false;
		}
		$item = new Item( $args['item_id'] );
		if ( ! $item->exists() ) {
			return false;
		}

		$default = array(
			'item_id'       => $item->get_id(),
			'item_name'     => $item->get_name(),
			'item_sku'      => $item->get_sku(),
			'item_price'    => $item->get_sale_price(),
			'quantity'      => 1,
			'discount_rate' => $this->discount,
			'tax_rate'      => $item->get_sales_tax_rate(),
			'vat_rate'      => $item->get_vat(),
		);

		$line_item = $this->get_line_item( $item->get_id() );
		if ( ! $line_item ) {
			$line_item = new LineItem();
		}

		$args = wp_parse_args( $args, $default );
		$line_item->set_props( $args );
		// Now prepare
		$tax_percent         = $line_item->get_tax_rate(); // Tax percentage
		$vat_percent         = $line_item->get_vat_rate(); // Tax percentage
		$subtotal            = $line_item->get_item_price() * $line_item->get_quantity();
		$discount            = $subtotal * ( $line_item->get_discount_rate() / 100 ); //calculated discount for item
		$discounted_subtotal = abs( $subtotal - $discount );
		$tax                 = $discounted_subtotal * ( $tax_percent / 100 );
		$subtotal            = eaccounting_get_price_excluding_tax( $discounted_subtotal, $tax ); // Recalculate subtotal if inclusive
		$vat                 = $discounted_subtotal * ( $vat_percent / 100 );
		$total               = abs( $subtotal + $tax + $vat - $discount );

		$line_item->set_total_discount( $discount );
		$line_item->set_subtotal( $subtotal );
		$line_item->set_total_tax( $tax );
		$line_item->set_total_vat( $vat );
		$line_item->set_total_discount( $discount );
		$line_item->set_total( $total );

		$this->line_items[ $line_item->get_item_id() ] = $line_item;

		return $line_item->get_item_id();
	}

	/**
	 * Remove item from the order.
	 *
	 * @param int  $item_id Item ID to delete.
	 *
	 * @param bool $by_line_id
	 *
	 * @return false|void
	 */
	public function remove_item( $item_id, $by_line_id = false ) {
		$line_item = $this->get_line_item( $item_id, $by_line_id );

		if ( ! $line_item ) {
			return false;
		}

		// Unset and remove later.
		$this->line_items_to_delete[ $line_item->get_item_id() ] = $line_item;
		unset( $this->line_items[ $line_item->get_item_id() ] );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param      $item_id
	 *
	 * @param bool $by_line_id
	 *
	 * @return LineItem|int
	 */
	public function get_line_item( $item_id, $by_line_id = false ) {
		$items = $this->get_line_items();

		// Search for item id.
		if ( ! empty( $items ) && ! $by_line_id ) {
			foreach ( $items as $id => $item ) {
				if ( isset( $items[ $item_id ] ) ) {
					return $items[ $item_id ];
				}
			}
		} elseif ( ! empty( $items ) && $by_line_id ) {
			foreach ( $items as $item ) {
				if ( $item->get_id() === absint( $item_id ) ) {
					return $item;
				}
			}
		}

		return false;
	}

	/**
	 * Calculate.
	 *
	 * @since 1.1.0
	 */
	public function calculate_total() {
		$subtotal       = 0;
		$tax_total      = 0;
		$vat_total      = 0;
		$discount_total = 0;
		$shipping_total = 0;
		foreach ( $this->get_line_items() as $item ) {
			$subtotal       += $item->get_subtotal();
			$tax_total      += $item->get_total_tax();
			$vat_total      += $item->get_total_vat();
			$discount_total += $item->get_total_discount();
		}
		$this->set_subtotal( $subtotal );
		$this->set_total_tax( $tax_total );
		$this->set_total_vat( $vat_total );
		$this->set_total_discount( $discount_total );
		$this->set_total_shipping( $shipping_total );
		$total = abs( $subtotal + $tax_total + $shipping_total - $discount_total );
		$this->set_total( $total );

		return array(
			'subtotal'       => $this->get_subtotal(),
			'total_tax'      => $this->get_total_tax(),
			'total_vat'      => $this->get_total_vat(),
			'total_discount' => $this->get_total_discount(),
			'total_shipping' => $this->get_total_shipping(),
			'total'          => $this->get_total(),
		);
	}

	/**
	 * @since 1.1.0
	 *
	 * @param string $prop
	 * @param        $item
	 *
	 * @return string
	 */
	public function get_formatted_line_amount( $item, $prop = 'total' ) {
		$getter = "get_$prop";

		return eaccounting_format_price( $item->$getter(), $this->get_currency_code() );
	}

	public function add_payment( $amount, $account_id, $payment_method, $date = null, $description = null ) {
		if ( null === $date ) {
			$date = current_time( 'mysql' );
		}

		try {
			$total_due = $this->get_total_due();
			$amount    = eaccounting_sanitize_price( $amount, $this->get_currency_code() );
			if ( $amount > $total_due ) {
				throw new \Exception(
					sprintf(
						/* translators: %s paying amount %s due amount */
						__( 'Amount is larger than due amount, amount: %1$s & due: %2$s' ),
						eaccounting_format_price( $amount, $this->get_currency_code() ),
						$this->get_formatted_total_due()
					)
				);
			}



		} catch ( \Exception $e ) {
			return false;
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Boolean methods
	|--------------------------------------------------------------------------
	|
	| Return true or false.
	|
	*/


	/**
	 * Checks if the invoice has a given status.
	 *
	 * @param $status
	 *
	 * @return bool
	 */
	public function is_status( $status ) {
		return $this->get_status() === eaccounting_clean( $status );
	}

	/**
	 * Check if an invoice is editable.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_editable() {
		return ! in_array( $this->get_status(), array( 'partial', 'paid' ), true );
	}

	/*
	|--------------------------------------------------------------------------
	| Boolean methods
	|--------------------------------------------------------------------------
	|
	| Return true or false.
	|
	*/

	/**
	 * @since 1.1.0
	 * @return bool|\Exception|int
	 */
	public function save() {
		if ( empty( $this->get_currency_code() ) ) {
			throw new \Exception(  __( 'Currency Code must be specified.', 'wp-ever-accounting' ) );
		}
		$this->calculate_total();
		$this->maybe_set_currency_rate();
		$this->maybe_set_customer_info();
		$this->maybe_set_invoice_number();
		$this->maybe_set_key();

		if ( ( 0 <= $this->get_total_paid() ) && ( $this->get_total_paid() <= $this->get_total() ) ) {
			$this->set_status( 'partial' );
			error_log( '1' );
		} elseif ( $this->get_total_paid() >= $this->get_total_paid() ) { // phpcs:ignore
			$this->set_status( 'paid' );
			error_log( '2' );
		}

		error_log( $this->get_status() );

		parent::save();
		$this->save_line_items();

		return $this->exists();
	}

	/**
	 * Set currency rate if not present.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	protected function maybe_set_currency_rate() {
		if ( empty( $this->get_currency_rate() ) && ! empty( $this->get_currency_code() ) ) {
			$currency = new Currency( $this->get_currency_code() );
			$this->set_currency_rate( $currency->exists() ? $currency->get_rate() : 1 );
		}
	}

	/**
	 * Set customer data if not present.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	protected function maybe_set_customer_info() {
		if ( empty( $this->get_customer_id() ) ) {
			return;
		}

		$customer = new Customer( $this->get_customer_id() );
		if ( ! $customer->exists() ) {
			return;
		}
		$info = $this->get_customer_info();
		unset( $info['id'] );
		foreach ( $info as $prop => $value ) {
			if ( ! empty( $value ) ) {
				continue;
			}
			$getter = "get_$prop";
			$setter = "set_$prop";
			if ( is_callable( array( $customer, $getter ) ) && is_callable( array( $this, $setter ) ) ) {
				$this->$setter( $customer->$getter() );
			}
		}

	}

	/**
	 * Generate invoice key.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	protected function maybe_set_key() {
		if ( ! empty( $this->get_key() ) ) {
			return;
		}
		$key = 'ea_' . apply_filters( 'eaccounting_generate_invoice_key', 'invoice_' . wp_generate_password( 19, false ) );
		$this->set_key( strtolower( $key ) );
	}

	/**
	 * Maybe set invoice number.
	 *
	 * @since 1.1.0
	 *
	 */
	protected function maybe_set_invoice_number() {
		if ( ! empty( $this->get_invoice_number() ) ) {
			return;
		}

		$this->set_invoice_number( $this->get_next_invoice_number() );
	}

	/**
	 * Get next invoice number.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	protected function get_next_invoice_number() {
		global $wpdb;
		$max              = (int) $wpdb->get_var( "select max(id) from {$wpdb->prefix}ea_invoices" );
		$prefix           = eaccounting()->settings->get( 'invoice_prefix', 'INV-' );
		$padd             = eaccounting()->settings->get( 'invoice_digit', 5 );
		$formatted_number = zeroise( absint( $max + 1 ), $padd );

		return $prefix . $formatted_number;
	}

	/**
	 * Save all order items which are part of this order.
	 */
	protected function save_line_items() {
		foreach ( $this->line_items_to_delete as $line_item ) {
			if ( $line_item->exists() ) {
				$line_item->delete();
			}
		}

		$this->line_items_to_delete = array();

		$line_items = array_filter( $this->line_items );
		// Add/save items.
		foreach ( $line_items as $line_item ) {
			$line_item->set_parent_id( $this->get_id() );
			$line_item->set_parent_type( 'invoice' );
			$line_item->save();
		}
	}

	/**
	 * Handle the status transition.
	 */
	protected function status_transition() {
		$status_transition = $this->status_transition;

		// Reset status transition variable.
		$this->status_transition = false;

		if ( $status_transition ) {
			try {
				do_action( 'eaccounting_invoice_status_' . $status_transition['to'], $this->get_id(), $this );

				if ( ! empty( $status_transition['from'] ) ) {
					/* translators: 1: old order status 2: new order status */
					$transition_note = sprintf( __( 'Status changed from %1$s to %2$s.', 'wp-ever-accounting' ), $status_transition['from'], $status_transition['to'] );

					// Note the transition occurred.
					$this->add_note( $transition_note, false, true );

					do_action( 'eaccounting_invoice_status_' . $status_transition['from'] . '_to_' . $status_transition['to'], $this->get_id(), $this );
					do_action( 'eaccounting_invoice_status_changed', $this->get_id(), $status_transition['from'], $status_transition['to'], $this );

					// Work out if this was for a payment, and trigger a payment_status hook instead.
					if (
						in_array( $status_transition['from'], array( 'cancelled', 'pending', 'viewed', 'approved', 'overdue', 'unpaid' ), true )
						&& in_array( $status_transition['to'], array( 'paid', 'partial' ), true )
					) {
						do_action( 'eaccounting_invoice_payment_status_changed', $this, $status_transition );
					}
				} else {
					/* translators: %s: new invoice status */
					$transition_note = sprintf( __( 'Status set to %s.', 'wp-ever-accounting' ), $status_transition['to'], $this );

					// Note the transition occurred.
					$this->add_note( trim( $status_transition['note'] . ' ' . $transition_note ), 0, false );
				}
			} catch ( \Exception $e ) {
				$this->add_note( __( 'Error during status transition.', 'wp-ever-accounting' ) . ' ' . $e->getMessage() );
			}
		}
	}


	/**
	 * Adds a note to an invoice.
	 *
	 * @param string $note The note being added.
	 *
	 * @return int|false The new note's ID on success, false on failure.
	 *
	 */
	public function add_note( $note = '', $customer_note = false, $added_by_user = false ) {

		// Bail if no note specified or this invoice is not yet saved.
		if ( ! empty( $note ) || ! $this->exists() || ( ! is_user_logged_in() && $added_by_user ) ) {
			return false;
		}

		$author       = 'System';
		$author_email = 'bot@wpeveraccounting.com';

		// If this is an admin comment or it has been added by the user.
		if ( is_user_logged_in() && ( $added_by_user ) ) {
			$user         = get_user_by( 'id', get_current_user_id() );
			$author       = $user->display_name;
			$author_email = $user->user_email;
		}

		//$this->respository->add_note($note, )
	}
}
