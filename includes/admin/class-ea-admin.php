<?php

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin {
	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  1.0.0
	 */
	private static $instance = null;

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @return self Main instance.
	 * @since  1.0.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * EAccounting_Admin constructor.
	 */
	public function __construct() {
		$this->define_constants();
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'admin_init', array( $this, 'buffer' ), 1 );
		add_action( 'admin_init', array( $this, 'set_eaccounting_actions' ));
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * define all required constants
	 *
	 * since 1.0.0
	 *
	 * @return void
	 */
	public function define_constants() {
		define( 'EACCOUNTING_ADMIN_ABSPATH', dirname( __FILE__ ) );
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		require_once dirname( __FILE__ ) . '/admin-functions.php';
		require_once dirname( __FILE__ ) . '/class-ea-admin-menus.php';
		require_once dirname( __FILE__ ) . '/class-ea-admin-settings.php';
		require_once dirname( __FILE__ ) . '/class-ea-contacts-page.php';
	}

	/**
	 * Output buffering allows admin screens to make redirects later on.
	 */
	public function buffer() {
		ob_start();
	}

	/**
	 * Setup eaccounting actions
	 *
	 * since 1.0.0
	 */
	public function set_eaccounting_actions() {

		$key = ! empty( $_GET['eaccounting-action'] ) ? sanitize_key( $_GET['eaccounting-action'] ) : false;

		if ( !empty($key)) {
			do_action( 'eaccounting_admin_get_' . $key, $_GET );
		}

		$key = ! empty( $_POST['eaccounting-action'] ) ? sanitize_key( $_POST['eaccounting-action'] ) : false;

		if ( !empty($key) ) {
			do_action( 'eaccounting_admin_post_' . $key, $_POST );
		}
	}

	/**
	 * Enqueue admin related assets
	 *
	 * @since 1.0.0
	 * @param $hook
	 */
	public function enqueue_scripts( $hook ) {
		if ( ! preg_match( '/accounting/', $hook ) ) {
			return;
		}
		global $wp_scripts;
		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';
		wp_enqueue_style( 'eaccounting-admin', eaccounting()->plugin_url() . '/assets/css/eaccounting-admin.css',[], time() );
		wp_enqueue_style( 'eaccounting-select2', eaccounting()->plugin_url() . '/assets/vendor/select2/select2.css',[], time() );
		wp_enqueue_style( 'eaccounting-fontawesome', eaccounting()->plugin_url() . '/assets/vendor/font-awesome/css/font-awesome.css',[], time() );

		wp_enqueue_script( 'jquery-iframe-transport', eaccounting()->plugin_url() . '/assets/vendor/fileupload/jquery.fileupload.js', array( 'jquery', 'jquery-ui-widget' ), time(), true );
		wp_enqueue_script( 'jquery-fileupload', eaccounting()->plugin_url() . '/assets/vendor/fileupload/jquery.fileupload.js', array( 'jquery', 'jquery-ui-core', 'jquery-iframe-transport' ), time(), true );
		wp_enqueue_script( 'eaccounting-select2', eaccounting()->plugin_url() . '/assets/vendor/select2/select2.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'eaccounting-mask-money', eaccounting()->plugin_url() . '/assets/vendor/mask-money/mask-money.js', array( 'jquery' ), time(), true );
		wp_register_script( 'eaccounting-datepicker', eaccounting()->plugin_url() . '/assets/js/eaccounting-datepicker.js', [ 'jquery', 'jquery-ui-datepicker' ], time(), true );
		wp_enqueue_script( 'eaccounting-fileupload', eaccounting()->plugin_url() . '/assets/js/eaccounting-fileupload.js', array('jquery', 'jquery-fileupload'), time(), true);
		wp_enqueue_script( 'eaccounting-admin', eaccounting()->plugin_url() . '/assets/js/eaccounting-admin.js', array(
			'jquery',
			'wp-util',
			'eaccounting-select2',
			'eaccounting-mask-money',
			'wp-color-picker',
			'eaccounting-datepicker',
		), time(), true );

		wp_localize_script( 'eaccounting-admin', 'eAccountingi18n', array(
			'localization' => array(
//				'thousands_separator' => eaccounting_get_price_thousands_separator(),
//				'decimal_separator'   => eaccounting_get_price_decimal_separator(),
//				'precision'           => (int) eaccounting_get_price_precision(),
//				'price_symbol'        => html_entity_decode( eaccounting_get_price_currency_symbol() ),
//				'symbol_first'        => true,
			)
		) );
	}

}

EAccounting_Admin::instance();
