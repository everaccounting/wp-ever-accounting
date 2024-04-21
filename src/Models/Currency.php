<?php

namespace EverAccounting\Models;

/**
 * Currency model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int    $id ID of the category.
 * @property string $name Name of the category.
 * @property string $code Code of the currency.
 * @property float  $rate Rate of the currency.
 * @property float  $exchange_rate Exchange rate of the currency.
 * @property int    $number Number of the currency.
 * @property int    $precision Precision of the currency.
 * @property int    $subunit Subunit of the currency.
 * @property string $symbol Symbol of the currency.
 * @property string $position Position of the currency.
 * @property string $decimal_separator Decimal separator of the currency.
 * @property string $thousand_separator A Thousand separator of the currency.
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
	protected $table = 'eac_currencies';

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
		'decimal_separator',
		'thousand_separator',
		'enabled',
		'date_updated',
		'date_created',
	);

	/**
	 * Model's data container.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $attributes = array(
		'exchange_rate'      => 1,
		'precision'          => 2,
		'subunit'            => 100,
		'position'           => 'before',
		'decimal_separator'  => '.',
		'thousand_separator' => ',',
		'symbol'             => '$',
		'enabled'            => 1,
	);

	/**
	 * Model's casts data.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	protected $casts = array(
		'id'            => 'int',
		'exchange_rate' => 'float',
		'precision'     => 'int',
		'subunit'       => 'int',
		'enabled'       => 'bool',
		'date_updated'  => 'datetime',
		'date_created'  => 'datetime',
	);

	protected $aliases = array(
		'rate' => 'exchange_rate',
	);

	/**
	 * Set position property.
	 *
	 * @param string $value Type of the currency.
	 *
	 * @since 1.0.0
	 */
	public function set_position_attribute( $value ) {
		$value                  = strtolower( $value );
		$value                  = in_array( $value, array( 'before', 'after' ) ) ? $value : 'before';
		$this->data['position'] = $value;
	}

	/**
	 * Read a record.
	 *
	 * @param int    $value Record ID.
	 * @param string $column Column name.
	 * @param string $type Data type.
	 *
	 * @since 1.0.0
	 *
	 * @return object
	 */
	protected function read( $value, $column = 'id', $type = '%d' ) {
		if ( ! is_numeric( $value ) && strlen( $value ) === 3 && false === wp_cache_get( $value, $this->get_cache_group() ) ) {
			$row = $this->wpdb()->get_row(
				$this->wpdb()->prepare(
					"SELECT * FROM {$this->get_wpdb_table()} WHERE code = %s",
					$value
				),
				ARRAY_A
			);
			if ( empty( $row ) ) {
				return null;
			}

			wp_cache_set( $value, (object) $row, $this->get_cache_group() );
			wp_cache_set( $row['id'], (object) $row, $this->get_cache_group() );

			$value = $row['id'];
		}

		return parent::read( $value, $column, $type );
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
}
