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
 * @property string $type Type of the category.
 * @property string $name Name of the category.
 * @property string $description Description of the category.
 * @property string $status Status of the category.
 * @property string $date_created Date created of the category.
 * @property string $date_updated Date updated of the category.
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
		'type',
		'name',
		'description',
		'status',
	);

	/**
	 * The model's attributes.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $attributes = array(
		'status' => 'active',
	);

	/**
	 * The attributes that should be cast.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array(
		'id'          => 'int',
		'type'        => 'sanitize_key',
		'name'        => 'sanitize_text',
		'description' => 'sanitize_textarea',
	);

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $appends = array(
		'formatted_name',
	);

	/**
	 * Searchable attributes.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $searchable = array(
		'name',
		'type',
		'description',
	);

	/**
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $timestamps = true;

	/*
	|--------------------------------------------------------------------------
	| Attributes & Relations
	|--------------------------------------------------------------------------
	| Define the attributes and relations of the model.
	*/

	/**
	 * Set the type of the category.
	 *
	 * @param string $type Type of the category.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function set_type_attribute( $type ) {
		$type = ! in_array( $type, eac_get_category_types(), true ) ? 'income' : $type;
		$this->set_attribute_value( 'type', $type );
	}

	/**
	 * Get the formatted name of the category.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_name_attribute() {
		return sprintf( '%s (#%d)', $this->name, $this->id );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	| Methods for saving, updating, and deleting objects.
	*/

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

		// Duplicate check. Same type and name should not exist.
		$existing = $this->find(
			array(
				'type' => $this->type,
				'name' => $this->name,
			)
		);
		if ( ! empty( $existing ) && $existing->id !== $this->id ) {
			return new \WP_Error( 'duplicate', __( 'Category with same name and type already exists.', 'wp-ever-accounting' ) );
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
