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
use EverAccounting\Traits\CurrencyTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Class Invoice
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Invoice extends ResourceModel {
	use CurrencyTrait;

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'invoice';

	/**
	 * @since 1.1.0
	 * 
	 * @var string
	 */
	public $cache_group = 'ea_invoices';

	/**
	 * Contains a reference to the repository for this class.
	 *
	 * @since 1.1.0
	 * 
	 * @var Invoices
	 */
	protected $repository;

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 * 
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
		'discount'       => 0.00,
		'discount_type'  => 'percentage',
		'total_tax'      => 0.00,
		'total'          => 0.00,
		'tax_inclusive'  => 1,
		'terms'          => '',
		'attachment_id'  => null,
		'currency_code'  => null,
		'currency_rate'  => 1,
		'key'            => null,
		'parent_id'      => null,
		'creator_id'     => null,
		'date_created'   => null,
	);

	/**
	 * Temporarily stores discount
	 *
	 * @since 1.1.0
	 * 
	 * @var float
	 */
	protected $discount = 0;

	/**
	 * Order items will be stored here, sometimes before they persist in the DB.
	 *
	 * @since 1.1.0
	 * 
	 * @var array
	 */
	protected $line_items = array();

	/**
	 * Order items that need deleting are stored here.
	 *
	 * @since 1.1.0
	 * 
	 * @var array
	 */
	protected $line_items_to_delete = array();

	/**
	 * @since 1.1.0
	 * 
	 * @var array
	 */
	private $status_transition = array();

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
		} elseif ( is_string( $invoice ) && $invoice_id = self::get_id_by( $invoice, 'key' ) ) { // phpcs:ignore
			$this->set_id( $invoice_id );
		} elseif ( is_string( $invoice ) && $invoice_id = self::get_id_by( $invoice, 'invoice_number' ) ) { // phpcs:ignore
			$this->set_id( $invoice_id );
		} else {
			$this->set_object_read( true );
		}

		//Load repository
		$this->repository = Repositories::load( 'invoices' );

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}

		$this->required_props = array(
			//'line_items'    => __( 'Line Items', 'wp-ever-accounting' ),
			'currency_code' => __( 'Currency', 'wp-ever-accounting' ),
			'category_id'   => __( 'Category', 'wp-ever-accounting' ),
			'customer_id'   => __( 'Customer', 'wp-ever-accounting' ),
			'issue_date'    => __( 'Issue date', 'wp-ever-accounting' ),
			'due_date'      => __( 'Due date', 'wp-ever-accounting' ),
		);
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
	 * @when  an invoice is created status is pending
	 * @when  sent to customer is sent
	 * @when  partially paid is partial
	 * @when  Full amount paid is paid
	 * @when  due date passed but not paid is overdue.
	 *
	 * @since 1.0.1
	 * 
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
	 * 
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
	 * @param string $format
	 *
	 * @return string
	 */
	public function get_issue_date( $format = 'y-m-d' ) {
		$date = $this->get_prop( 'issue_date', 'raw' );

		return empty( $date ) ? '' : eaccounting_format_datetime( $date, $format );
	}

	/**
	 * Return the due at.
	 *
	 * @since  1.1.0
	 *
	 * @param string $format
	 *
	 * @return string
	 */
	public function get_due_date( $format = 'y-m-d' ) {
		$date = $this->get_prop( 'due_date', 'raw' );

		return empty( $date ) ? '' : eaccounting_format_datetime( $date, $format );
	}

	/**
	 * Return the completed at.
	 *
	 * @since  1.1.0
	 *
	 * @param string $format
	 *
	 * @return string
	 */
	public function get_payment_date( $format = 'y-m-d' ) {
		$date = $this->get_prop( 'payment_date', 'raw' );

		return empty( $date ) ? '' : eaccounting_format_datetime( $date, $format );
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
	 * 
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
	 * Get the invoice discount total.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 */
	public function get_discount( $context = 'view' ) {
		return (float) $this->get_prop( 'discount', $context );
	}

	/**
	 * Get the invoice discount type.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 */
	public function get_discount_type( $context = 'view' ) {
		return $this->get_prop( 'discount_type', $context );
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
	 * Get the invoice discount total.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 */
	public function get_total_discount() {
		if ( $this->is_fixed_discount() ) {
			return (float) $this->get_discount();
		}

		return (float) ( $this->get_subtotal() * ( $this->get_discount() / 100 ) );
	}

	/**
	 * Get the invoice total.
	 *
	 * @since 1.1.0
	 * 
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
	 * Get tax inclusive or not.
	 *
	 * @since 1.1.0
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_tax_inclusive( $context = 'edit' ) {
		return $this->get_prop( 'tax_inclusive', $context );
	}

	/**
	 * Return the terms.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_terms( $context = 'edit' ) {
		return $this->get_prop( 'terms', $context );
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
	 * 
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
		$taxes = array();
		if ( empty( $this->get_line_items() ) ) {
			foreach ( $this->get_line_items() as $item ) {
				$taxes[ $item->get_item_id() ] = $item->get_total_tax();
			}
		}

		return array();
	}

	/**
	 * Get payments.
	 *
	 * @since 1.1.0
	 * 
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
	 * 
	 * @return float|int|string
	 */
	public function get_total_paid() {
		$total_paid = 0;
		foreach ( $this->get_payments() as $payment ) {
			$total_paid += (float) eaccounting_price_convert_between( $payment->get_amount(), $payment->get_currency_code(), $payment->get_currency_rate(), $this->get_currency_code(), $this->get_currency_rate() );
		}

		return $total_paid;
	}


	/**
	 * Get total due.
	 *
	 * @since 1.1.0
	 * 
	 * @return float|int
	 */
	public function get_total_due() {
		return $this->get_total() - $this->get_total_paid();
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
	 * @param bool   $customer_note
	 *
	 * @return array
	 */
	public function set_status( $status ) {
		$old_status = $this->get_status();
		$statuses   = $this->get_statuses();
		$this->set_prop( 'status', eaccounting_clean( $status ) );

		// If setting the status, ensure it's set to a valid status.
		if ( true === $this->object_read ) {

			// Only allow valid new status.
			if ( ! array_key_exists( $status, $statuses ) ) {
				$status = 'pending';
			}
		}

		if ( true === $this->object_read && $old_status !== $status ) {
			$this->status_transition = array(
				'from' => ! empty( $this->status_transition['from'] ) ? $this->status_transition['from'] : $old_status,
				'to'   => $status,
			);
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
	public function set_discount( $discount ) {
		$this->set_prop( 'discount', floatval( $discount ) );
	}

	/**
	 * set the discount type.
	 *
	 * @since  1.1.0
	 *
	 * @param DOUBLE $discount_type .
	 *
	 */
	public function set_discount_type( $discount_type ) {
		if ( in_array( $discount_type, array( 'percentage', 'fixed' ), true ) ) {
			$this->set_prop( 'discount_type', $discount_type );
		}
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
	public function set_tax_inclusive( $type ) {
		$this->set_prop( 'tax_inclusive', eaccounting_bool_to_number( $type ) );
	}

	/**
	 * set the note.
	 *
	 * @since  1.1.0
	 *
	 * @param string $note .
	 *
	 */
	public function set_terms( $terms ) {
		$this->set_prop( 'terms', eaccounting_sanitize_textarea( $terms ) );
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
		if ( ! empty( $currency_rate ) ) {
			$this->set_prop( 'currency_rate', floatval( $currency_rate ) );
		}
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
		//convert the price from default to invoice currency.

		$default = array(
			'item_id'    => $item->get_id(),
			'item_name'  => $item->get_name(),
			'unit_price' => eaccounting_price_convert_from_default( $item->get_sale_price(), $this->get_currency_code(), $this->get_currency_rate() ),
			'quantity'   => 1,
			'tax_rate'   => eaccounting_tax_enabled() ? $item->get_sales_tax_rate() : 0,
		);

		$line_item = $this->get_line_item( $item->get_id() );
		if ( ! $line_item ) {
			$line_item = new LineItem();
		}

		$args = wp_parse_args( $args, $default );
		$line_item->set_props( $args );

		//Now prepare
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

	/**
	 * @since 1.1.0
	 *
	 * @param      $account_id
	 * @param      $payment_method
	 * @param null $date
	 * @param null $description
	 * @param      $amount
	 *
	 * @throws \Exception
	 * @return false
	 */
	public function add_payment( $args = array() ) {
		if ( ! $this->exists() ) {
			return false;
		}

		if ( empty( $args['date'] ) ) {
			$args['date'] = current_time( 'mysql' );
		}

		if ( empty( $args['amount'] ) ) {
			throw new \Exception(
				__( 'Payment amount is required', 'wp-ever-accounting' )
			);
		}

		if ( empty( $args['account_id'] ) ) {
			throw new \Exception(
				__( 'Payment account is required', 'wp-ever-accounting' )
			);
		}

		if ( empty( $args['payment_method'] ) ) {
			throw new \Exception(
				__( 'Payment method is required', 'wp-ever-accounting' )
			);
		}

		$total_due = $this->get_total_due();
		$amount    = (float) eaccounting_sanitize_number( $args['amount'], true );
		//      if ( $amount  $total_due ) {
		//          throw new \Exception(
		//              sprintf(
		//              /* translators: %s paying amount %s due amount */
		//                  __( 'Amount is larger than due amount, input total: %1$s & due: %2$s', 'wp-ever-accounting' ),
		//                  eaccounting_format_price( $amount, $this->get_currency_code() ),
		//                  eaccounting_format_price( $this->get_total_due(), $this->get_currency_code() )
		//              )
		//          );
		//      }

		$account          = eaccounting_get_account( $args['account_id'] );
		$currency         = eaccounting_get_currency( $account->get_currency_code() );
		$converted_amount = eaccounting_price_convert_between( $amount, $this->get_currency_code(), $this->get_currency_rate(), $currency->get_code(), $currency->get_rate() );

		$income = new Income();
		$income->set_props(
			array(
				'payment_date'   => $args['date'],
				'document_id'    => $this->get_id(),
				'account_id'     => absint( $args['account_id'] ),
				'amount'         => $converted_amount,
				'category_id'    => $this->get_category_id(),
				'customer_id'    => $this->get_customer_id(),
				'payment_method' => eaccounting_clean( $args['payment_method'] ),
				'description'    => eaccounting_clean( $args['description'] ),
			)
		);

		$income->save();
		$this->save();
		/* translators: %s amount */
		$this->add_note( sprintf( __( 'Received payment %s', 'wp-ever-accounting' ), $income->get_formatted_amount() ), false );

		return true;
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Used for database transactions.
	|
	*/


	/**
	 * Calculate.
	 *
	 * @since 1.1.0
	 * 
	 * @throws \Exception
	 */
	public function recalculate() {

		$this->check_required_items();

		//if changing or inserting update currency rate.
		if ( array_key_exists( 'currency_code', $this->get_changes() ) || ! $this->exists() ) {
			$currency = new Currency( $this->get_currency_code() );
			$this->set_currency_rate( $currency->get_rate() );
		}

		$subtotal  = 0;
		$tax_total = 0;
		$total     = 0;

		// before calculating need to know subtotal so we can apply fixed discount
		foreach ( $this->get_line_items() as $item ) {
			$subtotal += $item->get_subtotal();
		}

		$discount_type = $this->get_discount_type();
		$discount_rate = 'percentage' === $discount_type ? $this->get_discount() : ( ( $this->get_discount() * 100 ) / $subtotal );
		$tax_inclusive = $this->exists() ? $this->is_tax_inclusive() : eaccounting_prices_include_tax();

		foreach ( $this->get_line_items() as $item ) {
			$line_subtotal = $item->get_subtotal();
			if ( ! empty( $this->get_discount() ) ) {
				$discount = $line_subtotal * ( $discount_rate / 100 );
				$discount = $discount >= $line_subtotal ? $line_subtotal : $discount;
				$item->set_discount( $discount );
			}
			if ( eaccounting_tax_enabled() ) {
				$tax_total = $item->get_total_tax();
			}
			$line_total = $tax_inclusive ? ( $line_subtotal - $tax_total - $item->get_discount() ) : ( $line_subtotal + $tax_total - $item->get_discount() );
			$item->set_total( $line_total );
			$tax_total += (float) $item->get_total_tax();
			$total     += (float) $item->get_total();
		}

		$this->set_subtotal( $subtotal );
		$this->set_total_tax( $tax_total );
		$this->set_total( $total );

		return array(
			'subtotal'  => $this->get_subtotal(),
			'total_tax' => $this->get_total_tax(),
			'total'     => $this->get_total(),
		);
	}

	/**
	 * Set currency rate if not present.
	 *
	 * @since 1.1.0
	 * 
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
	 * 
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
	 * 
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
	 * Set payment date.
	 *
	 * @since 1.1.0
	 */
	protected function maybe_set_payment_date() {
		if ( $this->is_status( 'paid' ) && empty( $this->get_payment_date() ) ) {
			$this->set_payment_date( time() );
		}
	}

	/**
	 * @since 1.1.0
	 * @throws \Exception
	 * @return bool|\Exception|int
	 */
	public function save() {
		$this->recalculate();
		$this->maybe_set_currency_rate();
		$this->maybe_set_customer_info();
		$this->maybe_set_invoice_number();
		$this->maybe_set_key();
		if ( ( 0 < $this->get_total_paid() ) && ( $this->get_total_paid() < $this->get_total() ) ) {
			$this->set_status( 'partial' );
		} elseif ( $this->get_total_paid() >= $this->get_total() ) { // phpcs:ignore
			$this->set_status( 'paid' );
		}

		$this->maybe_set_payment_date();
		parent::save();
		$this->status_transition();
		$this->save_line_items();

		return $this->exists();
	}

	/**
	 * Get next invoice number.
	 *
	 * @since 1.1.0
	 * 
	 * @return string
	 */
	public function get_next_invoice_number() {
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
					$this->add_note( $transition_note, false );

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
					$this->add_note( trim( $status_transition['note'] . ' ' . $transition_note ), false );
				}
			} catch ( \Exception $e ) {
				$this->add_note( __( 'Error during status transition.', 'wp-ever-accounting' ) . ' ' . $e->getMessage() );
			}
		}
	}

	/**
	 * Add note.
	 * 
	 * @since 1.1.0
	 * 
	 * @param       $note
	 * @param false $customer_note
	 *
	 * @return Note|false|int|\WP_Error
	 */
	public function add_note( $note, $customer_note = false ) {
		if ( ! $this->exists() ) {
			return false;
		}
		if ( $customer_note ) {
			do_action( 'eaccounting_invoice_customer_note', $note, $this );
		}

		$author = 'Ever Accounting';
		// If this is an admin comment or it has been added by the user.
		if ( is_user_logged_in() ) {
			$user   = get_user_by( 'id', get_current_user_id() );
			$author = $user->display_name;
		}

		return eaccounting_insert_note(
			array(
				'parent_id'   => $this->get_id(),
				'parent_type' => 'invoice',
				'note'        => $note,
				'highlight'   => $customer_note,
				'author'      => $author,
			)
		);
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
	 * @since 1.1.0
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
	 * 
	 * @return bool
	 */
	public function is_editable() {
		return ! in_array( $this->get_status(), array( 'partial', 'paid' ), true );
	}

	/**
	 * Check if tax inclusive or not.
	 *
	 * @since 1.1.0
	 * 
	 * @return mixed|null
	 */
	public function is_tax_inclusive() {
		return ! empty( $this->get_tax_inclusive() );
	}

	/**
	 * Get the type of discount.
	 *
	 * @since 1.1.0
	 * 
	 * @return bool
	 */
	public function is_fixed_discount() {
		return 'percentage' !== $this->get_discount_type();
	}

}
