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
		add_action( 'ever_accounting_products_products_content', array( __CLASS__, 'output_products_content' ) );
		add_action( 'ever_accounting_products_categories_content', array( __CLASS__, 'output_categories_content' ) );
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
	 * Output accounts content.
	 *
	 * @since 1.1.0
	 */
	public static function output_products_content() {
		$action     = eac_get_input_var( 'action' );
		$product_id = eac_get_input_var( 'product_id' );
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/products/edit-product.php';
		} else {
			include dirname( __FILE__ ) . '/views/products/list-products.php';
		}
	}

	/**
	 * Output categories content.
	 *
	 * @since 1.1.0
	 */
	public static function output_categories_content() {
		$action  = eac_get_input_var( 'action' );
		$term_id = eac_get_input_var( 'term_id' );
		$term    = empty( $term_id ) ? false : eac_get_term( $term_id, 'product_cat' );
		if ( ! empty( $term_id ) && empty( $term ) ) {
			wp_safe_redirect( admin_url( 'admin.php?page=eac-products&tab=categories' ) );
			exit;
		}
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/products/edit-category.php';
		} else {
			include dirname( __FILE__ ) . '/views/products/list-categories.php';
		}
	}
}
