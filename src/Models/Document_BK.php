<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Document.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
abstract class Document_BK extends Model {
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
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'type'            => '',
		'document_number' => '',
		'order_number'    => '',
		'status'          => 'draft',
		'contact_id'      => null,
		'discount_type'   => 'percentage',
		'discount_rate'   => 0.00,
		'subtotal'        => 0.00,
		'subtotal_tax'    => 0.00,
		'discount_total'  => 0.00,
		'discount_tax'    => 0.00,
		'shipping_total'  => 0.00,
		'shipping_tax'    => 0.00,
		'fees_total'      => 0.00,
		'fees_tax'        => 0.00,
		'total'           => 0.00,
		'total_tax'       => 0.00,
		'total_paid'      => 0.00,
		'total_refunded'  => 0.00,
		'tax_inclusive'   => 'yes',
		'vat_exempt'      => 'no',
		'billing'         => array(
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
		'shipping'        => array(
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
		'notes'           => '',
		'footer'          => '',
		'issued_at'       => null,
		'due_at'          => null,
		'sent_at'         => null,
		'viewed_at'       => null,
		'paid_at'         => null,
		'currency'        => '',
		'parent_id'       => null,
		'unique_hash'     => '',
		'created_via'     => '',
		'agent_id'        => null,
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
	protected $items = array();

	/**
	 * document taxes that need updating are stored here.
	 *
	 * @since 1.1.0
	 *
	 * @var DocumentTax[]
	 */
	protected $taxes = array();

	/**
	 * document items that need deleting are stored here.
	 *
	 * @since 1.1.0
	 *
	 * @var DocumentItem[]
	 */
	protected $deletables = array();


	/*
	|--------------------------------------------------------------------------
	| Getters and Setters
	|--------------------------------------------------------------------------
	|
	| Methods for getting and setting data.
	|
	*/

	/**
	 * Get documents number.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_document_number( $context = 'edit' ) {
		return $this->get_prop( 'document_number', $context );
	}

	/**
	 * set documents number.
	 *
	 * @param string $document_number Document number.
	 *
	 * @since  1.1.0
	 */
	public function set_document_number( $document_number ) {
		$this->set_prop( 'document_number', eac_clean( $document_number ) );
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
	 * Get order number.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_order_number( $context = 'edit' ) {
		return $this->get_prop( 'order_number', $context );
	}

	/**
	 * Set order number.
	 *
	 * @param string $order_number Order number.
	 *
	 * @since  1.1.0
	 */
	public function set_order_number( $order_number ) {
		$this->set_prop( 'order_number', eac_clean( $order_number ) );
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
	 * Get billing.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_billing( $context = 'edit' ) {
		return $this->get_prop( 'billing', $context );
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
		return $this->get_prop( 'discount_type', $context );
	}

	/**
	 * Set discount type.
	 *
	 * @param string $discount_type Discount type.
	 *
	 * @since  1.1.0
	 */
	public function set_discount_type( $discount_type ) {
		$this->set_prop( 'discount_type', eac_clean( $discount_type ) );
	}

	/**
	 * Get discount rate.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_discount_rate( $context = 'edit' ) {
		return $this->get_prop( 'discount_rate', $context );
	}

	/**
	 * Set discount rate.
	 *
	 * @param string $discount_rate Discount rate.
	 *
	 * @since  1.1.0
	 */
	public function set_discount_rate( $discount_rate ) {
		$this->set_prop( 'discount_rate', eac_sanitize_number( $discount_rate ) );
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
	 * @param float $subtotal Subtotal.
	 *
	 * @since  1.1.0
	 */
	public function set_subtotal( $subtotal ) {
		$this->set_prop( 'subtotal', eac_sanitize_number( $subtotal ) );
	}

	/**
	 * Get subtotal tax
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
	 */
	public function get_subtotal_tax( $context = 'edit' ) {
		return $this->get_prop( 'subtotal_tax', $context );
	}

	/**
	 * Set subtotal tax
	 *
	 * @param float $subtotal_tax Subtotal tax.
	 *
	 * @since  1.1.0
	 */
	public function set_subtotal_tax( $subtotal_tax ) {
		$this->set_prop( 'subtotal_tax', eac_sanitize_number( $subtotal_tax ) );
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
	 * @param float $discount_total Discount total.
	 *
	 * @since  1.1.0
	 */
	public function set_discount_total( $discount_total ) {
		$this->set_prop( 'discount_total', eac_sanitize_number( $discount_total ) );
	}

	/**
	 * Get discount tax
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return float
	 */
	public function get_discount_tax( $context = 'edit' ) {
		return $this->get_prop( 'discount_tax', $context );
	}

	/**
	 * Set discount tax
	 *
	 * @param float $discount_tax Discount tax.
	 *
	 * @since  1.1.0
	 */
	public function set_discount_tax( $discount_tax ) {
		$this->set_prop( 'discount_tax', eac_sanitize_number( $discount_tax ) );
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
	 * @param float $shipping_total Shipping total.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping_total( $shipping_total ) {
		$this->set_prop( 'shipping_total', eac_sanitize_number( $shipping_total ) );
	}

	/**
	 * Get shipping tax
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 * @return float
	 */
	public function get_shipping_tax( $context = 'edit' ) {
		return $this->get_prop( 'shipping_tax', $context );
	}

	/**
	 * Set shipping tax
	 *
	 * @param float $shipping_tax Shipping tax.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping_tax( $shipping_tax ) {
		$this->set_prop( 'shipping_tax', eac_sanitize_number( $shipping_tax ) );
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
	 * @param float $fees_total Shipping total.
	 *
	 * @since  1.1.0
	 */
	public function set_fees_total( $fees_total ) {
		$this->set_prop( 'fees_total', eac_sanitize_number( $fees_total ) );
	}

	/**
	 * Get fees tax
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 * @return float
	 */
	public function get_fees_tax( $context = 'edit' ) {
		return $this->get_prop( 'fees_tax', $context );
	}

	/**
	 * Set fees tax
	 *
	 * @param float $fees_tax Shipping tax.
	 *
	 * @since  1.1.0
	 */
	public function set_fees_tax( $fees_tax ) {
		$this->set_prop( 'fees_tax', eac_sanitize_number( $fees_tax ) );
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
	 * @param float $total Total.
	 *
	 * @since  1.1.0
	 */
	public function set_total( $total ) {
		$this->set_prop( 'total', eac_sanitize_number( $total ) );
	}

	/**
	 * Get total tax
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.1.0
	 * @return float
	 */
	public function get_total_tax( $context = 'edit' ) {
		return $this->get_prop( 'total_tax', $context );
	}

	/**
	 * Set total tax
	 *
	 * @param float $total_tax Total tax.
	 *
	 * @since  1.1.0
	 */
	public function set_total_tax( $total_tax ) {
		$this->set_prop( 'total_tax', eac_sanitize_number( $total_tax ) );
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
		$this->set_prop( 'total_paid', eac_sanitize_number( $total_paid ) );
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
		$this->set_prop( 'total_refunded', eac_sanitize_number( $total_refunded ) );
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
	 * set the billing.
	 *
	 * @param int $billing .
	 *
	 * @since  1.1.0
	 */
	public function set_billing( $billing ) {
		$this->set_prop( 'billing', maybe_unserialize( $billing ) );
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

		if ( array_key_exists( $prop, $this->data['billing'] ) ) {
			$value = isset( $this->changes['billing'][ $prop ] ) ? $this->changes['billing'][ $prop ] : $this->data['billing'][ $prop ];

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
		if ( array_key_exists( $prop, $this->data['billing'] ) ) {
			if ( true === $this->object_read ) {
				if ( $value !== $this->data['billing'][ $prop ] || ( isset( $this->changes['billing'] ) && array_key_exists( $prop, $this->changes['billing'] ) ) ) {
					$this->changes['billing'][ $prop ] = $value;
				}
			} else {
				$this->data['billing'][ $prop ] = $value;
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
	 * Get billing tax number.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
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
	 * Get shipping.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_shipping( $context = 'edit' ) {
		return $this->get_prop( 'shipping', $context );
	}

	/**
	 * set the shipping.
	 *
	 * @param int $shipping .
	 *
	 * @since  1.1.0
	 */
	public function set_shipping( $shipping ) {
		$this->set_prop( 'shipping', maybe_unserialize( $shipping ) );
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

		if ( array_key_exists( $prop, $this->data['shipping'] ) ) {
			$value = isset( $this->changes['shipping'][ $prop ] ) ? $this->changes['shipping'][ $prop ] : $this->data['shipping'][ $prop ];

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
		if ( array_key_exists( $prop, $this->data['shipping'] ) ) {
			if ( true === $this->object_read ) {
				if ( $value !== $this->data['shipping'][ $prop ] || ( isset( $this->changes['shipping'] ) && array_key_exists( $prop, $this->changes['shipping'] ) ) ) {
					$this->changes['shipping'][ $prop ] = $value;
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
	 * Get notes
	 *
	 * @param string $context View or edit context.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed|null
	 */
	public function get_notes( $context = 'edit' ) {
		return $this->get_prop( 'notes', $context );
	}

	/**
	 * Set notes
	 *
	 * @param string $notes Notes.
	 *
	 * @since  1.1.0
	 */
	public function set_notes( $notes ) {
		$this->set_prop( 'notes', eac_clean( $notes ) );
	}

	/**
	 * Get footer notes.
	 *
	 * @param string $context View or edit context.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed|null
	 */
	public function get_footer( $context = 'edit' ) {
		return $this->get_prop( 'footer', $context );
	}

	/**
	 * Set footer notes.
	 *
	 * @param string $footer Footer notes.
	 *
	 * @since  1.1.0
	 */
	public function set_footer( $footer ) {
		$this->set_prop( 'footer', eac_clean( $footer ) );
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
		return $this->get_prop( 'sent_at', $context );
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
		return $this->get_prop( 'viewed_at', $context, 'Y-m-d' );
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
	 * Get currency code.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_currency( $context = 'edit' ) {
		return $this->get_prop( 'currency', $context );
	}

	/**
	 * Set currency code.
	 *
	 * @param string $currency_code Currency code.
	 *
	 * @since  1.1.0
	 */
	public function set_currency( $currency_code ) {
		$this->set_prop( 'currency', eac_clean( $currency_code ) );
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
	public function get_unique_hash( $context = 'edit' ) {
		return $this->get_prop( 'unique_hash', $context );
	}

	/**
	 * Set the unique_hash.
	 *
	 * @param string $unique_hash unique_hash.
	 */
	public function set_unique_hash( $unique_hash ) {
		$this->set_prop( 'unique_hash', $unique_hash );
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
	 * Get the agent id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return int
	 */
	public function get_agent_id( $context = 'edit' ) {
		return $this->get_prop( 'agent_id', $context );
	}

	/**
	 * Set the agent id.
	 *
	 * @param int $agent_id agent id.
	 */
	public function set_agent_id( $agent_id ) {
		$this->set_prop( 'agent_id', absint( $agent_id ) );
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
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete discounts from the database.
	|
	*/

	/**
	 * Get items.
	 *
	 * @param int $line_id Line id.
	 *
	 * @return DocumentItem[]
	 */
	public function get_items( $line_id = null ) {
		if ( $this->exists() && empty( $this->items ) ) {
			$this->items = DocumentItem::query(
				array(
					'document_id' => $this->get_id(),
					'orderby'     => 'id',
					'order'       => 'ASC',
					'limit'       => - 1,
				)
			);
		}

		if ( ! empty( $line_id ) ) {
			$items = array();
			foreach ( $this->items as $key => $item ) {
				if ( $item->get_id() === $line_id ) {
					$items[ $key ] = $item;
				}
			}

			return $items;
		}

		return $this->items;
	}

	/**
	 * Get taxes.
	 *
	 * @param int $item_id Item id.
	 *
	 * @since 1.0.0
	 * @return DocumentTax[]
	 */
	public function get_taxes( $item_id = null ) {
		if ( $this->exists() && empty( $this->taxes ) ) {
			$this->taxes = DocumentTax::query(
				array(
					'document_id' => $this->get_id(),
					'orderby'     => 'id',
					'order'       => 'ASC',
					'limit'       => - 1,
				)
			);
		}

		if ( ! empty( $item_id ) ) {
			$taxes = array();
			foreach ( $this->taxes as $key => $tax ) {
				if ( $tax->get_item_id() === $item_id ) {
					$taxes[ $key ] = $tax;
				}
			}

			return $taxes;
		}

		return $this->taxes;
	}

	/**
	 * Get payments.
	 *
	 * @since 1.0.0
	 * @return Income[]
	 */
	public function get_payments() {
		// Get payments only if the amount is positive.
		$payments = array();
		if ( $this->exists() ) {
			$payments = Income::query(
				array(
					'document_id' => $this->get_id(),
					'orderby'     => 'id',
					'order'       => 'ASC',
					'limit'       => - 1,
					'where'       => array(
						array(
							'key'     => 'amount',
							'compare' => '>',
							'value'   => 0,
						),
					),
				)
			);
		}

		return $payments;
	}

	/**
	 * Get refund payments.
	 *
	 * @since 1.0.0
	 * @return  Income[] $payments Payments.
	 */
	public function get_refunds() {
		$refunds = array();
		if ( $this->exists() ) {
			$refunds = Income::query(
				array(
					'document_id' => $this->get_id(),
					'orderby'     => 'id',
					'order'       => 'ASC',
					'limit'       => - 1,
					'where'       => array(
						array(
							'key'     => 'amount',
							'compare' => '<',
							'value'   => 0,
						),
					),
				)
			);
		}

		return $refunds;
	}

	/**
	 * Add Payment.
	 *
	 * @param array|object $payment_data Payment.
	 *
	 * @return void
	 */
	public function add_payment( $payment_data ) {

	}

	/**
	 * Add Refund.
	 *
	 * @param array|object $refund_data Refund.
	 *
	 * @return void
	 */
	public function add_refund( $refund_data ) {

	}

	/**
	 * Set items.
	 *
	 * @param array $items Items.
	 *
	 * @return void
	 */
	public function set_items( $items ) {
		// Record old items then loop through new items if any of item is not in new items then delete it.
		$old_items = $this->get_items();

		if ( ! is_array( $items ) ) {
			$items = wp_parse_id_list( $items );
		}

		foreach ( $items as $item ) {
			$this->add_item( $item );
		}

		foreach ( $old_items as $old_item ) {
			$found = false;
			foreach ( $items as $item ) {
				if ( $old_item['id'] === $item['id'] ) {
					$found = true;
					break;
				}
			}
			if ( ! $found ) {
				$this->deletables[] = $old_item;
			}
		}
	}

	/**
	 * Add item.
	 *
	 * @param array|object $item_data Item.
	 *
	 * @return bool
	 */
	public function add_item( $item_data ) {
		$this->get_items();
		if ( is_numeric( $item_data ) ) {
			$item_data['id'] = $item_data;
		}
		// check if the item is valid.
		if ( ! is_array( $item_data ) || empty( $item_data['id'] ) ) {
			return false;
		}

		$default_item_data = array(
			'id'          => 0, // item id.
			'name'        => '',
			'description' => '',
			'unit'        => '',
			'price'       => 0,
			'quantity'    => 1,
			'discount'    => 0,
			'shipping'    => 0,
			'fee'         => 0,
			'taxes'       => array(),
		);
		$item              = eac_get_item( $item_data['id'] );
		if ( $item ) {
			$data              = wp_array_slice_assoc( $item->get_data(), array_keys( $default_item_data ) );
			$default_item_data = wp_parse_args( $data, $default_item_data );
		}
		$item_data = wp_parse_args( $item_data, $default_item_data );

		// create new item.
		$line_item = new DocumentItem();
		$line_item->set_document_id( $this->get_id() );
		$line_item->set_item_id( $item_data['id'] );
		$line_item->set_name( $item_data['name'] );
		$line_item->set_description( $item_data['description'] );
		$line_item->set_unit_type( $item_data['unit'] );
		$line_item->set_price( $item_data['price'] );
		$line_item->set_quantity( $item_data['quantity'] );
		$line_item->set_discount( $item_data['discount'] );
		$line_item->set_shipping( $item_data['shipping'] );
		$line_item->set_fee( $item_data['fee'] );
		$this->items[] = $line_item;

		// add taxes.
		// Sanitize taxes.
		if ( ! is_array( $item_data['taxes'] ) ) {
			$item_data['taxes'] = wp_parse_id_list( $item_data['taxes'] );
		}

		foreach ( $item_data['taxes'] as $tax_data ) {
			$default_tax_data = array(
				'id'       => 0,
				'name'     => '',
				'rate'     => 0,
				'compound' => false,
				'amount'   => 0,
			);
			if ( is_numeric( $tax_data ) ) {
				$tax_data = array(
					'id' => $tax_data,
				);
			}
			// check if the tax exists in the database.
			$tax = eac_get_tax( $tax_data['id'] );
			if ( $tax ) {
				$data             = wp_array_slice_assoc( $tax->get_data(), array_keys( $default_tax_data ) );
				$default_tax_data = wp_parse_args( $data, $default_tax_data );
			}
			$tax_data     = wp_parse_args( $tax_data, $default_tax_data );
			$document_tax = new DocumentTax();
			$document_tax->set_document_id( $this->get_id() );
			$document_tax->set_tax_id( $tax_data['id'] );
			$document_tax->set_item_id( $line_item->get_item_id() );
			$document_tax->set_name( $tax_data['name'] );
			$document_tax->set_rate( $tax_data['rate'] );
			$document_tax->set_compound( $tax_data['compound'] );
			$document_tax->set_amount( $tax_data['amount'] );

			$this->taxes[] = $document_tax;
		}

		return true;
	}

	/**
	 * Saves an object in the database.
	 *
	 * @since 1.0.0
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function save() {
		// contact id is required.
		if ( empty( $this->get_contact_id() ) ) {
			return new \WP_Error( 'missing-required', __( 'Contact ID is required.', 'wp-invoice-ultimate' ) );
		}

		if ( empty( $this->get_type() ) ) {
			return new \WP_Error( 'missing-required', __( 'Type is required.', 'wp-invoice-ultimate' ) );
		}

		if ( empty( $this->get_status() ) ) {
			return new \WP_Error( 'missing-required', __( 'Status is required.', 'wp-invoice-ultimate' ) );
		}

		// Once the invoice is paid, contact can't be changed.
		if ( $this->get_total_paid() > 0 && in_array( 'contact_id', $this->changes, true ) ) {
			return new \WP_Error( 'invalid-argument', __( 'Contact can\'t be changed once the document is paid.', 'wp-invoice-ultimate' ) );
		}

		// If date created is not set, set it to now.
		if ( empty( $this->get_created_at() ) ) {
			$this->set_created_at( current_time( 'mysql' ) );
		}

		// If It's update, set the updated date.
		if ( $this->exists() ) {
			$this->set_updated_at( current_time( 'mysql' ) );
		}

		return parent::save();
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
		$subtotal       = 0;
		$subtotal_tax   = 0;
		$discount_total = 0;
		$discount_tax   = 0;
		$shipping_total = 0;
		$shipping_tax   = 0;
		$fees_total     = 0;
		$fees_tax       = 0;
		$total          = 0;
		$total_tax      = 0;

		$this->calculate_discount();
		$this->calculate_taxes();
		var_dump( $this->get_taxes() );
		exit;

		// Calculate items total.
		foreach ( $this->get_items() as $item ) {
			$subtotal = (float) $item->get_price() * (float) $item->get_quantity();
			$discount = (float) $item->get_discount();
			$discount = min( $discount, $subtotal );
			$shipping = (float) $item->get_shipping();
			$fee      = (float) $item->get_fee();

			$taxes = $this->get_taxes( $item->get_id() );
			var_dump( $taxes );
			exit;

			// $subtotal_tax = array_sum( eac_calculate_taxes( $subtotal, $taxes ) );
			// $discount_tax = array_sum( eac_calculate_taxes( $discount, $taxes ) );
			// $shipping_tax = array_sum( eac_calculate_taxes( $shipping, $taxes ) );
			// $fee_tax      = array_sum( eac_calculate_taxes( $fee, $taxes ) );
			//
			// $total     = $subtotal + $subtotal_tax + $shipping + $shipping_tax + $fee + $fee_tax - $discount - $discount_tax;
			// $total_tax = $subtotal_tax + $shipping_tax + $fee_tax - $discount_tax;
			// $total     = max( 0, $total );
			// $total_tax = max( 0, $total_tax );
			//
			// $item->set_subtotal( $subtotal );
			// $item->set_subtotal_tax( $subtotal_tax );
			// $item->set_discount( $discount );
			// $item->set_discount_tax( $discount_tax );
			// $item->set_shipping( $shipping );
			// $item->set_shipping_tax( $shipping_tax );
			// $item->set_fee( $fee );
			// $item->set_fee_tax( $fee_tax );
			// $item->set_total( $total );
			// $item->set_total_tax( $total_tax );
			//
			// $subtotal       += $item->get_subtotal();
			// $subtotal_tax   += $item->get_subtotal_tax();
			// $discount_total += $item->get_discount();
			// $discount_tax   += $item->get_discount_tax();
			// $shipping_total += $item->get_shipping();
			// $shipping_tax   += $item->get_shipping_tax();
			// $fees_total     += $item->get_fee();
			// $fees_tax       += $item->get_fee_tax();
			// $total          += $item->get_total();
			// $total_tax      += $item->get_total_tax();
		}

		// If price includes tax then discount tax should be added to total tax.
		if ( 'yes' === $this->get_tax_inclusive() ) {
			$discount_total -= $discount_tax;
			$discount_total  = max( 0, $discount_total );
		}

		$this->set_subtotal( $subtotal );
		$this->set_subtotal_tax( $subtotal_tax );
		$this->set_discount_total( $discount_total );
		$this->set_discount_tax( $discount_tax );
		$this->set_shipping_total( $shipping_total );
		$this->set_shipping_tax( $shipping_tax );
		$this->set_fees_total( $fees_total );
		$this->set_fees_tax( $fees_tax );
		$this->set_total( $total );
		$this->set_total_tax( $total_tax );
	}

	/**
	 * Reset totals.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function reset_totals() {
		$this->set_subtotal( 0 );
		$this->set_subtotal_tax( 0 );
		$this->set_discount_total( 0 );
		$this->set_discount_tax( 0 );
		$this->set_total( 0 );
		$this->set_total_tax( 0 );
	}

	/**
	 * Calculate discount.
	 *
	 * @since 1.0.0
	 * @return float
	 */
	public function calculate_discount() {
		$discount_type  = $this->get_discount_type();
		$discount_rate  = $this->get_discount_rate();
		$total_discount = 0;
		$items          = $this->get_items();

		if ( $discount_rate <= 0 ) {
			foreach ( $items as $item ) {
				$item->set_discount( 0 );
				$item->set_discount_tax( 0 );
			}

			return $total_discount;
		}

		if ( 'fixed' === $discount_type ) {
			$total_discount = $this->apply_fixed_discount( $discount_rate, $items );
		} elseif ( 'percentage' === $discount_type ) {
			$total_discount = $this->apply_percentage_discount( $discount_rate, $items );
		}

		return $total_discount;
	}

	/**
	 * Calculate tax.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function calculate_taxes() {
		foreach ( $this->items as $item ) {
			$compound_taxes = array();
			$regular_taxes  = array();
			foreach ( $this->taxes as &$tax ) {
				if ( $tax->get_item_id() !== $item->get_item_id() ) {
					continue;
				}
				if ( $tax->is_compound() ) {
					$compound_taxes[] = &$tax;
				} else {
					$regular_taxes[] = &$tax;
				}
			}

			// we have to calculate tax for subtotal, shipping, fees and discount.
			$taxable_amount = $item->get_subtotal() + $item->get_shipping() + $item->get_fee() - $item->get_discount();
			$taxable_amount = max( 0, $taxable_amount );
			if ( empty( $taxable_amount ) ) {
				continue;
			}

			if ( $this->is_tax_inclusive() ) {
				foreach ( $compound_taxes as &$compound_tax ) {
					$tax_amount = $non_compounded - $non_compounded / ( 1 + $compound_tax->get_rate() / 100 );
					$compound_tax->increase_amount( $tax_amount );
					$non_compounded -= $tax_amount;
				}
				$regular_tax_rate = 0;
				foreach ( $regular_taxes as $regular_tax ) {
					$regular_tax_rate += $regular_tax->get_rate();
				}
				$regular_tax_rate = 1 + ( $regular_tax_rate / 100 );
				foreach ( $regular_taxes as &$regular_tax ) {
					$the_rate  = ( $regular_tax->get_rate() / 100 ) / $regular_tax_rate;
					$net_price = $taxable_amount - ( $the_rate * $non_compounded );
					$regular_tax->increase_amount( $net_price );
				}
			} else {

				foreach ( $regular_taxes as &$regular_tax ) {
					$tax_amount = $taxable_amount * ( $regular_tax->get_rate() / 100 );
					$regular_tax->increase_amount( $tax_amount );
				}

				$pre_compounded = $taxable_amount;
				foreach ( $compound_taxes as &$compound_tax ) {
					$tax_amount = ( $taxable_amount + $pre_compounded ) * ( $compound_tax->get_rate() / 100 );
					$compound_tax->increase_amount( $tax_amount );
					$pre_compounded += $tax_amount;
				}
			}
		}
	}

	/**
	 * Calculate tax.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function calculate_taxes123() {
		foreach ( $this->get_items() as $item ) {
			$taxes          = $this->get_taxes( $item->get_id() );
			$compound_taxes = array();
			$regular_taxes  = array();
			foreach ( $taxes as $key => $tax ) {
				if ( $tax->is_compound() ) {
					$compound_taxes[ $key ] = $tax;
				} else {
					$regular_taxes[ $key ] = $tax;
				}
			}

			// we have to calculate tax for subtotal, shipping, fees and discount.
			$taxable_amount = $item->get_subtotal() + $item->get_shipping() + $item->get_fee() - $item->get_discount();

			if ( $this->is_tax_inclusive() ) {
				usort(
					$compound_taxes,
					function ( $a, $b ) {
						return $a['rate'] <=> $b['rate'];
					}
				);
				$taxable_amount = max( 0, $taxable_amount );
				$non_compounded = $taxable_amount;

				foreach ( $compound_taxes as $compound_tax ) {
					$tax_amount = $non_compounded - $non_compounded / ( 1 + $compound_tax->get_rate() / 100 );
					$compound_tax->increase_amount( $tax_amount );
					$non_compounded -= $tax_amount;
				}

				$regular_tax_rate = 0;
				foreach ( $regular_taxes as $regular_tax ) {
					$regular_tax_rate += $regular_tax->get_rate();
				}
				$regular_tax_rate = 1 + ( $regular_tax_rate / 100 );
				foreach ( $regular_taxes as $regular_tax ) {
					$the_rate  = ( $regular_tax->get_rate() / 100 ) / $regular_tax_rate;
					$net_price = $taxable_amount - ( $the_rate * $non_compounded );
					$regular_tax->increase_amount( $net_price );
				}
			} else {
				foreach ( $regular_taxes as $regular_tax ) {
					$tax_amount = $taxable_amount * ( $regular_tax->get_rate() / 100 );
					$regular_tax->increase_amount( $tax_amount );
				}

				$pre_compounded = $taxable_amount;
				foreach ( $compound_taxes as $compound_tax ) {
					$tax_amount = ( $taxable_amount + $pre_compounded ) * ( $compound_tax->get_rate() / 100 );
					$compound_tax->increase_amount( $tax_amount );
					$pre_compounded += $tax_amount;
				}
			}
		}
	}


	/**
	 * Apply fixed discount.
	 *
	 * @param float          $amount Discount amount.
	 * @param DocumentItem[] $items Items.
	 *
	 * @since 1.0.0
	 * @return float
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

		$per_item_discount = round( $amount / $item_count, 2 );
		if ( $per_item_discount > 0 ) {
			foreach ( $items as $item ) {
				$discounted_price = $item->get_discounted_price();
				$discount         = $per_item_discount * (float) $item->get_quantity();
				$discount         = round( $discount, 2 );
				$discount         = min( $discounted_price, $discount );
				$item->set_discount( $item->get_discount() + $discount );

				$total_discount += $discount;
			}

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
			$discount         = $discounted_price * ( $amount / 100 );
			$discount         = round( min( $discounted_price, $discount ), 2 );
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
	 * @param float          $amount Shipping amount.
	 * @param DocumentItem[] $items Items.
	 *
	 * @since 1.0.0
	 * @return float
	 */
	public function apply_shipping( $amount, $items ) {
		$total_shipping = 0;
		$item_count     = 0;

		foreach ( $items as $item ) {
			$item_count += (float) $item->get_quantity();
		}

		if ( $amount <= 0 || empty( $items ) || $item_count <= 0 ) {
			return $total_shipping;
		}

		$per_item_shipping = round( $amount / $item_count, 2 );
		if ( $per_item_shipping > 0 ) {
			foreach ( $items as $item ) {
				$shipping = $per_item_shipping * (float) $item->get_quantity();
				$shipping = round( $shipping, 2 );
				$item->set_shipping( $item->get_shipping() + $shipping );

				$total_shipping += $shipping;
			}

			// If there is still shipping remaining, repeat the process.
			if ( $total_shipping > 0 && $total_shipping < $amount ) {
				$total_shipping += $this->apply_shipping( $amount - $total_shipping, $items );
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
	 * Apply Fees
	 *
	 * @param float          $amount Fee amount.
	 * @param DocumentItem[] $items Items.
	 *
	 * @since 1.0.0
	 * @return float
	 */
	public function apply_fees( $amount, $items ) {
		$total_fees = 0;
		$item_count = 0;

		foreach ( $items as $item ) {
			$item_count += (float) $item->get_quantity();
		}

		if ( $amount <= 0 || empty( $items ) || $item_count <= 0 ) {
			return $total_fees;
		}

		$per_item_fee = round( $amount / $item_count, 2 );
		if ( $per_item_fee > 0 ) {
			foreach ( $items as $item ) {
				$fee = $per_item_fee * (float) $item->get_quantity();
				$fee = round( $fee, 2 );
				$item->set_fee( $item->get_fee() + $fee );

				$total_fees += $fee;
			}

			// If there is still fees remaining, repeat the process.
			if ( $total_fees > 0 && $total_fees < $amount ) {
				$total_fees += $this->apply_fees( $amount - $total_fees, $items );
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
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/

	/**
	 * Get items subtotal.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_totals() {
		$totals = array(
			array(
				'label' => __( 'Subtotal', 'wp-ever-accounting' ),
				'value' => $this->get_subtotal(),
			),
			array(
				'label' => __( 'Discount', 'wp-ever-accounting' ),
				'value' => $this->get_discount_total(),
			),
			array(
				'label' => __( 'Shipping', 'wp-ever-accounting' ),
				'value' => $this->get_shipping_total(),
			),
			array(
				'label' => __( 'Fees', 'wp-ever-accounting' ),
				'value' => $this->get_fees_total(),
			),
			array(
				'label' => __( 'Tax', 'wp-ever-accounting' ),
				'value' => $this->get_total_tax(),
			),
			array(
				'label' => __( 'Total', 'wp-ever-accounting' ),
				'value' => $this->get_total(),
			),
		);

		return $totals;
	}

	/**
	 * is editable.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_editable() {
		return true;
	}

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
	 * Get next document number.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_next_document_number() {
		global $wpdb;
		// take the first 3 letters of the document type and make it uppercase.
		$prefix = strtoupper( substr( $this->get_type(), 0, 3 ) );
		$length = 6;
		// Use regular expression to extract the number from the the column.
		$number = (int) $wpdb->get_var( $wpdb->prepare( "SELECT MAX(REGEXP_REPLACE(document_number, '[^0-9]', '')) FROM {$this->table} WHERE type = %s", $this->get_type() ) );
		$number ++;

		// Pad the number with zeros.
		$number = str_pad( $number, $length, '0', STR_PAD_LEFT );

		return $prefix . $number;
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

	/**
	 * Get formatted shipping address.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_formatted_shipping_address() {
		$data = array(
			'name'      => $this->get_shipping_name(),
			'company'   => $this->get_shipping_company(),
			'address_1' => $this->get_shipping_address_1(),
			'address_2' => $this->get_shipping_address_2(),
			'city'      => $this->get_shipping_city(),
			'state'     => $this->get_shipping_state(),
			'postcode'  => $this->get_shipping_postcode(),
			'country'   => $this->get_shipping_country(),
		);

		return eac_get_formatted_address( $data );
	}
}

