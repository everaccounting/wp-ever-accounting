<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Products.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Products extends \EverAccounting\Singleton {

	/**
	 * Products constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		add_action( 'ever_accounting_products_tab_products', array( __CLASS__, 'output_products_tab' ) );
	}

	/**
	 * Output the banking page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output() {
		$tabs         = eac_get_products_tabs();
		$tab          = eac_get_input_var( 'tab' );
		$current_tab  = ! empty( $tab ) && array_key_exists( $tab, $tabs ) ? $tab : key( $tabs );
		$current_page = eac_get_input_var( 'page' );
		$page_name    = 'products';

		include dirname( __FILE__ ) . '/views/admin-page.php';
	}

	/**
	 * Output accounts tab.
	 *
	 * @since 1.1.0
	 */
	public static function output_products_tab() {
		$action     = eac_get_input_var( 'action' );
		$product_id = eac_get_input_var( 'product_id' );
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/products/edit-product.php';
		} else {
			include dirname( __FILE__ ) . '/views/products/list-products.php';
		}
	}
}
