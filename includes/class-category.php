<?php
/**
 * Handle the Category object.
 *
 * @package     EverAccounting
 * @class       Category
 * @version     1.2.1
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Category object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 * @property string $name
 * @property string $type
 * @property string $color
 * @property boolean $enabled
 * @property string $date_created
 */
class Category extends Data {
	/**
	 * Category data container.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $data = array(
		'name'         => '',
		'type'         => '',
		'color'        => '',
		'enabled'      => 1,
		'date_created' => null,
	);

	/**
	 * Category constructor.
	 *
	 * Get the category if ID is passed, otherwise the category is new and empty.
	 *
	 * @param int|object|Category $category object to read.
	 *
	 * @since 1.1.0
	 */
	public function __construct( $category ) {
		parent::__construct();
		if ( $category instanceof self ) {
			$this->set_id( $category->get_id() );
		} elseif ( is_object( $category ) && ! empty( $category->id ) ) {
			$this->set_id( $category->id );
		} elseif ( is_array( $category ) && ! empty( $category['id'] ) ) {
			$this->set_props( $category );
		} elseif ( is_numeric( $category ) ) {
			$this->set_id( $category );
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
	 * @param int $category_id Category id.
	 * @param string $field Database field.
	 *
	 * @return object|false Object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	public static function get_raw( $category_id, $field = 'id' ) {
		global $wpdb;

		$category_id = (int) $category_id;
		if ( ! $category_id ) {
			return false;
		}

		$category = wp_cache_get( $category_id, 'ea_categories' );

		if ( ! $category ) {
			$category = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_categories WHERE id = %d LIMIT 1", $category_id ) );

			if ( ! $category ) {
				return false;
			}

			wp_cache_add( $category->id, $category, 'ea_categories' );
		}

		return apply_filters( 'eaccounting_category_raw_category', $category );
	}

	/**
	 *  Insert a category in the database.
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
		 * Fires immediately before a category is inserted in the database.
		 *
		 * @param array $data Category data to be inserted.
		 * @param string $data_arr Sanitized category data.
		 * @param Category $category Category object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_category', $data, $data_arr, $this );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_categories', $data, $format ) ) {
			return new \WP_Error( 'eaccounting_category_db_insert_error', __( 'Could not insert category into the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		$this->set_id( $wpdb->insert_id );

		/**
		 * Fires immediately after an category is inserted in the database.
		 *
		 * @param int $category_id Category id.
		 * @param array $data Category has been inserted.
		 * @param array $data_arr Sanitized category data.
		 * @param Category $category Category object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_insert_category', $this->id, $data, $data_arr, $this );

		return true;
	}

	/**
	 *  Update a category in the database.
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
		 * Fires immediately before an existing category is updated in the database.
		 *
		 * @param int $category_id Category id.
		 * @param array $data Category data.
		 * @param array $changes The data will be updated.
		 * @param Category $category Category object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_category', $this->get_id(), $this->to_array(), $changes, $this );

		if ( false === $wpdb->update( $wpdb->prefix . 'ea_categories', $data, [ 'id' => $this->get_id() ], $format, [ 'id' => '%d' ] ) ) {
			return new \WP_Error( 'eaccounting_category_db_update_error', __( 'Could not update category in the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing category is updated in the database.
		 *
		 * @param int $category_id Category id.
		 * @param array $data Category data.
		 * @param array $changes The data will be updated.
		 * @param Category $category Transaction object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_category', $this->get_id(), $this->to_array(), $changes, $this );

		return true;
	}

	/**
	 * Saves a category in the database.
	 *
	 * @return \WP_Error|int id on success, WP_Error on failure.
	 * @since 1.1.0
	 */
	public function save() {
		$fields = array(
			'id'           => '%d',
			'name'         => '%s',
			'type'         => '%s',
			'color'        => '%s',
			'enabled'      => '%d',
			'date_created' => '%s',
		);

		// Check if category name exist or not
		if ( empty( $this->get_prop( 'name' ) ) ) {
			return new \WP_Error( 'invalid_category_name', esc_html__( 'Category name is required', 'wp-ever-accounting' ) );
		}

		// Check if category type exist or not
		if ( empty( $this->get_prop( 'type' ) ) ) {
			return new \WP_Error( 'invalid_category_type', esc_html__( 'Category type is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_prop( 'date_created' ) ) || '0000-00-00 00:00:00' === $this->get_prop( 'date_created' ) ) {
			$this->set_date_prop( 'date_created', current_time( 'mysql' ) );
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
		wp_cache_delete( $this->get_id(), 'ea_categories' );
		wp_cache_set( 'last_changed', microtime(), 'ea_categories' );

		/**
		 * Fires immediately after a category is inserted or updated in the database.
		 *
		 * @param int $category_id Item id.
		 * @param Item $category Item object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_saved_category', $this->get_id(), $this );

		return $this->get_id();
	}


	/**
	 * Deletes the category from database.
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
		 * Filters whether a category delete should take place.
		 *
		 * @param bool|null $delete Whether to go forward with deletion.
		 * @param int $category_id Category id.
		 * @param array $data Category data array.
		 * @param Category $category Transaction object.
		 *
		 * @since 1.2.1
		 */
		$check = apply_filters( 'eaccounting_check_delete_category', null, $this->get_id(), $data, $this );
		if ( null !== $check ) {
			return $check;
		}

		/**
		 * Fires before a category is deleted.
		 *
		 * @param int $category_id Category id.
		 * @param array $data Category data array.
		 * @param Category $category Category object.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_delete_category', $this->get_id(), $data, $this );

		$result = $wpdb->delete( $wpdb->prefix . 'ea_categories', array( 'id' => $this->get_id() ) );
		if ( ! $result ) {
			return false;
		}

		/**
		 * Fires after a category is deleted.
		 *
		 * @param int $category_id Category id.
		 * @param array $data Category data array.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_delete_category', $this->get_id(), $data );

		// Clear object.
		wp_cache_delete( $this->get_id(), 'ea_categories' );
		wp_cache_set( 'last_changed', microtime(), 'ea_categories' );
		$this->set_id( 0 );
		$this->set_defaults();

		return $data;
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Functions for setting category data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
	*/

	/**
	 * Set the category name.
	 *
	 * @param string $value Category Name
	 *
	 * @since 1.0.2
	 */
	public function set_name( $value ) {
		$this->set_prop( 'name', eaccounting_clean( $value ) );
	}

	/**
	 * Set the category type.
	 *
	 * @param string $value Category type
	 *
	 * @since 1.0.2
	 */
	public function set_type( $value ) {
		if ( array_key_exists( $value, eaccounting_get_category_types() ) ) {
			$this->set_prop( 'type', eaccounting_clean( $value ) );
		}
	}

	/**
	 * Set the category color.
	 *
	 * @param string $value Category color
	 *
	 * @since 1.0.2
	 */
	public function set_color( $value ) {
		$this->set_prop( 'color', eaccounting_clean( $value ) );
	}

	/**
	 * Set object status.
	 *
	 * @param int $enabled Category enabled or not
	 *
	 * @since 1.0.2
	 */
	public function set_enabled( $enabled ) {
		$this->set_prop( 'enabled', (int) $enabled );
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
}
