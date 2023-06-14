<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Products.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Items extends \EverAccounting\Singleton {

	/**
	 * Products constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		add_action( 'ever_accounting_items_content', array( __CLASS__, 'output_items_content' ) );
		add_action( 'ever_accounting_items_categories_content', array( __CLASS__, 'output_categories_content' ) );
	}

	/**
	 * Output the banking page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output() {
		$tabs         = eac_get_items_tabs();
		$tab          = eac_get_input_var( 'tab' );
		$current_tab  = ! empty( $tab ) && array_key_exists( $tab, $tabs ) ? $tab : key( $tabs );
		$current_page = eac_get_input_var( 'page' );
		$page_name    = 'items';

		include dirname( __FILE__ ) . '/views/admin-page.php';
	}

	/**
	 * Output accounts content.
	 *
	 * @since 1.1.0
	 */
	public static function output_items_content() {
		$action  = eac_get_input_var( 'action' );
		$item_id = eac_get_input_var( 'item_id' );
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/items/edit-item.php';
		} else {
			include dirname( __FILE__ ) . '/views/items/list-items.php';
		}
	}
}
