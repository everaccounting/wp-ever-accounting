<?php

namespace EverAccounting;

/**
 * Class Plugin.
 *
 *
 * @since   1.1.6
 * @package EverAccounting
 */
class Plugin {
	/**
	 * The plugin data store.
	 *
	 * @since 1.1.6
	 * @var array
	 */
	protected $data = array(
		'slug'    => 'wp-ever-accounting',
		'version' => '1.0.0',
	);

	/**
	 * Holds the components of the plugin
	 *
	 * @since   1.1.6
	 * @var     array
	 */
	public $components = array();

	/**
	 * The single instance of the class.
	 *
	 * @since 1.1.6
	 * @var self
	 */
	public static $instance;

	/**
	 * Gets the single instance of the class.
	 * This method is used to create a new instance of the class.
	 *
	 * @param string|array $args The plugin data.
	 * @param string       $version The plugin version.
	 *
	 * @since 1.1.6
	 * @return static
	 */
	final public static function create( $args = null, $version = null ) {
		if ( is_null( static::$instance ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			$called_class = get_called_class();
			if ( ! is_array( $args ) ) {
				$file         = $args;
				$args         = array();
				$args['file'] = $file;
			}
			$file        = $args['file'];
			$plugin_data = get_plugin_data( $file, false, false );
			$plugin_data = array_change_key_case( $plugin_data, CASE_LOWER );
			$plugin_data = array_merge( $plugin_data, $args );

			// If version is set, use it.
			if ( ! empty( $version ) ) {
				$plugin_data['version'] = $version;
			}

			static::$instance = new $called_class( $plugin_data );
		}

		return static::$instance;
	}

	/**
	 * Gets the instance of the class.
	 *
	 * @since 1.1.6
	 *
	 * @return static
	 */
	final public static function instance() {
		if ( null === static::$instance ) {
			_doing_it_wrong( __FUNCTION__, 'Plugin instance called before initiating the instance.', '1.0.0' );
		}

		return static::$instance;
	}

	/**
	 * Plugin constructor.
	 *
	 * @param array $data The plugin data.
	 *
	 * @since 1.1.6
	 */
	protected function __construct( $data ) {
		$this->data = array_merge( $this->data, $data );
		// Handle legacy version.
		if ( ! empty( get_option( 'eaccounting_version' ) ) ) {
			update_option( 'ever_accounting_version', get_option( 'eaccounting_version' ) );
			delete_option( 'eaccounting_version' );
		}
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Prevents cloning.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	final public function __clone() {
		_doing_it_wrong( __FUNCTION__, 'Cloning is forbidden.', '1.0.0' );
	}

	/**
	 * Prevents unserializing.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	final public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, 'Unserializing is forbidden.', '1.0.0' );
	}

	/**
	 * define all required constants
	 *
	 * since 1.0.0
	 *
	 * @return void
	 */
	public function define_constants() {
		$upload_dir = wp_upload_dir( null, false );

		define( 'EAC_VERSION', $this->get_version() );
		define( 'EAC_PLUGIN_BASENAME', $this->get_basename() );
		define( 'EAC_PLUGIN_FILE', $this->get_file() );
		define( 'EAC_PLUGIN_PATH', $this->get_dir_path() );
		define( 'EAC_PLUGIN_URL', $this->get_dir_url() );
		define( 'EAC_UPLOADS_DIR', $upload_dir['basedir'] . '/ever-accounting' );
		define( 'EAC_UPLOADS_URL', $upload_dir['baseurl'] . '/ever-accounting' );
		define( 'EAC_LOG_DIR', $upload_dir['basedir'] . '/ever-accounting-logs/' );
		define( 'EAC_ASSETS_URL', $this->get_assets_url() );
		define( 'EAC_ASSETS_DIR', $this->get_assets_path() );
		define( 'EAC_DIST_URL', $this->get_dir_url() . 'dist' );
		define( 'EAC_DIST_DIR', $this->get_dir_path() . 'dist' );
		define( 'EAC_TEMPLATES_DIR', EAC_PLUGIN_FILE . '/templates' );
	}

	/**
	 * Include all required files
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function includes() {}

	/**
	 * Init hooks.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function init_hooks() {

	}

	/**
	 * Get all components, or filter by type.
	 *
	 * @since  1.1.6
	 *
	 * @param string $name The component name.
	 *
	 * @return array Plugin components.
	 */
	private function get_components( $type = null ) {
		$components = $this->components;
		if ( ! empty( $type ) ) {
			$components = array_filter(
				$components,
				function ( $component ) use ( $type ) {
					return $component instanceof $type;
				}
			);
		}

		return $components;
	}
}
