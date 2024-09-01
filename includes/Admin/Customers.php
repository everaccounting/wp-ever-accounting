<?php

namespace EverAccounting\Admin;

use EverAccounting\Admin\ListTables\CustomersTable;

defined( 'ABSPATH' ) || exit;

/**
 * Customers class.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\Sales
 */
class Customers {

	/**
	 * Customers constructor.
	 */
	public function __construct() {
		add_filter( 'eac_sales_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'load_eac_sales_page_customers_home', array( __CLASS__, 'setup_table' ) );
		add_action( 'eac_sales_page_customers_home', array( __CLASS__, 'render_table' ) );
		add_action( 'eac_sales_page_customers_add', array( __CLASS__, 'render_add' ) );
		add_action( 'eac_sales_page_customers_edit', array( __CLASS__, 'render_edit' ) );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		$tabs['customers'] = __( 'Customers', 'wp-ever-accounting' );

		return $tabs;
	}

	/**
	 * Setup customers table.
	 *
	 * @since 1.0.0
	 */
	public static function setup_table() {
		global $list_table;
		$list_table = new CustomersTable();
		$list_table->prepare_items();
		add_screen_option( 'per_page', array(
			'label'   => __( 'Customers', 'wp-ever-accounting' ),
			'default' => 20,
			'option'  => 'eac_customers_per_page',
		) );
	}

	/**
	 * Render customers table.
	 *
	 * @since 1.0.0
	 */
	public static function render_table() {

	}

	/**
	 * Render add customer form.
	 *
	 * @since 1.0.0
	 */
	public static function render_add() {
		echo 'Add customer form';
	}

	/**
	 * Render edit customer form.
	 *
	 * @since 1.0.0
	 */
	public static function render_edit() {
		echo 'Edit customer form';
	}
}
