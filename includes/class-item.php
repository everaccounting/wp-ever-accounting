<?php
/**
 * Item data handler class.
 *
 * @version     1.0.2
 * @package     Ever_Accounting
 * @class       Item
 */

namespace Ever_Accounting;

defined( 'ABSPATH' ) || exit;

/**
 * Item class.
 */
class Item extends Abstracts\Data {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $object_type = 'item';

	/**
	 * Table name.
	 *
	 * @since 1.1.3
	 * @var string
	 */
	protected $table = 'ea_items';

	/**
	 * Cache group.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $cache_group = 'ea_items';


	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.1.3
	 * @var array
	 */
	protected $core_data = [
		'name'           => '',
		'sku'            => '',
		'thumbnail_id'   => null,
		'description'    => '',
		'sale_price'     => 0.0000,
		'purchase_price' => 0.0000,
		'quantity'       => 1,
		'category_id'    => null,
		'sales_tax'      => null,
		'purchase_tax'   => null,
		'enabled'        => 1,
		'creator_id'     => null,
		'date_created'   => null,
	];

	/**
	 * Item constructor.
	 *
	 * @param int|item|object|null $item  item instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $item = 0 ) {
		// Call early so default data is set.
		parent::__construct();

		if ( is_numeric( $item ) && $item > 0 ) {
			$this->set_id( $item );
		} elseif ( $item instanceof self ) {
			$this->set_id( absint( $item->get_id() ) );
		} elseif ( ! empty( $item->ID ) ) {
			$this->set_id( absint( $item->ID ) );
		} else {
			$this->set_object_read( true );
		}

		$this->read();
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
	 * Saves an object in the database.
	 *
	 * @return \WP_Error|int id on success, WP_Error on failure.
	 * @since 1.1.3
	 */
	public function save() {
		// check if anything missing before save.
		if ( ! $this->is_date_valid( $this->date_created ) ) {
			$this->date_created = current_time( 'mysql' );
		}

		$requires = [ 'name', 'quantity', 'sale_price', 'purchase_price' ];
		foreach ( $requires as $required ) {
			if ( empty( $this->$required ) ) {
				return new \WP_Error( 'missing_required_params', sprintf( __( 'Item %s is required.', 'wp-ever-accounting' ), $required ) );
			}
		}

		if ( $this->sale_price === $this->purchase_price ) {
			return new \WP_Error( 'duplicate_entry', __( 'Item sale price and purchase price can\'t be same.', 'wp-ever-accounting' ) );
		}

		if ( ! $this->exists() ) {
			$is_error = $this->create();
		} else {
			$is_error = $this->update();
		}

		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		$this->apply_changes();

		// Clear cache.
		wp_cache_delete( $this->get_id(), $this->cache_group );
		wp_cache_set( 'last_changed', microtime(), $this->cache_group );

		/**
		 * Fires immediately after a item is inserted or updated in the database.
		 *
		 * @param int $id Item id.
		 * @param array $data Item data array.
		 * @param Item $item Item object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'ever_accounting_saved_' . $this->object_type, $this->get_id(), $this );

		return $this->get_id();
	}
}