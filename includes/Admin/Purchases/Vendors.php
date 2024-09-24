<?php

namespace EverAccounting\Admin\Purchases;

use EverAccounting\Models\Vendor;

defined( 'ABSPATH' ) || exit;

/**
 * Class Vendors
 *
 * @since 3.0.0
 * @package EverAccounting\Admin\Purchases
 */
class Vendors {
	/**
	 * Vendors constructor.
	 */
	public function __construct() {
		add_filter( 'eac_purchases_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen_option' ), 10, 3 );
		add_action( 'load_eac_purchases_page_vendors', array( __CLASS__, 'setup_table' ) );
		add_action( 'eac_purchases_page_vendors', array( __CLASS__, 'render_table' ) );
		add_action( 'eac_purchases_page_vendors_add', array( __CLASS__, 'render_add' ) );
		add_action( 'eac_purchases_page_vendors_edit', array( __CLASS__, 'render_edit' ) );
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
		$tabs['vendors'] = __( 'Vendors', 'wp-ever-accounting' );

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
		if ( "eac_vendors_per_page" === $option ) {
			return $value;
		}

		return $status;
	}

	/**
	 * setup expenses list.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function setup_table() {
		global $list_table;
		$screen     = get_current_screen();
		$list_table = new Tables\VendorsTable();
		$list_table->prepare_items();
		$screen->add_option( 'per_page', array(
			'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
			'default' => 20,
			'option'  => "eac_vendors_per_page",
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
		include __DIR__ . '/views/vendor-list.php';
	}

	/**
	 * Render add form.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_add() {
		$vendor = new Vendor();
		include __DIR__ . '/views/vendor-add.php';
	}

	/**
	 * Render edit expense form.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_edit() {
		$id     = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		$vendor = Vendor::find( $id );
		if ( ! $vendor ) {
			esc_html_e( 'The specified expense does not exist.', 'wp-ever-accounting' );

			return;
		}

		include __DIR__ . '/views/vendor-edit.php';
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
		$vendor  = EAC()->vendors->insert(
			array(
				'id'            => isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0,
				'name'          => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
				'currency'      => isset( $_POST['currency'] ) ? sanitize_text_field( wp_unslash( $_POST['currency'] ) ) : eac_base_currency(),
				'email'         => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
				'phone'         => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
				'company'       => isset( $_POST['company'] ) ? sanitize_text_field( wp_unslash( $_POST['company'] ) ) : '',
				'website'       => isset( $_POST['website'] ) ? esc_url_raw( wp_unslash( $_POST['website'] ) ) : '',
				'vat_number'    => isset( $_POST['vat_number'] ) ? sanitize_text_field( wp_unslash( $_POST['vat_number'] ) ) : '',
				'vat_exempt'    => isset( $_POST['vat_exempt'] ) ? sanitize_text_field( wp_unslash( $_POST['vat_exempt'] ) ) : 'no', // phpcs:ignore
				'address_1'     => isset( $_POST['address_1'] ) ? sanitize_text_field( wp_unslash( $_POST['address_1'] ) ) : '',
				'address_2'     => isset( $_POST['address_2'] ) ? sanitize_text_field( wp_unslash( $_POST['address_2'] ) ) : '',
				'city'          => isset( $_POST['city'] ) ? sanitize_text_field( wp_unslash( $_POST['city'] ) ) : '',
				'state'         => isset( $_POST['state'] ) ? sanitize_text_field( wp_unslash( $_POST['state'] ) ) : '',
				'postcode'      => isset( $_POST['postcode'] ) ? sanitize_text_field( wp_unslash( $_POST['postcode'] ) ) : '',
				'country'       => isset( $_POST['country'] ) ? sanitize_text_field( wp_unslash( $_POST['country'] ) ) : '',
				'status'        => isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active',
			)
		);

		if ( is_wp_error( $vendor ) ) {
			EAC()->flash->error( $vendor->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Vendor saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg( [ 'action' => 'edit', 'id' => $vendor->id ], $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );
		}
		wp_safe_redirect( $referer );
		exit;
	}
}
