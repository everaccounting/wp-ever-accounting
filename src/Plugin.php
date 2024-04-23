<?php

namespace EverAccounting;

/**
 * Class Plugin.
 *
 * @since 1.2.1
 * @package EverAccounting
 */
class Plugin extends \ByteKit\Core\Plugin {
	/**
	 * Plugin constructor.
	 *
	 * @param array $data The plugin data.
	 *
	 * @since 1.0.0
	 */
	protected function __construct( $data ) {
		$data['id'] = 'eac';
		parent::__construct( $data );
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define constants.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function define_constants() {
		$upload_dir = wp_upload_dir( null, false );

		define( 'EAC_VERSION', $this->get_version() );
		define( 'EAC_PLUGIN_FILE', $this->get_file() );
		define( 'EAC_PLUGIN_BASENAME', $this->get_basename() );
		define( 'EAC_PLUGIN_PATH', $this->get_dir_path() . '/' );
		define( 'EAC_PLUGIN_URL', $this->get_dir_url() . '/' );
		define( 'EAC_UPLOADS_BASEDIR', $upload_dir['basedir'] . '/eac/' );
		define( 'EAC_UPLOADS_DIR', $upload_dir['basedir'] . '/eac/' );
		define( 'EAC_UPLOADS_URL', $upload_dir['baseurl'] . '/eac/' );
		define( 'EAC_LOG_DIR', $upload_dir['basedir'] . '/eac-logs/' );
		define( 'EAC_ASSETS_URL', $this->get_assets_url() . '/' );
		define( 'EAC_ASSETS_DIR', $this->get_assets_path() . '/' );
		define( 'EAC_TEMPLATES_DIR', $this->get_template_path() . '/' );
	}

	/**
	 * Include required files.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function includes() {
		require_once __DIR__ . '/functions.php';
//		$this->services->add( 'installer', new Installer() );
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init_hooks() {
		register_activation_hook( $this->get_file(), array( $this, 'on_activation' ) );
		add_action( 'plugins_loaded', array( $this, 'on_init' ), 0 );
	}

	/**
	 * Run on activation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function on_activation() {
//		$this->installer()->install();
	}

	/**
	 * Run on init.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function on_init() {
		$this->container['installer'] = new Installer();

		if ( is_admin() ) {
			new Admin\Admin();
			new Admin\Menus();
			new Admin\Actions();
		}

		/**
		 * Fires when the plugin is initialized.
		 *
		 * @since 1.0.0
		 */
		do_action( 'ever_accounting_init' );
	}

	/**
	 * Get queue instance.
	 *
	 * @since 1.0.0
	 * @return Utilities\Queue
	 */
	public function queue() {
		return Utilities\Queue::instance();
	}
}
