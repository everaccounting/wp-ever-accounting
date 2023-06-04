<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Category.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class Term extends Model {
	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const TABLE_NAME = 'ea_terms';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'term';

	/**
	 * Cache group.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const CACHE_GROUP = 'ea_terms';

	/**
	 * Meta type declaration for the object.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const META_TYPE = 'ea_term';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'name'        => '',
		'description' => '',
		'group'       => 'category',
		'status'      => 'active',
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
	 * Get the category group.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return mixed|null
	 */
	public function get_group( $context = 'edit' ) {
		return $this->get_prop( 'group', $context );
	}

	/**
	 * Set the category group.
	 *
	 * @param string $value Category group.
	 *
	 * @since 1.0.2
	 */
	public function set_group( $value ) {
		if ( ! array_key_exists( $value, eac_get_term_groups() ) ) {
			$value = '';
		}
		$this->set_prop( 'group', eac_clean( $value ) );
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
	 * Get associated parent id.
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
	 * Is the category active?
	 *
	 * @since 1.0.2
	 *
	 * @return bool
	 */
	public function is_active() {
		return 'active' === $this->get_status();
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
			return new \WP_Error( 'missing_required', __( 'Name is required.', 'wp-ever-accounting' ) );
		}

		// Type check.
		if ( empty( $this->get_group() ) ) {
			return new \WP_Error( 'missing_required', __( 'Group type is required.', 'wp-ever-accounting' ) );
		}

		// if the group is not valid.
		if ( ! array_key_exists( $this->get_group(), eac_get_term_groups() ) ) {
			return new \WP_Error( 'invalid_group', __( 'Invalid group type.', 'wp-ever-accounting' ) );
		}

		// Duplicate check.
		$term = $this->get(
			array(
				'name'  => $this->get_name(),
				'group' => $this->get_group(),
			)
		);

		if ( $term && $term->get_id() !== $this->get_id() ) {
			return new \WP_Error( 'duplicate', __( 'Duplicate name is not allowed.', 'wp-ever-accounting' ) );
		}

		return parent::save();
	}

	/*
	|--------------------------------------------------------------------------
	| Conditionals methods
	|--------------------------------------------------------------------------
	| Methods that check an object's status, typically based on internal or meta data.
	*/
	/**
	 * Is the group is a specific group.
	 *
	 * @param string $group Group name.
	 *
	 * @since 1.0.2
	 */
	public function is_group( $group ) {
		return $this->get_group() === $group;
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
	 * @since 1.1.6
	 * @return string
	 */
	public function get_formatted_name() {
		return sprintf( '%s (#%d)', $this->get_name(), $this->get_id() );
	}
}
