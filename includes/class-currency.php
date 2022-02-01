<?php
/**
 * Currency data handler class.
 *
 * @version     1.0.2
 * @package     EverAccounting
 * @class       Currency
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Currency class
 */
class Currency extends Data {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $object_type = 'currency';

	/**
	 * Table name.
	 *
	 * @since 1.1.3
	 * @var string
	 */
	protected $table = 'ea_currencies';

	/**
	 * Meta type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $meta_type = false;

	/**
	 * Cache group.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $cache_group = 'ea_currencies';

	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.1.3
	 * @var array
	 */
	protected $core_data = [
		'name'               => '',
		'code'               => '',
		'rate'               => 1,
		'number'             => '',
		'precision'          => 2,
		'subunit'            => 100,
		'symbol'             => '',
		'position'           => 'before',
		'decimal_separator'  => '.',
		'thousand_separator' => ',',
		'date_created'       => null,
	];

	/**
	 * Currency constructor.
	 *
	 * @param int|currency|object|null $currency currency instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $currency = 0 ) {
		// Call early so default data is set.
		parent::__construct();

		if ( is_numeric( $currency ) && $currency > 0 ) {
			$this->set_id( $currency );
		} elseif ( $currency instanceof self ) {
			$this->set_id( absint( $currency->get_id() ) );
		} elseif ( is_string( $currency ) ) {
			$this->set_code( $currency );
		} elseif ( is_array( $currency ) && ! empty( $currency['code'] ) ) {
			$this->set_code( $currency['code'] );
		} elseif ( ! empty( $currency->ID ) ) {
			$this->set_id( absint( $currency->ID ) );
		} else {
			$this->set_object_read( true );
		}

		$this->read();
	}

	/**
	 * Saves an object in the database.
	 *
	 * @return \WP_Error|int id on success, WP_Error on failure.
	 * @since 1.1.3
	 */
	public function save() {
		// check if anything missing before save.
		if ( ! $this->is_date_valid( $this->date_created ) ) {
			$this->date_created = current_time( 'mysql' );
		}

		$requires = [ 'code', 'rate', 'symbol', 'position', 'decimal_separator', 'thousand_separator' ];
		foreach ( $requires as $required ) {
			if ( empty( $this->$required ) ) {
				return new \WP_Error( 'missing_required_params', sprintf( __( ' %s is required', 'wp-ever-accounting' ), $required ) );
			}
		}

		if ( ! $this->exists() ) {
			$is_error = $this->create();
		} else {
			$is_error = $this->update();
		}

		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		$this->apply_changes();

		// Clear cache.
		wp_cache_delete( $this->get_id(), $this->cache_group );
		wp_cache_set( 'last_changed', microtime(), $this->cache_group );

		/**
		 * Fires immediately after a currency is inserted or updated in the database.
		 *
		 * @param int $id Currency id.
		 * @param array $data Currency data array.
		 * @param Currency $currency Currency object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eaccounting_saved_' . $this->object_type, $this->get_id(), $this );

		return $this->get_id();
	}

	/*
	|--------------------------------------------------------------------------
	| Additional methods
	|--------------------------------------------------------------------------
	|
	| Does extra thing as helper functions.
	|
	*/

	/**
	 * getPrefix.
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_prefix() {
		if ( ! $this->is_symbol_first() ) {
			return '';
		}

		return $this->get_symbol( 'edit' );
	}

	/**
	 * getSuffix.
	 *
	 * @return string
	 * @since 1.0.2
	 *
	 */
	public function get_suffix() {
		if ( $this->is_symbol_first() ) {
			return '';
		}

		return ' ' . $this->get_symbol( 'edit' );
	}

	/**
	 * equals.
	 *
	 * @param Currency $currency
	 *
	 * @return bool
	 * @since 1.0.2
	 *
	 */
	public function equals( self $currency ) {
		return $this->get_code( 'edit' ) === $currency->get_code( 'edit' );
	}

	/**
	 * is_symbol_first.
	 *
	 * @return bool
	 * @since 1.0.2
	 *
	 */
	public function is_symbol_first() {
		return 'before' === $this->get_position( 'edit' );
	}
}
