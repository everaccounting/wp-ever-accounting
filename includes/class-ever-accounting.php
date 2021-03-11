<?php
/**
 * Plugin Name: Ever Accounting
 * Plugin URI: https://wpeveraccounting.com/
 * Description: Manage your business finances right from your WordPress dashboard.
 * Version: 1.1.1
 * Author: everaccounting
 * Author URI: https://wpeveraccounting.com/
 * Requires at least: 4.7.0
 * Tested up to: 5.6.1
 * Text Domain: wp-ever-accounting
 * Domain Path: /i18n/languages/
 * License: GPL2+
 *
 * @package wp-ever-accounting
 */


defined( 'ABSPATH' ) || exit();

/**
 * Class EverAccounting
 *
 * @property-read \EverAccounting\Logger $logger
 * @method \EverAccounting\Logger logger()
 * @since 1.0.0
 */
final class EverAccounting {
	/**
	 * EverAccounting version.
	 *
	 * @var string
	 */
	public $version = '1.1.1';

	/**
	 * @since 1.0.2
	 * @var \EverAccounting\Utilities;
	 */
	public $utils;

	/**
	 * @since 1.0.2
	 * @var \EverAccounting_Settings
	 */
	public $settings;

	/**
	 * @var array all plugin's classes
	 *
	 * @var array
	 */
	protected $classes = [];

	/**
	 * The single instance of the class.
	 *
	 * @since 1.0.0
	 * @var EverAccounting
	 */
	protected static $instance = null;

	/**
	 * Main EverAccounting Instance.
	 *
	 * Ensures only one instance of EverAccounting is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see eaccounting()
	 * @return EverAccounting - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @return void
	 * @since 1.0.2
	 */

	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-ever-accounting' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @return void
	 * @since 1.0.2
	 */

	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-ever-accounting' ), '1.0.0' );
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 *
	 * @param mixed $key Key name.
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( array_key_exists( $key, $this->classes ) ) {
			return $this->classes[ $key ];
		}

		return $this->{$key};
	}

	/**
	 * @param $name
	 * @param array $params
	 *
	 * @return mixed
	 */
	public function __call( $name, array $params ) {
		if ( array_key_exists( $name, $this->classes ) ) {
			return $this->classes[ $name ];
		}

		return $this->{$name};
	}

	/**
	 * EverAccounting constructor.
	 */
	public function __construct() {
		define('EA_PLUGIN_FILE', __FILE__ );
		require_once __DIR__ . '/includes/class-ea-autoloader.php';
		// include hook files
		add_action( 'plugins_loaded', array( &$this, 'init' ), 0 );
	}

	public function init(){
		$this->classes['logger'] = new \EverAccounting\Logger();
	}

}

/**
 * Returns the main instance of Plugin.
 *
 * @return EverAccounting
 * @since  1.0.0
 */
function eaccounting() {
	return EverAccounting::instance();
}

eaccounting();
