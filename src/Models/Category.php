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
	public $table_name = 'ea_categories';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $object_type = 'category';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'id'           => null,
		'name'         => '',
		'type'         => '',
		'description'  => '',
		'status'       => 'active',
		'date_updated' => null,
		'date_created' => null,
	);

	/**
	 * When the object is cloned, make sure meta is duplicated correctly.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		parent::__clone();
		$this->set_name( $this->get_name() . ' ' . __( '(Copy)', 'wp-ever-accounting' ) );
		$this->set_date_updated( null );
		$this->set_date_created( current_time( 'mysql' ) );
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
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 * @since 1.0.0
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
		$category = $this->get(
			array(
				'name' => $this->get_name(),
				'type' => $this->get_type(),
			)
		);
		if ( $category && $category->get_id() !== $this->get_id() ) {
			return new \WP_Error( 'duplicate-error', __( 'Category name already exists.', 'wp-ever-accounting' ) );
		}

		// If date created is not set, set it to now.
		if ( empty( $this->get_date_created() ) ) {
			$this->set_date_created( current_time( 'mysql' ) );
		}

		// If It's update, set the updated date.
		if ( $this->exists() ) {
			$this->set_date_updated( current_time( 'mysql' ) );
		}

		return parent::save();
	}

	/*
	|--------------------------------------------------------------------------
	| Getters and Setters
	|--------------------------------------------------------------------------
	|
	| Methods for getting and setting data.
	|
	*/

	/**
	 * Get id.
	 *
	 * @return int
	 * @since 1.0.0
	 */
	public function get_id() {
		return (int) $this->get_prop( 'id' );
	}

	/**
	 * Set id.
	 *
	 * @param int $id
	 *
	 * @since 1.0.0
	 */
	public function set_id( $id ) {
		$this->set_prop( 'id', absint( $id ) );
	}

	/**
	 * Get category name.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return mixed|null
	 * @since 1.0.2
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
	 * @return mixed|null
	 * @since 1.0.2
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
		if ( array_key_exists( $value, eac_get_category_types() ) ) {
			$this->set_prop( 'type', eac_clean( $value ) );
		}
	}

	/**
	 * Get the category description.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return mixed|null
	 * @since 1.0.2
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
	 * Get the category status.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @return string
	 * @since 1.0.2
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
	 * Get created via.
	 *
	 * @param  string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_created_via( $context = 'view' ) {
		return $this->get_prop( 'created_via', $context );
	}

	/**
	 * Set created via.
	 *
	 * @param string $value Created via.
	 */
	public function set_created_via( $value ) {
		$this->set_prop( 'created_via', $value );
	}

	/**
	 * get the date updated.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_date_updated( $context = 'edit' ) {
		return $this->get_prop( 'date_updated', $context );
	}

	/**
	 * set the date updated.
	 *
	 * @param string $date date updated.
	 */
	public function set_date_updated( $date ) {
		$this->set_date_prop( 'date_updated', $date );
	}

	/**
	 * get the date created.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_date_created( $context = 'edit' ) {
		return $this->get_prop( 'date_created', $context );
	}

	/**
	 * set the date created.
	 *
	 * @param string $date date created.
	 */
	public function set_date_created( $date ) {
		$this->set_date_prop( 'date_created', $date );
	}


	/*
	|--------------------------------------------------------------------------
	| Conditionals methods
	|--------------------------------------------------------------------------
	| Methods that check an object's status, typically based on internal or meta data.
	*/

	/**
	 * Is the category active?
	 *
	 * @return bool
	 * @since 1.0.2
	 */
	public function is_active() {
		return 'active' === $this->get_status();
	}

	/**
	 * Check if order has been created via admin, checkout, or in another way.
	 *
	 * @param string $modes Created via.
	 *
	 * @since 1.5.6
	 * @return bool
	 */
	public function is_created_via( $modes ) {
		return $modes === $this->get_created_via();
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
