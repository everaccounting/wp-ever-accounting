<?php
/**
 * Handle the invoice object.
 *
 * @package     EverAccounting\Models
 * @class       Currency
 * @version     1.1.0
 */

namespace EverAccounting\Models;


use EverAccounting\Core\Repositories;

defined( 'ABSPATH' ) || exit;

/**
 * Class Invoice
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Invoice extends Document {

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
	 * Get the invoice if ID is passed, otherwise the account is new and empty.
	 *
	 * @since 1.1.0
	 *
	 * @param int|object|Invoice $invoice object to read.
	 *
	 */
	public function __construct( $invoice = 0 ) {
		$this->data = array_merge( $this->data, array( 'type' => 'invoice' ) );
		parent::__construct( $invoice );

		if ( $invoice instanceof self ) {
			$this->set_id( $invoice->get_id() );
		} elseif ( is_numeric( $invoice ) ) {
			$this->set_id( $invoice );
		} elseif ( ! empty( $invoice->id ) ) {
			$this->set_id( $invoice->id );
		} elseif ( is_array( $invoice ) ) {
			$this->set_props( $invoice );
		} else {
			$this->set_object_read( true );
		}

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
	 * Return the document number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_invoice_number( $context = 'edit' ) {
		return $this->get_prop( 'document_number', $context );
	}

	/**
	 * Get internal type.
	 *
	 * @return string
	 */
	public function get_type( $context = 'edit' ) {
		return 'invoice';
	}

	/**
	 * Return the customer id.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_customer_id( $context = 'edit' ) {
		return $this->get_prop( 'contact_id', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/
	/**
	 * set the customer id.
	 *
	 * @since  1.1.0
	 *
	 * @param int $customer_id .
	 *
	 */
	public function set_customer_id( $customer_id ) {
		$this->set_prop( 'contact_id', absint( $customer_id ) );
		if ( $this->get_customer_id() && ( ! $this->exists() || in_array( 'contact_id', $this->changes, true ) ) ) {
			$this->maybe_set_address( eaccounting_get_customer( $this->get_customer_id() ) );
		}
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
	 * Adds an item to the invoice.
	 *
	 * @param array $args
	 *
	 * @return \WP_Error|Bool
	 */
	public function add_item( $args ) {
		$args = wp_parse_args( $args, array( 'item_id' => null ) );
		if ( empty( $args['item_id'] ) ) {
			return false;
		}
		$product = new Item( $args['item_id'] );
		if ( ! $product->exists() ) {
			return false;
		}

		//convert the price from default to invoice currency.
		$default_currency = eaccounting()->settings->get( 'default_currency', 'USD' );
		$default          = array(
			'item_id'       => $product->get_id(),
			'item_name'     => $product->get_name(),
			'price'         => $product->get_sale_price(),
			'currency_code' => $this->get_currency_code() ? $this->get_currency_code() : $default_currency,
			'quantity'      => 1,
			'tax_rate'      => eaccounting_tax_enabled() ? $product->get_sales_tax() : 0,
		);
		$item             = $this->get_item( $product->get_id() );
		if ( ! $item ) {
			$item = new DocumentItem();
		}
		$args = wp_parse_args( $args, $default );
		$item->set_props( $args );

		//Now prepare
		$this->items[ $item->get_item_id() ] = $item;

		return $item->get_item_id();
	}
}
