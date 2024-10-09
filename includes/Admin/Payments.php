<?php

namespace EverAccounting\Admin;

use EverAccounting\Models\Payment;

defined( 'ABSPATH' ) || exit;

/**
 * Class Payments
 *
 * @package EverAccounting\Admin\Sales
 */
class Payments {

	/**
	 * Payments constructor.
	 */
	public function __construct() {
		add_filter( 'eac_sales_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'load_eac_sales_page_payments', array( __CLASS__, 'setup_page' ) );
		add_action( 'eac_sales_page_payments', array( __CLASS__, 'render_page' ) );
		add_action( 'eac_payment_meta_boxes_primary', array( __CLASS__, 'attributes_metabox' ), -1, 2 );
		add_action( 'eac_payment_meta_boxes_secondary', array( __CLASS__, 'action_metabox' ), -1, 2 );
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
		$tabs['payments'] = __( 'Payments', 'wp-ever-accounting' );

		return $tabs;
	}

	/**
	 * setup page.
	 *
	 * @param string $action Current action.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function setup_page( $action ) {
		global $list_table;
		$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );

		// if id set but payment not found, redirect to payments list.
		if ( ! empty( $id ) && empty( EAC()->payments->get( $id ) ) ) {
			wp_safe_redirect( remove_query_arg( array( 'id', 'action' ) ) );
			exit;
		}

		if ( ! in_array( $action, array( 'add', 'edit', 'view' ), true ) ) {
			$screen     = get_current_screen();
			$list_table = new ListTables\Payments();
			$list_table->prepare_items();
			$screen->add_option(
				'per_page',
				array(
					'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
					'default' => 20,
					'option'  => 'eac_payments_per_page',
				)
			);
		}
	}

	/**
	 * Payment attributes.
	 *
	 * @param Payment $payment Payment object.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public static function payment_attributes( $payment ) {}
}
