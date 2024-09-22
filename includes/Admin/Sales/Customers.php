<?php

namespace EverAccounting\Admin\Sales;

use EverAccounting\Models\Customer;

defined( 'ABSPATH' ) || exit;

/**
 * Class Customers
 *
 * @package EverAccounting\Admin\Sales
 */
class Customers {

	/**
	 * Customers constructor.
	 */
	public function __construct() {
		add_filter( 'eac_sales_page_tabs', array( $this, 'register_tabs' ) );
		add_action( 'load_eac_sales_page_customers', array( __CLASS__, 'setup_table' ) );
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen_option' ), 10, 3 );
		add_action( 'eac_sales_page_customers', array( $this, 'render_table' ) );
		add_action( 'eac_sales_page_customers_add', array( $this, 'render_add' ) );
		add_action( 'eac_sales_page_customers_edit', array( $this, 'render_edit' ) );
		add_action( 'admin_post_eac_edit_customer', array( $this, 'handle_edit' ) );
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
		$tabs['customers'] = __( 'Customers', 'wp-ever-accounting' );

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
		$list_table = new Tables\CustomersTable();
		$list_table->prepare_items();
		$screen->add_option(
			'per_page',
			array(
				'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
				'default' => 20,
				'option'  => "eac_{$list_table->_args['plural']}_per_page",
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
		global $list_table;
		if ( "eac_{$list_table->_args['plural']}_per_page" === $option ) {
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
	public function render_table() {
		global $list_table;
		include __DIR__ . '/views/customers-list.php';
	}

	/**
	 * Render add.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function render_add() {
		include __DIR__ . '/views/customers-add.php';
	}

	/**
	 * Render edit.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function render_edit() {
		$customer = new Customer( $_GET['id'] );
		include __DIR__ . '/views/customers-edit.php';
	}

	/**
	 * Handle edit.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function handle_edit() {

	}
}
