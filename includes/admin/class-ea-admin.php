<?php

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin {

	/**
	 * EAccounting_Admin constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'admin_init', array( $this, 'buffer' ), 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Output buffering allows admin screens to make redirects later on.
	 */
	public function buffer() {
		ob_start();
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {

		require_once dirname( __FILE__ ) . '/class-ea-ajax-account.php';

		require_once dirname( __FILE__ ) . '/class-ea-settings-api.php';
		require_once dirname( __FILE__ ) . '/class-ea-settings-page.php';
		require_once dirname( __FILE__ ) . '/class-ea-admin-menus.php';
		require_once dirname( __FILE__ ) . '/settings/class-ea-general-settings.php';
		require_once dirname( __FILE__ ) . '/settings/class-ea-localization-settings.php';

		require_once dirname( __FILE__ ) . '/accounts/account-page.php';
		require_once dirname( __FILE__ ) . '/taxes/tax-page.php';
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'eaccounting-admin', ever_accounting()->plugin_url() . '/assets/css/ever-accounting-admin.css', time() );
		wp_enqueue_style( 'eaccounting-fontawesome', ever_accounting()->plugin_url() . '/assets/vendor/font-awesome/css/font-awesome.css', time() );
		wp_register_script( 'eaccounting-accounts', ever_accounting()->plugin_url() . '/assets/js/eaccounting-accounts.js', array( 'jquery' ), time(), true );
	}
}

return new EAccounting_Admin();
