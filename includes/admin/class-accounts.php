<?php
/**
 * Admin Accounts Page
 *
 * Functions used for displaying account related pages.
 *
 * @author      EverAccounting
 * @category    Admin
 * @package     EverAccounting\Admin
 * @version     1.1.0
 */

namespace EverAccounting\Admin;

use EverAccounting\Models\Account;

defined( 'ABSPATH' ) || exit();

class Accounts {
	/**
	 * Accounts constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_banking_page_tab_accounts', array( $this, 'render_tab' ) );
	}

	/**
	 * Render accounts tab.
	 *
	 * @since 1.1.0
	 */
	public function render_tab(){
		$requested_view = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';
		if ( in_array( $requested_view, array( 'view' ), true ) && ! empty( $_GET['account_id'] ) ) {
			$account_id = isset( $_GET['account_id'] ) ? absint( $_GET['account_id'] ) : null;
			include dirname( __FILE__ ) . '/views/accounts/view-account.php';
		} elseif ( in_array( $requested_view, array( 'add', 'edit' ), true ) ) {
			$account_id = isset( $_GET['account_id'] ) ? absint( $_GET['account_id'] ) : null;
			include dirname( __FILE__ ) . '/views/accounts/edit-account.php';
		} else {
			include dirname( __FILE__ ) . '/views/accounts/list-account.php';
		}
	}
}

return new \EverAccounting\Admin\Accounts();
