<?php

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin{

	/**
	 * EAccounting_Admin constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'admin_init', array( $this, 'buffer' ), 1 );
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
		include_once dirname( __FILE__ ) . '/class-ea-admin-menus.php';
		include_once dirname( __FILE__ ) . '/class-ea-settings-api.php';
		include_once dirname( __FILE__ ) . '/class-ea-settings-page.php';
		include_once dirname( __FILE__ ) . '/settings/class-ea-general-settings.php';
		include_once dirname( __FILE__ ) . '/settings/class-ea-localization-settings.php';
	}
}

return new EAccounting_Admin();
