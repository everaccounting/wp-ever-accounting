<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Currency.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class Currency extends Model {
	/**
	 * Table name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table_name = 'ea_currencies';

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $object_type = 'currency';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $core_data = array(
		'code'               => '',
		'name'               => '',
		'rate'               => 1,
		'number'             => '',
		'precision'          => 2,
		'subunit'            => 100,
		'symbol'             => '',
		'position'           => 'before',
		'decimal_separator'  => '.',
		'thousand_separator' => ',',
		'status'             => 'active',
		'date_created'       => null,
	);

	/**
	 * Get code.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since 1.0.0
	 * @retrun string
	 */
	public function get_code( $context = 'edit' ) {
		return $this->get_prop( 'code', $context );
	}

	/**
	 * Set code.
	 *
	 * @param string $code Currency code.
	 *
	 * @since 1.0.0
	 */
	public function set_code( $code ) {
		$this->set_prop( 'code', strtoupper( $code ) );
	}

	/**
	 * Return the currency.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Set the currency name.
	 *
	 * @param string $name Currency name.
	 *
	 * @since 1.1.0
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', sanitize_text_field( $name ) );
	}

	/**
	 * Get currency rate.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_rate( $context = 'edit' ) {
		return $this->get_prop( 'rate', $context );
	}

	/**
	 * Set currency rate.
	 *
	 * @param string $rate Currency rate.
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_rate( $rate ) {
		$this->set_prop( 'rate', floatval( $rate ) );
	}

	/**
	 * Get currency number.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_number( $context = 'edit' ) {
		return $this->get_prop( 'number', $context );
	}

	/**
	 * Set currency number.
	 *
	 * @param string $number Currency number.
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_number( $number ) {
		$this->set_prop( 'number', sanitize_text_field( $number ) );
	}

	/**
	 * Get currency precision.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_precision( $context = 'edit' ) {
		return $this->get_prop( 'precision', $context );
	}

	/**
	 * Set currency precision.
	 *
	 * @param string $precision Currency precision.
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_precision( $precision ) {
		if ( $precision > 4 ) {
			$precision = 4;
		} elseif ( $precision < 0 ) {
			$precision = 0;
		}
		$this->set_prop( 'precision', intval( $precision ) );
	}

	/**
	 * Get currency subunit.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_subunit( $context = 'edit' ) {
		return $this->get_prop( 'subunit', $context );
	}

	/**
	 * Set currency subunit.
	 *
	 * @param string $subunit Currency subunit.
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_subunit( $subunit ) {
		$this->set_prop( 'subunit', intval( $subunit ) );
	}

	/**
	 * Get currency symbol.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_symbol( $context = 'edit' ) {
		return $this->get_prop( 'symbol', $context );
	}

	/**
	 * Set currency symbol.
	 *
	 * @param string $symbol Currency symbol.
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_symbol( $symbol ) {
		$this->set_prop( 'symbol', sanitize_text_field( $symbol ) );
	}

	/**
	 * Get currency position.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_position( $context = 'edit' ) {
		return $this->get_prop( 'position', $context );
	}

	/**
	 * Set currency position.
	 *
	 * @param string $position Currency position.
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_position( $position ) {
		if ( in_array( $position, array( 'before', 'after' ), true ) ) {
			$this->set_prop( 'position', $position );
		}
	}

	/**
	 * Get currency decimal separator.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_decimal_separator( $context = 'edit' ) {
		return $this->get_prop( 'decimal_separator', $context );
	}

	/**
	 * Set currency decimal separator.
	 *
	 * @param string $decimal_separator Currency decimal separator.
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_decimal_separator( $decimal_separator ) {
		$this->set_prop( 'decimal_separator', sanitize_text_field( $decimal_separator ) );
	}

	/**
	 * Get currency a thousand separator.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_thousand_separator( $context = 'edit' ) {
		return $this->get_prop( 'thousand_separator', $context );
	}

	/**
	 * Set currency a thousand separator.
	 *
	 * @param string $thousand_separator Currency a thousand separator.
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_thousand_separator( $thousand_separator ) {
		$this->set_prop( 'thousand_separator', sanitize_text_field( $thousand_separator ) );
	}

	/**
	 * Get status.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_status( $context = 'edit' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Set status.
	 *
	 * @param string $status Status.
	 *
	 * @since 1.0.2
	 */
	public function set_status( $status ) {
		$this->set_prop( 'status', $status );
	}

	/**
	 * Is currency active.
	 *
	 * @since 1.0.2
	 * @return bool
	 */
	public function is_active() {
		return 'active' === $this->get_status();
	}

	/**
	 * Get date created.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_date_created( $context = 'edit' ) {
		return $this->get_date_prop( 'date_created', $context );
	}

	/**
	 * Set date created.
	 *
	 * @param string $date_created Date created.
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_date_created( $date_created ) {
		$this->set_date_prop( 'date_created', $date_created );
	}
	/*
	|--------------------------------------------------------------------------
	| Query Methods
	|--------------------------------------------------------------------------
	|
	| Methods for reading and manipulating the object properties.
	|
	*/
	/**
	 * Retrieve the object instance.
	 *
	 * @param int $id Object id to retrieve.
	 * @param string $by Optional. The field to retrieve the object by. Default 'id'.
	 * @param array $args Optional. Additional arguments to pass to the query.
	 *
	 * @since 1.0.0
	 *
	 * @return static|false Object instance on success, false on failure.
	 */
	protected function get( $id, $by = null, $args = array() ) {
		if ( ! empty( $id ) && ! is_numeric( $id ) && is_null( $by ) ) {
			$by = 'code';
		}
		return parent::get( $id, $by, $args );
	}

	/*
	|--------------------------------------------------------------------------
	| Helpers
	|--------------------------------------------------------------------------
	|
	| Helper methods.
	|
	*/
	/**
	 * Sanitizes the data.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true
	 */
	protected function sanitize_data() {
		// Required fields check.
		if ( empty( $this->get_name() ) ) {
			return new \WP_Error( 'missing-required', __( 'Currency name is required.', 'wp-ever-accounting' ) );
		}

		// Rate should be greater than 0.
		if ( $this->get_rate() <= 0 ) {
			return new \WP_Error( 'invalid-rate', __( 'Rate should be greater than 0.', 'wp-ever-accounting' ) );
		}
		// symbol is required.
		if ( empty( $this->get_symbol() ) ) {
			return new \WP_Error( 'missing-required', __( 'Symbol is required.', 'wp-ever-accounting' ) );
		}
		// position is required.
		if ( empty( $this->get_position() ) ) {
			return new \WP_Error( 'missing-required', __( 'Position is required.', 'wp-ever-accounting' ) );
		}
		// decimal_separator is required.
		if ( empty( $this->get_decimal_separator() ) ) {
			return new \WP_Error( 'missing-required', __( 'Decimal separator is required.', 'wp-ever-accounting' ) );
		}

		// thousand_separator is required.
		if ( empty( $this->get_thousand_separator() ) ) {
			return new \WP_Error( 'missing-required', __( 'Thousand separator is required.', 'wp-ever-accounting' ) );
		}

		// Duplicate check.
		$currency = self::get( $this->get_name() );
		if ( $currency && $currency->get_id() !== $this->get_id() ) {
			return new \WP_Error( 'duplicate-currency', __( 'Currency already exists.', 'wp-ever-accounting' ) );
		}

		// If date created is not set, set it to now.
		if ( empty( $this->get_date_created() ) ) {
			$this->set_date_created( current_time( 'mysql' ) );
		}

		return parent::sanitize_data();
	}

	/**
	 * get prefix.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_prefix() {
		if ( $this->get_position() == 'before' ) {
			return $this->get_symbol();
		}

		return '';
	}

	/**
	 * get suffix.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_suffix() {
		if ( $this->get_position() == 'after' ) {
			return $this->get_symbol();
		}

		return '';
	}
}


