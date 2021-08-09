<?php
/**
 * Handle the Invoice object.
 *
 * @package     EverAccounting
 * @class       Invoice
 * @version     1.2.1
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Invoice object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 * @property string $document_number
 * @property string $type
 * @property string $order_number
 * @property string $status
 * @property string $issue_date
 * @property string $due_date
 * @property string $payment_date
 * @property int $category_id
 * @property int $contact_id
 * @property string $address
 * @property string $currency_code
 * @property float $currency_rate
 * @property float $discount
 * @property string $discount_type
 * @property float $subtotal
 * @property float $total_tax
 * @property float $total_discount
 * @property float $total_fees
 * @property float $total_shipping
 * @property float $total
 * @property boolean $tax_inclusive
 * @property string $note
 * @property string $terms
 * @property int $attachment_id
 * @property string $key
 * @property int $parent_id
 * @property int $creator_id
 * @property string $date_created
 *
 * @property Invoice_Item[] $items
 */
class Invoice extends Data {

	/**
	 * Invoice data container.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $data = array(
		'document_number' => '',
		'type'            => '',
		'order_number'    => '',
		'status'          => 'draft',
		'issue_date'      => null,
		'due_date'        => null,
		'payment_date'    => null,
		'category_id'     => null,
		'contact_id'      => null,
		'address'         => array(
			'name'       => '',
			'company'    => '',
			'street'     => '',
			'city'       => '',
			'state'      => '',
			'postcode'   => '',
			'country'    => '',
			'email'      => '',
			'phone'      => '',
			'vat_number' => '',
		),
		'discount'        => 0.00,
		'discount_type'   => 'percentage',
		'subtotal'        => 0.00,
		'total_tax'       => 0.00,
		'total_discount'  => 0.00,
		'total_fees'      => 0.00,
		'total_shipping'  => 0.00,
		'total'           => 0.00,
		'tax_inclusive'   => 1,
		'note'            => '',
		'terms'           => '',
		'attachment_id'   => null,
		'currency_code'   => null,
		'currency_rate'   => 1,
		'key'             => null,
		'parent_id'       => null,
		'creator_id'      => null,
		'date_created'    => null,
	);

	/**
	 * A map of database fields to data types.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $data_type = array(
		'id'              => '%d',
		'document_number' => '%s',
		'type'            => '%s',
		'order_number'    => '%s',
		'status'          => '%s',
		'issue_date'      => '%s',
		'due_date'        => '%s',
		'payment_date'    => '%s',
		'category_id'     => '%d',
		'contact_id'      => '%d',
		'address'         => '%s',
		'currency_code'   => '%s',
		'currency_rate'   => '%.8f',
		'discount'        => '%.4f',
		'discount_type'   => '%s',
		'subtotal'        => '%.4f',
		'total_tax'       => '%.4f',
		'total_discount'  => '%.4f',
		'total_fees'      => '%.4f',
		'total_shipping'  => '%.4f',
		'total'           => '%.4f',
		'tax_inclusive'   => '%d',
		'note'            => '%s',
		'terms'           => '%s',
		'attachment_id'   => '%d',
		'key'             => '%s',
		'parent_id'       => '%d',
		'creator_id'      => '%d',
		'date_created'    => '%s',
	);

	/**
	 * Contains invoice items.
	 *
	 * @since 1.2.1
	 * @var Invoice_Item[]
	 */
	protected $items = null;

	/**
	 * Invoice constructor.
	 *
	 * Get the note if id is passed, otherwise the note is new and empty.
	 *
	 * @param int|object|array|Invoice $invoice object to read.
	 *
	 * @since 1.1.0
	 */
	public function __construct( $invoice = 0 ) {
		parent::__construct();
		if ( $invoice instanceof self ) {
			$this->set_id( $invoice->get_id() );
		} elseif ( is_object( $invoice ) && ! empty( $invoice->id ) ) {
			$this->set_id( $invoice->id );
		} elseif ( is_array( $invoice ) && ! empty( $invoice['id'] ) ) {
			$this->set_props( $invoice );
		} elseif ( is_numeric( $invoice ) ) {
			$this->set_id( $invoice );
		} else {
			$this->set_object_read( true );
		}

		$data = self::get_raw( $this->get_id() );
		if ( $data ) {
			$this->set_props( $data );
			$this->set_object_read( true );
		} else {
			$this->set_id( 0 );
		}

	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete documents from the database.
	| Written in abstract fashion so that the way documents are stored can be
	| changed more easily in the future.
	|
	| A save method is included for convenience (chooses update or create based
	| on if the order exists yet).
	|
	*/
	/**
	 * Retrieve the Invoice from database instance.
	 *
	 * @param int $invoice_id Invoice id.
	 * @param string $field Database field.
	 *
	 * @return object|false Object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	public static function get_raw( $invoice_id, $field = 'id' ) {
		global $wpdb;

		$invoice_id = (int) $invoice_id;
		if ( ! $invoice_id ) {
			return false;
		}
		$invoice = wp_cache_get( $invoice_id, 'ea_invoices' );

		if ( ! $invoice ) {
			$invoice = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_invoices WHERE id = %d LIMIT 1", $invoice_id ) );

			if ( ! $invoice ) {
				return false;
			}

			wp_cache_add( $invoice->id, $invoice, 'ea_invoices' );
		}

		return apply_filters( 'eaccounting_invoice_raw_item', $invoice );
	}

	/**
	 *  Insert an item in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @param array $args An array of arguments for internal use case.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.1.0
	 */
	protected function insert( $args = [] ) {
		global $wpdb;
		$data_arr = $this->to_array();
		$data     = wp_array_slice_assoc( $data_arr, array_keys( $this->data_type ) );
		$format   = wp_array_slice_assoc( $this->data_type, array_keys( $data ) );
		$data     = wp_unslash( $data );

		// Bail if nothing to save
		if ( empty( $data ) ) {
			return true;
		}

		/**
		 * Fires immediately before a invoice is inserted in the database.
		 *
		 * @param array $data Invoice data to be inserted.
		 * @param string $data_arr Sanitized invoice item data.
		 * @param Invoice $invoice Invoice object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_invoice', $data, $data_arr, $this );

		/**
		 * Fires immediately before a invoice is inserted in the database.
		 *
		 * The dynamic portion of the hook name, `$this->type`, refers to
		 * the type of invoice.
		 *
		 * @param array $data Invoice data to be inserted.
		 * @param string $data_arr Sanitized invoice item data.
		 * @param Invoice $invoice Invoice object.
		 *
		 * @since 1.2.1
		 */
		do_action( "eaccounting_pre_insert_invoice_{$this->type}", $data, $data_arr, $this );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_invoices', $data, $format ) ) {
			return new \WP_Error( 'db_insert_error', __( 'Could not insert invoice into the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		$this->set_id( $wpdb->insert_id );

		/**
		 * Fires immediately after an invoice is inserted in the database.
		 *
		 * @param int $id Invoice id.
		 * @param array $data Invoice has been inserted.
		 * @param array $data_arr Sanitized invoice data.
		 * @param Invoice $invoice Invoice object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_insert_invoice', $this->id, $data, $data_arr, $this );

		/**
		 * Fires immediately after an invoice is inserted in the database.
		 *
		 * The dynamic portion of the hook name, `$this->type`, refers to
		 * the type of invoice.
		 *
		 * @param int $id Invoice id.
		 * @param array $data Invoice has been inserted.
		 * @param array $data_arr Sanitized invoice data.
		 * @param Invoice $invoice Invoice object.
		 *
		 * @since 1.2.1
		 */
		do_action( "eaccounting_insert_invoice_{$this->type}", $this->id, $data, $data_arr, $this );

		return true;
	}

	/**
	 *  Update an object in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @param array $args An array of arguments for internal use case.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.1.0
	 */
	protected function update( $args = [] ) {
		global $wpdb;
		$changes = $this->get_changes();
		$data    = wp_array_slice_assoc( $changes, array_keys( $this->data_type ) );
		$format  = wp_array_slice_assoc( $this->data_type, array_keys( $data ) );
		$data    = wp_unslash( $data );
		// Bail if nothing to save
		if ( empty( $data ) ) {
			return true;
		}

		/**
		 * Fires immediately before an existing invoice is updated in the database.
		 *
		 * @param int $id Invoice id.
		 * @param array $data Invoice data.
		 * @param array $changes The data will be updated.
		 * @param Invoice $invoice Invoice object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_invoices', $this->get_id(), $this->to_array(), $changes, $this );

		/**
		 * Fires immediately before an existing invoice is updated in the database.
		 *
		 * The dynamic portion of the hook name, `$this->type`, refers to
		 * the type of invoice.
		 *
		 * @param int $id Invoice id.
		 * @param array $data Invoice data.
		 * @param array $changes The data will be updated.
		 * @param Invoice $invoice Invoice object.
		 *
		 * @since 1.2.1
		 */
		do_action( "eaccounting_pre_update_invoices_{$this->type}", $this->get_id(), $this->to_array(), $changes, $this );

		if ( false === $wpdb->update( $wpdb->prefix . 'ea_invoices', $data, [ 'id' => $this->get_id() ], $format, [ 'id' => '%d' ] ) ) {
			return new \WP_Error( 'db_update_error', __( 'Could not update invoice in the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing invoice is updated in the database.
		 *
		 * @param int $id Invoice id.
		 * @param array $data Invoice data.
		 * @param array $changes The data will be updated.
		 * @param Invoice $invoice Invoice object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_update_invoice', $this->get_id(), $this->to_array(), $changes, $this );

		/**
		 * Fires immediately after an existing invoice is updated in the database.
		 *
		 * The dynamic portion of the hook name, `$this->type`, refers to
		 * the type of invoice.
		 *
		 * @param int $id Invoice id.
		 * @param array $data Invoice data.
		 * @param array $changes The data will be updated.
		 * @param Invoice $invoice Invoice object.
		 *
		 * @since 1.2.1
		 */
		do_action( "eaccounting_update_invoice_{$this->type}", $this->get_id(), $this->to_array(), $changes, $this );

		return true;
	}

	/**
	 * Saves an object in the database.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @since 1.1.0
	 */
	public function save() {
		$user_id = get_current_user_id();

		// Check required
		if ( empty( (int) $this->get_prop( 'account_id' ) ) ) {
			return new \WP_Error( 'empty_invoice_account_id', esc_html__( 'Invoice associated account is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'payment_date' ) ) ) {
			return new \WP_Error( 'empty_invoice_payment_date', esc_html__( 'Invoice payment date is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'type' ) ) ) {
			return new \WP_Error( 'empty_invoice_type', esc_html__( 'Invoice type is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'type' ) ) ) {
			return new \WP_Error( 'empty_invoice_payment_method', esc_html__( 'Invoice payment method is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'date_created' ) ) || '0000-00-00 00:00:00' === $this->get_prop( 'date_created' ) ) {
			$this->set_date_prop( 'date_created', current_time( 'mysql' ) );
		}

		if ( empty( $this->get_prop( 'creator_id' ) ) ) {
			$this->set_prop( 'creator_id', $user_id );
		}

		$changes = $this->get_changes();
		if ( array_key_exists( 'account_id', $changes ) || ! $this->exists() ) {
			$account = new Account( $this->account_id );
			if ( ! $account->exists() || empty( $account->currency_code ) ) {
				return new \WP_Error( 'invalid_invoice_account_id', esc_html__( 'Invoice associated account does not exist', 'wp-ever-accounting' ) );
			}

			$currency = new Currency( $account->currency_code );
			if ( ! $currency->exists() || empty( $currency->rate ) ) {
				return new \WP_Error( 'invalid_invoice_account_currency', esc_html__( 'Invoice associated account currency does not exist', 'wp-ever-accounting' ) );
			}

			$this->set_prop( 'currency_code', $account->currency_code );
			$this->set_prop( 'currency_rate', $currency->rate );
		}

		if ( $this->exists() ) {
			$is_error = $this->update();
		} else {
			$is_error = $this->insert();
		}

		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		$this->apply_changes();

		// Clear cache.
		wp_cache_delete( $this->get_id(), 'ea_invoices' );
		wp_cache_set( 'last_changed', microtime(), 'ea_invoices' );

		/**
		 * Fires immediately after a invoice is inserted or updated in the database.
		 *
		 * @param int $invoice_id Invoice id.
		 * @param Invoice $invoice Invoice object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_saved_invoice', $this->get_id(), $this );

		/**
		 * Fires immediately after a invoice is inserted or updated in the database.
		 *
		 * The dynamic portion of the hook name, `$this->type`, refers to
		 * the type of invoice.
		 *
		 * @param int $invoice_id Invoice id.
		 * @param Invoice $invoice Invoice object.
		 *
		 * @since 1.2.1
		 */
		do_action( "eaccounting_saved_invoice_{$this->type}", $this->get_id(), $this );

		return $this->exists();
	}

	/**
	 * Deletes the object from database.
	 *
	 * @return array|false true on success, false on failure.
	 * @since 1.1.0
	 */
	public function delete() {
		global $wpdb;
		if ( ! $this->exists() ) {
			return false;
		}

		$data = $this->to_array();

		/**
		 * Filters whether an invoice delete should take place.
		 *
		 * @param bool|null $delete Whether to go forward with deletion.
		 * @param int $invoice_id Invoice id.
		 * @param array $data Invoice data array.
		 * @param Invoice $invoice Invoice object.
		 *
		 * @since 1.2.1
		 */
		$check = apply_filters( 'eaccounting_pre_delete_invoice', null, $this->get_id(), $data, $this );
		if ( null !== $check ) {
			return $check;
		}

		/**
		 * Fires before an invoice is deleted.
		 *
		 * @param int $invoice_id Invoice id.
		 * @param array $data Invoice data array.
		 * @param Invoice $invoice Invoice object.
		 *
		 * @since 1.2.1
		 *
		 * @see eaccounting_delete_invoice()
		 */
		do_action( 'eaccounting_before_delete_invoice', $this->get_id(), $data, $this );

		$result = $wpdb->delete( $wpdb->prefix . 'ea_invoices', array( 'id' => $this->get_id() ) );
		if ( ! $result ) {
			return false;
		}

		eaccounting_delete_invoice_items( $this->get_id() );
		eaccounting_delete_invoice_notes( $this->get_id() );

		/**
		 * Fires after an invoice is deleted.
		 *
		 * @param int $invoice_id Invoice id.
		 * @param array $data Invoice data array.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_delete_invoice', $this->get_id(), $data );

		// Clear object.
		wp_cache_delete( $this->get_id(), 'ea_invoices' );
		wp_cache_set( 'last_changed', microtime(), 'ea_invoices' );
		$this->set_id( 0 );
		$this->set_defaults();

		return $data;
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Functions for getting item data. Getter methods wont change anything unless
	| just returning from the props.
	|
	*/
	/**
	 * Return the document number.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_document_number() {
		return $this->get_prop( 'document_number' );
	}

	/**
	 * Get internal type.
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->get_prop( 'type' );
	}

	/**
	 * Return the order number.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_order_number() {
		return $this->get_prop( 'order_number' );
	}

	/**
	 * Return the status.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_status() {
		return $this->get_prop( 'status' );
	}

	/**
	 * Return the issued at.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_issue_date() {
		return $this->get_prop( 'issue_date' );
	}

	/**
	 * Return the due at.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_due_date() {
		return $this->get_prop( 'due_date' );
	}

	/**
	 * Return the due at.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_payment_date() {
		return $this->get_prop( 'payment_date' );
	}

	/**
	 * Return the category id.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_category_id() {
		return $this->get_prop( 'category_id' );
	}

	/**
	 * Return the contact id.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_contact_id() {
		return $this->get_prop( 'contact_id' );
	}

	/**
	 * Return the address.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_address() {
		return $this->get_prop( 'address' );
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * @param string $prop Name of prop to get.
	 *
	 * @return mixed
	 * @since  1.1.0
	 *
	 */
	protected function get_address_prop( $prop ) {
		$value = null;

		if ( array_key_exists( $prop, $this->data['address'] ) ) {
			$value = isset( $this->changes['address'][ $prop ] ) ? $this->changes['address'][ $prop ] : $this->data['address'][ $prop ];
		}

		return $value;
	}

	/**
	 * Get name.
	 *
	 * @return string
	 * @since 1.1.0
	 *
	 */
	public function get_name() {
		return $this->get_address_prop( 'name' );
	}

	/**
	 * Get company.
	 *
	 * @return string
	 * @since 1.1.0
	 *
	 */
	public function get_company() {
		return $this->get_address_prop( 'company' );
	}

	/**
	 * Get street.
	 *
	 * @return string
	 * @since 1.1.0
	 *
	 */
	public function get_street() {
		return $this->get_address_prop( 'street' );
	}

	/**
	 * Get city.
	 *
	 * @return string
	 * @since 1.1.0
	 *
	 */
	public function get_city() {
		return $this->get_address_prop( 'city' );
	}

	/**
	 * Get state.
	 *
	 * @return string
	 * @since 1.1.0
	 *
	 */
	public function get_state() {
		return $this->get_address_prop( 'state' );
	}

	/**
	 * Get postcode.
	 *
	 * @return string
	 * @since 1.1.0
	 *
	 */
	public function get_postcode() {
		return $this->get_address_prop( 'postcode' );
	}

	/**
	 * Get country.
	 *
	 * @return string
	 * @since 1.1.0
	 *
	 */
	public function get_country() {
		return $this->get_address_prop( 'country' );
	}

	/**
	 * Get email.
	 *
	 * @return string
	 * @since 1.1.0
	 *
	 */
	public function get_email() {
		return $this->get_address_prop( 'email' );
	}

	/**
	 * Get phone.
	 *
	 * @return string
	 * @since 1.1.0
	 *
	 */
	public function get_phone() {
		return $this->get_address_prop( 'phone' );
	}

	/**
	 * Get vat_number.
	 *
	 * @return string
	 * @since 1.1.0
	 *
	 */
	public function get_vat_number() {
		return $this->get_address_prop( 'vat_number' );
	}

	/**
	 * Get the invoice discount total.
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 * @since 1.1.0
	 *
	 */
	public function get_discount() {
		return $this->get_prop( 'discount' );
	}

	/**
	 * Get the invoice discount type.
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 * @since 1.1.0
	 *
	 */
	public function get_discount_type() {
		return $this->get_prop( 'discount_type' );
	}

	/**
	 * Get the invoice subtotal.
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 * @since 1.1.0
	 *
	 */
	public function get_subtotal() {
		return (float) $this->get_prop( 'subtotal' );
	}

	/**
	 * Get the invoice tax total.
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 * @since 1.1.0
	 *
	 */
	public function get_total_tax() {
		return (float) $this->get_prop( 'total_tax' );
	}

	/**
	 * Get the invoice discount total.
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 * @since 1.1.0
	 *
	 */
	public function get_total_discount() {
		return (float) $this->get_prop( 'total_discount' );
	}

	/**
	 * Get total fees.
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 * @since 1.1.0
	 *
	 */
	public function get_total_fees() {
		return (float) $this->get_prop( 'total_fees' );
	}

	/**
	 * Get total shipping.
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 * @since 1.1.0
	 *
	 */
	public function get_total_shipping() {
		return (float) $this->get_prop( 'total_shipping' );
	}

	/**
	 * Get the document total.
	 *
	 * @param string $context View or edit context.
	 *
	 * @return float
	 * @since 1.1.0
	 *
	 */
	public function get_total() {
		return (float) $this->get_prop( 'total' );
	}

	/**
	 * Get tax inclusive or not.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_tax_inclusive() {
		if ( ! $this->exists() ) {
			return eaccounting_prices_include_tax();
		}

		return $this->get_prop( 'tax_inclusive' );
	}

	/**
	 * Return the note.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_note() {
		return $this->get_prop( 'note' );
	}

	/**
	 * Return the terms.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_terms() {
		return $this->get_prop( 'terms' );
	}

	/**
	 * Return the attachment.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_attachment_id() {
		return $this->get_prop( 'attachment_id' );
	}

	/**
	 * Return the currency code.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_currency_code() {
		return $this->get_prop( 'currency_code' );
	}

	/**
	 * Return the currency rate.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_currency_rate() {
		return $this->get_prop( 'currency_rate' );
	}

	/**
	 * Return the invoice key.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_key() {
		return $this->get_prop( 'key' );
	}

	/**
	 * Return the parent id.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_parent_id() {
		return $this->get_prop( 'parent_id' );
	}

	/**
	 * Return object created by.
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 */
	public function get_creator_id() {
		return $this->get_prop( 'creator_id' );
	}

	/**
	 * Get object created date.
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_date_created() {
		return $this->get_prop( 'date_created' );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Functions for setting item data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
	*/
	/**
	 * set the number.
	 *
	 * @param string $document_number .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_document_number( $document_number ) {
		$this->set_prop( 'document_number', eaccounting_clean( $document_number ) );
	}

	/**
	 * set the number.
	 *
	 * @param string $order_number .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_order_number( $order_number ) {
		$this->set_prop( 'order_number', eaccounting_clean( $order_number ) );
	}

	/**
	 * set the number.
	 *
	 * @param string $type .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_type( $type ) {
		$this->set_prop( 'type', eaccounting_clean( $type ) );
	}

	/**
	 * set the status.
	 *
	 * @param string $status .
	 *
	 * @return string[]
	 * @since  1.1.0
	 *
	 */
	public function set_status( $status ) {
		$old_status = $this->get_status();
		// If setting the status, ensure it's set to a valid status.
		if ( true === $this->object_read ) {
			// Only allow valid new status.
			if ( ! array_key_exists( $status, $this->get_statuses() ) ) {
				$status = 'draft';
			}

			// If the old status is set but unknown (e.g. draft) assume its pending for action usage.
			if ( $old_status && ! array_key_exists( $old_status, $this->get_statuses() ) ) {
				$old_status = 'draft';
			}
		}

		$this->set_prop( 'status', $status );

		return array(
			'from' => $old_status,
			'to'   => $status,
		);
	}

	/**
	 * Set date when the invoice was created.
	 *
	 * @param string $date Value to set.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_issue_date( $date ) {
		$this->set_date_prop( 'issue_date', $date );
	}

	/**
	 * set the due at.
	 *
	 * @param string $due_date .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_due_date( $due_date ) {
		$this->set_date_prop( 'due_date', $due_date );
	}

	/**
	 * set the completed at.
	 *
	 * @param string $payment_date .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_payment_date( $payment_date ) {
		$this->set_date_prop( 'payment_date', $payment_date );
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
	 * set the customer_id.
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
	 * set the address.
	 *
	 * @param int $address .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_address( $address ) {
		$this->set_prop( 'address', maybe_unserialize( $address ) );
	}

	/**
	 * Sets a prop for a setter method.
	 *
	 * @param string $prop Name of prop to set.
	 * @param mixed $value Value of the prop.
	 *
	 * @since 1.1.0
	 *
	 */
	protected function set_address_prop( $prop, $value ) {
		if ( array_key_exists( $prop, $this->data['address'] ) ) {
			if ( true === $this->object_read ) {
				if ( $value !== $this->data['address'][ $prop ] || ( isset( $this->changes['address'] ) && array_key_exists( $prop, $this->changes['address'] ) ) ) {
					$this->changes['address'][ $prop ] = $value;
				}
			} else {
				$this->data['address'][ $prop ] = $value;
			}
		}
	}

	/**
	 * Set name.
	 *
	 * @param string $name name.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_name( $name ) {
		$this->set_address_prop( 'name', eaccounting_clean( $name ) );
	}

	/**
	 * Set company.
	 *
	 * @param string $company company.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_company( $company ) {
		$this->set_address_prop( 'company', eaccounting_clean( $company ) );
	}

	/**
	 * Set street.
	 *
	 * @param string $street street.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_street( $street ) {
		$this->set_address_prop( 'street', eaccounting_clean( $street ) );
	}

	/**
	 * Set city.
	 *
	 * @param string $city city.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_city( $city ) {
		$this->set_address_prop( 'city', eaccounting_clean( $city ) );
	}

	/**
	 * Set state.
	 *
	 * @param string $state state.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_state( $state ) {
		$this->set_address_prop( 'state', eaccounting_clean( $state ) );
	}

	/**
	 * Set postcode.
	 *
	 * @param string $postcode postcode.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_postcode( $postcode ) {
		$this->set_address_prop( 'postcode', eaccounting_clean( $postcode ) );
	}

	/**
	 * Set country.
	 *
	 * @param string $country country.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_country( $country ) {
		$this->set_address_prop( 'country', eaccounting_clean( $country ) );
	}

	/**
	 * Set email.
	 *
	 * @param string $email email.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_email( $email ) {
		$this->set_address_prop( 'email', sanitize_email( $email ) );
	}

	/**
	 * Set phone.
	 *
	 * @param string $phone phone.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_phone( $phone ) {
		$this->set_address_prop( 'phone', eaccounting_clean( $phone ) );
	}

	/**
	 * Set vat_number.
	 *
	 * @param string $vat_number vat_number.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_vat_number( $vat_number ) {
		$this->set_address_prop( 'vat_number', eaccounting_clean( $vat_number ) );
	}

	/**
	 * set the discount.
	 *
	 * @param float $discount .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_discount( $discount ) {
		$this->set_prop( 'discount', abs( (float) $discount ) );
	}

	/**
	 * set the discount type.
	 *
	 * @param float $discount_type .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_discount_type( $discount_type ) {
		if ( in_array( $discount_type, array( 'percentage', 'fixed' ), true ) ) {
			$this->set_prop( 'discount_type', $discount_type );
		}
	}

	/**
	 * set the subtotal.
	 *
	 * @param float $subtotal .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_subtotal( $subtotal ) {
		$this->set_prop( 'subtotal', floatval( $subtotal ) );
	}

	/**
	 * set the tax.
	 *
	 * @param float $tax .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_total_tax( $tax ) {
		$this->set_prop( 'total_tax', floatval( $tax ) );
	}

	/**
	 * set the tax.
	 *
	 * @param float $discount .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_total_discount( $discount ) {
		$this->set_prop( 'total_discount', floatval( $discount ) );
	}

	/**
	 * set the fees.
	 *
	 * @param float $fees .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_total_fees( $fees ) {
		$this->set_prop( 'total_fees', floatval( $fees ) );
	}

	/**
	 * set the shipping.
	 *
	 * @param float $shipping .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_total_shipping( $shipping ) {
		$this->set_prop( 'total_shipping', floatval( $shipping ) );
	}

	/**
	 * set the total.
	 *
	 * @param float $total .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_total( $total ) {
		$this->set_prop( 'total', (float) $total );
	}

	/**
	 * set the note.
	 *
	 * @param string $note .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_tax_inclusive( $type ) {
		$this->set_prop( 'tax_inclusive', eaccounting_bool_to_number( $type ) );
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
	 * set the note.
	 *
	 * @param $terms
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_terms( $terms ) {
		$this->set_prop( 'terms', eaccounting_sanitize_textarea( $terms ) );
	}

	/**
	 * set the attachment.
	 *
	 * @param string $attachment .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_attachment_id( $attachment ) {
		$this->set_prop( 'attachment_id', absint( $attachment ) );
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
		$this->set_prop( 'currency_code', eaccounting_clean( $currency_code ) );
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
		$this->set_prop( 'currency_rate', (float) $currency_rate );
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

	/**
	 * Set the invoice key.
	 *
	 * @param string $value New key.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_key( $value ) {
		$key = strtolower( eaccounting_clean( $value ) );
		$this->set_prop( 'key', substr( $key, 0, 30 ) );
	}
}
