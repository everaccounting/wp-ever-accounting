<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Currency extends EAccounting_Object {
	/**
	 * Currency Data array.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
		'name'         => '',
		'code'         => '',
		'rate'         => 1,
		'creator_id'   => null,
		'company_id'   => 1,
		'date_created' => null,
	);

	/**
	 * @var int
	 */
	protected $precision;

	/**
	 * @var int
	 */
	protected $subunit;

	/**
	 * @var string
	 */
	protected $symbol;

	/**
	 * @var bool
	 */
	protected $symbolPosition;

	/**
	 * @var string
	 */
	protected $decimalMark;

	/**
	 * @var string
	 */
	protected $thousandsSeparator;

	/**
	 * Get the currency if ID is passed, otherwise the currency is new and empty.
	 * This class should NOT be instantiated, but the eaccounting_get_currency function
	 * should be used. It is possible, but the aforementioned are preferred and are the only
	 * methods that will be maintained going forward.
	 *
	 * @param int|object|EAccounting_Currency $data Order to read.
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		if ( is_numeric( $data ) && $data > 0 ) {
			$this->set_id( $data );
		} elseif ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} elseif ( is_string( $data ) && array_key_exists( $data, eaccounting_get_global_currencies() ) ) {
			$this->set_code( $data );
		} else {
			$this->set_id( 0 );
		}

		if ( $this->get_id() > 0 ) {
			$this->read( $this->get_id() );
		} elseif ( ! empty( $this->get_code( 'edit' ) ) ) {
			$this->read( $this->get_code( 'edit' ), 'code' );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get currency name.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_name( $context = 'view' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Get currency code.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_code( $context = 'view' ) {
		return $this->get_prop( 'code', $context );
	}

	/**
	 * Get currency rate.
	 *
	 * @param string $context
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_rate( $context = 'view' ) {
		return $this->get_prop( 'rate', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set the currency name.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_name( $value ) {
		$this->set_prop( 'name', eaccounting_clean( $value ) );
	}

	/**
	 * Set the code.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_code( $value ) {
		$code       = strtoupper( eaccounting_clean( $value ) );
		$currencies = eaccounting_get_global_currencies();
		if ( ! array_key_exists( $code, $currencies ) ) {
			$this->error( 'invalid_currency_code', __( 'Unsupported currency code', 'wp-ever-accounting' ) );
		}
		$currency = $currencies[ $code ];
		$this->set_prop( 'code', $code );
		$this->precision          = (int) $currency['precision'];
		$this->subunit            = (int) $currency['subunit'];
		$this->symbol             = (string) $currency['symbol'];
		$this->symbolPosition     = (string) $currency['position'];
		$this->decimalMark        = (string) $currency['decimalSeparator'];
		$this->thousandsSeparator = (string) $currency['thousandSeparator'];
	}

	/**
	 * Set the code.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_rate( $value ) {
		$this->set_prop( 'rate', eaccounting_sanitize_number( $value, true ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Crud
	|--------------------------------------------------------------------------
	*/

	/**
	 * Create a new currency in the database.
	 *
	 * @param string|int $id
	 * @param string $by
	 *
	 * @throws Exception
	 * @since 1.0.2
	 *
	 */
	public function read( $id, $by = 'id' ) {
		$this->set_defaults();
		global $wpdb;

		// Get from cache if available.
		$item = ! empty( $id ) ? wp_cache_get( 'currency-item-' . $id, 'currencies' ) : false;

		if ( false === $item ) {
			switch ( $by ) {
				case 'code':
					$code = sanitize_text_field( $id );
					$sql  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_currencies WHERE code=%s", $code );
					break;
				case 'id':
				default:
					$id  = absint( $id );
					$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_currencies WHERE id=%s", $id );
					break;
			}

			$item = $wpdb->get_row( $sql );
			if ( 0 < $item->id ) {
				wp_cache_set( 'currency-item-' . $id, $item, 'currencies' );
			}
		}

		if ( ! $item || ! $item->id ) {
			throw new Exception( __( 'Invalid currency.', 'wp-ever-accounting' ) );
		}

		// Gets extra data associated with the order if needed.
		foreach ( $item as $key => $value ) {
			$function = 'set_' . $key;
			if ( is_callable( array( $this, $function ) ) ) {
				$this->{$function}( $value );
			}
		}


		$this->set_object_read( true );
	}

	/**
	 * Validate the properties before saving the object
	 * in the database.
	 *
	 * @return void
	 * @since 1.0.2
	 */
	public function validate_props() {
		global $wpdb;

		if ( ! $this->get_date_created( 'edit' ) ) {
			$this->set_date_created( time() );
		}

		if ( ! $this->get_company_id( 'edit' ) ) {
			$this->set_company_id( 1 );
		}

		if ( ! $this->get_prop( 'creator_id' ) ) {
			$this->set_prop( 'creator_id', eaccounting_get_current_user_id() );
		}

		if ( empty( $this->get_name( 'edit' ) ) ) {
			throw new Exception( __( 'Currency name is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_code( 'edit' ) ) ) {
			throw new Exception( __( 'Currency code is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_rate( 'edit' ) ) ) {
			throw new Exception( __( 'Currency rate is required', 'wp-ever-accounting' ) );
		}

		if ( $existing_id = $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_currencies where code=%s", $this->get_code( 'edit' ) ) ) ) {
			if ( ! empty( $existing_id ) && $existing_id != $this->get_id() ) {
				throw new Exception( __( 'Duplicate currency code.', 'wp-ever-accounting' ) );
			}
		}
	}

	/**
	 * Create a new currency in the database.
	 *
	 * @throws Exception
	 * @since 1.0.2
	 */
	public function create() {
		$this->validate_props();
		global $wpdb;
		$currency_data = array(
			'name'         => $this->get_name( 'edit' ),
			'code'         => $this->get_code( 'edit' ),
			'rate'         => $this->get_rate( 'edit' ),
			'creator_id'   => $this->get_creator_id( 'edit' ),
			'company_id'   => $this->get_company_id( 'edit' ),
			'date_created' => $this->get_date_created( 'edit' )->get_mysql_date(),
		);

		do_action( 'eaccounting_pre_insert_currency', $this->get_id(), $this );

		$data = wp_unslash( apply_filters( 'eaccounting_new_currency_data', $currency_data ) );
		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_currencies', $data ) ) {
			throw new Exception( $wpdb->last_error );
		}

		do_action( 'eaccounting_insert_currency', $this->get_id(), $this );

		$this->set_id( $wpdb->insert_id );
		$this->apply_changes();
		$this->set_object_read( true );
	}

	/**
	 * Update a account in the database.
	 *
	 * @throws Exception
	 * @since 1.0.0
	 *
	 */
	public function update() {
		global $wpdb;

		$this->validate_props();
		$changes = $this->get_changes();
		if ( ! empty( $changes ) ) {
			do_action( 'eaccounting_pre_update_currency', $this->get_id(), $changes );

			try {
				$wpdb->update( $wpdb->prefix . 'ea_currencies', $changes, array( 'id' => $this->get_id() ) );
			} catch ( Exception $e ) {
				throw new Exception( __( 'Could not update currency.', 'wp-ever-accounting' ) );
			}

			do_action( 'eaccounting_update_currency', $this->get_id(), $changes, $this->data );

			$this->apply_changes();
			$this->set_object_read( true );
			wp_cache_delete( 'transaction-currency-' . $this->get_id(), 'currencies' );
		}
	}

	/**
	 * @return int|mixed
	 * @throws Exception
	 * @since 1.0.0
	 */
	public function save() {
		if ( $this->get_id() ) {
			$this->update();
		} else {
			$this->create();
		}

		return $this->get_id();
	}

	/**
	 * Remove currency from the database.
	 *
	 * @param array $args
	 *
	 * @since 1.0.
	 */
	public function delete( $args = array() ) {
		if ( $this->get_id() ) {
			global $wpdb;
			do_action( 'eaccounting_pre_delete_currency', $this->get_id() );
			$wpdb->delete( $wpdb->prefix . 'ea_currencies', array( 'id' => $this->get_id() ) );
			do_action( 'eaccounting_delete_currency', $this->get_id() );
			$this->set_id( 0 );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Extra
	|--------------------------------------------------------------------------
	*/

	/**
	 * __callStatic.
	 *
	 * @param string $method
	 * @param array $arguments
	 *
	 * @return EAccounting_Currency
	 */
	public static function __callStatic( $method, array $arguments ) {
		return new static( $method, $arguments );
	}

	/**
	 * equals.
	 *
	 * @param EAccounting_Currency $currency
	 *
	 * @return bool
	 */
	public function equals( self $currency ) {
		return $this->get_code('edit') === $currency->get_code('edit');
	}

	/**
	 * get_precision.
	 *
	 * @return int
	 */
	public function get_precision() {
		return $this->precision;
	}

	/**
	 * getSubunit.
	 *
	 * @return int
	 */
	public function get_subunit() {
		return $this->subunit;
	}

	/**
	 * get_symbol.
	 *
	 * @return string
	 */
	public function get_symbol() {
		return $this->symbol;
	}

	/**
	 * is_symbol_first.
	 *
	 * @return bool
	 */
	public function is_symbol_first() {
		return 'before' === $this->symbolPosition;
	}

	/**
	 * get_decimal_mark.
	 *
	 * @return string
	 */
	public function get_decimal_mark() {
		return $this->decimalMark;
	}

	/**
	 * getThousandsSeparator.
	 *
	 * @return string
	 */
	public function get_thousands_separator() {
		return $this->thousandsSeparator;
	}

	/**
	 * getPrefix.
	 *
	 * @return string
	 */
	public function get_prefix() {
		if ( ! $this->is_symbol_first() ) {
			return '';
		}

		return $this->symbol;
	}

	/**
	 * getSuffix.
	 *
	 * @return string
	 */
	public function get_suffix() {
		if ( $this->is_symbol_first() ) {
			return '';
		}

		return ' ' . $this->symbol;
	}

	/**
	 * Get the instance as an array.
	 *
	 * @return array
	 */
	public function toArray() {
		return [
			[
				'name'              => $this->get_name( 'edit' ),
				'code'              => $this->get_code( 'edit' ),
				'precision'         => $this->precision,
				'subunit'           => $this->subunit,
				'symbol'            => $this->symbol,
				'position'          => $this->symbolPosition,
				'decimalSeparator'  => $this->decimalMark,
				'thousandSeparator' => $this->thousandsSeparator,
				'prefix'            => $this->get_prefix(),
				'suffix'            => $this->get_suffix(),
			]
		];
	}

	/**
	 * Convert the object to its JSON representation.
	 *
	 * @param int $options
	 *
	 * @return string
	 */
	public function toJson( $options = 0 ) {
		return json_encode( $this->toArray(), $options );
	}

	/**
	 * jsonSerialize.
	 *
	 * @return array
	 */
	public function json_serialize() {
		return $this->toArray();
	}

	/**
	 * Get the evaluated contents of the object.
	 *
	 * @return string
	 */
	public function render() {
		return $this->get_code( 'edit' ) . ' (' . $this->get_name( 'edit' ) . ')';
	}

	/**
	 * __toString.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}
}
