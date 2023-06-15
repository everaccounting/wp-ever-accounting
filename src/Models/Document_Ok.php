<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Document.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
abstract class Document_Ok extends Model {
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
		'type'           => '',
		'status'         => 'draft',
		'number'         => '',
		'reference'      => '',
		'contact_id'     => null,
		'billing_data'   => array(
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
		'shipping_data'  => array(
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
		'subtotal'       => 0.00,
		'subtotal_tax'   => 0.00,
		'discount_total' => 0.00,
		'discount_tax'   => 0.00,
		'shipping_total' => 0.00,
		'shipping_tax'   => 0.00,
		'fees_total'     => 0.00,
		'fees_tax'       => 0.00,
		'total'          => 0.00,
		'total_tax'      => 0.00,
		'total_paid'     => 0.00,
		'total_refunded' => 0.00,
		'notes'          => '',
		'footer'         => '',
		'tax_inclusive'  => 'yes',
		'vat_exempt'     => 'no', // Based on customer's settings.
		'issued_at'      => null,
		'due_at'         => null,
		'sent_at'        => null,
		'viewed_at'      => null,
		'paid_at'        => null,
		'unique_hash'    => '',
		'created_via'    => '',
		'currency'       => '',
		'parent_id'      => null,
		'creator_id'     => null,
		'updated_at'     => null,
		'created_at'     => null,
	);

	/**
	 * Extra data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $extra_data = array(
		'discount_type'   => 'fixed',
		'discount_amount' => 0.00, // This is the discount applied to all items.
		'shipping_cost'   => 0.00, // This is the shipping cost applied to all items.
		'fee_amount'      => 0.00, // This is the fees amount applied to all items.
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
	protected $deletables = array();

	/**
	 * Document constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		$this->core_data['tax_inclusive'] = eac_price_includes_tax() ? 'yes' : 'no';
		$this->core_data['currency']      = eac_get_base_currency();
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
	 * @param float $value Subtotal tax.
	 *
	 * @since  1.1.0
	 */
	public function set_subtotal_tax( $value ) {
		$this->set_prop( 'subtotal_tax', eac_sanitize_number( $value ) );
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
	 * @param float $value Discount tax.
	 *
	 * @since  1.1.0
	 */
	public function set_discount_tax( $value ) {
		$this->set_prop( 'discount_tax', eac_sanitize_number( $value ) );
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
	 * @param float $value Shipping tax.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping_tax( $value ) {
		$this->set_prop( 'shipping_tax', eac_sanitize_number( $value ) );
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
	 * @param float $value Shipping tax.
	 *
	 * @since  1.1.0
	 */
	public function set_fees_tax( $value ) {
		$this->set_prop( 'fees_tax', eac_sanitize_number( $value ) );
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
	 * @param float $value Total tax.
	 *
	 * @since  1.1.0
	 */
	public function set_total_tax( $value ) {
		$this->set_prop( 'total_tax', eac_sanitize_number( $value ) );
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
	public function get_creator_id( $context = 'edit' ) {
		return $this->get_prop( 'creator_id', $context );
	}

	/**
	 * Set the agent id.
	 *
	 * @param int $creator_id agent id.
	 */
	public function set_creator_id( $creator_id ) {
		$this->set_prop( 'creator_id', absint( $creator_id ) );
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
	| Extra props getters and setters
	|--------------------------------------------------------------------------
	| Extra props are used to store additional data in the database.
	*/

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
	 * @param string $type Discount type.
	 *
	 * @since  1.1.0
	 */
	public function set_discount_type( $type ) {
		$this->set_prop( 'discount_type', eac_clean( $type ) );
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
		return $this->get_prop( 'discount_amount', $context );
	}

	/**
	 * Set discount amount.
	 *
	 * @param string $value Discount amount.
	 *
	 * @since  1.1.0
	 */
	public function set_discount_amount( $value ) {
		$this->set_prop( 'discount_amount', eac_sanitize_number( $value ) );
	}

	/**
	 * Get shipping cost.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_shipping_cost( $context = 'edit' ) {
		return $this->get_prop( 'shipping_cost', $context );
	}

	/**
	 * Set shipping cost.
	 *
	 * @param string $value Discount cost.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping_cost( $value ) {
		$this->set_prop( 'shipping_cost', eac_sanitize_number( $value ) );
	}

	/**
	 * Get fees amount.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_fee_amount( $context = 'edit' ) {
		return $this->get_prop( 'fee_amount', $context );
	}

	/**
	 * Set fees amount.
	 *
	 * @param string $value Discount amount.
	 *
	 * @since  1.1.0
	 */
	public function set_fee_amount( $value ) {
		$this->set_prop( 'fee_amount', eac_sanitize_number( $value ) );
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
	 * @param int $line_id Line id.
	 *
	 * @return DocumentItem[]
	 */
	public function get_items( $line_id = null ) {
		if ( $this->exists() && is_null( $this->items ) ) {
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
				if ( $old_item->get_id() === $item['id'] ) {
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
	 * @param int|array|object $item_data Item.
	 *
	 * @return bool
	 */
	public function add_item( $item_data ) {
		$default_data = array(
			'id'            => 0, // item id not line id be careful.
			'name'          => '',
			'description'   => '',
			'unit_type'     => '',
			'price'         => 0,
			'quantity'      => 1,
			'discount'      => 0,
			'shipping_cost' => 0,
			'fee_amount'    => 0,
			'is_taxable'    => $this->is_calculating_tax(),
			'tax_ids'       => array(),
		);
		$this->get_items();
		if ( is_numeric( $item_data ) ) {
			$item_data = array( 'id' => $item_data );
		}
		// check if the item is valid.
		if ( ! is_array( $item_data ) || empty( $item_data['id'] ) ) {
			return false;
		}
		$item = eac_get_item( $item_data['id'] );
		if ( empty( $item ) ) {
			return false;
		}
		$data         = wp_array_slice_assoc( $item->get_data(), array_keys( $default_data ) );
		$default_data = wp_parse_args( $data, $default_data );
		$item_data    = wp_parse_args( $item_data, $default_data );

		// create new item.
		$line_item = new DocumentItem();
		$line_item->set_document_id( $this->get_id() );
		$line_item->set_item_id( $item_data['id'] );
		$line_item->set_name( $item_data['name'] );
		$line_item->set_description( $item_data['description'] );
		$line_item->set_unit_type( $item_data['unit_type'] );
		$line_item->set_price( $item_data['price'] );
		$line_item->set_quantity( $item_data['quantity'] );
		$line_item->set_discount( $item_data['discount'] );
		$line_item->set_shipping_cost( $item_data['shipping_cost'] );
		$line_item->set_fee_amount( $item_data['fee_amount'] );
		$line_item->set_is_taxable( $item_data['is_taxable'] );
		$line_item->set_tax_ids( $item_data['tax_ids'] );
		$this->items[] = $line_item;

		return true;
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

		$this->calculate_subtotals();
		$this->calculate_discounts();
		$this->calculate_shipping_costs();
		$this->calculate_fees();
		$this->calculate_taxes();
		$this->calculate_total();

		// calculate totals.
		// $total = $this->get_subtotal();
		// $total += $this->get_shipping_total();
		// $total += $this->get_fees_total();
		// $total += $this->get_subtotal_tax();
		// $total += $this->get_shipping_tax();
		// $total += $this->get_fees_tax();
		// $total -= $this->get_discount_total();
		// $total -= $this->get_discount_tax();
		// $total  = round( max( $total, 0 ), 2 );
		// $this->set_total( $total );
	}

	/**
	 * Calculate subtotals.
	 *
	 * @return void
	 */
	public function calculate_subtotals() {
		$items = $this->get_items();
		foreach ( $items as $item ) {
			$subtotal       = (float) $item->get_price() * (float) $item->get_quantity();
			$subtotal_taxes = eac_calculate_taxes( $subtotal, $item->get_tax_objects(), $this->is_tax_inclusive() );
			if ( $this->is_tax_inclusive() ) {
				$subtotal -= array_sum( wp_list_pluck( $subtotal_taxes, 'amount' ) );
			}
			if ( ! $item->is_taxable() || $this->is_vat_exempt() ) {
				$subtotal_taxes = array();
			}
			$subtotal_tax         = array_sum( wp_list_pluck( $subtotal_taxes, 'amount' ) );
			$tax_data             = $item->get_tax_data();
			$tax_data['subtotal'] = $subtotal_taxes;
			$item->set_subtotal( $subtotal );
			$item->set_tax_data( $tax_data );

			// Document totals.
			$this->set_subtotal( $this->get_subtotal() + $subtotal );
			$this->set_subtotal_tax( $this->get_subtotal_tax() + $subtotal_tax );
		}
	}

	/**
	 * Calculate discounts.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function calculate_discounts() {
		$items           = $this->get_items();
		$discount_amount = $this->get_discount_amount();
		$discount_type   = $this->get_discount_type();
		// First apply the discount to the items.
		if ( $discount_amount > 0 && ! empty( $discount_type ) ) {
			$this->apply_discount( $discount_amount, $items, $discount_type );
			$this->set_discount_amount( 0 );
		}

		foreach ( $items as $item ) {
			$discount_taxes = eac_calculate_taxes( $item->get_discount(), $item->get_tax_objects(), false );
			if ( ! $item->is_taxable() || $this->is_vat_exempt() ) {
				$discount_taxes = array();
			}
			$discount_tax = array_sum( wp_list_pluck( $discount_taxes, 'amount' ) );
			// $tax_data             = $item->get_tax_data();
			// $tax_data['discount'] = $discount_taxes;
			// $item->set_tax_data( $tax_data );

			// Document totals.
			$this->set_discount_total( $this->get_discount_total() + $item->get_discount() );
			$this->set_discount_tax( $this->get_discount_tax() + $discount_tax );
		}
	}

	/**
	 * Calculate shipping costs.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function calculate_shipping_costs() {
		$items         = $this->get_items();
		$shipping_cost = $this->get_shipping_cost();
		// First apply the shipping cost to the items.
		if ( $shipping_cost > 0 ) {
			$total_shipping = $this->apply_shipping_cost( $shipping_cost, $items );
			$this->set_shipping_cost( 0 );
		}

		foreach ( $items as $item ) {
			$shipping_taxes = eac_calculate_taxes( $item->get_shipping_cost(), $item->get_tax_objects(), false );
			if ( ! $item->is_taxable() || $this->is_vat_exempt() ) {
				$shipping_taxes = array();
			}
			$tax_data             = $item->get_tax_data();
			$tax_data['shipping'] = $shipping_taxes;
			$item->set_tax_data( $tax_data );

			// Document totals.
			$this->set_shipping_total( $this->get_shipping_total() + $item->get_shipping_cost() );
			$this->set_shipping_tax( $this->get_shipping_tax() + array_sum( wp_list_pluck( $shipping_taxes, 'amount' ) ) );
		}
	}

	/**
	 * Calculate fees.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function calculate_fees() {
		$items = $this->get_items();
		$fess  = $this->get_fee_amount();
		// First apply the fees to the items.
		if ( $fess > 0 ) {
			$this->apply_fee_amount( $fess, $items );
			$this->set_fee_amount( 0 );
		}

		foreach ( $items as $item ) {
			$fee_taxes = eac_calculate_taxes( $item->get_fee_amount(), $item->get_tax_objects(), false );
			if ( ! $item->is_taxable() || $this->is_vat_exempt() ) {
				$fee_taxes = array();
			}
			$tax_data        = $item->get_tax_data();
			$tax_data['fee'] = $fee_taxes;
			$item->set_tax_data( $tax_data );

			// Document totals.
			$this->set_fees_total( $this->get_fees_total() + $item->get_fee_amount() );
			$this->set_fees_tax( $this->get_fees_tax() + array_sum( wp_list_pluck( $fee_taxes, 'amount' ) ) );
		}
	}

	/**
	 * Calculate taxes.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function calculate_taxes() {
		$items = $this->get_items();
		foreach ( $items as $item ) {
			$total_tax = 0;
			foreach ( $item->get_tax_data() as $tax_datum ) {
				foreach ( $tax_datum as $tax ) {
					$total_tax += isset( $tax['amount'] ) ? $tax['amount'] : 0;
				}
			}
			// Minus discount tax.
			$discount_taxes = eac_calculate_taxes( $item->get_discount(), $item->get_tax_objects(), false );
			$discount_tax   = array_sum( wp_list_pluck( $discount_taxes, 'amount' ) );
			$total_tax     -= $discount_tax;
			$total_tax      = round( $total_tax, 2 );
			$item->set_total_tax( $total_tax );
			// Document totals.
			$this->set_total_tax( $this->get_total_tax() + $total_tax );
		}
	}

	/**
	 * Calculate total.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function calculate_total() {
		$items = $this->get_items();
		foreach ( $items as $item ) {
			$total = $item->get_subtotal() + $item->get_shipping_cost() + $item->get_fee_amount() - $item->get_discount() + $item->get_total_tax();
			$item->set_total( $total );
			// Document totals.
			$this->set_total( $this->get_total() + $total );
		}
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
	 * @param float $amount Discount amount.
	 * @param array $items Items.
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

		$per_item_discount = round( $amount / $item_count, 2 );
		var_dump( $per_item_discount );
		if ( $per_item_discount > 0 ) {
			foreach ( $items as $item ) {
				$discounted_price = $item->get_discounted_price();
				$discount         = $per_item_discount * (float) $item->get_quantity();
				$discount         = min( $discounted_price, $discount );
				$item->set_discount( round( $item->get_discount() + $discount, 2 ) );

				$total_discount += $discount;
			}

			// If there is still discount remaining, repeat the process.
			if ( $total_discount > 0 && $total_discount < $amount ) {
				$total_discount += $this->apply_fixed_discount( round( $amount - $total_discount, 2 ), $items );
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
			$discount = round( min( $discounted_price, $discount ), 2 );
			$item->set_discount( round( $item->get_discount() + $discount, 2 ) );
			$total_discount += $discount;
			$document_total += $discounted_price;
		}

		// Work out how much discount would have been given to the cart as a whole and compare to what was discounted on all line items.
		$document_discount = round( $document_total * ( $amount / 100 ), 2 );
		$total_discount    = round( $total_discount, 2 );

		if ( $total_discount < $document_discount && $amount > 0 ) {
			$total_discount += $this->apply_discount_remainder( round( $amount - $total_discount, 2 ), $items );
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
				$discount         = round( min( $discounted_price, 1 ), 2 );
				$item->set_discount( round( $item->get_discount() + $discount, 2 ) );
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

		$per_item_shipping = round( $amount / $item_count, 2 );
		if ( $per_item_shipping > 0 ) {
			foreach ( $items as $item ) {
				$shipping = $per_item_shipping * (float) $item->get_quantity();
				$shipping = round( $shipping, 2 );
				$item->set_shipping_cost( round( $item->get_shipping_cost() + $shipping, 2 ) );

				$total_shipping += $shipping;
			}

			// If there is still shipping remaining, repeat the process.
			if ( $total_shipping > 0 && $total_shipping < $amount ) {
				$total_shipping += $this->apply_shipping_cost( round( $amount - $total_shipping, 2 ), $items );
			}
		} elseif ( $amount > 0 ) {
			foreach ( $items as $item ) {
				$quantity = $item->get_quantity();
				for ( $i = 0; $i < $quantity; $i ++ ) {
					$shipping = round( min( $amount, 1 ), 2 );
					$item->set_shipping_cost( round( $item->get_shipping_cost() + $shipping, 2 ) );
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
	 * Apply fee amounts.
	 *
	 * @param float $amount Shipping amount.
	 * @param array $items Items.
	 *
	 * @since 1.0.0
	 * @return float Total fee amount.
	 */
	public function apply_fee_amount( $amount = 0, $items = array() ) {
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
				$item->set_fee_amount( round( $item->get_fee_amount() + $fee, 2 ) );

				$total_fees += $fee;
			}

			// If there is still fees remaining, repeat the process.
			if ( $total_fees > 0 && $total_fees < $amount ) {
				$total_fees += $this->apply_fee_amount( round( $amount - $total_fees, 2 ), $items );
			}
		} elseif ( $amount > 0 ) {
			foreach ( $items as $item ) {
				$quantity = $item->get_quantity();
				for ( $i = 0; $i < $quantity; $i ++ ) {
					$fee = min( $amount, 1 );
					$item->set_fee_amount( round( $item->get_fee_amount() + $fee, 2 ) );
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

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/

	/**
	 * Set contact
	 *
	 * @param int|Contact $contact Contact id.
	 *
	 * @return void
	 */
	public function set_contact( $contact ) {
		if ( is_numeric( $contact ) ) {
			$contact = eac_get_contact( $contact );
		}

		if ( empty( $contact ) ) {
			return;
		}

		$keys = array_keys( $this->core_data['billing_data'] );
		$this->set_contact_id( $contact->get_id() );
		foreach ( $keys as $key ) {
			$getter = 'get_' . $key;
			$setter = 'set_billing_' . $key;

			if ( method_exists( $contact, $getter ) && method_exists( $this, $setter ) ) {
				$this->$setter( $contact->$getter( 'edit' ) );
			}
		}
	}

}


