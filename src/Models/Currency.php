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
 * @property int $precision Precision of the currency.
 * @property string $symbol Symbol of the currency.
 * @property int $subunit Subunit of the currency.
 * @property string $position Position of the currency.
 * @property string $decimal_separator Decimal separator of the currency.
 * @property string $thousand_separator A Thousand separator of the currency.
 * @property float  $exchange_rate Exchange rate of the currency.
 * @property string $date_updated Date updated of the currency.
 * @property string $date_created Date created of the currency.
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
		'precision',
		'symbol',
		'subunit',
		'position',
		'decimal_separator',
		'thousand_separator',
		'exchange_rate',
		'status',
	);

	/**
	 * Model's data container.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
		'precision'          => 2,
		'symbol'             => '$',
		'subunit'            => 100,
		'position'           => 'before',
		'decimal_separator'  => '.',
		'thousand_separator' => ',',
		'status'             => 'active',
	);

	/**
	 * Model's casts data.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	protected $casts = array(
		'id'            => 'int',
		'precision'     => 'int',
		'subunit'       => 'int',
		'exchange_rate' => 'float',
	);

	/**
	 * Searchable properties.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $searchable = array(
		'name',
		'code'
	);

	/**
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	public $timestamps = true;

	/**
	 * Set position property.
	 *
	 * @param string $value Type of the currency.
	 *
	 * @since 1.0.0
	 */
	protected function set_position_prop( $value ) {
		$value = strtolower( $value );
		$value = in_array( $value, array( 'before', 'after' ) ) ? $value : 'before';
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

	/**
	 * Get related accounts.
	 *
	 * @since 1.0.0
	 * @return Relation
	 */
	public function accounts() {
		return $this->has_many( Account::class, 'currency_code', 'code' );
	}
}
