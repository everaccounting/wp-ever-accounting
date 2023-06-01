<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Document.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
abstract class Document_BK3 extends Model {
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
		'type'             => '',
		'document_number'  => '',
		'order_number'     => '',
		'status'           => 'draft',
		'subtotal'         => 0.00,
		'subtotal_tax'     => 0.00,
		'discount_total'   => 0.00,
		'discount_tax'     => 0.00,
		'shipping_total'   => 0.00,
		'shipping_tax'     => 0.00,
		'fees_total'       => 0.00,
		'fees_tax'         => 0.00,
		'total'            => 0.00,
		'total_tax'        => 0.00,
		'total_paid'       => 0.00,
		'total_refunded'   => 0.00,
		'tax_inclusive'    => 'yes',
		'vat_exempt'       => 'no',
		'contact_id'       => null,
		'billing_address'  => array(
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
		'shipping_address' => array(
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
		'notes'            => '',
		'footer'           => '',
		'issued_at'        => null,
		'due_at'           => null,
		'sent_at'          => null,
		'viewed_at'        => null,
		'paid_at'          => null,
		'currency'         => '',
		'parent_id'        => null,
		'unique_hash'      => '',
		'created_via'      => '',
		'agent_id'         => null,
		'updated_at'       => null,
		'created_at'       => null,
	);

	/**
	 * Extra data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $extra_data = array(
		'discount_type'   => 'fixed',
		'discount_amount' => 0.00,
		'shipping_type'   => 'fixed',
		'shipping_cost'   => 0.00,
		'fees_type'       => 'fixed',
		'fees_amount'     => 0.00,
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



	/*
	|--------------------------------------------------------------------------
	| Extra Props getters and setters
	|--------------------------------------------------------------------------
	| These methods are used to get and set extra props.
	*/


	/**
	 * Get shipping.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_shipping43( $context = 'edit' ) {
		return $this->get_prop( 'shipping', $context );
	}

	/**
	 * Set shipping.
	 *
	 * @param string $value Discount rate.
	 *
	 * @since  1.1.0
	 */
	public function set_shipping123( $value ) {
		$this->set_prop( 'shipping', eac_sanitize_number( $value ) );
	}

	/**
	 * Get fees.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_fees( $context = 'edit' ) {
		return $this->get_prop( 'fees', $context );
	}

	/**
	 * Set fees.
	 *
	 * @param string $value Discount rate.
	 *
	 * @since  1.1.0
	 */
	public function set_fees( $value ) {
		$this->set_prop( 'fees', eac_sanitize_number( $value ) );
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
	 * @return Payment[]
	 */
	public function get_payments() {
		// Get payments only if the amount is positive.
		$payments = array();
		if ( $this->exists() ) {
			$payments = Payment::query(
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
	 * @return  Payment[] $payments Payments.
	 */
	public function get_refunds() {
		$refunds = array();
		if ( $this->exists() ) {
			$refunds = Payment::query(
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

		// Discounts are applied before taxes.
		$this->calculate_discounts();
		$this->calculate_subtotals();
		$this->calculate_shipping();
		// $this->calculate_fees();
		// $this->calculate_taxes();
		// foreach ( $this->get_taxes() as $tax ) {
		// var_dump( $tax->get_data() );
		// }
		// foreach ( $this->get_items() as $item ) {
		// var_dump( $item->get_data() );
		// }
		// exit();
	}

	/**
	 * Calculate Item discounts.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function calculate_discounts() {

	}

	/**
	 * Subtotals are costs before discounts.
	 *
	 * To prevent rounding issues we need to work with the inclusive price where possible
	 * otherwise we'll see errors such as when working with a 9.99 inc price, 20% VAT which would
	 * be 8.325 leading to totals being 1p off.
	 *
	 * Pre tax coupons come off the price the customer thinks they are paying - tax is calculated
	 * afterwards.
	 *
	 * e.g. $100 bike with $10 coupon = customer pays $90 and tax worked backwards from that.
	 *
	 * @since 1.0.0
	 */
	protected function calculate_subtotals() {
		foreach ( $this->get_items() as &$item ) {
			$taxes    = $this->get_taxes( $item->get_item_id() );
			$subtotal = $item->get_price() * $item->get_quantity();
			$discount = $item->get_discount();
			if ( $this->is_tax_inclusive() ) {
				$subtotal_tax = eac_calculate_taxes( $subtotal - $discount, $taxes, true );
				$subtotal    -= $subtotal_tax;
			}
			$subtotal = max( $subtotal, 0 );
			$item->set_subtotal( $subtotal );

			// tax calculation.
			$taxable_amount = $this->is_calculating_tax() ? $subtotal - $discount : 0;
			$subtotal_taxes = eac_calculate_taxes( $taxable_amount, $taxes );
			foreach ( $subtotal_taxes as $subtotal_tax ) {
				$subtotal_tax = max( $subtotal_tax, 0 );
				foreach ( $taxes as &$tax ) {
					if ( $tax->get_tax_id() === $subtotal_tax['tax_id'] ) {
						$tax->set_subtotal( $tax->get_subtotal() + $subtotal_tax['amount'] );
					}
				}
			}

			// Update document properties.
			$this->set_subtotal( $this->get_subtotal() + $subtotal );
		}
	}

	/**
	 * Calculate shipping.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function calculate_shipping() {
		foreach ( $this->get_items() as &$item ) {
			$shipping = $item->get_shipping();
			// Update document properties.
			$this->set_shipping_total( $this->get_shipping_total() + $shipping );
		}
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
		foreach ( $this->get_items() as $item ) {
			$taxes = $this->get_taxes( $item->get_id() );
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
	 * Is calculating tax.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_calculating_tax() {
		return 'yes' !== eac_tax_enabled() || ! $this->is_vat_exempt();
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

