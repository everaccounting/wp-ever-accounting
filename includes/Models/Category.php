<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relations\HasMany;

defined( 'ABSPATH' ) || exit;

/**
 * Category model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 * @extends Model Category model.
 *
 * @property int    $id ID of the category.
 * @property string $type Type of the category.
 * @property string $name Name of the category.
 * @property string $description Description of the category.
 * @property string $status Status of the category.
 * @property string $formatted_name Formatted name of the category.
 * @property string $created_at Date created of the category.
 * @property string $updated_at Date updated of the category.
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
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $has_timestamps = true;

	/**
	 * The attributes that are searchable.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $searchable = array(
		'name',
		'type',
		'description',
	);


	/*
	|--------------------------------------------------------------------------
	| Property Definition Methods
	|--------------------------------------------------------------------------
	| This section contains static methods that define and return specific
	| property values related to the model.
	| These methods are accessible without creating an instance of the model.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get all the available type of category the plugin support.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public static function get_types() {
		$types = array(
			'item'    => esc_html__( 'Item', 'wp-ever-accounting' ),
			'payment' => esc_html__( 'Payment', 'wp-ever-accounting' ),
			'expense' => esc_html__( 'Expense', 'wp-ever-accounting' ),
		);

		return apply_filters( 'ever_accounting_category_types', $types );
	}

	/*
	|--------------------------------------------------------------------------
	| Accessors, Mutators and Relationship Methods
	|--------------------------------------------------------------------------
	| This section contains methods for getting and setting attributes (accessors
	| and mutators) as well as defining relationships between models.
	|--------------------------------------------------------------------------
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
		$this->attributes['type'] = ! array_key_exists( $type, self::get_types() ) ? 'item' : $type;
	}

	/**
	 * Get the formatted name of the category.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_name() {
		return sprintf( '%s (#%d)', $this->name, $this->id );
	}

	/**
	 * Get the transactions of the category.
	 *
	 * @since 1.0.0
	 * @return HasMany
	 */
	public function transactions() {
		return $this->has_many( Transaction::class );
	}

	/**
	 * Get the revenues of the category.
	 *
	 * @since 1.0.0
	 * @return HasMany
	 */
	public function payments() {
		return $this->has_many( Payment::class );
	}

	/**
	 * Get the expenses of the category.
	 *
	 * @since 1.0.0
	 * @return HasMany
	 */
	public function expenses() {
		return $this->has_many( Expense::class );
	}

	/**
	 * Get items of the category.
	 *
	 * @since 1.0.0
	 * @return HasMany
	 */
	public function items() {
		return $this->has_many( Item::class );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD Methods
	|--------------------------------------------------------------------------
	| This section contains methods for creating, reading, updating, and deleting
	| objects in the database.
	|--------------------------------------------------------------------------
	*/
	/**
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|static WP_Error on failure, or the object on success.
	 */
	public function save() {
		if ( empty( $this->name ) ) {
			return new \WP_Error( 'missing_required', __( 'Category name is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->type ) ) {
			return new \WP_Error( 'missing_required', __( 'Category type is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->status ) ) {
			return new \WP_Error( 'missing_required', __( 'Category status is required.', 'wp-ever-accounting' ) );
		}

		// Duplicate check. Same type and name should not exist.
		$existing = static::results(
			array(
				'type'  => $this->type,
				'name'  => $this->name,
				'limit' => 1,
			)
		);
		if ( ! empty( $existing ) && $existing[0]->id !== $this->id ) {
			return new \WP_Error( 'duplicate', __( 'Category with same name and type already exists.', 'wp-ever-accounting' ) );
		}

		return parent::save();
	}

	/*
	|--------------------------------------------------------------------------
	| Helper Methods
	|--------------------------------------------------------------------------
	| This section contains utility methods that are not directly related to this
	| object but can be used to support its functionality.
	|--------------------------------------------------------------------------
	*/
}
