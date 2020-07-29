<?php
/**
 * EverAccounting Admin
 *
 * @class    EAccounting_Admin
 * @package  EverAccounting/Admin
 * @version  1.0.2
 */


defined( 'ABSPATH' ) || exit();

/**
 * Class EAccounting_Admin
 * @since 1.0.2
 */
class EAccounting_Admin {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'admin_init', array( $this, 'buffer' ), 1 );
		add_action( 'admin_footer', 'eaccounting_print_js', 25 );
		//add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
	}

	/**
	 * Output buffering allows admin screens to make redirects later on.
	 *
	 * @return void
	 * @since 1.0.2
	 */
	public function buffer() {
		ob_start();
	}


	/**
	 * Include any classes we need within admin.
	 *
	 * @return void
	 * @since 1.0.2
	 */
	public function includes() {
		include_once __DIR__ . '/ea-admin-functions.php';
	}
}

return new EAccounting_Admin();
