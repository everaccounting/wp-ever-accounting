<?php
/**
 * Singleton Class.
 *
 * @since       1.1.0
 * @subpackage  Abstracts
 * @package     EverAccounting
 */

namespace EverAccounting\Abstracts;

defined( 'ABSPATH' ) || exit;

/**
 * Class Singleton
 *
 * @package EverAccounting\Abstracts
 */
abstract class Singleton {
	/**
	 * The single instance of the class.
	 *
	 * @var $this []
	 */
	protected static $instance = array();

	/**
	 * Ensures only one instance of the implemented class is loaded or can be loaded.
	 *
	 * @return $this
	 */
	public static function instance() {
		$class = get_called_class();

		if ( ! array_key_exists( $class, self::$instance ) ) {
			self::$instance[ $class ] = new $class();
		}

		return self::$instance[ $class ];
	}

	/**
	 * Ensures only one instance of the implemented class is loaded or can be loaded.
	 *
	 * @return $this
	 */
	public static function init() {
		return self::instance();
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
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of this class is forbidden.', 'wp-ever-accounting' ), '1.1.0' );
		die();
	}
}
