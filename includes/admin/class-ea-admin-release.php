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
		add_action( 'admin_menu', array( $this, 'register_page' ) );
//		add_action( 'admin_menu', array( $this, 'register_page' ), 30 );
		//add_filter( 'admin_title', array( $this, 'admin_title' ), 10 );
	}

	/**
	 * Registers the new releases page.
	 *
	 */
	public function register_page() {
		add_dashboard_page( '', '', 'manage_options', 'ea-release', array( $this, 'render_page') );
	}

	/**
	 * Render page.
	 *
	 * @since 1.1.0
	 */
	public function render_page() {
		$version = eaccounting()->get_version();
		$suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'ea-admin-styles', eaccounting()->plugin_url() . '/assets/css/admin.css', array(), $version );
		wp_enqueue_style( 'ea-release-styles', eaccounting()->plugin_url() . '/assets/css/release.css', array(), $version );

		include dirname( __FILE__ ) . '/views/admin-page-release.php';
	}

	public function admin_title() {
		$title  = '';
		$title .= '<div class="ea-release_logo">';
		$title .= '<img scr="' . eaccounting()->plugin_url( '/assets/images/everaccountinglogo.png' ) . '" alt="ea-release-logo">';
		$title .= '</div>';
		$title .= '<div class="ea-release_tag">';
		$title .= '<h2 class="wp-heading-inline">' . esc_html__( 'Best WordPress Accounting Plugin', 'wp-ever-accounting' ) . '</h2>';
		$title .= '</div>';
		return $title;

	}
}

new EverAccounting_Admin_Release();