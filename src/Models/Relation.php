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
		'id_1'       => 0,
		'id_2'       => 0,
		'type_1'     => '',
		'type_2'     => '',
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
	public function get_id_1( $context = 'edit' ) {
		return $this->get_prop( 'id_1', $context );
	}

	/**
	 * Set the first item id.
	 *
	 * @param int $id_1 ID of the first item.
	 *
	 * @since 1.1.0
	 */
	public function set_name( $id_1 ) {
		$this->set_prop( 'id_1', absint( $id_1 ) );
	}

	/**
	 * Get the second item id.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return int
	 */
	public function get_id_2( $context = 'edit' ) {
		return $this->get_prop( 'id_2', $context );
	}

	/**
	 * Set the second item id.
	 *
	 * @param int $id_2 ID of the second item.
	 *
	 * @since 1.1.0
	 */
	public function set_id_2( $id_2 ) {
		$this->set_prop( 'id_2', absint( $id_2 ) );
	}

	/**
	 * Get the first item type.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_type_1( $context = 'edit' ) {
		return $this->get_prop( 'type_1', $context );
	}

	/**
	 * Set the first item type.
	 *
	 * @param string $type_1 Type of the first item.
	 *
	 * @since 1.1.0
	 */
	public function set_type_1( $type_1 ) {
		$this->set_prop( 'type_1', $type_1 );
	}

	/**
	 * Get the second item type.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_type_2( $context = 'edit' ) {
		return $this->get_prop( 'type_2', $context );
	}

	/**
	 * Set the second item type.
	 *
	 * @param string $type_2 Type of the second item.
	 *
	 * @since 1.1.0
	 */
	public function set_type_2( $type_2 ) {
		$this->set_prop( 'type_2', $type_2 );
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

		if ( empty( $this->get_id_1() ) ) {
			return new \WP_Error( 'missing_required', __( 'Missing required id_1.', 'easy-appointments' ) );
		}

		if ( empty( $this->get_id_2() ) ) {
			return new \WP_Error( 'missing_required', __( 'Missing required id_2.', 'easy-appointments' ) );
		}

		if ( empty( $this->get_type_1() ) ) {
			return new \WP_Error( 'missing_required', __( 'Missing required type_1.', 'easy-appointments' ) );
		}

		if ( empty( $this->get_type_2() ) ) {
			return new \WP_Error( 'missing_required', __( 'Missing required type_2.', 'easy-appointments' ) );
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
	 * Replace related items.
	 *
	 * @param int    $id_1    ID of the first item.
	 * @param int    $id_2    ID of the second item.
	 * @param string $type_2  Type of the second item.
	 */
	public static function replace( $id_1, $ids_2, $type_2 ) {

	}
}
