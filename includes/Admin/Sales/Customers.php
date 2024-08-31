<?php

namespace EverAccounting\Admin\Sales;

defined( 'ABSPATH' ) || exit;

/**
 * Customers class.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\Sales
 */
class Customers {

	/**
	 * List table object.
	 *
	 * @since 1.0.0
	 * @var CustomersTable
	 */
	protected $list_table;

	/**
	 * Customers constructor.
	 */
	public function __construct() {
		add_filter( 'ever_accounting_sales_page_tabs', array( $this, 'register_tab' ) );
		add_action( 'ever_accounting_sales_screen_customers_home', array( $this, 'home_screen' ) );
		add_action( 'ever_accounting_sales_page_customers_home', array( $this, 'render_list' ) );
		add_action( 'ever_accounting_sales_page_customers_add', array( $this, 'render_add' ) );
		add_action( 'ever_accounting_sales_page_customers_edit', array( $this, 'render_edit' ) );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function register_tab( $tabs ) {
		$tabs['customers'] = __( 'Customers', 'wp-ever-accounting' );

		return $tabs;
	}

	/**
	 * Load customers list.
	 *
	 * @param \WP_Screen $screen Screen object.
	 *
	 * @since 1.0.0
	 */
	public function home_screen( $screen ) {
		$this->list_table = new CustomersTable();
		$this->list_table->prepare_items();
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
	public function render_list() {
		$this->list_table->display();
	}

	/**
	 * Render add customer form.
	 *
	 * @since 1.0.0
	 */
	public function render_add() {
		echo 'Add customer form';
	}

	/**
	 * Render edit customer form.
	 *
	 * @since 1.0.0
	 */
	public function render_edit() {
		echo 'Edit customer form';
	}
}
