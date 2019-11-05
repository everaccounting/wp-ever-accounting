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
	}
}

return new EAccounting_Admin();
