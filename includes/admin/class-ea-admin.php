<?php

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin {
	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  1.0.0
	 */
	private static $instance = null;

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @return self Main instance.
	 * @since  1.0.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * EAccounting_Admin constructor.
	 */
	public function __construct() {
		$this->define_constants();
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'admin_menu', array( $this, 'register_pages' ), 20 );
		add_action( 'admin_init', array( $this, 'setup_files' ) );
		add_action( 'admin_head', array($this,'hide_notices'), 1 );
		add_action( 'admin_body_class', array( $this, 'add_body_class'));
	}

	/**
	 * define all required constants
	 *
	 * since 1.0.0
	 *
	 * @return void
	 */
	public function define_constants() {
		define( 'EACCOUNTING_ADMIN_ABSPATH', dirname( __FILE__ ) );
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		require_once dirname( __FILE__ ) . '/admin-functions.php';
		require_once dirname( __FILE__ ) . '/class-ea-menu-controller.php';
		require_once dirname( __FILE__ ) . '/class-ea-admin-notices.php';
	}


	public function register_pages() {
		$pages = array(
			array(
				'id'       => 'eaccounting',
				'title'    => __( 'Accounting', 'wp-ever-accounting' ),
				'path'     => 'eaccounting',
				'icon'     => 'dashicons-chart-area',
				'position' => 55.5,
			),
			array(
				'id'     => 'eaccounting-dashboard',
				'parent' => 'eaccounting',
				'title'  => 'Dashboard',
				'path'   => 'eaccounting',
			),
			array(
				'id'     => 'eaccounting-transactions',
				'title'  => __( 'Transactions', 'wp-ever-accounting' ),
				'parent' => 'eaccounting',
				'path'   => '/transactions',
			),
			array(
				'id'     => 'eaccounting-items',
				'title'  => __( 'Items', 'wp-ever-accounting' ),
				'parent' => 'eaccounting',
				'path'   => '/items',
			),
//			array(
//				'id'     => 'eaccounting-contacts',
//				'title'  => __( 'Contacts', 'wp-ever-accounting' ),
//				'parent' => 'eaccounting',
//				'path'   => '/contacts',
//			),
			array(
				'id'     => 'eaccounting-sales',
				'title'  => __( 'Sales', 'wp-ever-accounting' ),
				'parent' => 'eaccounting',
				'path'   => '/sales/invoices',
			),
			array(
				'id'     => 'eaccounting-purchases',
				'title'  => __( 'Purchases', 'wp-ever-accounting' ),
				'parent' => 'eaccounting',
				'path'   => '/purchases/bills',
			),
			array(
				'id'     => 'eaccounting-banking',
				'title'  => __( 'Banking', 'wp-ever-accounting' ),
				'parent' => 'eaccounting',
				'path'   => '/banking',
			),
//			array(
//				'id'     => 'eaccounting-misc',
//				'title'  => __( 'Misc', 'wp-ever-accounting' ),
//				'parent' => 'eaccounting',
//				'path'   => '/misc/categories',
//			),
//			array(
//				'id'     => 'eaccounting-reports',
//				'title'  => __( 'Reports', 'wp-ever-accounting' ),
//				'parent' => 'eaccounting',
//				'path'   => '/reports',
//			),
			array(
				'id'     => 'eaccounting-example',
				'title'  => __( 'Example', 'wp-ever-accounting' ),
				'parent' => 'eaccounting',
				'path'   => '/example',
			),
			array(
				'id'     => 'eaccounting-settings',
				'title'  => __( 'Settings', 'wp-ever-accounting' ),
				'parent' => 'eaccounting',
				'path'   => '/settings',
			),
		);

		$admin_pages = apply_filters( 'eaccounting_menu_items', $pages );


		foreach ( $admin_pages as $page ) {
			if ( ! is_null( $page ) ) {
				eaccounting_register_page( $page );
			}
		}
	}

	/**
	 * Set up files
	 *
	 * @since 1.0.0
	 */
	public function setup_files() {
		eaccounting_protect_files();
	}

	/**
	 * Set up a div for the app to render into.
	 */
	public static function page_wrapper() {
		?>
		<div id="eaccounting"></div>
		<?php
	}

	/**
	 * Remove all notices from out plugin page
	 * @since 1.0.0
	 */
	function hide_notices() {
		global $current_screen;
		if( is_object( $current_screen ) && $current_screen->id == 'toplevel_page_eaccounting' ){
			remove_all_actions( 'admin_notices' );
		}
	}


	/**
	 * Set custom class for body when in eaccounting page
	 * @since 1.0.0
	 * @return bool|string
	 */
	public function add_body_class(){
		$current_screen = get_current_screen();
		if(!isset( $current_screen->base)){
			return false;
		}
		if($current_screen->base !== 'toplevel_page_eaccounting'){
			return false ;
		}

		return 'eaccounting';
	}


}

EAccounting_Admin::instance();
