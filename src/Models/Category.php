<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Category model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int    $id ID of the category.
 * @property string $name Name of the category.
 * @property string $type Type of the category.
 * @property string $color Color of the category.
 * @property bool   $enabled Whether the category is enabled or not.
 * @property string $date_created Date created of the category.
 */
class Category extends Model {
	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_categories';

	/**
	 * Table columns.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'name',
		'type',
		'color',
		'enabled',
		'date_created',
	);

	/**
	 * Model's data container.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $attributes = array(
		'enabled' => 1,
	);

	/**
	 * Model's casts data.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	protected $casts = array(
		'id'           => 'int',
		'name'         => 'sanitize_text',
		'type'         => 'sanitize_key',
		'enabled'      => 'bool',
		'date_created' => 'datetime',
	);

	/**
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 */
	public function save() {
		if ( empty( $this->name ) ) {
			return new \WP_Error( 'missing_required', __( 'Category name is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->type ) ) {
			return new \WP_Error( 'missing_required', __( 'Category type is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->color ) ) {
			// make a random hex color.
			$this->color = '#' . dechex( mt_rand( 0, 0xFFFFFF ) );
		}

		if ( empty( $this->date_created ) ) {
			$this->date_created = current_time( 'mysql' );
		}

		// Duplicate check. Same type and name should not exist.
		$existing = $this->find_where( array(
			'type' => $this->type,
			'name' => $this->name,
		) );
		if ( ! empty( $existing ) && $existing->id !== $this->id ) {
			return new \WP_Error( 'duplicate', __( 'Category with same name and type already exists.', 'wp-ever-accounting' ) );
		}

		return parent::save();
	}

	/*
	|--------------------------------------------------------------------------
	| Getters & Setters
	|--------------------------------------------------------------------------
	| Below are the getters and setters for the model.
	*/
	/**
	 * Set the type of the category.
	 *
	 * @param string $type Type of the category.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_type_attribute( $type ) {
		var_dump($type);
		var_dump(eac_get_category_types());
		if ( array_key_exists( $type, eac_get_category_types() ) ) {
			$this->attributes['type'] = $type;
		}
	}
}
