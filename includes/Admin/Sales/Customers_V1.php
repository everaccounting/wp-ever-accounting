<?php

namespace EverAccounting\Admin\Sales;

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
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen_option' ), 10, 3 );
		add_action( 'load_eac_sales_page_customers_index', array( $this, 'setup_table' ) );
		add_action( 'eac_sales_page_customers_index', array( $this, 'render_table' ) );
		add_action( 'eac_sales_page_customers_add', array( $this, 'render_add' ) );
		add_action( 'eac_sales_page_customers_edit', array( $this, 'render_edit' ) );
		add_action( 'admin_post_eac_edit_customer', array( $this, 'handle_edit_customer' ) );
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
		$screen->add_option( 'per_page', array(
			'label'   => __( 'Number of customer per page:', 'wp-ever-accounting' ),
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
		include __DIR__ . '/views/customers/table.php';
	}

	/**
	 * Render add.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_add() {
		$customer = new Customer();
		include __DIR__ . '/views/customers/add.php';
	}

	/**
	 * Render edit form.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_edit() {
		$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		$customer = Customer::find( $id );
		if ( ! $customer ) {
			esc_html_e( 'The specified customer does not exist.', 'wp-ever-accounting' );

			return;
		}
		include __DIR__ . '/views/customers/edit.php';
	}

	/**
	 * Edit customer.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public static function handle_edit_customer() {
		check_admin_referer( 'eac_edit_customer' );
		$referer  = wp_get_referer();
		$customer = eac_insert_customer(
			array(
				'id'            => isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0,
				'name'          => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
				'currency_code' => isset( $_POST['currency_code'] ) ? sanitize_text_field( wp_unslash( $_POST['currency_code'] ) ) : eac_base_currency(),
				'email'         => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
				'phone'         => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
				'company'       => isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '',
				'website'       => isset( $_POST['website'] ) ? esc_url_raw( wp_unslash( $_POST['website'] ) ) : '',
				'vat_number'    => isset( $_POST['vat_number'] ) ? sanitize_text_field( wp_unslash( $_POST['vat_number'] ) ) : '',
				'vat_exempt'    => isset( $_POST['vat_exempt'] ) ? sanitize_text_field( wp_unslash( $_POST['vat_exempt'] ) ) : '', // phpcs:ignore
				'address_1'     => isset( $_POST['address_1'] ) ? sanitize_text_field( wp_unslash( $_POST['address_1'] ) ) : '',
				'address_2'     => isset( $_POST['address_2'] ) ? sanitize_text_field( wp_unslash( $_POST['address_2'] ) ) : '',
				'city'          => isset( $_POST['city'] ) ? sanitize_text_field( wp_unslash( $_POST['city'] ) ) : '',
				'state'         => isset( $_POST['state'] ) ? sanitize_text_field( wp_unslash( $_POST['state'] ) ) : '',
				'postcode'      => isset( $_POST['postcode'] ) ? sanitize_text_field( wp_unslash( $_POST['postcode'] ) ) : '',
				'country'       => isset( $_POST['country'] ) ? sanitize_text_field( wp_unslash( $_POST['country'] ) ) : '',
				'status'        => isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active',
			)
		);

		if ( is_wp_error( $customer ) ) {
			EAC()->flash->error( $customer->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Customer saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( ['view' => 'edit', 'id' => $customer->id ], $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );

		}
		wp_safe_redirect( $referer );
		exit;
	}
}
