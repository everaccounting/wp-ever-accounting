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
	protected $props = array(
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
	protected $timestamps = true;

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


	/*
	|--------------------------------------------------------------------------
	| Prop Definition Methods
	|--------------------------------------------------------------------------
	| This section contains methods that define and provide specific prop values
	| related to the model, such as statuses or types. These methods can be accessed
	| without instantiating the model.
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
			'revenue' => esc_html__( 'Revenue', 'wp-ever-accounting' ),
			'expense' => esc_html__( 'Expense', 'wp-ever-accounting' ),
			'item'    => esc_html__( 'Item', 'wp-ever-accounting' ),
		);

		return apply_filters( 'ever_accounting_category_types', $types );
	}

	/*
	|--------------------------------------------------------------------------
	| Accessors, Mutators, Relationship and Validation Methods
	|--------------------------------------------------------------------------
	| This section contains methods for getting and setting properties (accessors
	| and mutators) as well as defining relationships between models. It also includes
	| a data validation method that ensures data integrity before saving.
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
	protected function set_type_prop( $type ) {
		$type = ! in_array( $type, self::get_types(), true ) ? 'income' : $type;
		$this->set_prop_value( 'type', $type );
	}

	/**
	 * Get the formatted name of the category.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_name_prop() {
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
	public function revenues() {
		return $this->has_many( Revenue::class );
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

	/**
	 * Sanitize data before saving.
	 *
	 * @since 1.0.0
	 * @return void|\WP_Error Return WP_Error if data is not valid or void.
	 */
	protected function validate_save_data() {
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
