<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Customer;

defined( 'ABSPATH' ) || exit;

/**
 * Class Customers
 *
 * @package EverAccounting\Admin\Sales
 */
class Customers_V1 {

	/**
	 * Customers constructor.
	 */
	public function __construct() {
		add_filter( 'eac_sales_page_tabs', array( $this, 'register_tabs' ) );
		add_action( 'load_eac_sales_page_customers', array( __CLASS__, 'setup_table' ) );
		add_action( 'eac_sales_page_customers', array( $this, 'render_table' ) );
		add_action( 'eac_sales_page_customers_add', array( $this, 'render_add' ) );
		add_action( 'eac_sales_page_customers_edit', array( $this, 'render_edit' ) );
		add_action( 'eac_sales_page_customers_view', array( $this, 'render_view' ) );
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
		$list_table = new ListTables\Customers();
		$list_table->prepare_items();
		$screen->add_option(
			'per_page',
			array(
				'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
				'default' => 20,
				'option'  => 'eac_customers_per_page',
			)
		);
	}

	/**
	 * Render table.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function render_table() {
		global $list_table;
		include __DIR__ . '/views/customer-list.php';
	}

	/**
	 * Render add.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function render_add() {
		$customer = new Customer();
		include __DIR__ . '/views/customer-add.php';
	}

	/**
	 * Render edit.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function render_edit() {
		$id       = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		$customer = Customer::find( $id );
		if ( ! $customer ) {
			wp_die( 'Invalid customer ID' );
		}
		include __DIR__ . '/views/customer-edit.php';
	}

	/**
	 * Render view.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function render_view() {
		$id       = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		$customer = Customer::find( $id );
		if ( ! $customer ) {
			wp_die( 'Invalid customer ID' );
		}
		include __DIR__ . '/views/customer-view.php';
	}

	/**
	 * Handle edit.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function handle_edit() {
		$referer = wp_get_referer();
		if ( ! check_admin_referer( 'eac_edit_customer' ) || ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Unauthorized' );
		}
		$data = array(
			'id'         => isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '',
			'name'       => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
			'currency'   => isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : '',
			'email'      => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
			'phone'      => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
			'company'    => isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '',
			'tax_number' => isset( $_POST['tax_number'] ) ? sanitize_text_field( wp_unslash( $_POST['tax_number'] ) ) : '',
			'website'    => isset( $_POST['website'] ) ? esc_url_raw( wp_unslash( $_POST['website'] ) ) : '',
			'address'    => isset( $_POST['address'] ) ? sanitize_text_field( wp_unslash( $_POST['address'] ) ) : '',
			'city'       => isset( $_POST['city'] ) ? sanitize_text_field( wp_unslash( $_POST['city'] ) ) : '',
			'state'      => isset( $_POST['state'] ) ? sanitize_text_field( wp_unslash( $_POST['state'] ) ) : '',
			'zip'        => isset( $_POST['zip'] ) ? sanitize_text_field( wp_unslash( $_POST['zip'] ) ) : '',
			'country'    => isset( $_POST['country'] ) ? sanitize_text_field( wp_unslash( $_POST['country'] ) ) : '',
		);

		$customer = EAC()->customers->insert( $data );

		if ( is_wp_error( $customer ) ) {
			EAC()->flash->error( $customer->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Customer saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg(
				array(
					'action' => 'edit',
					'id'     => $customer->id,
				),
				$referer
			);
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}
}
