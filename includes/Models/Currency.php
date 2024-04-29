<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relation;

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
	 * The model's attributes.
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
		'code',
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
	 * Set position property.
	 *
	 * @param string $value Type of the currency.
	 *
	 * @since 1.0.0
	 */
	protected function set_position_attribute( $value ) {
		$value = strtolower( $value );
		$value = in_array( $value, array( 'before', 'after', true ), true ) ? $value : 'before';
		$this->set_attribute_value( 'position', $value );
	}

	/**
	 * Get exchange_rate property.
	 *
	 * @since 1.0.0
	 */
	protected function get_exchange_rate_attribute() {
		return eac_get_base_currency() === $this->code ? 1 : $this->get_attribute_value( 'exchange_rate' );
	}

	/**
	 * Get formatted name.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	protected function get_formatted_name_prop() {
		return sprintf( '%s (%s)', $this->name, $this->code );
	}

	/**
	 * Get related accounts.
	 *
	 * @since 1.0.0
	 * @return Relation
	 */
	protected function accounts() {
		return $this->has_many( Account::class, 'currency_code', 'code' );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	| Methods for reading, creating, updating and deleting objects.
	*/

	/**
	 * Read a record.
	 *
	 * @param int|string $key Record ID.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	protected function read( $key ) {
		if ( ! is_numeric( $key ) && strlen( $key ) === 3 && false === wp_cache_get( $key, $this->get_cache_group() ) ) {
			$table = $this->wpdb()->prefix . $this->get_table();
			$row   = $this->wpdb()->get_row(
				$this->wpdb()->prepare(
					"SELECT * FROM {$table} WHERE code = %s",
					$key
				)
			);
			if ( empty( $row ) ) {
				return null;
			}

			wp_cache_set( $key, $row, $this->get_cache_group() );
			wp_cache_set( $row->id, $row, $this->get_cache_group() );

			$key = $row->id;
		}

		return parent::read( $key );
	}

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

		if ( empty( $this->date_created ) ) {
			$this->date_created = current_time( 'mysql' );
		}

		if ( empty( $this->date_updated ) && $this->exists() ) {
			$this->date_updated = current_time( 'mysql' );
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
	 * Determine if the currency is base currency.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	public function is_base_currency() {
		return eac_get_base_currency() === $this->code;
	}
}
