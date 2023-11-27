<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Menus.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Menus extends \EverAccounting\Singleton {

	/**
	 * Menus constructor.
	 */
	public function __construct() {
		// filter admin body class.
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_menu', array( $this, 'remove_menus' ), 20 );
	}

	/**
	 * Add admin body class.
	 *
	 * @param string $classes
	 * @return string
	 */
	public function admin_body_class( $classes ) {
		$classes .= ' eac-admin';
		return $classes;
	}

	/**
	 * Add admin menu.
	 */
	public function admin_menu() {
		$pages = array(
			array(
				'title' => __( 'Dashboard', 'ever-accounting-client' ),
				'path'  => '/',
			),
			array(
				'title'  => __( 'Items', 'ever-accounting-client' ),
				'path'   => '/items',
			),
			array(
				'title'  => __( 'Sales', 'ever-accounting-client' ),
				'path'   => '/sales',
			),
			array(
				'title'  => __( 'Purchases', 'ever-accounting-client' ),
				'path'   => '/purchases',
			),
			array(
				'title'  => __( 'Banking', 'ever-accounting-client' ),
				'path'   => '/banking',
			),
			array(
				'title'  => __( 'Reports', 'ever-accounting-client' ),
				'path'   => '/reports',
			),
			array(
				'title'  => __( 'Tools', 'ever-accounting-client' ),
				'path'   => '/tools',
			),
			array(
				'title'  => __( 'Settings', 'ever-accounting-client' ),
				'path'   => '/settings',
			),
			array(
				'title'  => __( 'Add-ons', 'ever-accounting-client' ),
				'path'   => '/addons',
			),
			array(
				'title'  => __( 'Help', 'ever-accounting-client' ),
				'path'   => '/help',
			),
		);

		$submenus = apply_filters( 'ever_accounting_submenus', $pages );
		// sort submenus by position.
		usort( $submenus, function( $a, $b ) {
			$a['position'] = isset( $a['position'] ) ? $a['position'] : 10;
			$b['position'] = isset( $b['position'] ) ? $b['position'] : 10;
			return $a['position'] <=> $b['position'];
		} );

		// add main menu.
		add_menu_page(
			__( 'Accounting', 'ever-accounting-client' ),
			__( 'Accounting', 'ever-accounting-client' ),
			'manage_options',
			'accounting',
			array( $this, 'page_wrapper' ),
			'dashicons-chart-area',
			2.2
		);

		// add submenus.
		foreach ( $submenus as $submenu ) {
			$submenu = wp_parse_args( $submenu, array(
				'title'      => '',
				'capability' => 'read',
				'path'       => '',
			) );

			add_submenu_page(
				'accounting',
				$submenu['title'],
				$submenu['title'],
				$submenu['capability'],
				'accounting#/' . ltrim( $submenu['path'], '/' ),
				array( $this, 'page_wrapper' )
			);
		}
	}

	/**
	 * Remove menus.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function remove_menus() {
		remove_submenu_page( 'accounting', 'accounting' );
	}

	/**
	 * Set up a div for the app to render into.
	 *
	 * @since 1.0.0
	 */
	public static function page_wrapper() {
		?>
        <div class="wrap">
            <div id="eac-admin-root" class="eac-app">
                <div class="eac-app-loading">
                    <span class="eac-app-loading__spinner"></span>
                    <span class="eac-app-loading__text"><?php esc_html_e( 'Loading...', 'ever-accounting-client' ); ?></span>
                </div>
            </div>
        </div>
		<?php
	}
}
