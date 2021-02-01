<?php
/**
 * Admin Vendors Page
 *
 * Functions used for displaying vendor related pages.
 *
 * @author      EverAccounting
 * @category    Admin
 * @package     EverAccounting\Admin
 * @version     1.1.0
 */

use EverAccounting\Models\Vendor;

defined( 'ABSPATH' ) || exit();

class EverAccounting_Admin_Vendors {
	/**
	 * EverAccounting_Admin_Vendors constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_expenses_page_tab_vendors', array( $this, 'render_tab' ) );
	}

	/**
	 * Render vendors tab.
	 *
	 * @since 1.1.0
	 */
	public function render_tab(){
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['vendor_id'] ) ) {
			$vendor_id = isset( $_GET['vendor_id'] ) ? absint( $_GET['vendor_id'] ) : null;
			include dirname( __FILE__ ) . '/views/vendors/view-vendor.php';
		} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$vendor_id = isset( $_GET['vendor_id'] ) ? absint( $_GET['vendor_id'] ) : null;
			include dirname( __FILE__ ) . '/views/vendors/edit-vendor.php';
		} else {
			include dirname( __FILE__ ) . '/views/vendors/list-vendor.php';
		}
	}
}

return new \EverAccounting_Admin_Vendors();
