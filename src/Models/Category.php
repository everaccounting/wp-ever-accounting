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
	 * @since 1.0.0
	 * @var string
	 */
	protected $table_name = 'ea_categories';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $object_type = 'category';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'name'         => '',
		'type'         => '',
		'color'        => '',
		'status'       => 'active',
		'date_created' => null,
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
		$this->set_prop( 'name', eaccounting_clean( $value ) );
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
			$this->set_prop( 'type', eaccounting_clean( $value ) );
		}
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
		$this->set_prop( 'color', eaccounting_clean( $value ) );
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
	 * Set the category enabled status.
	 *
	 * @param string $value Category enabled status.
	 *
	 * @since 1.0.2
	 */
	public function set_enabled( $value ) {
		$this->set_prop( 'enabled', $this->string_to_int( $value ) );
	}

	/**
	 * Is the category enabled?
	 *
	 * @since 1.0.2
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return 'active' === $this->get_status();
	}

	/**
	 * Is the category active?
	 *
	 * @since 1.0.2
	 *
	 * @return bool
	 */
	public function is_active() {
		return $this->is_enabled();
	}

	/**
	 * Get the category date created.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_date_created( $context = 'edit' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * Set the category date created.
	 *
	 * @param string $value Category date created.
	 *
	 * @since 1.0.2
	 */
	public function set_date_created( $value ) {
		$this->set_prop( 'date_created', eaccounting_clean( $value ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Helpers
	|--------------------------------------------------------------------------
	|
	| Helper methods.
	|
	*/
	/**
	 * Sanitizes the data.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true
	 */
	protected function sanitize_data() {
		// Required fields check.
		if ( empty( $this->get_name() ) ) {
			return new \WP_Error( 'missing-required', __( 'Category name is required.', 'wp-ever-accounting' ) );
		}

		// Type check.
		if ( empty( $this->get_type() ) ) {
			return new \WP_Error( 'missing-required', __( 'Category type is required.', 'wp-ever-accounting' ) );
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

		// If date created is not set, set it to now.
		if ( empty( $this->get_date_created() ) ) {
			$this->set_date_created( current_time( 'mysql' ) );
		}

		return parent::sanitize_data();
	}
}
