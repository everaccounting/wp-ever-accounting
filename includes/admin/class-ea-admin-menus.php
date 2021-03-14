<?php
/**
 * Setup menus in WP admin.
 *
 * @package    EverAccounting
 * @subpackage Admin
 * @version    1.0.2
 */

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Menus
 * @package EverAccounting\Admin
 */
class Menus {

	/**
	 * EverAccounting_Admin_Menus constructor.
	 *
	 * @version 1.0.2
	 */
	public function __construct() {
		add_filter( 'eaccounting_settings_tabs', array( $this, 'add_setting_tabs') );
		add_action( 'eaccounting_settings_tab_currencies', array( $this, 'render_currencies_tab' ) );
		add_action( 'eaccounting_settings_tab_categories', array( $this, 'render_categories_tab' ) );
	}

	public function add_setting_tabs($tabs){
		$tabs['currencies'] = __( 'Currencies', 'wp-ever-accounting' );
		$tabs['categories'] = __( 'Categories', 'wp-ever-accounting' );
		return $tabs;
	}

	/**
	 * @since 1.1.0
	 */
	public function render_currencies_tab() {
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$currency_id = isset( $_GET['currency_id'] ) ? absint( $_GET['currency_id'] ) : null;
			include dirname( __FILE__ ) . '/views/currencies/edit-currency.php';
		} else {
			include dirname( __FILE__ ) . '/views/currencies/list-currency.php';
		}
	}

	/**
	 * @since 1.1.0
	 */
	public function render_categories_tab() {
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$category_id = isset( $_GET['category_id'] ) ? absint( $_GET['category_id'] ) : null;
			include dirname( __FILE__ ) . '/views/categories/edit-category.php';
		} else {
			include dirname( __FILE__ ) . '/views/categories/list-category.php';
		}
	}
}

return new Menus();
