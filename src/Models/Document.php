<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Document.
 *
 * Calculations:
 * When tax is inclusive:
 * 1. Calculate tax amount from subtotal as inclusive.
 * $subtotal_tax = array_sum( eac_calculate_taxes( $subtotal, $rates, true, true ) ); // note that inclusive parameter is true.
 * $subtotal = $subtotal - $subtotal_tax;
 * 2. Discount is also inclusive.
 * $discount_tax = array_sum( eac_calculate_taxes( $discount, $rates, true, true ) ); // note that inclusive parameter is true.
 * $discount = $discount - $discount_tax;
 * 3. Calculate shipping tax as exclusive and store the shipping tax.
 * $shipping_tax = array_sum( eac_calculate_taxes( $shipping, $rates, false, true ) ); // note that inclusive parameter is false.
 * 4. Calculate fee tax as exclusive and store the fee tax.
 * $fee_tax = array_sum( eac_calculate_taxes( $fee, $rates, false, true ) ); // note that inclusive parameter is false.
 * 5. Calculate total tax.
 * $total_tax = $subtotal_tax + $shipping_tax + $fee_tax - $discount_tax;
 * 6. Calculate total.
 * $total = $subtotal + $shipping + $fee - $discount + $total_tax;
 * When tax is exclusive:
 * 1. Calculate tax amount from subtotal as exclusive.
 * $subtotal_tax = array_sum( eac_calculate_taxes( $subtotal, $rates, false, true ) ); // note that inclusive parameter is false.
 * 2. Discount is also exclusive.
 * $discount_tax = array_sum( eac_calculate_taxes( $discount, $rates, false, true ) ); // note that inclusive parameter is false.
 * 3. Calculate shipping tax as exclusive and store the shipping tax.
 * $shipping_tax = array_sum( eac_calculate_taxes( $shipping, $rates, false, true ) ); // note that inclusive parameter is false.
 * 4. Calculate fee tax as exclusive and store the fee tax.
 * $fee_tax = array_sum( eac_calculate_taxes( $fee, $rates, false, true ) ); // note that inclusive parameter is false.
 * 5. Calculate total tax.
 * $total_tax = $subtotal_tax + $shipping_tax + $fee_tax - $discount_tax;
 * 6. Calculate total.
 * $total = $subtotal + $shipping + $fee - $discount + $total_tax;
 *
 * Note: Round the values to 2 decimal places only when calculating the document totals.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
abstract class Document extends Model {
	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $table_name = 'ea_documents';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $object_type = 'document';

	/**
	 * Meta type declaration for the object.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $meta_type = 'ea_document';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'id'              => null,
		'type'            => '',
		'status'          => 'draft', // draft, sent, viewed, paid, cancelled, refunded.
		'number'          => '', // Document number, invoice number, bill number, estimate number, etc.
		'contact_id'      => null,
		'items_total'     => 0.00,
		'discount_total'  => 0.00,
		'shipping_total'  => 0.00,
		'fees_total'      => 0.00,
		'tax_total'       => 0.00,
		'total'           => 0.00,
		'total_paid'      => 0.00,
		'balance'         => 0.00,
		'discount_type'   => 'fixed',
		'discount_amount' => 0.00,
		'billing_data'    => array(
			'name'       => '',
			'company'    => '',
			'address_1'  => '',
			'address_2'  => '',
			'city'       => '',
			'state'      => '',
			'postcode'   => '',
			'country'    => '',
			'email'      => '',
			'phone'      => '',
			'vat_number' => '',
		),
		'reference'       => '',
		'note'            => '',
		'tax_inclusive'   => '0',
		'vat_exempt'      => '0',
		'issue_date'      => null,
		'due_date'        => null,
		'sent_date'       => null,
		'payment_date'    => null,
		'currency_code'   => '',
		'exchange_rate'   => 1.00,
		'parent_id'       => null,
		'uuid'            => '',
		'created_via'     => 'manual',
		'creator_id'      => null,
		'date_updated'    => null,
		'date_created'    => null,
	);

	/**
	 * document items will be stored here, sometimes before they persist in the DB.
	 *
	 * @since 1.1.0
	 *
	 * @var DocumentItem[]
	 */
	protected $items = null;

	/**
	 * document items that need deleting are stored here.
	 *
	 * @since 1.1.0
	 *
	 * @var DocumentItem[]
	 */
	protected $deletable = array();

	/**
	 * Document constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		$this->core_data['tax_inclusive'] = eac_price_includes_tax() ? 1 : 0;
		$this->core_data['currency_code'] = eac_get_base_currency();
		$this->core_data['creator_id']    = get_current_user_id();
		$this->core_data['date_created']  = wp_date( 'Y-m-d H:i:s' );
		$this->core_data['uuid']          = wp_generate_uuid4();
		parent::__construct( $data );
	}

	/**
	 * Returns all data for this object.
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public function get_data() {
		$data          = parent::get_data();
		$items         = $this->get_items();
		$data['items'] = array();
		foreach ( $items as $item ) {
			$data['items'][] = $item->get_data();
		}

		return $data;
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete discounts from the database.
	|
	*/
	/**
	 * Saves an object in the database.
	 *
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 * @throws \Exception When the invoice is already paid.
	 * @since 1.0.0
	 */
	public function save() {
		global $wpdb;
		$this->calculate_totals();

		// contact id is required.
		if ( empty( $this->get_contact_id() ) ) {
			return new \WP_Error( 'missing_required', __( 'Contact ID is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_type() ) ) {
			return new \WP_Error( 'missing_required', __( 'Type is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_status() ) ) {
			return new \WP_Error( 'missing_required', __( 'Status is required.', 'wp-ever-accounting' ) );
		}

		// Once the invoice is paid, contact can't be changed.
		if ( $this->get_total_paid() > 0 && in_array( 'contact_id', $this->changes, true ) ) {
			return new \WP_Error( 'invalid-argument', __( 'Contact can\'t be changed once the document is paid.', 'wp-ever-accounting' ) );
		}

		// check if the document number is already exists.
		if ( empty( $this->get_number() ) ) {
			$next_number = $this->get_next_number();
			$this->set_number( $next_number );
		}

		// If date created is not set, set it to now.
		if ( empty( $this->get_date_created() ) ) {
			$this->set_date_created( current_time( 'mysql' ) );
		}

		// If It's update, set the updated date.
		if ( $this->exists() ) {
			$this->set_date_updated( current_time( 'mysql' ) );
		}

		// uuid is required.
		if ( empty( $this->get_uuid() ) ) {
			$this->set_uuid( wp_generate_uuid4() );
		}

		try {
			$wpdb->query( 'START TRANSACTION' );
			$saved = parent::save();
			if ( is_wp_error( $saved ) ) {
				throw new \Exception( $saved->get_error_message() );
			}

			foreach ( $this->get_items() as $item ) {
				$item->set_document_id( $this->get_id() );
				$saved = $item->save();
				if ( is_wp_error( $saved ) ) {
					throw new \Exception( $saved->get_error_message() );
				}
			}

			foreach ( $this->deletable as $deletable ) {
				if ( $deletable->exists() && ! $deletable->delete() ) {
					// translators: %s: error message.
					throw new \Exception( sprintf( __( 'Error while deleting items. error: %s', 'wp-ever-accounting' ), $wpdb->last_error ) );
				}
			}

			$wpdb->query( 'COMMIT' );

			return true;
		} catch ( \Exception $e ) {
			$wpdb->query( 'ROLLBACK' );

			return new \WP_Error( 'db-error', $e->getMessage() );
		}
	}

	/**
	 * Deletes an object from the database.
	 *
	 * @param bool $force_delete Whether to bypass trash and force deletion. Default false.
	 *
	 * @return bool|\WP_Error True on success, false or WP_Error on failure.
	 * @since 1.0.0
	 */
	public function delete( $force_delete = false ) {
		$deleted = parent::delete( $force_delete );

		if ( $deleted ) {
			foreach ( $this->get_items() as $item ) {
				$item->delete( $force_delete );
			}

			foreach ( $this->get_taxes() as $tax ) {
				$tax->delete( $force_delete );
			}

			foreach ( $this->get_notes() as $note ) {
				$note->delete( $force_delete );
			}
		}

		return $deleted;
	}
	/*
	|--------------------------------------------------------------------------
	| Crud Getters and Setters
	|--------------------------------------------------------------------------
	| These methods are used to get and set the core data of the document.
	*/
	/**
	 * Get id.
	 *
	 * @return int
	 * @since 1.0.0
	 */
	public function get_id() {
		return (int) $this->get_prop( 'id' );
	}

	/**
	 * Set id.
	 *
	 * @param int $id
	 *
	 * @since 1.0.0
	 */
	public function set_id( $id ) {
		$this->set_prop( 'id', absint( $id ) );
	}

	/**
	 * Get internal type.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_type( $context = 'edit' ) {
		return $this->get_prop( 'type', $context );
	}

	/**
	 * Set internal type.
	 *
	 * @param string $type Document type.
	 *
	 * @since 1.0.0
	 */
	public function set_type( $type ) {
		$this->set_prop( 'type', eac_clean( $type ) );
	}

	/**
	 * Get status.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 */
	public function get_status( $context = 'edit' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Set status.
	 *
	 * @param string $status Status.
	 *
	 * @since  1.1.0
	 */
	public function set_status( $status ) {
		$this->set_prop( 'status', eac_clean( $status ) );
	}

	/**
	 * Get documents number.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 */
	public function get_number( $context = 'edit' ) {
		if ( ! $this->exists() && empty( $this->data['number'] ) ) {
			$this->set_number( $this->get_next_number() );
		}

		return $this->get_prop( 'number', $context );
	}

	/**
	 * set documents number.
	 *
	 * @param string $value Document number.
	 *
	 * @since  1.1.0
	 */
	public function set_number( $value ) {
		$this->set_prop( 'number', eac_clean( $value ) );
	}

	/**
	 * Get contact id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return int
	 * @since  1.1.0
	 */
	public function get_contact_id( $context = 'edit' ) {
		return $this->get_prop( 'contact_id', $context );
	}

	/**
	 * Set contact id.
	 *
	 * @param int $contact_id Contact id.
	 *
	 * @since  1.1.0
	 */
	public function set_contact_id( $contact_id ) {
		$this->set_prop( 'contact_id', absint( $contact_id ) );
	}

	/**
	 * Get item_total.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 * @since  1.1.0
	 */
	public function get_items_total( $context = 'edit' ) {
		return $this->get_prop( 'items_total', $context );
	}

	/**
	 * Set item_total.
	 *
	 * @param float $value Subtotal.
	 *
	 * @since  1.1.0
	 */
	public function set_items_total( $value ) {
		$this->set_prop( 'items_total', eac_sanitize_number( $value ) );
	}

	/**
	 * Get discount total.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 * @since  1.1.0
	 */
	public function get_discount_total( $context = 'edit' ) {
		return $this->get_prop( 'discount_total', $context );
	}

	/**
	 * Set discount total.
	 *
	 * @param float $value Discount total.
	 *
	 * @since  1.1.0
	 */
	public function set_discount_total( $value ) {
		$this->set_prop( 'discount_total', eac_sanitize_number( $value ) );
	}

	/**
	 * Get shipping total.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 * @since 1.1.0
	 */
	public function get_shipping_total( $context = 'edit' ) {
		return $this->get_prop( 'shipping_total', $context );
	}

	/**
	 * Set shipping total.
	 *
	 * @param float $value Shipping total.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping_total( $value ) {
		$this->set_prop( 'shipping_total', eac_sanitize_number( $value ) );
	}

	/**
	 * Get fees total.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 * @since 1.1.0
	 */
	public function get_fees_total( $context = 'edit' ) {
		return $this->get_prop( 'fees_total', $context );
	}

	/**
	 * Set fees total.
	 *
	 * @param float $value Shipping total.
	 *
	 * @since  1.1.0
	 */
	public function set_fees_total( $value ) {
		$this->set_prop( 'fees_total', eac_sanitize_number( $value ) );
	}

	/**
	 * Get total tax
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 * @since 1.1.0
	 */
	public function get_tax_total( $context = 'edit' ) {
		return $this->get_prop( 'tax_total', $context );
	}

	/**
	 * Set total tax
	 *
	 * @param float $value Total tax.
	 *
	 * @since  1.1.0
	 */
	public function set_tax_total( $value ) {
		$this->set_prop( 'tax_total', eac_sanitize_number( $value ) );
	}

	/**
	 * Get total.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 * @since 1.1.0
	 */
	public function get_total( $context = 'edit' ) {
		return $this->get_prop( 'total', $context );
	}

	/**
	 * Set total.
	 *
	 * @param float $value Total.
	 *
	 * @since  1.1.0
	 */
	public function set_total( $value ) {
		$this->set_prop( 'total', eac_sanitize_number( $value ) );
	}

	/**
	 * Get total paid
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 * @since 1.1.0
	 */
	public function get_total_paid( $context = 'edit' ) {
		return $this->get_prop( 'total_paid', $context );
	}

	/**
	 * Set total paid
	 *
	 * @param float $total_paid Total paid.
	 *
	 * @since  1.1.0
	 */
	public function set_total_paid( $total_paid ) {
		$this->set_prop( 'total_paid', eac_sanitize_number( $total_paid, 4 ) );
	}

	/**
	 * Get balance due.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return float
	 * @since 1.1.0
	 */
	public function get_balance( $context = 'edit' ) {
		return $this->get_prop( 'balance', $context );
	}

	/**
	 * Set balance due.
	 *
	 * @param float $balance Total refunded.
	 *
	 * @since  1.1.0
	 */
	public function set_balance( $balance ) {
		$balance = $balance < 0 ? 0 : $balance;
		$this->set_prop( 'balance', eac_sanitize_number( $balance, 4 ) );
	}

	/**
	 * Get discount amount.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 */
	public function get_discount_amount( $context = 'edit' ) {
		return (float) $this->get_prop( 'discount_amount', $context );
	}

	/**
	 * Set discount amount.
	 *
	 * @param string $value Discount amount.
	 *
	 * @since  1.1.0
	 */
	public function set_discount_amount( $value ) {
		$this->set_prop( 'discount_amount', eac_sanitize_number( $value, 4 ) );
	}

	/**
	 * Get discount type.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 */
	public function get_discount_type( $context = 'edit' ) {
		$type = $this->get_prop( 'discount_type', $context );
		if ( ! in_array( $type, array( 'percent', 'fixed' ), true ) ) {
			$type = 'fixed';
		}

		return $type;
	}

	/**
	 * Set discount type.
	 *
	 * @param string $type Discount type.
	 *
	 * @since  1.1.0
	 */
	public function set_discount_type( $type ) {
		if ( ! in_array( $type, array( 'percent', 'fixed' ), true ) ) {
			$type = 'fixed';
		}
		$this->set_prop( 'discount_type', eac_clean( $type ) );
	}

	/**
	 * Get billing.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 */
	public function get_billing_data( $context = 'edit' ) {
		return $this->get_prop( 'billing_data', $context );
	}

	/**
	 * set the billing_data.
	 *
	 * @param int $data .
	 *
	 * @since  1.1.0
	 */
	public function set_billing_data( $data ) {
		$this->set_prop( 'billing_data', maybe_unserialize( $data ) );
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * @param string $prop Name of prop to get.
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return mixed
	 * @since  1.1.0
	 */
	protected function get_billing_prop( $prop, $context = 'view' ) {
		$value = null;

		if ( array_key_exists( $prop, $this->data['billing_data'] ) ) {
			$value = isset( $this->changes['billing_data'][ $prop ] ) ? $this->changes['billing_data'][ $prop ] : $this->data['billing_data'][ $prop ];

			if ( 'view' === $context ) {
				$value = apply_filters( $this->get_hook_prefix() . 'billing_' . $prop, $value, $this );
			}
		}

		return $value;
	}

	/**
	 * Sets a prop for a setter method.
	 *
	 * @param string $prop Name of prop to set.
	 * @param mixed $value Value of the prop.
	 *
	 * @since 1.1.0
	 */
	protected function set_billing_prop( $prop, $value ) {
		if ( array_key_exists( $prop, $this->data['billing_data'] ) && true === $this->object_read ) {
			if ( $value !== $this->data['billing_data'][ $prop ] || ( isset( $this->changes['billing_data'] ) && array_key_exists( $prop, $this->changes['billing_data'] ) ) ) {
				$this->changes['billing_data'][ $prop ] = $value;
			}
		} else if (true === $this->object_read) {
			$this->changes['billing_data'][ $prop ] = $value;
		}else{
			$this->data['billing_data'][ $prop ] = $value;
		}
	}


	/**
	 * Get billing name.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 */
	public function get_billing_name( $context = 'edit' ) {
		return $this->get_billing_prop( 'name', $context );
	}

	/**
	 * Set billing name.
	 *
	 * @param string $name Billing name.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_name( $name ) {
		$this->set_billing_prop( 'name', eac_clean( $name ) );
	}

	/**
	 * Get billing company name.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since 1.1.0
	 */
	public function get_billing_company( $context = 'edit' ) {
		return $this->get_billing_prop( 'company', $context );
	}

	/**
	 * Set billing company name.
	 *
	 * @param string $company Billing company name.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_company( $company ) {
		$this->set_billing_prop( 'company', eac_clean( $company ) );
	}

	/**
	 * Get billing address_1 address.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 */
	public function get_billing_address_1( $context = 'edit' ) {
		return $this->get_billing_prop( 'address_1', $context );
	}

	/**
	 * Set billing address_1 address.
	 *
	 * @param string $address_1 Billing address_1 address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_address_1( $address_1 ) {
		$this->set_billing_prop( 'address_1', eac_clean( $address_1 ) );
	}


	/**
	 * Get billing address_2 address.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 */
	public function get_billing_address_2( $context = 'edit' ) {
		return $this->get_billing_prop( 'address_2', $context );
	}

	/**
	 * Set billing address_2 address.
	 *
	 * @param string $address_2 Billing address_2 address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_address_2( $address_2 ) {
		$this->set_billing_prop( 'address_2', eac_clean( $address_2 ) );
	}

	/**
	 * Get billing city address.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since 1.1.0
	 */
	public function get_billing_city( $context = 'edit' ) {
		return $this->get_billing_prop( 'city', $context );
	}

	/**
	 * Set billing city address.
	 *
	 * @param string $city Billing city address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_city( $city ) {
		$this->set_billing_prop( 'city', eac_clean( $city ) );
	}

	/**
	 * Get billing state address.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since 1.1.0
	 */
	public function get_billing_state( $context = 'edit' ) {
		return $this->get_billing_prop( 'state', $context );
	}

	/**
	 * Set billing state address.
	 *
	 * @param string $state Billing state address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_state( $state ) {
		$this->set_billing_prop( 'state', eac_clean( $state ) );
	}

	/**
	 * Get billing postcode code address.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since 1.1.0
	 */
	public function get_billing_postcode( $context = 'edit' ) {
		return $this->get_billing_prop( 'postcode', $context );
	}

	/**
	 * Set billing postcode code address.
	 *
	 * @param string $postcode Billing postcode code address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_postcode( $postcode ) {
		$this->set_billing_prop( 'postcode', eac_clean( $postcode ) );
	}

	/**
	 * Get billing country address.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since 1.1.0
	 */
	public function get_billing_country( $context = 'edit' ) {
		return $this->get_billing_prop( 'country', $context );
	}

	/**
	 * Set billing country address.
	 *
	 * @param string $country Billing country address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_country( $country ) {
		$this->set_billing_prop( 'country', eac_clean( $country ) );
	}

	/**
	 * Get billing phone number.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since 1.1.0
	 */
	public function get_billing_phone( $context = 'edit' ) {
		return $this->get_billing_prop( 'phone', $context );
	}

	/**
	 * Set billing phone number.
	 *
	 * @param string $phone Billing phone number.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_phone( $phone ) {
		$this->set_billing_prop( 'phone', eac_clean( $phone ) );
	}

	/**
	 * Get billing email address.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since 1.1.0
	 */
	public function get_billing_email( $context = 'edit' ) {
		return $this->get_billing_prop( 'email', $context );
	}

	/**
	 * Set billing email address.
	 *
	 * @param string $email Billing email address.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_email( $email ) {
		$this->set_billing_prop( 'email', eac_clean( $email ) );
	}

	/**
	 * Get billing vat number.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 */
	public function get_billing_vat_number( $context = 'edit' ) {
		return $this->get_billing_prop( 'vat_number', $context );
	}

	/**
	 * Set billing vat number.
	 *
	 * @param string $vat Billing vat number.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_vat_number( $vat ) {
		$this->set_billing_prop( 'vat_number', eac_clean( $vat ) );
	}

	/**
	 * Get billing tax number.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 */
	public function get_billing_tax_number( $context = 'edit' ) {
		return $this->get_billing_prop( 'tax_number', $context );
	}

	/**
	 * Set billing tax number.
	 *
	 * @param string $tax Billing tax number.
	 *
	 * @since  1.1.0
	 */
	public function set_billing_tax_number( $tax ) {
		$this->set_billing_prop( 'tax_number', eac_clean( $tax ) );
	}

	/**
	 * Get order number.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 */
	public function get_reference( $context = 'edit' ) {
		return $this->get_prop( 'reference', $context );
	}

	/**
	 * Set order number.
	 *
	 * @param string $value Order number.
	 *
	 * @since  1.1.0
	 */
	public function set_reference( $value ) {
		$this->set_prop( 'reference', eac_clean( $value ) );
	}

	/**
	 * Get notes
	 *
	 * @param string $context View or edit context.
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 */
	public function get_note( $context = 'edit' ) {
		return $this->get_prop( 'note', $context );
	}

	/**
	 * Set note
	 *
	 * @param string $note Notes.
	 *
	 * @since  1.1.0
	 */
	public function set_note( $note ) {
		$this->set_prop( 'note', eac_clean( $note ) );
	}


	/**
	 * Get tax inclusive or not.
	 *
	 * @param string $context View or edit context.
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 */
	public function get_tax_inclusive( $context = 'edit' ) {
		return $this->get_prop( 'tax_inclusive', $context );
	}

	/**
	 * Set tax inclusive or not.
	 *
	 * @param bool $value Tax inclusive or not.
	 *
	 * @since  1.1.0
	 */
	public function set_tax_inclusive( $value ) {
		$this->set_prop( 'tax_inclusive', $this->string_to_int( $value ) );
	}

	/**
	 * Get var exempt or not.
	 *
	 * @param string $context View or edit context.
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 */
	public function get_vat_exempt( $context = 'edit' ) {
		return $this->get_prop( 'vat_exempt', $context );
	}

	/**
	 * Set tax inclusive or not.
	 *
	 * @param bool $value Tax inclusive or not.
	 *
	 * @since  1.1.0
	 */
	public function set_vat_exempt( $value ) {
		$this->set_prop( 'vat_exempt', $this->string_to_int( $value ) );
	}


	/**
	 * Get the date issued.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_issue_date( $context = 'edit' ) {
		return $this->get_date_prop( 'issue_date', $context, 'Y-m-d' );
	}

	/**
	 * Set the date issued.
	 *
	 * @param string $date date issued.
	 */
	public function set_issue_date( $date ) {
		$this->set_date_prop( 'issue_date', $date );
	}

	/**
	 * Get the date due.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_due_date( $context = 'edit' ) {
		return $this->get_date_prop( 'due_date', $context, 'Y-m-d' );
	}

	/**
	 * Set the date due.
	 *
	 * @param string $date date due.
	 */
	public function set_due_date( $date ) {
		$this->set_date_prop( 'due_date', $date );
	}

	/**
	 * Get the date sent.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_sent_date( $context = 'edit' ) {
		return $this->get_date_prop( 'sent_date', $context, 'Y-m-d' );
	}

	/**
	 * Set the date sent.
	 *
	 * @param string $date date sent.
	 */
	public function set_sent_date( $date ) {
		$this->set_date_prop( 'sent_date', $date );
	}

	/**
	 * Get the date paid.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_payment_date( $context = 'edit' ) {
		return $this->get_date_prop( 'payment_date', $context, 'Y-m-d' );
	}

	/**
	 * Set the date paid.
	 *
	 * @param string $date date paid.
	 */
	public function set_payment_date( $date ) {
		$this->set_date_prop( 'payment_date', $date );
	}

	/**
	 * Get the created via.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return int
	 */
	public function get_created_via( $context = 'edit' ) {
		return $this->get_prop( 'created_via', $context );
	}

	/**
	 * Set the created via.
	 *
	 * @param int $created_via created via.
	 */
	public function set_created_via( $created_via ) {
		$this->set_prop( 'created_via', eac_clean( $created_via ) );
	}

	/**
	 * Get currency code.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 */
	public function get_currency_code( $context = 'edit' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/**
	 * Set currency code.
	 *
	 * @param string $currency_code Currency code.
	 *
	 * @since  1.1.0
	 */
	public function set_currency_code( $currency_code ) {
		$this->set_prop( 'currency_code', eac_clean( $currency_code ) );
	}

	/**
	 * Currency rate.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 */
	public function get_exchange_rate( $context = 'edit' ) {
		return $this->get_prop( 'exchange_rate', $context );
	}

	/**
	 * Set exchange rate.
	 *
	 * @param string $value Currency rate.
	 *
	 * @since 1.0.2
	 */
	public function set_exchange_rate( $value ) {
		$this->set_prop( 'exchange_rate', eac_format_decimal( $value, 8 ) );
	}

	/**
	 * Get associated parent payment id.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 */
	public function get_parent_id( $context = 'edit' ) {
		return $this->get_prop( 'parent_id', $context );
	}

	/**
	 * Set parent id.
	 *
	 * @param string $value Parent id.
	 *
	 * @since 1.0.2
	 */
	public function set_parent_id( $value ) {
		$this->set_prop( 'parent_id', absint( $value ) );
	}

	/**
	 * Get the unique_hash.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_uuid( $context = 'edit' ) {
		return $this->get_prop( 'uuid', $context );
	}

	/**
	 * Set the uuid.
	 *
	 * @param string $key uuid.
	 */
	public function set_uuid( $key ) {
		$this->set_prop( 'uuid', $key );
	}

	/**
	 * Get the date updated.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_date_updated( $context = 'edit' ) {
		return $this->get_prop( 'date_updated', $context );
	}

	/**
	 * Set the date updated.
	 *
	 * @param string $date date updated.
	 */
	public function set_date_updated( $date ) {
		$this->set_date_prop( 'date_updated', $date );
	}

	/**
	 * Get the date created.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_date_created( $context = 'edit' ) {
		return $this->get_date_prop( 'date_created', $context );
	}

	/**
	 * Set the date created.
	 *
	 * @param string $date date created.
	 */
	public function set_date_created( $date ) {
		$this->set_date_prop( 'date_created', $date );
	}

	/*
	|--------------------------------------------------------------------------
	| Line Items related methods
	|--------------------------------------------------------------------------
	| These methods are related to line items.
	*/
	/**
	 * Get items.
	 *
	 * @return DocumentItem[]
	 */
	public function get_items( $type = null ) {
		if ( is_null( $this->items ) ) {
			$this->items = array();

			if ( $this->exists() ) {
				$this->items = DocumentItem::query(
					array(
						'document_id' => $this->get_id(),
						'orderby'     => 'id',
						'order'       => 'ASC',
						'limit'       => - 1,
						'no_count'    => true,
					)
				);
			}
		}

		// Filter by type.
		if ( ! empty( $type ) && 'all' !== $type ) {
			return array_filter(
				$this->items,
				function ( $item ) use ( $type ) {
					if ( 'line_item' === $type ) {
						return 'standard' === $item->get_type();
					}

					return $item->get_type() === $type;
				}
			);
		}

		return $this->items;
	}

	/**
	 * Get item.
	 *
	 * @param int $item_id Line ID.
	 *
	 * @return DocumentItem|false False if not found, DocumentItem if found.
	 */
	public function get_item( $item_id ) {
		if ( ! empty( $item_id ) ) {
			foreach ( $this->get_items() as $item ) {
				if ( $item->get_id() === $item_id ) {
					return $item;
				}
			}
		}

		return false;
	}

	/**
	 * Set items.
	 *
	 * @param array $items Items.
	 *
	 * @return void
	 */
	public function set_items( $items ) {
		$old_items       = array_merge( $this->get_items(), $this->deletable );
		$this->items     = array();
		$this->deletable = array_filter(
			$old_items,
			function ( $item ) {
				return $item->exists();
			}
		);

		if ( ! is_array( $items ) ) {
			$items = wp_parse_id_list( $items );
		}

		foreach ( $items as $item ) {
			$this->add_item( $item );
		}

		// Go through deletable items and if they are in the new items list, remove them from the deletable list.
		foreach ( $this->deletable as $key => $item ) {
			foreach ( $this->items as $new_item ) {
				if ( $item->get_id() === $new_item->get_id() ) {
					unset( $this->deletable[ $key ] );
				}
			}
		}
	}

	/**
	 * Add item.
	 *
	 * Subtotal, discount is restricted to pass in item data.
	 *
	 * @param array $data Item.
	 *
	 * @return void
	 */
	public function add_item( $data ) {
		$default     = array(
			'id'          => 0, // line id.
			'item_id'     => 0, // item id not line id be careful.
			'type'        => 'item', // 'line_item', 'fee', 'shipping
			'name'        => '',
			'description' => '',
			'unit'        => '',
			'price'       => 0,
			'quantity'    => 1,
			'taxable'     => $this->is_calculating_tax(),
			'tax_ids'     => '',
		);

		if ( is_object( $data ) ) {
			$data = $data instanceof \stdClass ? get_object_vars( $data ) : $data->get_data();
		} elseif ( is_numeric( $data ) ) {
			$data = array( 'item_id' => $data );
		}

		// The data must be a line item with id or a new array with product_id and additional data.
		if ( ! isset( $data['id'] ) && ! isset( $data['item_id'] ) ) {
			return;
		}

		if ( ! empty( $data['item_id'] ) ) {
			$product       = eac_get_item( $data['item_id'] );
			$product_data  = $product ? $product->get_data() : array();
			$accepted_keys = array(
				'name',
				'type',
				'description',
				'unit',
				'price',
				'taxable',
				'tax_ids',
			);
			// if the currency is not the as the base currency, we need to convert the price.

			if ( eac_get_base_currency() !== $this->get_currency_code() ) {
				$price                 = eac_convert_money( $product_data['price'], eac_get_base_currency(), $this->get_currency_code() );
				$product_data['price'] = $price;
			}

			$product_data = wp_array_slice_assoc( $product_data, $accepted_keys );
			$data         = wp_parse_args( $data, $product_data );
		}

		$data                = wp_parse_args( $data, $default );
		$data['name']        = wp_strip_all_tags( $data['name'] );
		$data['description'] = wp_strip_all_tags( $data['description'] );
		$data['description'] = wp_trim_words( $data['description'], 20, '' );
		$data['unit']        = wp_strip_all_tags( $data['unit'] );
		$data['tax_ids']     = wp_parse_id_list( $data['tax_ids'] );

		$item = new DocumentItem( $data['id'] );
		$item->set_data( $data );
		$item->set_document_id( $this->get_id() );

		// if product id is not set then it is not product item.
		if ( empty( $item->get_item_id() ) || empty( $item->get_quantity() ) ) {
			return;
		}

		// Check if the item is set to be deleted and all the data matches. If so, remove it from the deletable list and add it to the items list.
		foreach ( $this->deletable as $key => $deletable_item ) {
			if ( $deletable_item->is_similar( $item ) ) {
				unset( $this->deletable[ $key ] );
				$deletable_item->set_data( $data );
				$this->items[] = $deletable_item;

				return;
			}
		}

		// Check if the item already exists in the items list and all the data matches. If so, update the quantity.
		foreach ( $this->get_items() as $key => $existing_item ) {
			if ( $existing_item->is_similar( $item ) ) {
				$existing_item->set_quantity( $existing_item->get_quantity() + $item->get_quantity() );

				return;
			}
		}


		$this->items[] = $item;
	}

	/**
	 * Delete items.
	 *
	 * @since 1.1.6
	 *
	 * return void
	 */
	public function delete_items() {
		foreach ( $this->get_items() as $item ) {
			$item->delete();
		}
	}

	/*
	|--------------------------------------------------------------------------
	|  Taxes related methods
	|--------------------------------------------------------------------------
	| These methods are related to line items taxes.
	*/
	/**
	 * Get merged taxes.
	 *
	 * @return DocumentItemTax[]
	 * @since 1.0.0
	 */
	public function get_taxes() {
		$taxes = array();
		foreach ( $this->get_items() as $item ) {
			foreach ( $item->get_taxes() as $tax ) {
				$index = md5( $tax->get_tax_id() . $tax->get_rate() );
				if ( ! isset( $taxes[ $index ] ) ) {
					$taxes[ $index ] = $tax;
				} else {
					$taxes[ $index ]->merge( $tax );
				}
			}
		}

		return $taxes;
	}

	/*
	|--------------------------------------------------------------------------
	|  Notes related methods
	|--------------------------------------------------------------------------
	| These methods are related to notes.
	*/
	/**
	 * Get notes.
	 *
	 * @param array $args Query arguments.
	 *
	 * @return Note[]
	 * @since 1.0.0
	 */
	public function get_notes( $args = array() ) {
		$args = array_merge(
			array(
				'document_id' => $this->get_id(),
				'limit'       => - 1,
			),
			$args
		);

		return eac_get_notes( $args );
	}

	/**
	 * Remove notes.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function delete_notes() {
		foreach ( $this->get_notes() as $note ) {
			$note->delete();
		}
	}

	/**
	 * Add note.
	 *
	 * @param array $data Note data.
	 *
	 * @return int| \WP_Error Note ID on success, WP_Error otherwise.
	 * @since 1.0.0
	 */
	public function add_note( $data ) {
		$data = wp_parse_args(
			$data,
			array(
				'object_id'    => $this->get_id(),
				'content'      => '',
				'creator_id'   => get_current_user_id(),
				'date_created' => current_time( 'mysql' ),
			)
		);

		if ( empty( $data['note'] ) ) {
			return new \WP_Error( 'missing_required', __( 'Note is required.', 'wp-ever-accounting' ) );
		}

		$note = new Note();
		$saved = $note->set_data($data)->save();
		if ( is_wp_error( $saved ) ) {
			return $saved;
		}

		return $note->get_id();
	}
	/*
	|--------------------------------------------------------------------------
	| Calculations
	|--------------------------------------------------------------------------
	| This section contains methods for calculating totals.
	*/

	/**
	 * Prepare object for database.
	 * This method is called before saving the object to the database.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function calculate_totals() {
		$this->calculate_item_prices();
		$this->calculate_item_subtotals();
		$this->calculate_item_discounts();
		$this->calculate_item_taxes();
		$this->calculate_item_totals();

		$this->set_items_total( $this->get_items_totals( 'standard', 'subtotal', true ) );
		$this->set_discount_total( $this->get_items_totals( 'standard', 'discount', true ) );
		$this->set_shipping_total( $this->get_items_totals( 'shipping', 'subtotal', true ) );
		$this->set_fees_total( $this->get_items_totals( 'fee', 'subtotal', true ) );
		$this->set_tax_total( $this->get_items_totals( 'all', 'tax_total', true ) );
		$this->set_total( $this->get_items_totals( 'all', 'total', true ) );
		$this->set_balance( $this->get_total() - $this->get_total_paid() );
	}


	/**
	 * Convert totals to selected currency.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function calculate_item_prices() {
		// if the currency is changed, we need to convert the totals.
		if ( ! array_key_exists( 'currency_code', $this->get_changes() ) ) {
			return;
		}
		foreach ( $this->get_items() as $item ) {
			$price = eac_convert_money( $item->get_price(), $this->data['currency_code'], $this->get_currency_code() );
			$item->set_price( $price );
		}
	}

	/**
	 * Calculate item subtotals.
	 *
	 * @return void
	 */
	protected function calculate_item_subtotals() {
		$items = $this->get_items();

		foreach ( $items as $item ) {
			$price        = $item->get_price();
			$qty          = $item->get_quantity();
			$subtotal     = $price * $qty;
			$subtotal_tax = array_sum( eac_calculate_taxes( $subtotal, $item->get_taxes(), $this->is_tax_inclusive() ) );
			// If the tax is inclusive, we need to subtract the tax amount from the line subtotal.
			if ( $this->is_tax_inclusive() ) {
				$subtotal -= $subtotal_tax;
			}

			$subtotal = max( 0, $subtotal );

			$item->set_subtotal( $subtotal );
			$item->set_subtotal_tax( $subtotal_tax );
		}
	}

	/**
	 * Calculate item discounts.
	 *
	 * @return void
	 */
	protected function calculate_item_discounts() {
		$items           = $this->get_items( 'standard' );
		$discount_amount = $this->get_discount_amount();
		$discount_type   = $this->get_discount_type();

		// sort the items array by price.

		// Reset item discounts.
		foreach ( $items as $item ) {
			$item->set_discount( 0 );
		}

		// First apply the discount to the items.
		if ( $discount_amount > 0 && ! empty( $discount_type ) ) {
			$this->apply_discount( $discount_amount, $items, $discount_type );
		}

		foreach ( $items as $item ) {
			$discount     = $item->get_discount();
			$discount_tax = array_sum( eac_calculate_taxes( $discount, $item->get_taxes(), $this->is_tax_inclusive() ) );
			if ( $this->is_tax_inclusive() ) {
				$discount -= $discount_tax;
			}
			$discount = max( 0, $discount );

			$item->set_discount( $discount );
			$item->set_discount_tax( $discount_tax );
		}
	}


	/**
	 * Calculate item taxes.
	 *
	 * @return void
	 */
	protected function calculate_item_taxes() {
		$items = $this->get_items();
		// Calculate item taxes.
		foreach ( $items as $item ) {
			$taxable_amount = $item->get_subtotal() - $item->get_discount();
			$taxable_amount = max( 0, $taxable_amount );
			$taxes          = eac_calculate_taxes( $taxable_amount, $item->get_taxes(), false );
			$line_tax       = 0;
			foreach ( $item->get_taxes() as $tax ) {
				$amount = isset( $taxes[ $tax->get_tax_id() ] ) ? $taxes[ $tax->get_tax_id() ] : 0;
				$tax->set_amount( $amount );
				$line_tax += $amount;
			}
			$item->set_tax_total( $line_tax );
		}

	}

	/**
	 * Calculate item totals.
	 *
	 * @return void
	 */
	protected function calculate_item_totals() {
		foreach ( $this->get_items() as $item ) {
			$total = $item->get_subtotal() + $item->get_tax_total() - $item->get_discount();
			$total = max( 0, $total );
			$item->set_total( $total );
		}
	}

	/**
	 * Apply discounts.
	 *
	 * @param float $amount Discount amount.
	 * @param array $items Items.
	 * @param string $type Discount type.
	 *
	 * @return float Total discount.
	 * @since 1.0.0
	 */
	public function apply_discount( $amount = 0, $items = array(), $type = 'fixed' ) {
		$total_discounted = 0;
		if ( 'fixed' === $type ) {
			$total_discounted = $this->apply_fixed_discount( $amount, $items );
		} elseif ( 'percent' === $type ) {
			$total_discounted = $this->apply_percentage_discount( $amount, $items );
		}

		return $total_discounted;
	}

	/**
	 * Apply fixed discount.
	 *
	 * @param float $amount Discount amount.
	 * @param DocumentItem[] $items Items.
	 *
	 * @return float Total discounted.
	 * @since 1.0.0
	 */
	public function apply_fixed_discount( $amount, $items ) {
		$total_discount = 0;
		$item_count     = 0;

		foreach ( $items as $item ) {
			$item_count += (float) $item->get_quantity();
		}

		if ( $amount <= 0 || empty( $items ) || $item_count <= 0 ) {
			return $total_discount;
		}

		$per_item_discount = $amount / $item_count;
		if ( $per_item_discount > 0 ) {
			foreach ( $items as $item ) {
				$discounted_price = $item->get_discounted_price();
				$discount         = $per_item_discount * (float) $item->get_quantity();
				$discount         = eac_round_number( $discount );
				$discount         = min( $discounted_price, $discount );
				$item->set_discount( $item->get_discount() + $discount );

				$total_discount += $discount;
			}

			$total_discount = round( $total_discount, 2 );
			$amount         = round( $amount, 2 );
			// If there is still discount remaining, repeat the process.
			if ( $total_discount > 0 && $total_discount < $amount ) {
				$total_discount += $this->apply_fixed_discount( $amount - $total_discount, $items );
			}
		} elseif ( $amount > 0 ) {
			$total_discount += $this->apply_discount_remainder( $amount, $items );
		}

		return $total_discount;
	}

	/**
	 * Apply percentage discount.
	 *
	 * @param float $amount Discount amount.
	 * @param DocumentItem[] $items Items.
	 */
	public function apply_percentage_discount( $amount, $items ) {
		$total_discount = 0;
		$document_total = 0;

		if ( $amount <= 0 || empty( $items ) ) {
			return $total_discount;
		}

		foreach ( $items as $item ) {
			$discounted_price = $item->get_discounted_price();
			// If the item is not created yet, we need to calculate the discounted price without tax.
			$discount = $discounted_price * ( $amount / 100 );
			$discount = min( $discounted_price, $discount );
			$item->set_discount( $item->get_discount() + $discount );
			$total_discount += $discount;
			$document_total += $discounted_price;
		}
		// Work out how much discount would have been given to the cart as a whole and compare to what was discounted on all line items.
		$document_discount = round( $document_total * ( $amount / 100 ), 2 );
		$total_discount    = round( $total_discount, 2 );

		if ( $total_discount < $document_discount && $amount > 0 ) {
			$total_discount += $this->apply_discount_remainder( $amount - $total_discount, $items );
		}

		return $total_discount;
	}

	/**
	 * Apply remainder discount.
	 *
	 * @param float $amount Discount amount.
	 * @param DocumentItem[] $items Items.
	 *
	 * @return float
	 * @since 1.0.0
	 */
	public function apply_discount_remainder( $amount, $items ) {
		$total_discount = 0;
		foreach ( $items as $item ) {
			$quantity = $item->get_quantity();
			for ( $i = 0; $i < $quantity; $i ++ ) {
				$discounted_price = $item->get_discounted_price();
				$discount         = min( $discounted_price, 1 );
				$item->set_discount( $item->get_discount() + $discount );
				$total_discount += $discount;
				if ( $total_discount >= $amount ) {
					break 2;
				}
			}
			if ( $total_discount >= $amount ) {
				break;
			}
		}

		return $total_discount;
	}


	/**
	 * Get totals.
	 *
	 * @param string $type Type of items.
	 * @param string $column Column name.
	 * @param bool $round Round the value or not.
	 *
	 * @since 1.0.0
	 */
	public function get_items_totals( $type, $column = 'total', $round = false ) {
		$items = $this->get_items( $type );
		$total = 0;
		foreach ( $items as $item ) {
			$caller = "get_{$column}";
			$amount = is_callable( array( $item, $caller ) ) ? $item->$caller() : 0;
			$total  += $round ? round( $amount, 2 ) : $amount;
		}
		return $round ? round( $total, 2 ) : $total;
	}

	/**
	 * Get merged taxes.
	 *
	 * @return DocumentItemTax[]
	 * @since 1.0.0
	 */
	public function get_merged_taxes() {
		$taxes = array();
		foreach ( $this->get_taxes() as $tax ) {
			$index = md5( $tax->get_tax_id() . $tax->get_rate() );
			if ( ! isset( $taxes[ $index ] ) ) {
				$taxes[ $index ] = $tax;
			} else {
				$taxes[ $index ]->merge( $tax );
			}
		}

		return $taxes;
	}

	/*
	|--------------------------------------------------------------------------
	| Conditionals methods
	|--------------------------------------------------------------------------
	| Methods that check an object's status, typically based on internal or meta data.
	*/

	/**
	 * Is price inclusive of tax.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function is_tax_inclusive() {
		return 'yes' === $this->get_tax_inclusive();
	}

	/**
	 * Is vat exempt.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function is_vat_exempt() {
		return (bool) $this->get_vat_exempt();
	}

	/**
	 * Is calculating tax.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function is_calculating_tax() {
		return 'yes' !== eac_tax_enabled() && ! $this->is_vat_exempt();
	}

	/**
	 * is editable.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function is_editable() {
		return $this->get_total_paid() <= 0;
	}

	/**
	 * Checks if the invoice has a given status.
	 *
	 * @param string $status Status to check.
	 *
	 * @return bool
	 * @since 1.1.0
	 */
	public function is_status( $status ) {
		return $this->get_status() === $status;
	}

	/**
	 * Returns if an order has been paid for based on the order status.
	 *
	 * @return bool
	 * @since 1.10
	 */
	public function is_paid() {
		return $this->is_status( 'paid' );
	}

	/**
	 * Checks if the invoice is draft.
	 *
	 * @return bool
	 * @since 1.1.0
	 */
	public function is_draft() {
		return $this->is_status( 'draft' );
	}

	/**
	 * Has due date.
	 *
	 * @return bool
	 * @since 1.1.0
	 */
	public function has_due_date() {
		return ! empty( $this->sanitize_date( $this->get_due_date() ) );
	}

	/**
	 * Checks if an order needs payment, based on status and order total.
	 *
	 * @return bool
	 */
	public function needs_payment() {
		return ! $this->is_status( 'paid' ) && $this->get_total() > 0;
	}

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/
	/**
	 * Set billing vat exempt.
	 *
	 * @param bool $vat_exempt Vat exempt.
	 *
	 * @return void
	 */
	public function set_billing_vat_exempt( $vat_exempt ) {
		$this->set_vat_exempt( $vat_exempt );
	}

	/**
	 * Get next doc number.
	 *
	 * @return string
	 * @since 1.1.6
	 */
	public function get_max_number() {
		global $wpdb;

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT MAX(REGEXP_REPLACE(number, '[^0-9]', '')) FROM {$wpdb->{$this->table_name}} WHERE type = %s",
				$this->get_type()
			)
		);
	}

	/**
	 * Get document number prefix.
	 *
	 * @return string
	 * @since 1.1.6
	 */
	public function get_number_prefix() {
		// First 3 letters of the document type.
		return strtoupper( substr( $this->get_type(), 0, 3 ) ) . '-';
	}

	/**
	 * Get formatted document number.
	 *
	 * @return string
	 * @since 1.1.6
	 */
	public function get_next_number() {
		$number = $this->get_max_number();
		$prefix = $this->get_number_prefix();
		$number = absint( $number ) + 1;

		return implode( '', [ $prefix, $number ] );
	}


	/**
	 * Get formatted subtotal.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_formatted_items_total() {
		return eac_format_money( $this->get_items_total(), $this->get_currency_code() );
	}

	/**
	 * Get formatted discount.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_formatted_discount_total() {
		return eac_format_money( $this->get_discount_total(), $this->get_currency_code() );
	}

	/**
	 * Get formatted shipping total.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_formatted_shipping_total() {
		return eac_format_money( $this->get_shipping_total(), $this->get_currency_code() );
	}

	/**
	 * Get formatted fee total.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_formatted_fees_total() {
		return eac_format_money( $this->get_fees_total(), $this->get_currency_code() );
	}

	/**
	 * Get formatted tax total.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_formatted_tax_total() {
		return eac_format_money( $this->get_tax_total(), $this->get_currency_code() );
	}

	/**
	 * Get formatted itemized list of taxes.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_formatted_itemized_taxes() {
		$taxes = $this->get_merged_taxes();
		$list  = array();
		foreach ( $taxes as $tax ) {
			if ( $tax->get_amount() > 0 ) {
				$list[ $tax->get_label() ] = $tax->get_formatted_total();
			}
		}

		return $list;
	}

	/**
	 * Get formatted total.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_formatted_total() {
		return eac_format_money( $this->get_total(), $this->get_currency_code() );
	}

	/**
	 * Get formatted total paid.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_formatted_total_paid() {
		return eac_format_money( $this->get_total_paid(), $this->get_currency_code() );
	}

	/**
	 * Get formatted balance.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_formatted_balance() {
		return eac_format_money( $this->get_balance(), $this->get_currency_code() );
	}

	/**
	 * Get formatted totals.
	 *
	 * @param bool $itemized_taxes Whether to return itemized taxes.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function get_formatted_totals( $itemized_taxes = false ) {
		$totals = array(
			'items_total' => $this->get_formatted_items_total(),
			'discount'    => $this->get_formatted_discount_total(),
			'shipping'    => $this->get_formatted_shipping_total(),
			'fees'        => $this->get_formatted_fees_total(),
			'taxes'       => $itemized_taxes ? $this->get_formatted_itemized_taxes() : $this->get_formatted_tax_total(),
			'total'       => $this->get_formatted_total(),
		);

		return $totals;
	}

	/**
	 * Get formatted billing address.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_formatted_billing_address() {
		$data = array(
			'name'      => $this->get_billing_name(),
			'company'   => $this->get_billing_company(),
			'address_1' => $this->get_billing_address_1(),
			'address_2' => $this->get_billing_address_2(),
			'city'      => $this->get_billing_city(),
			'state'     => $this->get_billing_state(),
			'postcode'  => $this->get_billing_postcode(),
			'country'   => $this->get_billing_country(),
		);

		return eac_get_formatted_address( $data );
	}

	/**
	 * Get formatted invoice name.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public function get_formatted_name() {
		// example: #INV-0001 (Paid) - John Doe
		$invoice_name = $this->get_number();
		if ( $this->is_paid() ) {
			$invoice_name .= ' (' . __( 'Paid', 'easy-appointments' ) . ')';
		}
		if ( ! empty( $this->get_billing_name() ) ) {
			$invoice_name .= ' - ' . $this->get_billing_name();
		}

		return $invoice_name;
	}
}


