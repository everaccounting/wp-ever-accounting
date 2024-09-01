<?php

namespace EverAccounting\Admin;

use EverAccounting\Admin\ListTables\InvoicesTable;

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
		add_filter( 'eac_sales_page_tabs', array( $this, 'register_tabs' ) );
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
}
