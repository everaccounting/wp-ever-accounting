<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Relation.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class Relation extends Model {

	/**
	 * Table name.
	 *
	 * This is also used as table alias.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const TABLE_NAME = 'ea_relationships';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const OBJECT_TYPE = 'relation';

	/**
	 * Cache group.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const CACHE_GROUP = 'ea_relationships';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'id1'        => 0,
		'id2'        => 0,
		'type1'      => '',
		'type2'      => '',
		'updated_at' => null,
		'created_at' => null,
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
	 * Get the first item id.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return int
	 */
	public function get_id1( $context = 'edit' ) {
		return $this->get_prop( 'id1', $context );
	}

	/**
	 * Set the first item id.
	 *
	 * @param int $id1 ID of the first item.
	 *
	 * @since 1.1.0
	 */
	public function set_name( $id1 ) {
		$this->set_prop( 'id1', absint( $id1 ) );
	}

	/**
	 * Get the second item id.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return int
	 */
	public function get_id2( $context = 'edit' ) {
		return $this->get_prop( 'id2', $context );
	}

	/**
	 * Set the second item id.
	 *
	 * @param int $id2 ID of the second item.
	 *
	 * @since 1.1.0
	 */
	public function set_id2( $id2 ) {
		$this->set_prop( 'id2', absint( $id2 ) );
	}

	/**
	 * Get the first item type.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_type1( $context = 'edit' ) {
		return $this->get_prop( 'type1', $context );
	}

	/**
	 * Set the first item type.
	 *
	 * @param string $type1 Type of the first item.
	 *
	 * @since 1.1.0
	 */
	public function set_type1( $type1 ) {
		$this->set_prop( 'type1', $type1 );
	}

	/**
	 * Get the second item type.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_type2( $context = 'edit' ) {
		return $this->get_prop( 'type2', $context );
	}

	/**
	 * Set the second item type.
	 *
	 * @param string $type2 Type of the second item.
	 *
	 * @since 1.1.0
	 */
	public function set_type2( $type2 ) {
		$this->set_prop( 'type2', $type2 );
	}


	/**
	 * Get the date updated.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_updated_at( $context = 'edit' ) {
		return $this->get_prop( 'updated_at', $context );
	}

	/**
	 * Set the date updated.
	 *
	 * @param string $updated_at date updated.
	 */
	public function set_updated_at( $updated_at ) {
		$this->set_date_prop( 'updated_at', $updated_at );
	}

	/**
	 * Get the date created.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_created_at( $context = 'edit' ) {
		return $this->get_prop( 'created_at', $context );
	}

	/**
	 * Set the date created.
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

		if ( empty( $this->get_id1() ) ) {
			return new \WP_Error( 'missing_required', __( 'Missing required id1.', 'easy-appointments' ) );
		}

		if ( empty( $this->get_id2() ) ) {
			return new \WP_Error( 'missing_required', __( 'Missing required id2.', 'easy-appointments' ) );
		}

		if ( empty( $this->get_type1() ) ) {
			return new \WP_Error( 'missing_required', __( 'Missing required type1.', 'easy-appointments' ) );
		}

		if ( empty( $this->get_type2() ) ) {
			return new \WP_Error( 'missing_required', __( 'Missing required type2.', 'easy-appointments' ) );
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
}
