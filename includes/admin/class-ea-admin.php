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
		add_action( 'admin_menu', array( $this, 'register_pages' ), 20 );
//		add_action( 'admin_init', array( $this, 'set_eaccounting_actions' ) );
		add_action( 'admin_init', array( $this, 'setup_files' ) );
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
		require_once dirname( __FILE__ ) . '/class-ea-menu-controller.php';
		require_once dirname( __FILE__ ) . '/class-ea-admin-notices.php';
		require_once dirname( __FILE__ ) . '/tables/class-ea-admin-list-table.php';
		require_once dirname( __FILE__ ) . '/settings/class-ea-settings.php';

		require_once dirname( __FILE__ ) . '/actions/contact-actions.php';
		require_once dirname( __FILE__ ) . '/actions/category-actions.php';
		require_once dirname( __FILE__ ) . '/actions/revenue-actions.php';
		require_once dirname( __FILE__ ) . '/actions/payment-actions.php';
		require_once dirname( __FILE__ ) . '/actions/account-actions.php';
		require_once dirname( __FILE__ ) . '/actions/transfer-actions.php';
		require_once dirname( __FILE__ ) . '/actions/tax-actions.php';
	}


	public function register_pages() {
		$pages = array(
			array(
				'id'       => 'eaccounting',
				'title'    => __( 'Accounting', 'wp-ever-accounting' ),
				'path'     => 'eaccounting',
				'icon'     => 'dashicons-chart-area',
				'position' => 55.5,
			),
			array(
				'id'     => 'eaccounting-dashboard',
				'parent' => 'eaccounting',
				'title'  => 'Dashboard',
				'path'   => 'eaccounting',
			),
			array(
				'id'         => 'eaccounting-transactions',
				'title'      => __( 'Transactions', 'wp-ever-accounting' ),
				'parent'     => 'eaccounting',
				'path'       => '/transactions',
			),
			array(
				'id'         => 'eaccounting-items',
				'title'      => __( 'Items', 'wp-ever-accounting' ),
				'parent'     => 'eaccounting',
				'path'       => '/items',
			),
			array(
				'id'         => 'eaccounting-contacts',
				'title'      => __( 'Contacts', 'wp-ever-accounting' ),
				'parent'     => 'eaccounting',
				'path'       => '/contacts',
			),
			array(
				'id'         => 'eaccounting-incomes',
				'title'      => __( 'Incomes', 'wp-ever-accounting' ),
				'parent'     => 'eaccounting',
				'path'       => '/incomes',
			),
			array(
				'id'         => 'eaccounting-expenses',
				'title'      => __( 'Expenses', 'wp-ever-accounting' ),
				'parent'     => 'eaccounting',
				'path'       => '/expenses',
			),
			array(
				'id'         => 'eaccounting-banking',
				'title'      => __( 'Banking', 'wp-ever-accounting' ),
				'parent'     => 'eaccounting',
				'path'       => '/banking',
			),
			array(
				'id'         => 'eaccounting-misc',
				'title'      => __( 'Misc', 'wp-ever-accounting' ),
				'parent'     => 'eaccounting',
				'path'       => '/misc',
			),
		);

		$admin_pages = apply_filters( 'woocommerce_analytics_report_menu_items', $pages );


		foreach ( $admin_pages as $page ) {
			if ( ! is_null( $page ) ) {
				eaccounting_register_page( $page );
			}
		}
	}

	/**
	 * Setup eaccounting actions
	 *
	 * since 1.0.0
	 */
	public function set_eaccounting_actions() {

		$key = ! empty( $_GET['eaccounting-action'] ) ? sanitize_key( $_GET['eaccounting-action'] ) : false;

		if ( ! empty( $key ) ) {
			error_log( 'eaccounting_admin_get_' . $key );
			do_action( 'eaccounting_admin_get_' . $key, $_GET );
		}

		$key = ! empty( $_POST['eaccounting-action'] ) ? sanitize_key( $_POST['eaccounting-action'] ) : false;

		if ( ! empty( $key ) ) {
			do_action( 'eaccounting_admin_post_' . $key, $_POST );
		}
	}

	/**
	 * Set up files
	 *
	 * @since 1.0.0
	 */
	public function setup_files() {
		eaccounting_protect_files();
	}

	/**
	 * Enqueue admin related assets
	 *
	 * @param $hook
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts( $hook ) {
		if ( ! preg_match( '/accounting/', $hook ) ) {
			return;
		}
		$assets = untrailingslashit( eaccounting()->plugin_url() ) . '/assets';
		$js     = $assets . '/js';
		$css    = $assets . '/css';
		$vendor = $assets . '/vendor';
		$dist   = eaccounting()->plugin_url() . '/dist';

		$app_deps_path        = EACCOUNTING_ABSPATH . '/dist/app/index.deps.json';
		$components_deps_path = EACCOUNTING_ABSPATH . '/dist/components/index.deps.json';
		$app_dependencies     = file_exists( $app_deps_path )
			? json_decode( file_get_contents( $app_deps_path ) )
			: array();

		$component_dependencies = file_exists( $components_deps_path )
			? json_decode( file_get_contents( $components_deps_path ) )
			: array();

		wp_enqueue_style( 'eaccounting-components', $dist . '/components/style.css', array(
			'wp-components',
			'editor-buttons',
			'wp-editor'
		), time() );

		wp_enqueue_style( 'eaccounting', $dist . '/app/style.css', array(
			'wp-components',
			'editor-buttons',
			'wp-editor',
			'eaccounting-components'
		), time() );


		wp_register_script(
			'eaccounting-navigation',
			$dist . '/navigation/index.js',
			[],
			time(),
			true
		);

		wp_register_script(
			'eaccounting-components',
			$dist . '/components/index.js',
			array_merge( $component_dependencies, array(
				'wp-hooks',
				'wp-element',
				'wp-editor',
				'wp-i18n',
				'wp-tinymce',
			) ),
			time(),
			true
		);

		wp_register_script(
			'eaccounting',
			$dist . '/app/index.js',
			array_merge( $app_dependencies, array(
				'wp-hooks',
				'wp-element',
				'wp-editor',
				'wp-i18n',
				'wp-tinymce',
				'eaccounting-components',
				'eaccounting-navigation'
			) ),
			time(),
			true
		);

		wp_localize_script( 'eaccounting', 'eAccountingi10n', [
			'api'              => [
				'WP_API_root'  => esc_url_raw( get_rest_url() ),
				'WP_API_nonce' => wp_create_nonce( 'wp_rest' ),
			],
			'pluginBaseUrl'    => plugins_url( '', EACCOUNTING_PLUGIN_FILE ),
			'pluginRoot'       => admin_url( 'admin.php?page=eaccounting' ),
			'per_page'         => 20,
			'default_currency' => eaccounting_get_default_currency(),
		] );

		wp_enqueue_script( 'eaccounting' );

		wp_enqueue_style( 'eaccounting-fontawesome', $vendor . '/font-awesome/css/font-awesome.css', [], time() );

		//styles
//		wp_enqueue_style( 'eaccounting-jquery-ui', $vendor . '/jquery-ui/jquery-ui.css', false, time() );
//		wp_enqueue_style( 'eaccounting-select2', $vendor . '/vendor/select2/select2.css', [], time() );
//		wp_enqueue_style( 'eaccounting-admin', $css . '/eaccounting-admin.css', array(), time() );
//
//
//		//scripts
//		wp_register_script( 'chart-js', $vendor . '/chartjs/chart.bundle.min.js', array( 'jquery' ), time(), true );
//		wp_enqueue_script( 'jquery-iframe-transport', $vendor . '/fileupload/jquery.fileupload.js', array(
//			'jquery',
//			'jquery-ui-widget'
//		), time(), true );
//		wp_enqueue_script( 'jquery-fileupload', $vendor . '/fileupload/jquery.fileupload.js', array(
//			'jquery',
//			'jquery-ui-core',
//			'jquery-iframe-transport'
//		), time(), true );
//		wp_enqueue_script( 'eaccounting-select2', $vendor . '/select2/select2.js', array( 'jquery' ), time(), true );
//		wp_enqueue_script( 'eaccounting-validate', $vendor . '/validate/jquery.validate.js', array( 'jquery' ), time(), true );
//		wp_enqueue_script( 'eaccounting-inputmask', $vendor . '/inputmask/jquery.inputmask.js', array( 'jquery' ), time(), true );
//		wp_register_script( 'eaccounting-datepicker', $js . '/eaccounting-datepicker.js', [
//			'jquery',
//			'jquery-ui-datepicker'
//		], time(), true );
//		wp_enqueue_script( 'eaccounting-fileupload', $js . '/eaccounting-fileupload.js', array(
//			'jquery',
//			'jquery-fileupload'
//		), time(), true );
//		wp_register_script( 'eaccounting-invoice', $js . '/eaccounting-invoice.js', array( 'jquery' ), time(), true );
//		wp_register_script( 'eaccounting-dashboard', $js . '/eaccounting-dashboard.js', array(
//			'jquery',
//			'chart-js'
//		), time(), true );
//		wp_enqueue_script( 'eaccounting-form', $js . '/eaccounting-modal.js', array( 'jquery', 'wp-util', 'underscore', 'backbone' ), time(), true );
//		wp_enqueue_script( 'eaccounting-form', $js . '/eaccounting-form.js', array( 'jquery' ), time(), true );
//		wp_enqueue_script( 'eaccounting-admin', $js . '/eaccounting-admin.js', array(
//			'jquery',
//			'wp-util',
//			'eaccounting-select2',
//			'eaccounting-inputmask',
//			'wp-color-picker',
//			'eaccounting-datepicker'
//		), time(), true );

//		wp_localize_script( 'eaccounting-admin', 'eAccountingi18n', array(
//			'localization' => array(
//				'thousands_separator' => eaccounting_get_price_thousands_separator(),
//				'decimal_separator'   => eaccounting_get_price_decimal_separator(),
//				'precision'           => (int) eaccounting_get_price_precision(),
//				'price_symbol'        => html_entity_decode( eaccounting_get_price_currency_symbol() ),
//				'symbol_first'        => true,
//			),
//		) );

//		wp_localize_script( 'eaccounting-dashboard', 'eAccountingi18n', array(
//			'localization' => array(
//				'ajax_url' => admin_url( 'admin-ajax.php' ),
//			)
//		) );
//
//		wp_localize_script( 'eaccounting-form', 'eAccountingi18n', array(
//			'ajax_url' => admin_url( 'admin-ajax.php' ),
//		) );

	}

	/**
	 * Set up a div for the app to render into.
	 */
	public static function page_wrapper() {
		?>
		<div class="wrap eaccounting">
			<div id="eaccounting"></div>
		</div>
		<?php
	}
}

EAccounting_Admin::instance();
