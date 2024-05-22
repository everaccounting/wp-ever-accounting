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
	 * Data properties of the model.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
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
	 * The properties that should be cast to native types.
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
	 * The properties that have aliases.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $aliases = array(
		'rate' => 'exchange_rate',
	);

	/**
	 * The properties that should be appended to the model's array form.
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
	 * The properties that should be searchable when querying.
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
	| Props & Relations
	|--------------------------------------------------------------------------
	| Define the props and relations of the model.
	*/

	/**
	 * Set position property.
	 *
	 * @param string $value Type of the currency.
	 *
	 * @since 1.0.0
	 */
	protected function set_position_prop( $value ) {
		$value = strtolower( $value );
		$value = in_array( $value, array( 'before', 'after', true ), true ) ? $value : 'before';
		$this->set_prop_value( 'position', $value );
	}

	/**
	 * Get exchange_rate property.
	 *
	 * @since 1.0.0
	 */
	protected function get_exchange_rate_prop() {
		return eac_get_base_currency() === $this->code ? 1 : $this->get_prop_value( 'exchange_rate' );
	}

	/**
	 * Set exchange_rate property.
	 *
	 * @param float $value Exchange rate of the currency.
	 *
	 * @since 1.0.0
	 */
	protected function set_exchange_rate_prop( $value ) {
		if ( eac_get_base_currency() === $this->code ) {
			$value = 1;
		}
		$this->set_prop_value( 'exchange_rate', $value );
	}

	/**
	 * Get formatted name.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	protected function get_formatted_name_prop() {
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
		if ( empty( $this->decimal_separator ) ) {
			return new \WP_Error( 'missing_required', __( 'Currency decimal separator is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->thousand_separator ) ) {
			return new \WP_Error( 'missing_required', __( 'Currency thousand separator is required.', 'wp-ever-accounting' ) );
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
		// Currency can't be deleted.
		return false;
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
	 * @param mixed $args The value to search for.
	 *
	 * @since 1.0.0
	 * @return static|null The model instance, or null if not found.
	 */
	public static function find( $args ) {
		if ( ! is_numeric( $args ) && strlen( $args ) === 3 ) {
			$value = array( 'code' => strtoupper( $args ) );
		}

		return parent::find( $value );
	}

	/**
	 * Find an object by its primary key or create a new instance.
	 *
	 * @param mixed $props Entity data.
	 *
	 * @since 1.0.0
	 * @return static The model instance.
	 */
	public static function make( $props = null ) {
		$model       = new static();
		$primary_key = $model->get_key_name();
		$id          = null;
		if ( is_scalar( $props ) ) {
			$id = $props;
		} elseif ( is_array( $props ) && isset( $props[ $primary_key ] ) ) {
			$id = $props[ $primary_key ];
		} elseif ( is_object( $props ) && isset( $props->$primary_key ) ) {
			$id = $props->$primary_key;
		} elseif ( $props instanceof static ) {
			$id = $props->get_key_value();
		} elseif ( is_array( $props ) && isset( $props['code'] ) ) {
			$id = $props['code'];
		}

		$item = $id ? static::find( $id ) : $model;

		return $item ? $item->set_props( $props ) : $model->new_instance( $props );
	}


	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/

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
