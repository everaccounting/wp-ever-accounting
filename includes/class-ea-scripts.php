<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Scripts {
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
	 * EAccounting_Scripts constructor.
	 */
	private function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}


	/**
	 * since 1.0.0
	 *
	 * @param $hook
	 */
	public function register_scripts() {

		$app_dependencies  = require_once EACCOUNTING_ABSPATH . '/dist/app/index.asset.php';
		$comp_dependencies = require_once EACCOUNTING_ABSPATH . '/dist/components/index.asset.php';

		wp_register_script(
			'eaccounting-components',
			self::get_url( 'components/index.js' ),
			$comp_dependencies['dependencies'],
			self::get_file_version( 'components/index.js' ),
			true
		);

		wp_set_script_translations( 'eaccounting-components', 'wp-ever-accounting' );

		wp_register_script(
			'eaccounting',
			self::get_url( 'app/index.js' ),
			array_merge( $app_dependencies['dependencies'], [ 'eaccounting-components' ] ),
			self::get_file_version( 'app/index.js' ),
			true
		);

		wp_set_script_translations( 'eaccounting', 'wp-ever-accounting' );

		wp_register_style(
			'eaccounting-components',
			self::get_url( 'components/style.css' ),
			array( 'wp-components' ),
			self::get_file_version( 'components/style.css' )
		);

		wp_register_style(
			'eaccounting',
			self::get_url( 'app/style.css' ),
			array( 'wp-components', 'eaccounting-components' ),
			self::get_file_version( 'app/style.css' )
		);

		wp_enqueue_style(
			'eaccounting-fontawesome',
			EACCOUNTING_ASSETS_URL . '/vendor/font-awesome/css/font-awesome.css',
			array(),
			self::get_file_version( 'app/style.css' )
		);

	}

	/**
	 * since 1.0.0
	 *
	 * @param $hook
	 */
	public function enqueue_scripts( $hook ) {
		if ( ! preg_match( '/accounting/', $hook ) ) {
			return;
		}
		wp_localize_script( 'eaccounting', 'eAccountingi10n', $this->get_localized_data() );
		wp_enqueue_script( 'eaccounting-components' );
		wp_enqueue_script( 'eaccounting' );
		wp_enqueue_style( 'eaccounting-components' );
		wp_enqueue_style( 'eaccounting' );
	}

	/**
	 * Gets the path for the asset depending on file type.
	 *
	 * @return string Folder path of asset.
	 */
	private static function get_path() {
		return '/dist/';
	}

	/**
	 * Gets the URL to an asset file.
	 *
	 * @param string $file name.
	 *
	 * @return string URL to asset.
	 */
	public static function get_url( $file ) {
		return plugins_url( self::get_path() . $file, EACCOUNTING_PLUGIN_FILE );
	}

	/**
	 * Gets the file modified time as a cache buster if we're in dev mode, or the plugin version otherwise.
	 *
	 * @param $file string path to the file.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public static function get_file_version( $file ) {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			return filemtime( EACCOUNTING_PLUGIN_FILE );
		}

		return EACCOUNTING_VERSION;
	}


	/**
	 * since 1.0.0
	 * @return mixed|void
	 */
	private function get_localized_data() {
		$data = [
			'api'           => [
				'WP_API_root'  => esc_url_raw( get_rest_url() ),
				'WP_API_nonce' => wp_create_nonce( 'wp_rest' ),
			],
			'pluginBaseUrl' => plugins_url( '', EACCOUNTING_PLUGIN_FILE ),
			'pluginRoot'    => admin_url( 'admin.php?page=eaccounting' ),
			'baseUrl'       => get_site_url(),
			'per_page'      => 20,
			'data'          => [
				'transactionTypes' => eaccounting_get_transaction_types(),
				'currency'         => eaccounting_get_default_currency(),
				'paymentMethods'   => eaccounting_get_payment_methods(),
				'account'          => eaccounting_get_default_account(),
				'currencies'       => eaccounting_get_currencies_data(),
				'categoryTypes'    => eaccounting_get_category_types(),
				'taxRateTypes'     => eaccounting_get_tax_types(),
			]
		];


		return apply_filters( 'eaccounting_localized_data', $data );
	}

}

EAccounting_Scripts::instance();
