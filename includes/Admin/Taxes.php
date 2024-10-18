<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Tax;

defined( 'ABSPATH' ) || exit;

/**
 * Taxes class.
 *
 * @since 3.0.0
 * @package EverAccounting\Admin
 */
class Taxes {

	/**
	 * TaxRates constructor.
	 */
	public function __construct() {
		add_action( 'eac_settings_page_taxes_loaded', array( __CLASS__, 'handle_actions' ) );
		add_action( 'eac_settings_taxes_tab_rates_content', array( __CLASS__, 'render_content' ) );
	}

	/**
	 * Handle actions.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function handle_actions() {
		if ( isset( $_POST['action'] ) && 'eac_edit_tax' === $_POST['action'] && check_admin_referer( 'eac_edit_tax' ) && current_user_can( 'eac_manage_tax' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown -- Custom capability.
			$referer = wp_get_referer();
			$data    = array(
				'id'          => isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0,
				'name'        => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
				'rate'        => isset( $_POST['rate'] ) ? floatval( wp_unslash( $_POST['rate'] ) ) : 0,
				'compound'    => isset( $_POST['compound'] ) ? sanitize_text_field( wp_unslash( $_POST['compound'] ) ) : '',
				'description' => isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '',
			);

			$item = EAC()->taxes->insert( $data );
			if ( is_wp_error( $item ) ) {
				EAC()->flash->error( $item->get_error_message() );
			} else {
				EAC()->flash->success( __( 'Tax saved successfully.', 'wp-ever-accounting' ) );
				$referer = add_query_arg( 'id', $item->id, $referer );
				$referer = remove_query_arg( array( 'add' ), $referer );
			}

			wp_safe_redirect( $referer );
			exit;
		}
	}

	/**
	 * Render content.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_content() {
		$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$id     = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );

		switch ( $action ) {
			case 'add':
			case 'edit':
				include __DIR__ . '/views/tax-edit.php';
				break;
			default:
				global $list_table;
				$list_table = new ListTables\Taxes();
				$list_table->prepare_items();
				include __DIR__ . '/views/tax-list.php';
				break;
		}
	}

	/**
	 * Render table.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function render_table() {
		$list_table = new ListTables\Taxes();
		$list_table->prepare_items();
		include __DIR__ . '/views/tax-list.php';
	}

	/**
	 * Render add category form.
	 *
	 * @since 3.0.0
	 */
	public static function render_add() {
		$tax = new Tax();
		include __DIR__ . '/views/tax-add.php';
	}

	/**
	 * Render edit tax form.
	 *
	 * @since 3.0.0
	 */
	public static function render_edit() {
		$id  = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
		$tax = Tax::find( $id );
		if ( ! $tax ) {
			esc_html_e( 'The specified tax does not exist.', 'wp-ever-accounting' );

			return;
		}

		include __DIR__ . '/views/tax-edit.php';
	}

	/**
	 * Edit tax.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public static function handle_edit_tax() {
		check_admin_referer( 'eac_edit_tax' );
		$referer  = wp_get_referer();
		$id       = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$name     = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$rate     = isset( $_POST['rate'] ) ? doubleval( wp_unslash( $_POST['rate'] ) ) : '';
		$compound = isset( $_POST['compound'] ) ? sanitize_text_field( wp_unslash( $_POST['compound'] ) ) : '';
		$desc     = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';
		$status   = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active';
		if ( $compound ) {
			$compound = 'yes' === $compound ? true : false;
		}
		$tax = EAC()->taxes->insert(
			array(
				'id'          => $id,
				'name'        => $name,
				'rate'        => $rate,
				'compound'    => $compound,
				'description' => $desc,
				'status'      => $status,
			)
		);

		if ( is_wp_error( $tax ) ) {
			EAC()->flash->error( $tax->get_error_message() );
		} else {
			EAC()->flash->success( __( 'Tax saved successfully.', 'wp-ever-accounting' ) );
			$referer = add_query_arg(
				array(
					'action' => 'edit',
					'id'     => $tax->id,
				),
				$referer
			);
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}
}
