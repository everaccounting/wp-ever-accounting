<?php
/**
 * Shows whats new.
 *
 * @package    EverAccounting
 * @subpackage Admin
 * @version    1.0.2
 */

defined( 'ABSPATH' ) || exit;

class EverAccounting_Admin_Release {

	/**
	 * EverAccounting_Admin_Sales constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_page' ), 30 );
	}

	/**
	 * Registers the new releases page.
	 *
	 */
	public function register_page() {
		add_options_page(
			__( "What's New", 'wp-ever-accounting' ),
			__( "What's New Page", 'wp-ever-accounting' ),
			'manage_eaccounting',
			'ea-release',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render page.
	 *
	 * @since 1.1.0
	 */
	public function render_page() {
		include dirname( __FILE__ ) . '/views/admin-page-release.php';

	}
}

new EverAccounting_Admin_Release();
