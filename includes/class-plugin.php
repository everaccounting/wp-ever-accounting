<?php
/**
 * Main plugin file.
 *
 * @since    1.0.0
 * @package  Ever_Accounting
 */

namespace Ever_Accounting;

use Ever_Accounting\REST\REST_Manager;

defined( 'ABSPATH' ) || exit();

/**
 * Final Plugin class.
 */
final class Plugin {
	/**
	 * Plugin version number.
	 *
	 * @since 1.1.3
	 * @var string
	 */
	public $version = '1.1.2';

	/**
	 * Plugin name.
	 *
	 * @since 1.1.3
	 * @var string
	 */
	public $name = 'WP Ever Accounting';

	/**
	 * Plugin slug.
	 *
	 * @since 1.1.3
	 * @var string
	 */
	public $slug = 'wp-ever-accounting';

	/**
	 * The single instance of the class.
	 *
	 * @since 1.1.3
	 * @var Plugin
	 */
	protected static $instance;

	/**
	 * Main Plugin Instance.
	 *
	 * Ensures only one instance of Plugin is loaded or can be loaded.
	 *
	 * @since 1.1.3
	 * @return Plugin - Main instance.
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
	 * @since 1.1.3
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is forbidden.', 'wp-ever-accounting' ), '1.1.3' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of this class is forbidden.', 'wp-ever-accounting' ), '1.1.3' );
	}

	/**
	 * Plugin Constructor.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	protected function __construct() {
		$this->define_constants();
		$this->includes();
		$this->register_hooks();
	}

	/**
	 * Define the plugin constants.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	protected function define_constants() {
		$file = EACCOUNTING_FILE;
		define( 'EACCOUNTING_VERSION', $this->version );
		define( 'EACCOUNTING_NAME', $this->name );
		define( 'EACCOUNTING_SLUG', $this->slug );
		define( 'EACCOUNTING_BASENAME', plugin_basename( $file ) );
		define( 'EACCOUNTING_PATH', untrailingslashit( plugin_dir_path( $file ) ) );
		define( 'EACCOUNTING_URL', untrailingslashit( plugin_dir_url( $file ) ) );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	protected function includes() {
		include_once __DIR__ . '/class-autoloader.php';
		include_once __DIR__ . '/libraries/wp-async-request.php';
		include_once __DIR__ . '/libraries/wp-background-process.php';
		include_once __DIR__ . '/core-functions.php';
//		include_once __DIR__ . '/form-functions.php';
//		include_once __DIR__ . '/formatting-functions.php';
	}

	/**
	 * Register plugin hooks.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	protected function register_hooks() {
		register_activation_hook( EACCOUNTING_FILE, array( $this, 'install' ) );
		add_filter( 'plugin_action_links_' . EACCOUNTING_BASENAME, array( $this, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );

		// Init the plugin after WordPress inits.
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Install the plugin.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	public function install() {
		Lifecycle::install();
	}

	/**
	 * Return the plugin action links.  This will only be called if the plugin
	 * is active.
	 *
	 * @param array $links associative array of action names to anchor tags.
	 *
	 * @since 1.1.3
	 * @return array associative array of plugin action links.
	 */
	public function action_links( $links ) {
		array_unshift( $links, sprintf( '<a href="%1$s">%2$s</a>', esc_url( admin_url( 'admin.php?page=admin' ) ), __( 'Settings', 'wp-ever-accounting' ) ) );

		return $links;
	}

	/**
	 * Filters the array of row meta for each plugin in the Plugins list table.
	 *
	 * @param string[] $links An array of the plugin's metadata.
	 * @param string $file Path to the plugin file relative to the plugins' directory.
	 *
	 * @since 1.1.3
	 * @return string[] An array of the plugin's metadata.
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( EACCOUNTING_BASENAME !== $file ) {
			return $links;
		}

		return array_merge(
			$links,
			array(
				'docs'        => sprintf( '<a href="https://pluginever.com/docs" target="_blank">%s</a>', esc_html__( 'Docs', 'wp-ever-accounting' ) ),
				'support_url' => sprintf( '<a href="https://pluginever.com/support_url" target="_blank">%s</a>', esc_html_x( 'Support', 'noun', 'wp-ever-accounting' ) ),
				'reviews'     => sprintf( '<a href="https://pluginever.com/reviews" target="_blank">%s</a>', esc_html_x( 'Reviews', 'verb', 'wp-ever-accounting' ) ),
			)
		);
	}

	/**
	 * Initialize plugin for localization
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function localization_setup() {
		$locale = ( get_locale() !== '' ) ? get_locale() : 'en_US';
		load_textdomain( 'wp-ever-accounting', WP_LANG_DIR . '/plugins/wp-ever-accounting-' . $locale . '.mo' );
		load_plugin_textdomain( 'wp-ever-accounting', false, dirname( EACCOUNTING_BASENAME ) . '/languages' );
	}

	/**
	 * Initialize plugin.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	public function init() {
		do_action( 'ever_accounting_before_init' );

		// Set up localisation.
		$this->localization_setup();
		$this->queue();

//		Frontend_Scripts::init();
//		Admin_Manager::init();

//		Lifecycle::init();
		REST_Manager::init();
//
		if ( is_admin() ) {
			new Admin\Admin_Manager();
		}

		Scripts::init();


		do_action( 'ever_accounting_init' );
	}

	/**
	 * Get queue instance.
	 *
	 * @since 1.1.3
	 * @return Queue
	 */
	public function queue() {
		return Queue::instance();
	}


	/**
	 * Register the relations.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_relations() {
		$relations = array(
			'customer_note',
			'invoice_note',
		);
//		$this->set( 'relations', apply_filters( 'ever_accounting_relations', $relations ) );
	}

	public function ajax_url() {
		return admin_url( 'admin-ajax.php' );
	}
}
