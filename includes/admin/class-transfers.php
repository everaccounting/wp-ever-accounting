<?php
/**
 * Admin Transfers Page
 *
 * Functions used for displaying account related pages.
 *
 * @author      EverAccounting
 * @category    Admin
 * @package     EverAccounting\Admin
 * @version     1.1.0
 */

namespace EverAccounting\Admin;
use EverAccounting\Models\Customer;

defined( 'ABSPATH' ) || exit();

class Transfers {
	/**
	 * Transfers constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_banking_page_tab_transfers', array( $this, 'render_tab' ) );
	}

	/**
	 * Render transfers tab.
	 *
	 * @since 1.1.0
	 */
	public function render_tab(){
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$transfer_id = isset( $_GET['transfer_id'] ) ? absint( $_GET['transfer_id'] ) : null;
			include dirname( __FILE__ ) . '/views/transfers/edit-transfer.php';
		} else {
			include dirname( __FILE__ ) . '/views/transfers/list-transfer.php';
		}
	}
}

return new \EverAccounting\Admin\Transfers();
