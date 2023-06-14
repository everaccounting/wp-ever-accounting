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
	const TABLE_NAME = 'ea_documents';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'document';

	/**
	 * Cache group.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const CACHE_GROUP = 'ea_documents';

	/**
	 * Meta type declaration for the object.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const META_TYPE = 'ea_document';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'type'            => 'invoice',
		'status'          => 'draft',
		'number'          => '', // Document number, invoice number, bill number, estimate number, etc.
		'contact_id'      => null,
		'subtotal'        => 0.00,
		'discount_total'  => 0.00,
		'shipping_total'  => 0.00,
		'fees_total'      => 0.00,
		'tax_total'       => 0.00,
		'total'           => 0.00,
		'total_paid'      => 0.00,
		'total_refunded'  => 0.00,
		'discount_type'   => 'fixed',
		'discount_amount' => 0.00,
		'shipping_amount' => 0.00,
		'fees_amount'     => 0.00,
		'tax_inclusive'   => 'no',
		'vat_exempt'      => 'no',
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
		'shipping_data'   => array(
			'name'      => '',
			'company'   => '',
			'address_1' => '',
			'address_2' => '',
			'city'      => '',
			'state'     => '',
			'postcode'  => '',
			'country'   => '',
			'email'     => '',
			'phone'     => '',
		),
		'reference'       => '',
		'document_note'   => '',
		'issued_at'       => null,
		'due_at'          => null,
		'sent_at'         => null,
		'viewed_at'       => null,
		'paid_at'         => null,
		'created_via'     => '',
		'currency_code'   => '',
		'currency_rate'   => 1.00,
		'parent_id'       => null,
		'creator_id'      => null,
		'uuid'        => '',
		'updated_at'      => null,
		'created_at'      => null,
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
		$this->core_data['tax_inclusive'] = eac_price_includes_tax() ? 'yes' : 'no';
		$this->core_data['document_key']  = eac_generate_uuid();
		$this->core_data['currency_code'] = eac_get_base_currency();
		$this->core_data['creator_id']    = get_current_user_id();
		$this->core_data['created_at']    = wp_date( 'Y-m-d H:i:s' );
		parent::__construct( $data );
	}

	/*
	|--------------------------------------------------------------------------
	| Crud Getters and Setters
	|--------------------------------------------------------------------------
	| These methods are used to get and set the core data of the document.
	*/
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
	 * @since  1.1.0
	 *
	 * @return string
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
	 * @since  1.1.0
	 *
	 * @return string
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
	 * @since  1.1.0
	 *
	 * @return int
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
	 * Get subtotal.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
	 */
	public function get_subtotal( $context = 'edit' ) {
		return $this->get_prop( 'subtotal', $context );
	}

	/**
	 * Set subtotal.
	 *
	 * @param float $value Subtotal.
	 *
	 * @since  1.1.0
	 */
	public function set_subtotal( $value ) {
		$this->set_prop( 'subtotal', eac_sanitize_number( $value ) );
	}

	/**
	 * Get discount total.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
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
	 * @since 1.1.0
	 * @return float
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
	 * @since 1.1.0
	 * @return float
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
	 * @since 1.1.0
	 * @return float
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
	 * @since 1.1.0
	 * @return float
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
	 * @since 1.1.0
	 * @return float
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
	 * Get total refunded
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 * @return float
	 */
	public function get_total_refunded( $context = 'edit' ) {
		return $this->get_prop( 'total_refunded', $context );
	}

	/**
	 * Set total refunded
	 *
	 * @param float $total_refunded Total refunded.
	 *
	 * @since  1.1.0
	 */
	public function set_total_refunded( $total_refunded ) {
		$this->set_prop( 'total_refunded', eac_sanitize_number( $total_refunded, 4 ) );
	}

	/**
	 * Get discount amount.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
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
	 * @since  1.1.0
	 * @return string
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
	 * Get shipping cost.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_shipping_amount( $context = 'edit' ) {
		return (float) $this->get_prop( 'shipping_amount', $context );
	}

	/**
	 * Set shipping cost.
	 *
	 * @param string $value Discount cost.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping_amount( $value ) {
		$this->set_prop( 'shipping_amount', eac_sanitize_number( $value, 4 ) );
	}

	/**
	 * Get fees amount.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_fees_amount( $context = 'edit' ) {
		return (float) $this->get_prop( 'fees_amount', $context );
	}

	/**
	 * Set fees amount.
	 *
	 * @param string $value Discount amount.
	 *
	 * @since  1.1.0
	 */
	public function set_fees_amount( $value ) {
		$this->set_prop( 'fees_amount', eac_sanitize_number( $value, 4 ) );
	}

	/**
	 * Get tax inclusive or not.
	 *
	 * @param string $context View or edit context.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed|null
	 */
	public function get_tax_inclusive( $context = 'edit' ) {
		$enabled = $this->get_prop( 'tax_inclusive', $context );
		if ( empty( $enabled ) ) {
			$enabled = eac_price_includes_tax();
		}

		return $enabled;
	}

	/**
	 * Set tax inclusive or not.
	 *
	 * @param bool $value Tax inclusive or not.
	 *
	 * @since  1.1.0
	 */
	public function set_tax_inclusive( $value ) {
		if ( ! in_array( $value, array( 'yes', 'no' ), true ) ) {
			$value = 'no';
		}
		$this->set_prop( 'tax_inclusive', eac_clean( $value ) );
	}

	/**
	 * Get var exempt or not.
	 *
	 * @param string $context View or edit context.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed|null
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
		if ( ! in_array( $value, array( 'yes', 'no' ), true ) ) {
			$value = 'no';
		}
		$this->set_prop( 'vat_exempt', eac_clean( $value ) );
	}

	/**
	 * Get billing.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
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
	 * @since  1.1.0
	 *
	 * @return mixed
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
	 * @param mixed  $value Value of the prop.
	 *
	 * @since 1.1.0
	 */
	protected function set_billing_prop( $prop, $value ) {
		if ( array_key_exists( $prop, $this->data['billing_data'] ) ) {
			if ( true === $this->object_read ) {
				if ( $value !== $this->data['billing_data'][ $prop ] || ( isset( $this->changes['billing_data'] ) && array_key_exists( $prop, $this->changes['billing_data'] ) ) ) {
					$this->changes['billing_data'][ $prop ] = $value;
				}
			} else {
				$this->data['billing_data'][ $prop ] = $value;
			}
		}
	}


	/**
	 * Get billing name.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
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
	 * @since 1.1.0
	 *
	 * @return string
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
	 * @since  1.1.0
	 *
	 * @return string
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
	 * @since  1.1.0
	 *
	 * @return string
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
	 * @since 1.1.0
	 *
	 * @return string
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
	 * @since 1.1.0
	 *
	 * @return string
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
	 * @since 1.1.0
	 *
	 * @return string
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
	 * @since 1.1.0
	 *
	 * @return string
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
	 * @since 1.1.0
	 *
	 * @return string
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
	 * @since 1.1.0
	 *
	 * @return string
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
	 * @since  1.1.0
	 *
	 * @return string
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
	 * Get shipping_data.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_shipping_data( $context = 'edit' ) {
		return $this->get_prop( 'shipping_data', $context );
	}

	/**
	 * set the shipping_data.
	 *
	 * @param int $data .
	 *
	 * @since  1.1.0
	 */
	public function set_shipping_data( $data ) {
		$this->set_prop( 'shipping_data', maybe_unserialize( $data ) );
	}

	/**
	 * Gets a prop for a getter method.
	 *
	 * @param string $prop Name of prop to get.
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since  1.1.0
	 *
	 * @return mixed
	 */
	protected function get_shipping_prop( $prop, $context = 'view' ) {
		$value = null;

		if ( array_key_exists( $prop, $this->data['shipping_data'] ) ) {
			$value = isset( $this->changes['shipping_data'][ $prop ] ) ? $this->changes['shipping_data'][ $prop ] : $this->data['shipping_data'][ $prop ];

			if ( 'view' === $context ) {
				$value = apply_filters( $this->get_hook_prefix() . 'shipping_' . $prop, $value, $this );
			}
		}

		return $value;
	}

	/**
	 * Sets a prop for a setter method.
	 *
	 * @param string $prop Name of prop to set.
	 * @param mixed  $value Value of the prop.
	 *
	 * @since 1.1.0
	 */
	protected function set_shipping_prop( $prop, $value ) {
		if ( array_key_exists( $prop, $this->data['shipping_data'] ) ) {
			if ( true === $this->object_read ) {
				if ( $value !== $this->data['shipping_data'][ $prop ] || ( isset( $this->changes['shipping_data'] ) && array_key_exists( $prop, $this->changes['shipping_data'] ) ) ) {
					$this->changes['shipping_data'][ $prop ] = $value;
				}
			} else {
				$this->data['shipping'][ $prop ] = $value;
			}
		}
	}

	/**
	 * Get shipping name.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_shipping_name( $context = 'edit' ) {
		return $this->get_shipping_prop( 'name', $context );
	}

	/**
	 * Set shipping name.
	 *
	 * @param string $name Billing name.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping_name( $name ) {
		$this->set_shipping_prop( 'name', eac_clean( $name ) );
	}

	/**
	 * Get shipping company name.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_shipping_company( $context = 'edit' ) {
		return $this->get_shipping_prop( 'company', $context );
	}

	/**
	 * Set shipping company name.
	 *
	 * @param string $company Billing company name.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping_company( $company ) {
		$this->set_shipping_prop( 'company', eac_clean( $company ) );
	}

	/**
	 * Get shipping address_1 address.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_shipping_address_1( $context = 'edit' ) {
		return $this->get_shipping_prop( 'address_1', $context );
	}

	/**
	 * Set shipping address_1 address.
	 *
	 * @param string $address_1 Billing address_1 address.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping_address_1( $address_1 ) {
		$this->set_shipping_prop( 'address_1', eac_clean( $address_1 ) );
	}


	/**
	 * Get shipping address_2 address.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_shipping_address_2( $context = 'edit' ) {
		return $this->get_shipping_prop( 'address_2', $context );
	}

	/**
	 * Set shipping address_2 address.
	 *
	 * @param string $address_2 Billing address_2 address.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping_address_2( $address_2 ) {
		$this->set_shipping_prop( 'address_2', eac_clean( $address_2 ) );
	}

	/**
	 * Get shipping city address.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_shipping_city( $context = 'edit' ) {
		return $this->get_shipping_prop( 'city', $context );
	}

	/**
	 * Set shipping city address.
	 *
	 * @param string $city Billing city address.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping_city( $city ) {
		$this->set_shipping_prop( 'city', eac_clean( $city ) );
	}

	/**
	 * Get shipping state address.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_shipping_state( $context = 'edit' ) {
		return $this->get_shipping_prop( 'state', $context );
	}

	/**
	 * Set shipping state address.
	 *
	 * @param string $state Billing state address.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping_state( $state ) {
		$this->set_shipping_prop( 'state', eac_clean( $state ) );
	}

	/**
	 * Get shipping postcode code address.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_shipping_postcode( $context = 'edit' ) {
		return $this->get_shipping_prop( 'postcode', $context );
	}

	/**
	 * Set shipping postcode code address.
	 *
	 * @param string $postcode Billing postcode code address.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping_postcode( $postcode ) {
		$this->set_shipping_prop( 'postcode', eac_clean( $postcode ) );
	}

	/**
	 * Get shipping country address.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_shipping_country( $context = 'edit' ) {
		return $this->get_shipping_prop( 'country', $context );
	}

	/**
	 * Set shipping country address.
	 *
	 * @param string $country Billing country address.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping_country( $country ) {
		$this->set_shipping_prop( 'country', eac_clean( $country ) );
	}

	/**
	 * Get shipping phone number.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_shipping_phone( $context = 'edit' ) {
		return $this->get_shipping_prop( 'phone', $context );
	}

	/**
	 * Set shipping phone number.
	 *
	 * @param string $phone Billing phone number.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping_phone( $phone ) {
		$this->set_shipping_prop( 'phone', eac_clean( $phone ) );
	}

	/**
	 * Get shipping email address.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	public function get_shipping_email( $context = 'edit' ) {
		return $this->get_shipping_prop( 'email', $context );
	}

	/**
	 * Set shipping email address.
	 *
	 * @param string $email Billing email address.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping_email( $email ) {
		$this->set_shipping_prop( 'email', eac_clean( $email ) );
	}


	/**
	 * Get order number.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
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
	 * @since 1.1.0
	 *
	 * @return mixed|null
	 */
	public function get_document_note( $context = 'edit' ) {
		return $this->get_prop( 'document_note', $context );
	}

	/**
	 * Set document_note
	 *
	 * @param string $note Notes.
	 *
	 * @since  1.1.0
	 */
	public function set_document_note( $note ) {
		$this->set_prop( 'document_note', eac_clean( $note ) );
	}


	/**
	 * Get the date issued.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_issued_at( $context = 'edit' ) {
		return $this->get_date_prop( 'issued_at', $context, 'Y-m-d' );
	}

	/**
	 * Set the date issued.
	 *
	 * @param string $issued_at date issued.
	 */
	public function set_issued_at( $issued_at ) {
		$this->set_date_prop( 'issued_at', $issued_at );
	}

	/**
	 * Get the date due.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_due_at( $context = 'edit' ) {
		return $this->get_date_prop( 'due_at', $context, 'Y-m-d' );
	}

	/**
	 * Set the date due.
	 *
	 * @param string $due_at date due.
	 */
	public function set_due_at( $due_at ) {
		$this->set_date_prop( 'due_at', $due_at );
	}

	/**
	 * Get the date sent.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_sent_at( $context = 'edit' ) {
		return $this->get_date_prop( 'sent_at', $context, 'Y-m-d' );
	}

	/**
	 * Set the date sent.
	 *
	 * @param string $sent_at date sent.
	 */
	public function set_sent_at( $sent_at ) {
		$this->set_date_prop( 'sent_at', $sent_at );
	}

	/**
	 * Get the date viewed.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_viewed_at( $context = 'edit' ) {
		return $this->get_date_prop( 'viewed_at', $context, 'Y-m-d' );
	}

	/**
	 * Set the date viewed.
	 *
	 * @param string $viewed_at date viewed.
	 */
	public function set_viewed_at( $viewed_at ) {
		$this->set_date_prop( 'viewed_at', $viewed_at );
	}

	/**
	 * Get the date paid.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_paid_at( $context = 'edit' ) {
		return $this->get_date_prop( 'paid_at', $context, 'Y-m-d' );
	}

	/**
	 * Set the date paid.
	 *
	 * @param string $paid_at date paid.
	 */
	public function set_paid_at( $paid_at ) {
		$this->set_date_prop( 'paid_at', $paid_at );
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
	 * @since  1.1.0
	 *
	 * @return string
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
	 * @since 1.0.2
	 * @return mixed|null
	 */
	public function get_currency_rate( $context = 'edit' ) {
		return $this->get_prop( 'currency_rate', $context );
	}

	/**
	 * Set currency rate.
	 *
	 * @param string $value Currency rate.
	 *
	 * @since 1.0.2
	 */
	public function set_currency_rate( $value ) {
		$this->set_prop( 'currency_rate', eac_format_decimal( $value, 8 ) );
	}

	/**
	 * Get associated parent payment id.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return mixed|null
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
	public function get_updated_at( $context = 'edit' ) {
		return $this->get_prop( 'updated_at', $context );
	}

	/**
	 * Set the date updated.
	 *
	 * @param string $updated_at date updated.
	 */
	public function set_updated_at( $updated_at ) {
		$this->set_date_prop( 'updated_at', $updated_at );
	}

	/**
	 * Get the date created.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_created_at( $context = 'edit' ) {
		return $this->get_prop( 'created_at', $context );
	}

	/**
	 * Set the date created.
	 *
	 * @param string $created_at date created.
	 */
	public function set_created_at( $created_at ) {
		$this->set_date_prop( 'created_at', $created_at );
	}

	/*
	|--------------------------------------------------------------------------
	|  Meta properties
	|--------------------------------------------------------------------------
	| These are the meta properties for the document.
	*/

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
	public function get_items() {
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
		$tax_enabled = $this->is_calculating_tax() ? 'yes' : 'no';
		$default     = array(
			'id'               => 0, // line id.
			'product_id'       => 0, // item id not line id be careful.
			'name'             => '',
			'description'      => '',
			'unit'             => '',
			'price'            => 0,
			'quantity'         => 1,
			'taxable'          => $tax_enabled,
			'taxable_shipping' => $tax_enabled,
			'taxable_fee'      => $tax_enabled,
			'tax_ids'          => '',
		);

		if ( is_object( $data ) ) {
			$data = $data instanceof \stdClass ? get_object_vars( $data ) : $data->get_data();
		} elseif ( is_numeric( $data ) ) {
			$data = array( 'product_id' => $data );
		}

		// The data must be a line item with id or a new array with product_id and additional data.
		if ( ! isset( $data['id'] ) && ! isset( $data['product_id'] ) ) {
			return;
		}

		if ( ! empty( $data['product_id'] ) ) {
			$product       = eac_get_product( $data['product_id'] );
			$product_data  = $product ? $product->get_data() : array();
			$accepted_keys = array( 'name', 'description', 'unit', 'price', 'taxable', 'taxable_shipping', 'taxable_fee', 'tax_ids' );
			$product_data  = wp_array_slice_assoc( $product_data, $accepted_keys );
			$data          = wp_parse_args( $data, $product_data );
		}

		$data                = wp_parse_args( $data, $default );
		$data['name']        = wp_strip_all_tags( $data['name'] );
		$data['description'] = wp_strip_all_tags( $data['description'] );
		$data['description'] = wp_trim_words( $data['description'], 20, '' );
		$data['unit']        = wp_strip_all_tags( $data['unit'] );
		$data['tax_ids']     = wp_parse_id_list( $data['tax_ids'] );

		$item = new DocumentItem( $data['id'] );
		$item->set_props( $data );
		$item->set_document_id( $this->get_id() );

		// if product id is not set then it is not product item.
		if ( empty( $item->get_product_id() ) || empty( $item->get_quantity() ) ) {
			return;
		}

		// Check if the item is set to be deleted and all the data matches. If so, remove it from the deletable list and add it to the items list.
		foreach ( $this->deletable as $key => $deletable_item ) {
			if ( $deletable_item->is_similar( $item ) ) {
				unset( $this->deletable[ $key ] );
				$deletable_item->set_props( $data );
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

	/*
	|--------------------------------------------------------------------------
	|  Taxes related methods
	|--------------------------------------------------------------------------
	| These methods are related to line items taxes.
	*/
	/**
	 * Get merged taxes.
	 *
	 * @since 1.0.0
	 * @return DocumentLineTax[]
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
	| Calculations
	|--------------------------------------------------------------------------
	| This section contains methods for calculating totals.
	*/
	/**
	 * Prepare object for database.
	 * This method is called before saving the object to the database.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function calculate_totals() {
		$this->calculate_item_subtotals();
		$this->calculate_item_discounts();
		$this->calculate_item_shipping();
		$this->calculate_item_fees();
		$this->calculate_item_taxes();
		$this->calculate_item_totals();

		$this->set_subtotal( $this->get_items_totals( 'subtotal', true ) );
		$this->set_discount_total( $this->get_items_totals( 'discount', true ) );
		$this->set_shipping_total( $this->get_items_totals( 'shipping', true ) );
		$this->set_fees_total( $this->get_items_totals( 'fee', true ) );
		$this->set_tax_total( $this->get_items_totals( 'tax', true ) );
		$this->set_total( $this->get_items_totals( 'total', true ) );
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
		$items           = $this->get_items();
		$discount_amount = $this->get_discount_amount();
		$discount_type   = $this->get_discount_type();

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
	 * Calculate item shipping.
	 *
	 * @return void
	 */
	protected function calculate_item_shipping() {
		$items           = $this->get_items();
		$shipping_amount = $this->get_shipping_amount();

		// Reset item shipping.
		foreach ( $items as $item ) {
			$item->set_shipping( 0 );
		}

		if ( $shipping_amount > 0 ) {
			$this->apply_shipping_cost( $shipping_amount, $items );
		}

		foreach ( $items as $item ) {
			$shipping_tax = 0;
			if ( $item->get_shipping() > 0 && $item->is_taxable_shipping() ) {
				$shipping_tax = array_sum( eac_calculate_taxes( $item->get_shipping(), $item->get_taxes(), false ) );
			}

			$item->set_shipping_tax( $shipping_tax );
		}
	}

	/**
	 * Calculate item fees.
	 *
	 * @return void
	 */
	protected function calculate_item_fees() {
		$items       = $this->get_items();
		$fees_amount = $this->get_fees_amount();

		// Reset item fees.
		foreach ( $items as $item ) {
			$item->set_fee( 0 );
		}

		if ( $fees_amount > 0 ) {
			$this->apply_fees_amount( $fees_amount, $items );
		}

		foreach ( $items as $item ) {
			$fees_tax = 0;
			if ( $item->get_fee() > 0 && $item->is_taxable_fee() ) {
				$fees_tax = array_sum( eac_calculate_taxes( $item->get_fee(), $item->get_taxes(), false ) );
			}

			$item->set_fee_tax( $fees_tax );
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
			$taxable_amount = $item->get_subtotal() + $item->get_shipping() + $item->get_fee() - $item->get_discount();
			$taxable_amount = max( 0, $taxable_amount );
			$taxes          = eac_calculate_taxes( $taxable_amount, $item->get_taxes(), false );
			$line_tax       = 0;
			foreach ( $item->get_taxes() as $tax ) {
				$amount = isset( $taxes[ $tax->get_tax_id() ] ) ? $taxes[ $tax->get_tax_id() ] : 0;
				$tax->set_amount( $amount );
				$line_tax += $amount;
			}
			$item->set_tax( $line_tax );
		}

	}

	/**
	 * Calculate item totals.
	 *
	 * @return void
	 */
	protected function calculate_item_totals() {
		foreach ( $this->get_items() as $item ) {
			$total = $item->get_subtotal() + $item->get_tax() + $item->get_shipping() + $item->get_fee() - $item->get_discount();
			$total = max( 0, $total );
			$item->set_total( $total );
		}
	}

	/**
	 * Convert totals to selected currency.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function convert_totals() {
	}


	/**
	 * Apply discounts.
	 *
	 * @param float  $amount Discount amount.
	 * @param array  $items Items.
	 * @param string $type Discount type.
	 *
	 * @since 1.0.0
	 * @return float Total discount.
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
	 * @param float          $amount Discount amount.
	 * @param DocumentItem[] $items Items.
	 *
	 * @since 1.0.0
	 * @return float Total discounted.
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
	 * @param float          $amount Discount amount.
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
	 * @param float          $amount Discount amount.
	 * @param DocumentItem[] $items Items.
	 *
	 * @since 1.0.0
	 * @return float
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
	 * Apply shipping.
	 *
	 * @param float $amount Shipping amount.
	 * @param array $items Items.
	 *
	 * @since 1.0.0
	 * @return float Total shipping.
	 */
	public function apply_shipping_cost( $amount = 0, $items = array() ) {
		$total_shipping = 0;
		$item_count     = 0;

		foreach ( $this->get_items() as $item ) {
			$item_count += (float) $item->get_quantity();
		}

		if ( $amount <= 0 || empty( $items ) || $item_count <= 0 ) {
			return $total_shipping;
		}

		$per_item_shipping = $amount / $item_count;
		if ( $per_item_shipping > 0 ) {
			foreach ( $items as $item ) {
				$shipping = $per_item_shipping * (float) $item->get_quantity();
				$item->set_shipping( $item->get_shipping() + $shipping );

				$total_shipping += $shipping;
			}

			// If there is still shipping remaining, repeat the process.
			if ( $total_shipping > 0 && $total_shipping < $amount ) {
				$total_shipping += $this->apply_shipping_cost( $amount - $total_shipping, $items );
			}
		} elseif ( $amount > 0 ) {
			foreach ( $items as $item ) {
				$quantity = $item->get_quantity();
				for ( $i = 0; $i < $quantity; $i ++ ) {
					$shipping = min( $amount, 1 );
					$item->set_shipping( $item->get_shipping() + $shipping );
					$total_shipping += $shipping;
					if ( $total_shipping >= $amount ) {
						break 2;
					}
				}
			}
		}

		return $total_shipping;
	}

	/**
	 * Apply fees amounts.
	 *
	 * @param float $amount Shipping amount.
	 * @param array $items Items.
	 *
	 * @since 1.0.0
	 * @return float Total fee amount.
	 */
	public function apply_fees_amount( $amount = 0, $items = array() ) {
		$total_fees = 0;
		$item_count = 0;

		foreach ( $items as $item ) {
			$item_count += (float) $item->get_quantity();
		}

		if ( $amount <= 0 || empty( $items ) || $item_count <= 0 ) {
			return $total_fees;
		}

		$per_item_fee = $amount / $item_count;
		if ( $per_item_fee > 0 ) {
			foreach ( $items as $item ) {
				$fee = $per_item_fee * (float) $item->get_quantity();
				$item->set_fee( $item->get_fee() + $fee );

				$total_fees += $fee;
			}

			// If there is still fees remaining, repeat the process.
			if ( $total_fees > 0 && $total_fees < $amount ) {
				$total_fees += $this->apply_fees_amount( $amount - $total_fees, $items );
			}
		} elseif ( $amount > 0 ) {
			foreach ( $items as $item ) {
				$quantity = $item->get_quantity();
				for ( $i = 0; $i < $quantity; $i ++ ) {
					$fee = min( $amount, 1 );
					$item->set_fee( $item->get_fee() + $fee );
					$total_fees += $fee;
					if ( $total_fees >= $amount ) {
						break 2;
					}
				}
			}
		}

		return $total_fees;
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
	 * @throws \Exception When the invoice is already paid.
	 * @since 1.0.0
	 * @return true|\WP_Error True on success, WP_Error on failure.
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
		if ( empty( $this->get_created_at() ) ) {
			$this->set_created_at( current_time( 'mysql' ) );
		}

		// If It's update, set the updated date.
		if ( $this->exists() ) {
			$this->set_updated_at( current_time( 'mysql' ) );
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
	 * Returns all data for this object.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_data( $context = 'edit' ) {
		$props          = $this->get_props( $context );
		$items          = $this->get_items();
		$props['items'] = array();
		foreach ( $items as $item ) {
			$props['items'][] = $item->get_data( $context );
		}
		if ( static::META_TYPE ) {
			$props['metadata'] = $this->get_metadata();
		}

		return $props;
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
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_tax_inclusive() {
		return 'yes' === $this->get_tax_inclusive();
	}

	/**
	 * Is vat exempt.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_vat_exempt() {
		return 'yes' === $this->get_vat_exempt();
	}

	/**
	 * Is calculating tax.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_calculating_tax() {
		return 'yes' !== eac_tax_enabled() || ! $this->is_vat_exempt();
	}

	/**
	 * is editable.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_editable() {
		return $this->get_total_paid() <= 0;
	}

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/
	/**
	 * Get totals.
	 *
	 * @param string $column Column name.
	 * @param bool   $round Round the value or not.
	 *
	 * @since 1.0.0
	 */
	public function get_items_totals( $column = 'total', $round = false ) {
		$items = $this->get_items();
		$total = 0;
		foreach ( $items as $item ) {
			$caller = "get_{$column}";
			$amount = is_callable( array( $item, $caller ) ) ? $item->$caller() : 0;
			$total  += $round ? round( $amount, 2 ) : $amount;
		}

		return $round ? round( $total, 2 ) : $total;
	}

	/**
	 * Get next doc number.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public function get_max_number() {
		global $wpdb;

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT MAX(REGEXP_REPLACE(number, '[^0-9]', '')) FROM {$this->table} WHERE type = %s",
				$this->get_type()
			)
		);
	}

	/**
	 * Get document number prefix.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public function get_number_prefix() {
		// First 3 letters of the document type.
		return strtoupper( substr( $this->get_type(), 0, 3 ) ) . '-';
	}

	/**
	 * Get formatted document number.
	 *
	 * @since 1.1.6
	 * @return string
	 */
	public function get_next_number() {
		$number = $this->get_max_number();
		$prefix = $this->get_number_prefix();
		$number = absint( $number ) + 1;

		return implode( '', [ $prefix, $number ] );
	}

	/**
	 * Get merged taxes.
	 *
	 * @since 1.0.0
	 * @return DocumentLineTax[]
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

	/**
	 * Get formatted subtotal.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_formatted_subtotal() {
		return eac_format_money( $this->get_subtotal(), $this->get_currency_code() );
	}

	/**
	 * Get formatted discount.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_formatted_discount_total() {
		return eac_format_money( $this->get_discount_total(), $this->get_currency_code() );
	}

	/**
	 * Get formatted shipping total.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_formatted_shipping_total() {
		return eac_format_money( $this->get_shipping_total(), $this->get_currency_code() );
	}

	/**
	 * Get formatted fee total.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_formatted_fees_total() {
		return eac_format_money( $this->get_fees_total(), $this->get_currency_code() );
	}

	/**
	 * Get formatted tax total.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_formatted_tax_total() {
		return eac_format_money( $this->get_tax_total(), $this->get_currency_code() );
	}

	/**
	 * Get formatted itemized list of taxes.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_formatted_itemized_taxes() {
		$taxes = $this->get_merged_taxes();
		$list  = array();
		foreach ( $taxes as $tax ) {
			if ( $tax->get_total() > 0 ) {
				$list[ $tax->get_label() ] = $tax->get_formatted_total();
			}
		}

		return $list;
	}

	/**
	 * Get formatted total.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_formatted_total() {
		return eac_format_money( $this->get_total(), $this->get_currency_code() );
	}

	/**
	 * Get formatted totals.
	 *
	 * @param bool $itemized_taxes Whether to return itemized taxes.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_formatted_totals( $itemized_taxes = false ) {
		$totals = array(
			'subtotal' => $this->get_formatted_subtotal(),
			'discount' => $this->get_formatted_discount_total(),
			'shipping' => $this->get_formatted_shipping_total(),
			'fees'     => $this->get_formatted_fees_total(),
			'taxes'    => $itemized_taxes ? $this->get_formatted_itemized_taxes() : $this->get_formatted_tax_total(),
			'total'    => $this->get_formatted_total(),
		);

		return $totals;
	}

	/**
	 * Get formatted billing address.
	 *
	 * @since 1.0.0
	 * @return string
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
}


