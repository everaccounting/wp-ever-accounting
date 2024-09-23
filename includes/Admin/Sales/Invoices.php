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
		add_filter( 'eac_sales_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'load_eac_sales_page_invoices', array( __CLASS__, 'setup_table' ) );
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen_option' ), 10, 3 );
		add_action( 'eac_sales_page_invoices', array( __CLASS__, 'render_table' ) );
		add_action( 'eac_sales_page_invoices_add', array( __CLASS__, 'render_add' ) );
		add_action( 'eac_sales_page_invoices_edit', array( __CLASS__, 'render_edit' ) );
		add_action( 'admin_post_eac_edit_invoice', array( __CLASS__, 'handle_edit' ) );
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
		$screen->add_option(
			'per_page',
			array(
				'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
				'default' => 20,
				'option'  => 'eac_invoices_per_page',
			)
		);
	}

	/**
	 * Set screen option.
	 *
	 * @param mixed  $status Status.
	 * @param string $option Option.
	 * @param mixed  $value Value.
	 *
	 * @since 3.0.0
	 * @return mixed
	 */
	public static function set_screen_option( $status, $option, $value ) {
		if ( 'eac_invoices_per_page_per_page' === $option ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Render table.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_table() {
		global $list_table;
		include __DIR__ . '/views/invoice-list.php';
	}

	/**
	 * Render add.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_add() {
		$invoice = new Invoice();
		include __DIR__ . '/views/invoice-add.php';
	}

	/**
	 * Render edit.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_edit() {
		$id      = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		$invoice = Invoice::find( $id );
		if ( ! $invoice ) {
			esc_html_e( 'The specified invoice does not exist.', 'wp-ever-accounting' );

			return;
		}
		include __DIR__ . '/views/invoice-edit.php';
	}
}
