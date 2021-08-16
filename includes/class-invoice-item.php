<?php
/**
 * Handle the Invoice_Item object.
 *
 * @package     EverAccounting
 * @class       Invoice_Item
 * @version     1.2.1
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Invoice_Item object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 * @property int $invoice_id
 * @property int $item_id
 * @property string $item_name
 * @property float $price
 * @property float $quantity
 * @property float $subtotal
 * @property float $tax_rate
 * @property float $discount
 * @property float $tax
 * @property float $total
 * @property string $currency_code
 * @property string $extra
 * @property string $date_created
 */
class Invoice_Item extends Data {

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	public $data = array(
		'invoice_id'    => null,
		'item_id'       => null,
		'item_name'     => '',
		'price'         => 0.00,
		'quantity'      => 1,
		'subtotal'      => 0.00,
		'tax_rate'      => 0.00,
		'discount'      => 0.00,
		'tax'           => 0.00,
		'total'         => 0.00,
		'currency_code' => '',
		'extra'         => array(
			'shipping'     => 0.00,
			'shipping_tax' => 0.00,
			'fees'         => 0.00,
			'fees_tax'     => 0.00,
		),
		'date_created'  => null,
	);

	/**
	 * A map of database fields to data types.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $data_type = array(
		'id'            => '%d',
		'invoice_id'    => '%d',
		'item_id'       => '%d',
		'item_name'     => '%s',
		'price'         => '%.4f',
		'quantity'      => '%.2f',
		'subtotal'      => '%.4f',
		'tax_rate'      => '%.4f',
		'discount'      => '%.4f',
		'tax'           => '%.4f',
		'total'         => '%.4f',
		'currency_code' => '%s',
		'extra'         => '%s',
		'date_created'  => '%s',
	);

	/**
	 * Invoice item constructor.
	 *
	 * Get the invoice item if ID is passed, otherwise the invoice item is new and empty.
	 *
	 * @param int|object|Invoice_Item $invoice_item object to read.
	 *
	 * @since 1.1.0
	 */
	public function __construct( $invoice_item = 0 ) {
		parent::__construct();
		if ( $invoice_item instanceof self ) {
			$this->set_id( $invoice_item->get_id() );
		} elseif ( is_object( $invoice_item ) && ! empty( $invoice_item->id ) ) {
			$this->set_id( $invoice_item->id );
		} elseif ( is_array( $invoice_item ) && ! empty( $invoice_item['id'] ) ) {
			$this->set_props( $invoice_item );
		} elseif ( is_numeric( $invoice_item ) ) {
			$this->set_id( $invoice_item );
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

	/**
	 * Retrieve the object from database instance.
	 *
	 * @param int $invoice_item_id Object id.
	 * @param string $field Database field.
	 *
	 * @return object|false Object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	public static function get_raw( $invoice_item_id, $field = 'id' ) {
		global $wpdb;

		$invoice_item_id = (int) $invoice_item_id;
		if ( ! $invoice_item_id ) {
			return false;
		}

		$invoice_item = wp_cache_get( $invoice_item_id, 'ea_invoice_items' );

		if ( ! $invoice_item ) {
			$invoice_item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_invoice_items WHERE id = %d LIMIT 1", $invoice_item_id ) );

			if ( ! $invoice_item ) {
				return false;
			}

			wp_cache_add( $invoice_item->id, $invoice_item, 'ea_invoice_items' );
		}

		return apply_filters( 'eaccounting_invoice_item', $invoice_item );
	}

	/**
	 *  Insert an invoice item in the database.
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
	protected function insert( $args = array() ) {
		global $wpdb;
		$data_arr = $this->to_array();
		$data     = wp_array_slice_assoc( $data_arr, array_keys( $this->data_type ) );
		$format   = wp_array_slice_assoc( $this->data_type, array_keys( $data ) );
		$data     = wp_unslash( $data );

		// Bail if nothing to save
		if ( empty( $data ) ) {
			return true;
		}

		if( isset( $data['extra'] ) ){
			$data['extra'] = maybe_serialize( $data['extra'] );
		}

		/**
		 * Fires immediately before an invoice item is inserted in the database.
		 *
		 * @param array $data Invoice item data to be inserted.
		 * @param string $data_arr Sanitized invoice item data.
		 * @param Invoice_Item $invoice_item Invoice item object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_invoice_item', $data, $data_arr, $this );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_invoice_items', $data, $format ) ) {
			return new \WP_Error( 'db_insert_error', __( 'Could not insert invoice item into the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		$this->set_id( $wpdb->insert_id );

		/**
		 * Fires immediately after an invoice item is inserted in the database.
		 *
		 * @param int $invoice_item_id Invoice item id.
		 * @param array $data Invoice item data to be inserted.
		 * @param string $data_arr Sanitized account data.
		 * @param Invoice_Item $invoice_item Invoice item object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_insert_invoice_item', $this->id, $data, $data_arr, $this );

		return true;
	}

	/**
	 *  Update an invoice item in the database.
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
	protected function update( $args = array() ) {
		global $wpdb;
		$changes = $this->get_changes();
		$data    = wp_array_slice_assoc( $changes, array_keys( $this->data_type ) );
		$format  = wp_array_slice_assoc( $this->data_type, array_keys( $data ) );
		$data    = wp_unslash( $data );
		// Bail if nothing to save
		if ( empty( $data ) ) {
			return true;
		}

		if( isset( $data['extra'] ) ){
			$data['extra'] = maybe_serialize( $data['extra'] );
		}

		/**
		 * Fires immediately before an existing invoice item is updated in the database.
		 *
		 * @param int $invoice_item_id Invoice item id.
		 * @param array $data Invoice item data.
		 * @param array $changes The data will be updated.
		 * @param Invoice_Item $invoice_item Invoice item object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_invoice_item', $this->get_id(), $this->to_array(), $changes, $this );

		if ( false === $wpdb->update( $wpdb->prefix . 'ea_invoice_items', $data, [ 'id' => $this->get_id() ], $format, [ 'id' => '%d' ] ) ) {
			return new \WP_Error( 'db_update_error', __( 'Could not update invoice item in the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing invoice item is updated in the database.
		 *
		 * @param int $invoice_item_id Invoice item id.
		 * @param array $data Invoice item data.
		 * @param array $changes The data will be updated.
		 * @param Invoice_Item $invoice_item Invoice item object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_invoice_item', $this->get_id(), $this->to_array(), $changes, $this );

		return true;
	}

	/**
	 * Saves an invoice item in the database.
	 *
	 * @return \WP_Error|int id on success, WP_Error on failure.
	 * @since 1.1.0
	 */
	public function save() {
		if ( empty( $this->get_prop( 'item_name' ) ) ) {
			return new \WP_Error( 'invalid_invoice_item_name', esc_html__( 'Invoice item name is required', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->get_prop( 'item_id' ) ) ) {
			return new \WP_Error( 'invalid_invoice_item_id', esc_html__( 'Invoice item id is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'currency_code' ) ) ) {
			return new \WP_Error( 'invalid_invoice_item_currency_code', esc_html__( 'Invoice item currency is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'invoice_id' ) ) ) {
			return new \WP_Error( 'invalid_invoice_item_invoice_id', esc_html__( 'Invoice id is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'date_created' ) ) || '0000-00-00 00:00:00' === $this->get_prop( 'date_created' ) ) {
			$this->set_date_prop( 'date_created', current_time( 'mysql' ) );
		}

		if ( ! empty( !empty( $this->changes ) ) || ! $this->exists() ) {
			$this->calculate_total();
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
		wp_cache_delete( $this->get_id(), 'ea_invoice_items' );
		wp_cache_set( 'last_changed', microtime(), 'ea_invoice_items' );

		/**
		 * Fires immediately after an invoice item is inserted or updated in the database.
		 *
		 * @param int $invoice_item_id Invoice item id.
		 * @param Invoice_Item $invoice_item Invoice item object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_saved_invoice_item', $this->get_id(), $this );

		return $this->get_id();
	}


	/**
	 * Deletes the invoice item from database.
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
		 * Filters whether an invoice item delete should take place.
		 *
		 * @param bool|null $delete Whether to go forward with deletion.
		 * @param int $invoice_item_id Invoice item id.
		 * @param array $data Invoice item data array.
		 * @param Invoice_Item $invoice_item Invoice item object.
		 *
		 * @since 1.2.1
		 */
		$check = apply_filters( 'eaccounting_check_delete_invoice_item', null, $this->get_id(), $data, $this );
		if ( null !== $check ) {
			return $check;
		}

		/**
		 * Fires before an invoice item is deleted.
		 *
		 * @param int $invoice_item_id Invoice item id.
		 * @param array $data Invoice item data array.
		 * @param Invoice_Item $invoice_item Invoice item object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_delete_invoice_item', $this->get_id(), $data, $this );

		$result = $wpdb->delete( $wpdb->prefix . 'ea_invoice_items', array( 'id' => $this->get_id() ) );
		if ( ! $result ) {
			return false;
		}

		/**
		 * Fires after an invoice item is deleted.
		 *
		 * @param int $invoice_item_id Invoice item id.
		 * @param array $data Invoice item data array.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_delete_invoice_item', $this->get_id(), $data );

		// Clear object.
		wp_cache_delete( $this->get_id(), 'ea_invoice_items' );
		wp_cache_set( 'last_changed', microtime(), 'ea_invoice_items' );
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
	 * Return the order id.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_invoice_id() {
		return $this->get_prop( 'invoice_id' );
	}

	/**
	 * Return the item id.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_item_id() {
		return $this->get_prop( 'item_id' );
	}

	/**
	 * Return the name.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_item_name() {
		return $this->get_prop( 'item_name' );
	}

	/**
	 * Return the price.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_price() {
		return $this->get_prop( 'price' );
	}

	/**
	 * Return the quantity.
	 *
	 * @return int
	 * @since  1.1.0
	 *
	 */
	public function get_quantity() {
		return $this->get_prop( 'quantity' );
	}

	/**
	 * Return the sub_total.
	 *
	 * @param $context
	 *
	 * @return float
	 * @since  1.1.0
	 *
	 */
	public function get_subtotal() {
		return $this->get_prop( 'subtotal' );
	}

	/**
	 * Return the tax.
	 *
	 * @return float
	 * @since  1.1.0
	 *
	 */
	public function get_tax_rate() {
		return $this->get_prop( 'tax_rate' );
	}

	/**
	 * Return the discount.
	 *
	 * @return float
	 * @since  1.1.0
	 *
	 */
	public function get_discount() {
		return $this->get_prop( 'discount' );
	}

	/**
	 * Get total tax.
	 *
	 * @return float
	 * @since 1.1.0
	 *
	 */
	public function get_tax() {
		return $this->get_prop( 'tax' );
	}

	/**
	 * Return the total.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_total() {
		return $this->get_prop( 'total' );
	}

	/**
	 * Return the total.
	 *
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_currency_code() {
		return $this->get_prop( 'currency_code' );
	}

	/**
	 * @return array|mixed|string
	 * @since 1.1.0
	 *
	 */
	public function get_extra() {
		return $this->get_prop( 'extra' );
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
	protected function get_extra_prop( $prop ) {
		$value = null;

		if ( array_key_exists( $prop, $this->data['extra'] ) ) {
			$value = isset( $this->changes['extra'][ $prop ] ) ? $this->changes['extra'][ $prop ] : $this->data['extra'][ $prop ];
		}

		return $value;
	}

	/**
	 * Get shipping cost
	 *
	 * @return float
	 * @since 1.1.0
	 *
	 */
	public function get_shipping() {
		return $this->get_extra_prop( 'shipping' );
	}

	/**
	 * get shipping tax
	 *
	 * @return float
	 * @since 1.1.0
	 *
	 */
	public function get_shipping_tax() {
		return $this->get_extra_prop( 'shipping_tax' );
	}

	/**
	 * Get fees.
	 *
	 * @return float
	 * @since 1.1.0
	 *
	 */
	public function get_fees() {
		return $this->get_extra_prop( 'fees' );
	}

	/**
	 * Get fees tax.
	 *
	 * @return float
	 * @since 1.1.0
	 *
	 */
	public function get_fees_tax() {
		return $this->get_extra_prop( 'fees_tax' );
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
	 * set the order id.
	 *
	 * @param int $invoice_id .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_invoice_id( $invoice_id ) {
		$this->set_prop( 'invoice_id', absint( $invoice_id ) );
	}

	/**
	 * set the item_id.
	 *
	 * @param int $item_id .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_item_id( $item_id ) {
		$this->set_prop( 'item_id', absint( $item_id ) );
	}

	/**
	 * set the name.
	 *
	 * @param string $name .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_item_name( $name ) {
		$this->set_prop( 'item_name', sanitize_text_field( $name ) );
	}

	/**
	 * set the price.
	 *
	 * @param double $price .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_price( $price ) {
		$this->set_prop( 'price', floatval( $price ) );
	}


	/**
	 * set the quantity.
	 *
	 * @param int $quantity .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_quantity( $quantity = 1 ) {
		$this->set_prop( 'quantity', floatval( $quantity ) );
	}

	/**
	 * set the tax.
	 *
	 * Flat amount
	 *
	 * @param double $subtotal .
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
	 * @param $tax_rate
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_tax_rate( $tax_rate ) {
		$this->set_prop( 'tax_rate', floatval( $tax_rate ) );
	}

	/**
	 * set the tax.
	 *
	 * @param $tax
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_tax( $tax ) {
		$this->set_prop( 'tax', floatval( $tax ) );
	}

	/**
	 * set the tax.
	 *
	 * Flat amount
	 *
	 * @param double $discount .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_discount( $discount ) {
		$this->set_prop( 'discount', floatval( $discount ) );
	}

	/**
	 * set the total.
	 *
	 * @param int $total .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_total( $total ) {
		$this->set_prop( 'total', floatval( $total ) );
	}

	/**
	 * set the total.
	 *
	 * @param $currency_code
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_currency_code( $currency_code ) {
		$this->set_prop( 'currency_code', eaccounting_clean( $currency_code ) );
	}

	/**
	 * set the total.
	 *
	 * @param string $extra
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_extra( $extra ) {
		$this->set_prop( 'extra', eaccounting_clean( maybe_unserialize( $extra ) ) );
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
	protected function set_extra_prop( $prop, $value ) {
		if ( array_key_exists( $prop, $this->data['extra'] ) ) {
			if ( true === $this->object_read ) {
				if ( $value !== $this->data['extra'][ $prop ] || ( isset( $this->changes['extra'] ) && array_key_exists( $prop, $this->changes['extra'] ) ) ) {
					$this->changes['extra'][ $prop ] = $value;
				}
			} else {
				$this->data['extra'][ $prop ] = $value;
			}
		}
	}

	/**
	 * Set shipping.
	 *
	 * @param string $shipping shipping.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_shipping( $shipping ) {
		$this->set_extra_prop( 'shipping', floatval( $shipping ) );
	}

	/**
	 * Set shipping_tax.
	 *
	 * @param string $shipping_tax shipping_tax.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_shipping_tax( $shipping_tax ) {
		$this->set_extra_prop( 'shipping_tax', floatval( $shipping_tax ) );
	}

	/**
	 * Set fees.
	 *
	 * @param string $fees fees.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_fees( $fees ) {
		$this->set_extra_prop( 'fees', floatval( $fees ) );
	}

	/**
	 * Set fees_tax.
	 *
	 * @param string $fees_tax fees_tax.
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_fees_tax( $fees_tax ) {
		$this->set_extra_prop( 'fees_tax', floatval( $fees_tax ) );
	}

	/**
	 * Set object created date.
	 *
	 * @param string $date Created date
	 *
	 * @since 1.0.2
	 */
	public function set_date_created( $date = null ) {
		if ( null === $date ) {
			$date = current_time( 'mysql' );
		}
		$this->set_date_prop( 'date_created', $date );
	}

	/*
	|--------------------------------------------------------------------------
	| Calculations
	|--------------------------------------------------------------------------
	| Functions related to calculations.
	*/
	/**
	 * Increment quantity.
	 *
	 * @param int $increment Number to increment
	 *
	 * @since 1.1.0
	 */
	public function increment_quantity( $increment = 1 ) {
		$this->set_quantity( $this->get_quantity() + $increment );
	}

	/**
	 * Calculate total.
	 *
	 * @since 1.1.0
	 */
	public function calculate_total() {
		$subtotal         = $this->get_price() * $this->get_quantity();
		$discount         = $this->get_discount();
		$subtotal_for_tax = $subtotal - $discount;
		$tax_rate         = ( $this->get_tax_rate() / 100 );
		$total_tax        = eaccounting_calculate_tax( $subtotal_for_tax, $tax_rate );

		if ( 'tax_subtotal_rounding' !== eaccounting()->settings->get( 'tax_subtotal_rounding', 'tax_subtotal_rounding' ) ) {
			$total_tax = eaccounting_format_decimal( $total_tax, 2 );
		}
		if ( eaccounting_prices_include_tax() ) {
			$subtotal -= $total_tax;
		}
		$total = $subtotal - $discount + $total_tax;
		if ( $total < 0 ) {
			$total = 0;
		}

		$this->set_subtotal( $subtotal );
		$this->set_tax( $total_tax );
		$this->set_total( $total );

	}
}
