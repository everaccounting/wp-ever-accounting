<?php

namespace EverAccounting\Admin\Purchases;

use EverAccounting\Models\Bill;

defined( 'ABSPATH' ) || exit;

/**
 * Class Bills
 *
 * @since 3.0.0
 * @package EverAccounting\Admin\Purchases
 */
class Bills {
	/**
	 * Bills constructor.
	 */
	public function __construct() {
		add_filter( 'eac_purchases_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen_option' ), 10, 3 );
		add_action( 'load_eac_purchases_page_bills_index', array( __CLASS__, 'setup_table' ) );
		add_action( 'eac_purchases_page_bills_index', array( __CLASS__, 'render_table' ) );
		add_action( 'eac_purchases_page_bills_add', array( __CLASS__, 'render_add' ) );
		add_action( 'eac_purchases_page_bills_edit', array( __CLASS__, 'render_edit' ) );
		add_action( 'admin_post_eac_edit_vendor', array( __CLASS__, 'handle_edit' ) );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		$tabs['bills'] = __( 'Bills', 'wp-ever-accounting' );

		return $tabs;
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
	 * setup bills list.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function setup_table() {
		global $list_table;
		$screen     = get_current_screen();
		$list_table = new Tables\BillsTable();
		$list_table->prepare_items();
		$screen->add_option( 'per_page', array(
			'label'   => __( 'Number of bills per page:', 'wp-ever-accounting' ),
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
		include __DIR__ . '/views/bills/table.php';
	}

	/**
	 * Render add form.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_add() {
		$bill = new Bill();
		include __DIR__ . '/views/bills/add.php';
	}

	/**
	 * Render edit bill form.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_edit() {
		$id   = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		$bill = Bill::find( $id );
		if ( ! $bill ) {
			esc_html_e( 'The specified bill does not exist.', 'wp-ever-accounting' );

			return;
		}

		include __DIR__ . '/views/bills/edit.php';
	}

	/**
	 * Edit vendor.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public static function handle_edit() {
		check_admin_referer( 'eac_edit_vendor' );
		$referer = wp_get_referer();
		// TODO: Edit Code will handle here.
		wp_safe_redirect( $referer );
		exit;
	}
}
