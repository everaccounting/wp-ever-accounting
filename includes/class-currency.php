<?php
/**
 * Currency data handler class.
 *
 * @version     1.0.2
 * @package     Ever_Accounting
 * @class       Currency
 */

namespace Ever_Accounting;

defined( 'ABSPATH' ) || exit;

/**
 * Currency class
 */
class Currency extends Abstracts\Data {
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
		'code'               => 'USD',
		'name'               => '',
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
		} elseif ( ! empty( $currency->id ) ) {
			$this->set_id( absint( $currency->id ) );
		} else {
			$this->set_object_read( true );
		}

		$this->read();
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Functions for setting boll data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
	|
	*/

	/**
	 * Set Code.
	 *
	 * @param string $code Currency code.
	 *
	 * @since 1.0.0
	 */
	protected function set_code( $code ) {
		$this->set_prop( 'code', Currencies::sanitize_code( $code ) );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete documents from the database.
	| Written in abstract fashion so that the way documents are stored can be
	| changed more easily in the future.
	|
	| A save method is included for convenience (chooses update or create based
	| on if the order exists yet).
	|
	*/
	/**
	 * Saves an object in the database.
	 *
	 * @since 1.1.3
	 * @return \WP_Error|int id on success, WP_Error on failure.
	 */
	public function save() {
		// check if anything missing before save.
		if ( ! $this->is_date_valid( $this->date_created ) ) {
			$this->date_created = current_time( 'mysql' );
		}

		$requires = [ 'code', 'rate', 'symbol', 'position', 'decimal_separator', 'thousand_separator' ];
		foreach ( $requires as $required ) {
			if ( empty( $this->$required ) ) {
				return new \WP_Error( 'missing_required_params', sprintf( __( 'Currency %s is required.', 'wp-ever-accounting' ), $required ) );
			}
		}

		$duplicate = Currencies::get_by_code( $this->code );

		if ( $duplicate && $duplicate->exists() && $duplicate->get_id() !== $this->get_id() ) {
			return new \WP_Error( 'duplicate_currency', __( 'Currency already exists', 'wp-ever-accounting' ) );
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
		wp_cache_delete( $this->get_code(), $this->cache_group );
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
		do_action( 'ever_accounting_saved_' . $this->object_type, $this->get_id(), $this );

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
	 * @since 1.0.2
	 *
	 * @return string
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
	 * @since 1.0.2
	 *
	 * @return string
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
	 * @since 1.0.2
	 *
	 * @return bool
	 */
	public function equals( self $currency ) {
		return $this->get_code( 'edit' ) === $currency->get_code( 'edit' );
	}

	/**
	 * is_symbol_first.
	 *
	 * @since 1.0.2
	 *
	 * @return bool
	 */
	public function is_symbol_first() {
		return 'before' === $this->get_position( 'edit' );
	}
}
