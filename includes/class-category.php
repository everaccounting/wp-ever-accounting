<?php
/**
 * Category data handler class.
 *
 * @version     1.0.2
 * @package     Ever_Accounting
 * @class       Category
 */

namespace Ever_Accounting;

defined( 'ABSPATH' ) || exit;

/**
 * Category class.
 */
class Category extends Abstracts\Data {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $object_type = 'category';

	/**
	 * Table name.
	 *
	 * @since 1.1.3
	 * @var string
	 */
	protected $table = 'ea_categories';

	/**
	 * Cache group.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $cache_group = 'ea_categories';


	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.1.3
	 * @var array
	 */
	protected $core_data = [
		'name'         => '',
		'type'         => '',
		'color'        => '',
		'enabled'      => 1,
		'date_created' => null,
	];

	/**
	 * Category constructor.
	 *
	 * @param int|category|object|null $category  category instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $category = 0 ) {
		// Call early so default data is set.
		parent::__construct();

		if ( is_numeric( $category ) && $category > 0 ) {
			$this->set_id( $category );
		} elseif ( $category instanceof self ) {
			$this->set_id( absint( $category->get_id() ) );
		} elseif ( ! empty( $category->ID ) ) {
			$this->set_id( absint( $category->ID ) );
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

		$requires = [ 'name', 'type' ];
		foreach ( $requires as $required ) {
			if ( empty( $this->$required ) ) {
				return new \WP_Error( 'missing_required_params', sprintf( __( 'Category %s is required.', 'wp-ever-accounting' ), $required ) );
			}
		}

		$duplicate = Categories::get_by_name( $this->name, $this->object_type );

		if ( $duplicate && $duplicate->exists() && $duplicate->get_id() !== $this->get_id() ) {
			return new \WP_Error( 'duplicate_category', __( 'Category already exists', 'wp-ever-accounting' ) );
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
		 * Fires immediately after a category is inserted or updated in the database.
		 *
		 * @param int $id Category id.
		 * @param array $data Category data array.
		 * @param Category $category Category object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eaccounting_saved_' . $this->object_type, $this->get_id(), $this );

		return $this->get_id();
	}
}