<?php
/**
 * Handle the Item object.
 *
 * @package     EverAccounting
 * @class       Item
 * @version     1.2.1
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Item object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 * @property int $id
 * @property string $name
 * @property string $sku
 * @property string $description
 * @property float $sale_price
 * @property float $purchase_price
 * @property float $quantity
 * @property int $category_id
 * @property float $sales_tax
 * @property int $purchase_tax
 * @property float $thumbnail_id
 * @property boolean $enabled
 * @property int $creator_id
 * @property string $date_created
 */
class Item extends Data {
	/**
	 * Item data container.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $data = array(
		'name'           => '',
		'sku'            => '',
		'thumbnail_id'   => null,
		'description'    => '',
		'sale_price'     => 0.0000,
		'purchase_price' => 0.0000,
		'quantity'       => 1,
		'category_id'    => null,
		'sales_tax'      => 0,
		'purchase_tax'   => 0,
		'enabled'        => 1,
		'creator_id'     => null,
		'date_created'   => null,
	);

	/**
	 * Item constructor.
	 *
	 * Get the item if id is passed, otherwise the item is new and empty.
	 *
	 * @param int|object|array|Item $item object to read.
	 *
	 * @since 1.1.0
	 */
	public function __construct( $item = 0 ) {
		parent::__construct();
		if ( $item instanceof self ) {
			$this->set_id( $item->get_id() );
		} elseif ( is_object( $item ) && ! empty( $item->id ) ) {
			$this->set_id( $item->id );
		} elseif ( is_array( $item ) && ! empty( $item['id'] ) ) {
			$this->set_props( $item );
		} elseif ( is_numeric( $item ) ) {
			$this->set_id( $item );
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
	| Methods which create, read, update and delete items from the database.
	|
	*/

	/**
	 * Retrieve the object from database instance.
	 *
	 * @param int    $item_id Item id.
	 * @param string $field Database field.
	 *
	 * @return object|false Object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	static function get_raw( $item_id, $field = 'id' ) {
		global $wpdb;

		$item_id = (int) $item_id;
		if ( ! $item_id ) {
			return false;
		}

		$item = wp_cache_get( $item_id, 'ea_items' );

		if ( ! $item ) {
			$item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_items WHERE id = %d LIMIT 1", $item_id ) );

			if ( ! $item ) {
				return false;
			}

			wp_cache_add( $item->id, $item, 'ea_items' );
		}

		return apply_filters( 'eaccounting_item_raw_item', $item );
	}

	/**
	 *  Insert an item in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @param array $fields An array of database fields and type.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.1.0
	 */
	protected function insert( $fields ) {
		global $wpdb;
		$data_arr = $this->to_array();
		$data     = wp_array_slice_assoc( $data_arr, array_keys( $fields ) );
		$format   = wp_array_slice_assoc( $fields, array_keys( $data ) );
		$data     = wp_unslash( $data );

		// Bail if nothing to save
		if ( empty( $data ) ) {
			return true;
		}

		/**
		 * Fires immediately before an item is inserted in the database.
		 *
		 * @param array $data Item data to be inserted.
		 * @param string $data_arr Sanitized item data.
		 * @param Item $item Item object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_item', $data, $data_arr, $this );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_items', $data, $format ) ) {
			return new \WP_Error( 'eaccounting_item_db_insert_error', __( 'Could not insert item into the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		$this->set_id( $wpdb->insert_id );

		/**
		 * Fires immediately after an item is inserted in the database.
		 *
		 * @param int $item_id Item id.
		 * @param array $data Item has been inserted.
		 * @param array $data_arr Sanitized item data.
		 * @param Item $item Item object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_insert_item', $this->id, $data, $data_arr, $this );

		return true;
	}

	/**
	 *  Update an object in the database.
	 *
	 * This method is not meant to call publicly instead call save
	 * which will conditionally decide which method to call.
	 *
	 * @param array $fields An array of database fields and type.
	 *
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 * @since 1.1.0
	 */
	protected function update( $fields ) {
		global $wpdb;
		$changes = $this->get_changes();
		$data    = wp_array_slice_assoc( $changes, array_keys( $fields ) );
		$format  = wp_array_slice_assoc( $fields, array_keys( $data ) );
		$data    = wp_unslash( $data );
		// Bail if nothing to save
		if ( empty( $data ) ) {
			return true;
		}

		/**
		 * Fires immediately before an existing item is updated in the database.
		 *
		 * @param int $item_id Item id.
		 * @param array $data Item data.
		 * @param array $changes The data will be updated.
		 * @param Item $item Item object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_item', $this->get_id(), $this->to_array(), $changes, $this );

		if ( false === $wpdb->update( $wpdb->prefix . 'ea_items', $data, [ 'id' => $this->get_id() ], $format, [ 'id' => '%d' ] ) ) {
			return new \WP_Error( 'eaccounting_item_db_update_error', __( 'Could not update item in the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing item is updated in the database.
		 *
		 * @param int $item_id Item id.
		 * @param array $data Item data.
		 * @param array $changes The data will be updated.
		 * @param Item $item Transaction object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_item', $this->get_id(), $this->to_array(), $changes, $this );

		return true;
	}

	/**
	 * Saves an object in the database.
	 *
	 * @return \WP_Error|int id on success, WP_Error on failure.
	 * @since 1.1.0
	 */
	public function save() {
		$user_id = get_current_user_id();
		$fields  = array(
			'id'             => '%d',
			'name'           => '%s',
			'sku'            => '%s',
			'description'    => '%s',
			'sale_price'     => '%.4f',
			'purchase_price' => '%.4f',
			'quantity'       => '%f',
			'category_id'    => '%d',
			'sales_tax'      => '%.4f',
			'purchase_tax'   => '%.4f',
			'thumbnail_id'   => '%d',
			'enabled'        => '%d',
			'creator_id'     => '%d',
			'date_created'   => '%s',
		);

		// Check if item name exist or not
		if ( empty( $this->get_prop( 'name' ) ) ) {
			return new \WP_Error( 'invalid_item_name', esc_html__( 'Item name is required', 'wp-ever-accounting' ) );
		}

		// Check if the sale price exists or not
		if ( empty( $this->get_prop( 'sale_price' ) ) ) {
			return new \WP_Error( 'invalid_item_sale_price', esc_html__( 'Item sale price is required', 'wp-ever-accounting' ) );
		}

		// Check if the sale price exists or not
		if ( empty( $this->get_prop( 'purchase_price' ) ) ) {
			return new \WP_Error( 'invalid_item_purchase_price', esc_html__( 'Item purchase price is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'date_created' ) ) || '0000-00-00 00:00:00' === $this->get_prop( 'date_created' ) ) {
			$this->set_date_prop( 'date_created', current_time( 'mysql' ) );
		}

		if ( empty( $this->get_prop( 'creator_id' ) ) ) {
			$this->set_prop( 'creator_id', $user_id );
		}

		if ( $this->exists() ) {
			$is_error = $this->update( $fields );
		} else {
			$is_error = $this->insert( $fields );
		}

		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		$this->apply_changes();

		// Clear cache.
		wp_cache_delete( $this->get_id(), 'ea_items' );
		wp_cache_set( 'last_changed', microtime(), 'ea_items' );

		/**
		 * Fires immediately after an item is inserted or updated in the database.
		 *
		 * @param int $item_id Item id.
		 * @param Item $item Item object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_saved_item', $this->get_id(), $this );

		return $this->get_id();
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
		 * Filters whether an item delete should take place.
		 *
		 * @param bool|null $delete Whether to go forward with deletion.
		 * @param int $item_id Item id.
		 * @param array $data Item data array.
		 * @param Item $item Transaction object.
		 *
		 * @since 1.2.1
		 */
		$check = apply_filters( 'eaccounting_check_delete_item', null, $this->get_id(), $data, $this );
		if ( null !== $check ) {
			return $check;
		}

		/**
		 * Fires before an item is deleted.
		 *
		 * @param int $item_id Item id.
		 * @param array $data Item data array.
		 * @param Item $item Item object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_delete_item', $this->get_id(), $data, $this );

		$result = $wpdb->delete( $wpdb->prefix . 'ea_items', array( 'id' => $this->get_id() ) );
		if ( ! $result ) {
			return false;
		}

		/**
		 * Fires after an item is deleted.
		 *
		 * @param int $item_id Item id.
		 * @param array $data Item data array.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_delete_item', $this->get_id(), $data );

		// Clear object.
		wp_cache_delete( $this->get_id(), 'ea_items' );
		wp_cache_set( 'last_changed', microtime(), 'ea_items' );
		$this->set_id( 0 );
		$this->set_defaults();

		return $data;
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
	 * Ser item name
	 *
	 * @param string $name Item name.
	 *
	 * @since 1.1.0
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', eaccounting_clean( $name ) );
	}

	/**
	 * Set item sku
	 *
	 * @param string $sku Item SKU
	 *
	 * @since 1.1.0
	 */
	public function set_sku( $sku ) {
		$this->set_prop( 'sku', eaccounting_clean( $sku ) );
	}

	/**
	 * Set item thumbnail id
	 * @param int $thumbnail_id Thumbnail id.
	 *
	 * @since 1.1.0
	 */
	public function set_thumbnail_id( $thumbnail_id ) {
		$this->set_prop( 'thumbnail_id', absint( $thumbnail_id ) );
	}

	/**
	 * Set item descriptions
	 *
	 * @param string $description Item description.
	 *
	 * @since 1.1.0
	 */
	public function set_description( $description ) {
		$this->set_prop( 'description', sanitize_textarea_field( $description ) );
	}

	/**
	 * Set item sale price
	 *
	 * @param float $sale_price Sale price
	 *
	 * @since 1.1.0
	 */
	public function set_sale_price( $sale_price ) {
		$this->set_prop( 'sale_price', (float) $sale_price );
	}

	/**
	 * Set item purchase price
	 *
	 * @param float $purchase_price Purchase price.
	 *
	 * @since 1.1.0
	 */
	public function set_purchase_price( $purchase_price ) {
		$this->set_prop( 'purchase_price', (float) $purchase_price );
	}

	/**
	 * Set item quantity
	 *
	 * @param float $quantity Item quantity
	 *
	 * @since 1.1.0
	 */
	public function set_quantity( $quantity ) {
		$this->set_prop( 'quantity', absint( $quantity ) );
	}

	/**
	 * Set item category id
	 *
	 * @param int $category_id Item category id
	 *
	 * @since 1.1.0
	 */
	public function set_category_id( $category_id ) {
		$this->set_prop( 'category_id', absint( $category_id ) );
	}

	/**
	 * Set sale tax
	 *
	 * @param float $sales_tax Tax amount
	 *
	 * @since 1.1.0
	 */
	public function set_sales_tax( $sales_tax ) {
		$this->set_prop( 'sales_tax', (float) $sales_tax );
	}

	/**
	 * Set purchase tax
	 *
	 * @param float $purchase_tax Tax amount
	 *
	 * @since 1.1.0
	 */
	public function set_purchase_tax( $purchase_tax ) {
		$this->set_prop( 'purchase_tax', (float) $purchase_tax );
	}

	/**
	 * Set object status.
	 *
	 * @param int $enabled Enabled or not
	 *
	 * @since 1.0.2
	 */
	public function set_enabled( $enabled ) {
		$this->set_prop( 'enabled', (int) $enabled );
	}

	/**
	 * Set object creator id.
	 *
	 * @param int $creator_id Creator id
	 *
	 * @since 1.0.2
	 */
	public function set_creator_id( $creator_id = null ) {
		if ( null === $creator_id ) {
			$creator_id = get_current_user_id();
		}
		$this->set_prop( 'creator_id', absint( $creator_id ) );
	}

	/**
	 * Set object created date.
	 *
	 * @param string $date Creation date
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
	| Getters
	|--------------------------------------------------------------------------
	|
	| Functions for getting item data. Getter methods wont change anything unless
	| just returning from the props.
	|
	*/


	/*
	|--------------------------------------------------------------------------
	| Additional methods
	|--------------------------------------------------------------------------
	|
	| Does extra thing as helper functions.
	|
	*/
}
