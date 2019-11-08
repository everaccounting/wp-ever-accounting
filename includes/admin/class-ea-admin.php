<?php

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin {

	/**
	 * EAccounting_Admin constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'admin_init', array( $this, 'buffer' ), 1 );
		add_action( 'admin_init', array( $this, 'set_eaccounting_actions' ));
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
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
	 * Include any classes we need within admin.
	 */
	public function includes() {

		require_once dirname( __FILE__ ) . '/class-ea-ajax-product.php';
		require_once dirname( __FILE__ ) . '/class-ea-ajax-account.php';
		require_once dirname( __FILE__ ) . '/class-ea-ajax-tax.php';

		require_once dirname( __FILE__ ) . '/class-ea-settings-api.php';
		require_once dirname( __FILE__ ) . '/class-ea-settings-page.php';
		require_once dirname( __FILE__ ) . '/class-ea-admin-menus.php';
		require_once dirname( __FILE__ ) . '/settings/class-ea-general-settings.php';
		require_once dirname( __FILE__ ) . '/settings/class-ea-localization-settings.php';

		//product
		require_once dirname( __FILE__ ) . '/products/products-page.php';
		require_once dirname( __FILE__ ) . '/products/product-actions.php';

		//pages
		require_once dirname( __FILE__ ) . '/expense/expense-page.php';
		require_once dirname( __FILE__ ) . '/income/income-page.php';
		require_once dirname( __FILE__ ) . '/banking/banking-page.php';
		require_once dirname( __FILE__ ) . '/contacts/contacts-page.php';
		require_once dirname( __FILE__ ) . '/tools/tools-page.php';

		//banking tabs
		require_once dirname( __FILE__ ) . '/banking/accounts/account-actions.php';
		require_once dirname( __FILE__ ) . '/banking/accounts/accounts-tab.php';

//		require_once dirname( __FILE__ ) . '/expense/expense-page.php';
//		require_once dirname( __FILE__ ) . '/accounts/account-page.php';
//		require_once dirname( __FILE__ ) . '/taxes/tax-page.php';
		require_once dirname( __FILE__ ) . '/products/products-page.php';
	}

	public function enqueue_scripts( $hook ) {
		if ( ! preg_match( '/accounting/', $hook ) ) {
			return;
		}
		wp_enqueue_style( 'eaccounting-admin', eaccounting()->plugin_url() . '/assets/css/ever-accounting-admin.css', time() );
		wp_enqueue_style( 'eaccounting-select2', eaccounting()->plugin_url() . '/assets/vendor/select2/select2.css', time() );
		wp_enqueue_style( 'eaccounting-toastr', eaccounting()->plugin_url() . '/assets/vendor/toastr/toastr.min.css', time() );
		wp_enqueue_style( 'eaccounting-fontawesome', eaccounting()->plugin_url() . '/assets/vendor/font-awesome/css/font-awesome.css', time() );

		wp_enqueue_script( 'eaccounting-select2', eaccounting()->plugin_url() . '/assets/vendor/select2/select2.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'eaccounting-mask-money', eaccounting()->plugin_url() . '/assets/vendor/mask-money/mask-money.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'eaccounting-toastr', eaccounting()->plugin_url() . '/assets/vendor/toastr/toastr.min.js', array( 'jquery' ), time(), true );
		wp_enqueue_script( 'eaccounting-admin', eaccounting()->plugin_url() . '/assets/js/eaccounting-admin.js', array( 'jquery', 'eaccounting-select2', 'eaccounting-mask-money', 'wp-util' ), time(), true );

		wp_localize_script( 'eaccounting-admin', 'eAccountingi18n', array(
			'localization' => array(
				'thousands_separator' => eaccounting_get_price_thousands_separator(),
				'decimal_separator'   => eaccounting_get_price_decimal_separator(),
				'precision'           => (int)eaccounting_get_price_precision(),
				'price_symbol'        => html_entity_decode(eaccounting_get_price_currency_symbol()),
				'symbol_first'        => true,
			)
		) );

//		wp_register_script( 'eaccounting-products', eaccounting()->plugin_url() . '/assets/js/eaccounting-products.js', array(
//			'jquery',
//			'wp-util'
//		), time(), true );
//		wp_register_script( 'eaccounting-accounts', eaccounting()->plugin_url() . '/assets/js/eaccounting-accounts.js', array(
//			'jquery',
//			'eaccounting-toastr'
//		), time(), true );
//		wp_register_script( 'eaccounting-taxes', eaccounting()->plugin_url() . '/assets/js/eaccounting-taxes.js', array( 'jquery' ), time(), true );
//		wp_register_script( 'eaccounting-form', eaccounting()->plugin_url() . '/assets/js/eaccounting-form.js', array(
//			'jquery',
//			'eaccounting-select2',
//			'eaccounting-mask-money'
//		), time(), true );
//		wp_localize_script( 'eaccounting-form', 'Eaccountingi18n', array(
//			'localization' => array(
//				'thousands_separator' => eaccounting_get_price_thousands_separator(),
//				'decimal_separator'   => eaccounting_get_price_decimal_separator(),
//				'precision'           => eaccounting_get_price_precision(),
//				'price_symbol'        => eaccounting_get_price_currency_symbol(),
//			)
//		) );
//		wp_enqueue_script( 'eaccounting-notify' );
		wp_enqueue_script( 'eaccounting-admin' );
	}
}

return new EAccounting_Admin();
