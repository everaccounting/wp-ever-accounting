<?php

namespace EverAccounting\Admin\Sales;

defined( 'ABSPATH' ) || exit;

/**
 * Invoices class.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\Sales
 */
class Invoices {

	/**
	 * Invoices table.
	 *
	 * @var InvoicesTable
	 */
	protected $list_table;

	/**
	 * Invoices constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'eac_sales_page_tabs', array( $this, 'register_tab' ) );
		add_action( 'ever_accounting_sales_screen_invoices_home', array( $this, 'home_screen' ) );
		add_action( 'ever_accounting_sales_page_invoices_home', array( $this, 'render_list' ) );
		add_action( 'ever_accounting_sales_page_invoices_add', array( $this, 'render_add' ) );
		add_action( 'ever_accounting_sales_page_invoices_edit', array( $this, 'render_edit' ) );
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
		$tabs['invoices'] = __( 'Invoices', 'wp-ever-accounting' );

		return $tabs;
	}

	/**
	 * Load invoices list.
	 *
	 * @param \WP_Screen $screen Screen object.
	 *
	 * @since 1.0.0
	 */
	public function home_screen( $screen ) {
		$this->list_table = new InvoicesTable();
		$this->list_table->prepare_items();
		add_screen_option( 'per_page', array(
			'label'   => __( 'Invoices', 'wp-ever-accounting' ),
			'default' => 20,
			'option'  => 'eac_invoices_per_page',
		) );
	}

	/**
	 * Render invoices list.
	 *
	 * @since 1.0.0
	 */
	public function render_list() {
		$this->list_table->display();
	}

	/**
	 * Render invoices add.
	 *
	 * @since 1.0.0
	 */
	public function render_add() {
		echo 'Add invoice';
	}

	/**
	 * Render invoices edit.
	 *
	 * @since 1.0.0
	 */
	public function render_edit() {
		echo 'Edit invoice';
	}
}
