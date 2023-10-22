<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class Plugin.
 *
 *
 * @since   1.1.6
 * @package EverAccounting
 */
class Plugin extends BasePlugin {

	/**
	 * Plugin constructor.
	 *
	 * @param array $args Plugin arguments.
	 *
	 * @since 1.1.6
	 */
	public function __construct( $args ) {
		parent::__construct( $args );
		// Handle legacy version.
		$version = get_option( 'eaccounting_version' );
		if ( $version && empty( $this->get_db_version() ) ) {
			$this->update_db_version( $version );
		}
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
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
	public function includes() {
		require_once __DIR__ . '/Functions.php';
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function init_hooks() {
		register_activation_hook( $this->get_file(), array( Installer::class, 'install' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ), 0 );
	}

	/**
	 * Init plugin when WordPress Initialises.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function init() {
		// Before init action.
		do_action( 'before_ever_accounting_init' );

		// Load class instances.
		Installer::instance();
		Rewrites::instance();
		Scripts::instance();
		//ScriptsLegacy::instance();
		Notices::instance();
		Actions::instance();
		Cache::instance();
		API::instance();
		Shortcodes::instance();

		// If frontend.
		if ( self::is_request( 'frontend' ) ) {
			Frontend\Frontend::instance();
		}

		if ( self::is_request( 'admin' ) ) {
			Admin\Admin::instance();
		}

		// Init action.
		do_action( 'ever_accounting_init' );
	}
}
