<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relations\HasMany;

/**
 * Currency model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int    $id ID of the category.
 * @property string $code Code of the currency.
 * @property string $name Name of the category.
 * @property float  $exchange_rate Exchange rate of the currency.
 * @property float  $rate Exchange rate of the currency.
 * @property int    $precision Precision of the currency.
 * @property string $symbol Symbol of the currency.
 * @property int    $subunit Subunit of the currency.
 * @property string $position Position of the currency.
 * @property string $thousand_separator A Thousand separator of the currency.
 * @property string $decimal_separator Decimal separator of the currency.
 * @property string $status Status of the currency.
 * @property string $date_updated Date updated of the currency.
 * @property string $date_created Date created of the currency.
 *
 * @property string $formatted_name Formatted name.
 */
class Currency extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_currencies';

	/**
	 * Table columns.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'code',
		'name',
		'exchange_rate',
		'precision',
		'symbol',
		'subunit',
		'position',
		'thousand_separator',
		'decimal_separator',
		'status',
	);

	/**
	 * The attributes of the model.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $attributes = array(
		'exchange_rate'      => 1,
		'precision'          => 2,
		'symbol'             => '$',
		'subunit'            => 100,
		'position'           => 'before',
		'thousand_separator' => ',',
		'decimal_separator'  => '.',
		'status'             => 'active',
	);

	/**
	 * The attributes that should be cast.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array(
		'id'            => 'int',
		'exchange_rate' => 'float',
		'precision'     => 'int',
		'subunit'       => 'int',
	);

	/**
	 * The attributes that have aliases.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $aliases = array(
		'rate' => 'exchange_rate',
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
	 * The attributes that are searchable.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $searchable = array(
		'name',
		'code',
	);

	/*
	|--------------------------------------------------------------------------
	| Accessors, Mutators and Relationship Methods
	|--------------------------------------------------------------------------
	| This section contains methods for getting and setting attributes (accessors
	| and mutators) as well as defining relationships between models.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set position property.
	 *
	 * @param string $value Type of the currency.
	 *
	 * @since 1.0.0
	 */
	protected function set_position_attribute( $value ) {
		$value                        = strtolower( $value );
		$this->attributes['position'] = in_array( $value, array( 'before', 'after' ), true ) ? $value : 'before';
	}

	/**
	 * Get exchange_rate property.
	 *
	 * @since 1.0.0
	 */
	protected function get_exchange_rate_attribute() {
		return eac_get_base_currency() === $this->code ? 1 : $this->attributes['exchange_rate'];
	}

	/**
	 * Set exchange_rate property.
	 *
	 * @param float $value Exchange rate of the currency.
	 *
	 * @since 1.0.0
	 */
	protected function set_exchange_rate_attribute( $value ) {
		$this->attributes['exchange_rate'] = eac_get_base_currency() === $this->code ? 1 : $this->cast_attribute( 'exchange_rate', $value );
	}

	/**
	 * Get formatted name.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	protected function get_formatted_name_attribute() {
		return sprintf( '%s (%s)', $this->name, $this->code );
	}

	/**
	 * Get related accounts.
	 *
	 * @since 1.0.0
	 * @return HasMany
	 */
	public function accounts() {
		return $this->has_many( Account::class, 'currency_code', 'code' );
	}

	/**
	 * Transactions relationship.
	 *
	 * @since 1.0.0
	 * @return HasMany
	 */
	public function transactions() {
		return $this->has_many( Transaction::class, 'currency_code', 'code' );
	}

	/**
	 * Get documents relationship.
	 *
	 * @since 1.0.0
	 * @return HasMany
	 */
	public function documents() {
		return $this->has_many( Document::class, 'currency_code', 'code' );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	| Methods for reading, creating, updating and deleting objects.
	*/

	/**
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 */
	public function save() {
		if ( empty( $this->name ) ) {
			return new \WP_Error( 'missing_required', __( 'Currency name is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->exchange_rate ) ) {
			return new \WP_Error( 'missing_required', __( 'Currency exchange rate is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->code ) ) {
			return new \WP_Error( 'missing_required', __( 'Currency code is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->symbol ) ) {
			return new \WP_Error( 'missing_required', __( 'Currency symbol is required.', 'wp-ever-accounting' ) );
		}

		return parent::save();
	}

	/**
	 * Delete the object from the database.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function delete() {
		if ( ! $this->is_deletable() ) {
			return false;
		}

		return parent::delete();
	}


	/*
	|--------------------------------------------------------------------------
	| Query methods.
	|--------------------------------------------------------------------------
	| Methods for querying data.
	*/

	/**
	 * Find an object by its primary key or query.
	 *
	 * @param string|int $id The ID of the object to find.
	 *
	 * @since 1.0.0
	 * @return static|null The model instance, or null if not found.
	 */
	public static function find( $id ) {
		if ( ! is_numeric( $id ) && strlen( $id ) === 3 ) {
			$id = array( 'code' => strtoupper( $id ) );
		}

		return parent::find( $id );
	}

	/**
	 * Find an object by its primary key or create a new instance.
	 *
	 * @param mixed $attributes Attributes to set.
	 *
	 * @since 1.0.0
	 * @return static The model instance.
	 */
	public static function make( $attributes = null ) {
		$model       = new static();
		$primary_key = $model->get_key_name();
		$id          = null;
		if ( is_scalar( $attributes ) ) {
			$id = $attributes;
		} elseif ( is_array( $attributes ) && isset( $attributes[ $primary_key ] ) ) {
			$id = $attributes[ $primary_key ];
		} elseif ( is_object( $attributes ) && isset( $attributes->$primary_key ) ) {
			$id = $attributes->$primary_key;
		} elseif ( $attributes instanceof static ) {
			$id = $attributes->get_key_value();
		} elseif ( is_array( $attributes ) && isset( $attributes['code'] ) ) {
			$id = $attributes['code'];
		}

		$item = $id ? static::find( $id ) : $model;

		return $item ? $item->fill( $attributes ) : $model->new_instance( $attributes );
	}


	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/
	/**
	 * Is deletable.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_deletable() {
		return ! $this->is_base_currency() && $this->accounts()->get_count() === 0 && $this->transactions()->get_count() === 0 && $this->documents()->get_count() === 0;
	}

	/**
	 * Determine if the currency is base currency.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_base_currency() {
		return eac_get_base_currency() === $this->code;
	}
}
