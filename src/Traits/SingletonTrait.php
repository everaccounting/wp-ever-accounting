<?php

namespace EverAccounting\Traits;
/**
 * Trait SingletonTrait
 *
 * @since   1.1.0
 * @package EverAccounting\Traits
 */
trait SingletonTrait {

	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function __construct() {
	}

	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	final public static function init() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Prevent cloning.
	 */
	private function __clone() {
	}

	/**
	 * Prevent unserializing.
	 */
	final public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'wp-ever-accounting' ), '1.1.0' );
		die();
	}
}
