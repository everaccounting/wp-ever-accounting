<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Tax;

defined( 'ABSPATH' ) || exit;

/**
 * TaxRates class.
 *
 * @since 3.0.0
 * @package EverAccounting\Admin
 */
class Taxes {

	/**
	 * TaxRates constructor.
	 */
	public function __construct() {
		add_action( 'eac_settings_taxes_tab_rates', array( __CLASS__, 'render_content' ) );
		add_action( 'admin_post_eac_edit_tax', array( __CLASS__, 'handle_edit_tax' ) );
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

		if ( 'add' === $action ) {
			self::render_add();
		} elseif ( 'edit' === $action && $id ) {
			self::render_edit();
		} else {
			self::render_table();
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
