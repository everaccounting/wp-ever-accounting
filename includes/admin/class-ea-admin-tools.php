<?php
/**
 * Admin Tools Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Tools
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Tools {
	/**
	 * EAccounting_Admin_Banking constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_page' ), 70 );
		add_action( 'eaccounting_tools_page_tab_export', array( $this, 'render_export_page' ), 20 );
		add_action( 'eaccounting_tools_page_tab_import', array( $this, 'render_import_page' ), 20 );
		add_action( 'eaccounting_tools_page_tab_system_info', array( $this, 'render_system_info_page' ), 20 );
	}

	/**
	 * Registers the reports page.
	 *
	 */
	public function register_page() {
		add_submenu_page(
			'eaccounting',
			__( 'Tools', 'wp-ever-accounting' ),
			__( 'Tools', 'wp-ever-accounting' ),
			'manage_eaccounting',
			'ea-tools',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Get tools page tabs.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function get_tabs() {
		$tabs = array();
		if ( current_user_can( 'ea_import' ) ) {
			$tabs['import'] = __( 'Import', 'wp-ever-accounting' );
		}
		if ( current_user_can( 'ea_export' ) ) {
			$tabs['export'] = __( 'Export', 'wp-ever-accounting' );
		}
		$tabs['system_info'] = __( 'System Info', 'wp-ever-accounting' );

		return apply_filters( 'eaccounting_tools_tabs', $tabs );
	}

	/**
	 * Render page.
	 *
	 * @since 1.1.0
	 */
	public function render_page() {
		$tabs        = $this->get_tabs();
		$first_tab   = current( array_keys( $tabs ) );
		$current_tab = ! empty( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? sanitize_title( $_GET['tab'] ) : $first_tab;
		include dirname( __FILE__ ) . '/views/admin-page-tools.php';
	}

	/**
	 * Render Export page.
	 *
	 * @since 1.0.2
	 */
	public function render_export_page() {
		include dirname( __FILE__ ) . '/views/tools/export.php';
	}

	/**
	 * Render Import page.
	 *
	 * @since 1.0.2
	 */
	public function render_import_page() {
		include dirname( __FILE__ ) . '/views/tools/import.php';
	}
	/**
	 * Render Import page.
	 *
	 * @since 1.0.2
	 */
	public function render_system_info_page() {
		require_once dirname(__FILE__).'/views/tools/system-info.php';
		include dirname( __FILE__ ) . '/views/tools/system_info.php';
	}

}

new EAccounting_Admin_Tools();
