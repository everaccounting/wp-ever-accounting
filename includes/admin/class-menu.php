<?php
/**
 * Handles admin related menus.
 */

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit();

/**
 * Class Menu
 * @package EverAccounting\Admin
 */
class Menu{

	/**
	 * Menu constructor.
	 */
	public function __construct() {
		//Register menus.
		add_action( 'admin_menu', array( $this, 'register_parent_page' ), 1 );
		add_action( 'admin_menu', array( $this, 'register_items_page' ), 20 );

		//Register tabs.
		add_action( 'eaccounting_items_page_tab_items', array( $this, 'render_items_tab' ), 20 );
	}

	/**
	 * Registers the overview page.
	 *
	 * @since 1.1.0
	 */
	public function register_parent_page() {
		global $menu;

		if ( current_user_can( 'manage_eaccounting' ) ) {
			$menu[] = array( '', 'read', 'ea-separator', '', 'wp-menu-separator accounting' );
		}
		$icons = 'data:image/svg+xml;base64,' . base64_encode( file_get_contents( eaccounting()->plugin_path( 'assets/images/icon.svg' ) ) );

		add_menu_page(
			__( 'Accounting', 'wp-ever-accounting' ),
			__( 'Accounting', 'wp-ever-accounting' ),
			'manage_eaccounting',
			'eaccounting',
			null,
			$icons,
			'54.5'
		);
		add_submenu_page(
			'eaccounting',
			__( 'Overview', 'wp-ever-accounting' ),
			__( 'Overview', 'wp-ever-accounting' ),
			'manage_eaccounting',
			'eaccounting',
			array( $this, 'render_overview_page' )
		);
	}

	/**
	 * Registers the reports page.
	 *
	 */
	public function register_items_page() {
		add_submenu_page(
			'eaccounting',
			__( 'Items', 'wp-ever-accounting' ),
			__( 'Items', 'wp-ever-accounting' ),
			'manage_eaccounting',
			'ea-items',
			array( $this, 'render_items_page' )
		);
	}

	/**
	 * Render overview page.
	 *
	 * @since 1.1.0
	 */
	public function render_overview_page() {
		include dirname( __FILE__ ) . '/views/admin-page-overview.php';
	}

	/**
	 * Render items page.
	 *
	 * @since 1.1.0
	 */
	public function render_items_page() {
		$tabs = array();
		if ( current_user_can( 'ea_manage_item' ) ) {
			$tabs['items'] = __( 'Items', 'wp-ever-accounting' );
		}
		$tabs = apply_filters( 'eaccounting_item_tabs', $tabs );
		$first_tab   = current( array_keys( $tabs ) );
		$current_tab = ! empty( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? sanitize_title( $_GET['tab'] ) : $first_tab;
		include dirname( __FILE__ ) . '/views/admin-page-items.php';
	}

	/**
	 * Render Items tab.
	 *
	 * @since 1.1.0
	 */
	public function render_items_tab() {
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$item_id = isset( $_GET['item_id'] ) ? absint( $_GET['item_id'] ) : null;
			include dirname( __FILE__ ) . '/views/items/edit-item.php';
		} else {
			include dirname( __FILE__ ) . '/views/items/list-item.php';
		}
	}

}

new Menu();
