<?php

namespace EverAccounting\Admin\Misc;

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
		add_filter( 'eac_misc_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_filter( 'set-screen-option', array( __CLASS__, 'set_screen_option' ), 10, 3 );
		add_action( 'load_eac_misc_page_taxes', array( __CLASS__, 'setup_table' ) );
		add_action( 'eac_misc_page_taxes', array( __CLASS__, 'render_table' ) );
		add_action( 'eac_misc_page_taxes_add', array( __CLASS__, 'render_add' ) );
		add_action( 'eac_misc_page_taxes_edit', array( __CLASS__, 'render_edit' ) );
		add_action( 'admin_post_eac_edit_tax', array( $this, 'handle_edit_tax' ) );
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
		$tabs['taxes'] = __( 'Taxes', 'wp-ever-accounting' );

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
		if ( "eac_taxes_per_page" === $option ) {
			return $value;
		}

		return $status;
	}

	/**
	 * setup taxes list.
	 *
	 * @since 3.0.0
	 */
	public static function setup_table() {
		global $list_table;
		$screen     = get_current_screen();
		$list_table = new \EverAccounting\Admin\ListTables\Taxes();
		$list_table->prepare_items();
		$screen->add_option( 'per_page', array(
			'label'   => __( 'Number of taxes per page:', 'wp-ever-accounting' ),
			'default' => 20,
			'option'  => "eac_taxes_per_page",
		) );
	}

	/**
	 * Render taxes table.
	 *
	 * @since 3.0.0
	 */
	public static function render_table() {
		global $list_table;
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
		$referer     = wp_get_referer();
		$id          = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
		$name        = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$rate        = isset( $_POST['rate'] ) ? doubleval( wp_unslash( $_POST['rate'] ) ) : '';
		$compound    = isset( $_POST['compound'] ) ? sanitize_text_field( wp_unslash( $_POST['compound'] ) ) : '';
		$desc        = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';
		$status      = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : 'active';
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
			$referer = add_query_arg( ['action' => 'edit', 'id' => $tax->id ], $referer );
			$referer = remove_query_arg( array( 'add' ), $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}
}
