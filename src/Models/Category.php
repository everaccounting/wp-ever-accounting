<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Category.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class Category extends Model {
	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const TABLE_NAME = 'ea_categories';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'category';

	/**
	 * Cache group.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const CACHE_GROUP = 'ea_categories';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'name'        => '',
		'type'        => '',
		'description' => '',
		'color'       => '',
		'status'      => 'active',
		'updated_at'  => '',
		'created_at'  => '',
	);

	/*
	|--------------------------------------------------------------------------
	| Getters and Setters
	|--------------------------------------------------------------------------
	|
	| Methods for getting and setting data.
	|
	*/

	/**
	 * Get category name.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Set the category name.
	 *
	 * @param string $value Category name.
	 *
	 * @since 1.0.2
	 */
	public function set_name( $value ) {
		$this->set_prop( 'name', eac_clean( $value ) );
	}

	/**
	 * Get the category type.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_type( $context = 'edit' ) {
		return $this->get_prop( 'type', $context );
	}

	/**
	 * Set the category type.
	 *
	 * @param string $value Category type.
	 *
	 * @since 1.0.2
	 */
	public function set_type( $value ) {
		if ( array_key_exists( $value, eaccounting_get_category_types() ) ) {
			$this->set_prop( 'type', eac_clean( $value ) );
		}
	}

	/**
	 * Get the category description.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_description( $context = 'edit' ) {
		return $this->get_prop( 'description', $context );
	}

	/**
	 * Set the category description.
	 *
	 * @param string $value Category description.
	 *
	 * @since 1.0.2
	 */
	public function set_description( $value ) {
		$this->set_prop( 'description', eac_clean( $value ) );
	}

	/**
	 * Get the category color.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_color( $context = 'edit' ) {
		return $this->get_prop( 'color', $context );
	}

	/**
	 * Set the category color.
	 *
	 * @param string $value Category color.
	 *
	 * @since 1.0.2
	 */
	public function set_color( $value ) {
		$this->set_prop( 'color', eac_clean( $value ) );
	}

	/**
	 * Get the category status.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_status( $context = 'edit' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Set the category status.
	 *
	 * @param string $value Category status.
	 *
	 * @since 1.0.2
	 */
	public function set_status( $value ) {
		if ( in_array( $value, array( 'active', 'inactive' ), true ) ) {
			$this->set_prop( 'status', $value );
		}
	}

	/**
	 * Is the category active?
	 *
	 * @since 1.0.2
	 *
	 * @return bool
	 */
	public function is_active() {
		return 'active' === $this->get_status();
	}

	/**
	 * get the date updated.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_updated_at( $context = 'edit' ) {
		return $this->get_prop( 'updated_at', $context );
	}

	/**
	 * set the date updated.
	 *
	 * @param string $updated_at date updated.
	 */
	public function set_updated_at( $updated_at ) {
		$this->set_date_prop( 'updated_at', $updated_at );
	}

	/**
	 * get the date created.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_created_at( $context = 'edit' ) {
		return $this->get_prop( 'created_at', $context );
	}

	/**
	 * set the date created.
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
	 * Saves an object in the database.
	 *
	 * @since 1.0.0
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function save() {
		// Required fields check.
		if ( empty( $this->get_name() ) ) {
			return new \WP_Error( 'missing_required', __( 'Category name is required.', 'wp-ever-accounting' ) );
		}

		// Type check.
		if ( empty( $this->get_type() ) ) {
			return new \WP_Error( 'missing_required', __( 'Category type is required.', 'wp-ever-accounting' ) );
		}

		// Duplicate check.
		$categories = $this->query(
			array(
				'name' => $this->get_name(),
				'type' => $this->get_type(),
			)
		);
		foreach ( $categories as $category ) {
			if ( $category->get_id() !== $this->get_id() ) {
				return new \WP_Error( 'duplicate-error', __( 'Category name already exists.', 'wp-ever-accounting' ) );
			}
		}

		// If color is not set, set a random hex color.
		if ( empty( $this->get_color() ) ) {
			$this->set_color( '#' . substr( md5( wp_rand() ), 0, 6 ) );
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
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/

	/**
	 * Get formatted name.
	 *
	 * @return string
	 * @since 1.1.6
	 */
	public function get_formatted_name() {
		return sprintf( '%s (#%d)', $this->get_name(), $this->get_id() );
	}
}
