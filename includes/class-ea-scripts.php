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
		//scripts
		wp_register_script(
			'eaccounting-components',
			self::get_url( 'components.js' ),
			self::get_asset_prop( 'components', 'dependencies' ),
			self::get_asset_prop( 'components', 'version' ),
			true
		);
		wp_set_script_translations( 'eaccounting-components', 'wp-ever-accounting' );

		wp_register_script(
			'eaccounting-data',
			self::get_url( 'data.js' ),
			self::get_asset_prop( 'data', 'dependencies' ),
			self::get_asset_prop( 'data', 'version' ),
			true
		);
		wp_set_script_translations( 'eaccounting-data', 'wp-ever-accounting' );

		wp_register_script(
			'eaccounting',
			self::get_url( 'eaccounting.js' ),
			array_merge( self::get_asset_prop( 'data', 'dependencies' ), [ 'eaccounting-data', 'eaccounting-components' ] ),
			self::get_asset_prop( 'data', 'version' ),
			true
		);
		wp_set_script_translations( 'eaccounting', 'wp-ever-accounting' );

		wp_register_style(
			'eaccounting-components',
			self::get_url( 'components.css' ),
			array( 'wp-components' ),
			self::get_asset_prop( 'components', 'version' )
		);

		wp_register_style(
			'eaccounting',
			self::get_url( 'eaccounting.css' ),
			array( 'wp-components' ),
			self::get_asset_prop( 'eaccounting', 'version' )
		);

		wp_enqueue_style(
			'eaccounting-fontawesome',
			EACCOUNTING_ASSETS_URL . '/vendor/font-awesome/css/font-awesome.css',
			array(),
			self::get_asset_prop( 'eaccounting', 'version' )
		);
	}

	/**
	 * since 1.0.0
	 * @param $file
	 * @param $prop
	 *
	 * @return mixed
	 */
	public static function get_asset_prop( $file, $prop ) {
		$file_path = EACCOUNTING_ABSPATH . '/assets/dist/' . $file . '.asset.php';
		try {
			$props = require $file_path;

			return $props[ $prop ];
		} catch ( Exception $exception ) {
			wp_die( $exception );
		}
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

		wp_localize_script( 'eaccounting', 'eAccounting', $this->get_localized_data() );
		wp_enqueue_script( 'eaccounting' );
		wp_enqueue_style( 'eaccounting-components' );
		wp_enqueue_style( 'eaccounting' );
	}

	/**
	 * Gets the URL to an asset file.
	 *
	 * @param string $file name.
	 *
	 * @return string URL to asset.
	 */
	public static function get_url( $file ) {
		return plugins_url( '/assets/dist/' . $file, EACCOUNTING_PLUGIN_FILE );
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
				'site_formats'    => array(
					'date_formats' => eaccounting_convert_php_to_moment_formats()
				),
				'currency_config' => eaccounting_get_currency_config()
//				'transactionTypes' => eaccounting_get_transaction_types(),
//				'currency'         => eaccounting_get_default_currency(),
//				'paymentMethods'   => eaccounting_get_payment_methods(),
//				'account'          => eaccounting_get_default_account(),
//				'currencies'       => eaccounting_get_currencies_data(),
//				'categoryTypes'    => eaccounting_get_category_types(),
//				'taxRateTypes'     => eaccounting_get_tax_types(),
			]
		];


		return apply_filters( 'eaccounting_localized_data', $data );
	}

}

EAccounting_Scripts::instance();
