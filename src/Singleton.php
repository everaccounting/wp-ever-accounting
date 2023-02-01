<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class Singleton.
 *
 * @since   1.0.0
 * @package EverAccounting
 */
abstract class Singleton {
	/**
	 * The singleton class's instance.
	 *
	 * @since 1.0.0
	 * @var static
	 */
	private static $instance = array();

	/**
	 * Gets the single instance of the class.
	 * This method is used to create a new instance of the class.
	 *
	 * @since 1.0.0
	 *
	 * @return static
	 */
	final public static function get_instance() {
		return static::instantiate();
	}

	/**
	 * Initializes the class.
	 *
	 * @since 1.0.0
	 * @return static
	 */
	final public static function instantiate() {
		$class = get_called_class();
		if ( ! isset( self::$instance[ $class ] ) ) {
			self::$instance[ $class ] = new static( ...func_get_args() );
		}
		return self::$instance[ $class ];
	}

	/**
	 * Prevent the class instance being serialized.
	 *
	 * @throws  \Exception Exception.
	 * @since  1.0.0
	 */
	final public function __sleep() {
		throw new \Exception( 'You cannot serialize a singleton.' );
	}

	/**
	 * Prevent the class instance being unserialized.
	 *
	 * @throws  \Exception Exception.
	 * @since  1.0.0
	 */
	final public function __wakeup() {
		throw new \Exception( 'You cannot unserialize a singleton.' );
	}

	/**
	 * Prevent the class instance being cloned.
	 *
	 * @since  1.0.0
	 */
	final public function __clone() {
		// Do nothing.
	}

}
