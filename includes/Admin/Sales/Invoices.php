<?php

namespace EverAccounting\Admin\Sales;

use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit;

/**
 * Class Invoices
 *
 * @package EverAccounting\Admin\Sales
 */
class Invoices {

	/**
	 * Invoices constructor.
	 */
	public function __construct() {
		add_filter( 'eac_sales_page_tabs', array( $this, 'register_tabs' ) );
		add_action( 'load_eac_sales_page_invoices_index', array( $this, 'setup_table' ) );
		add_action( 'eac_sales_page_invoices_index', array( $this, 'render_table' ) );
		add_action( 'eac_sales_page_invoices_add', array( $this, 'render_add' ) );
		add_action( 'eac_sales_page_invoices_edit', array( $this, 'render_edit' ) );
		add_action( 'admin_post_eac_edit_invoice', array( $this, 'handle_edit' ) );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function register_tabs( $tabs ) {
		$tabs['invoices'] = __( 'Invoices', 'wp-ever-accounting' );

		return $tabs;
	}

	/**
	 * setup table.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function setup_table() {
		global $list_table;
		$screen     = get_current_screen();
		$list_table = new Tables\InvoicesTable();
		$list_table->prepare_items();
		$screen->add_option( 'per_page', array(
			'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
			'default' => 20,
			'option'  => "eac_{$list_table->_args['plural']}_per_page",
		) );
	}

	/**
	 * Render table.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_table() {
		global $list_table;
		include __DIR__ . '/views/invoices/table.php';
	}

	/**
	 * Render add.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_add() {
		$document = new Invoice();
		include __DIR__ . '/views/invoices/add.php';
	}

	/**
	 * Render edit.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_edit() {

	}
}
