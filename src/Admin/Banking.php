<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Banking.
 *
 * @since   1.0.0
 * @package EverAccounting\Admin
 */
class Banking extends \EverAccounting\Singleton {

	/**
	 * Banking constructor.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		add_action( 'ever_accounting_banking_transactions_content', array( __CLASS__, 'output_transactions_tab' ) );
		add_action( 'ever_accounting_banking_accounts_content', array( __CLASS__, 'output_accounts_tab' ) );
		add_action( 'ever_accounting_banking_transfers_content', array( __CLASS__, 'output_transfers_tab' ) );
	}

	/**
	 * Output the banking page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output() {
		$tabs         = eac_get_banking_tabs();
		$tab          = eac_get_input_var( 'tab' );
		$current_tab  = ! empty( $tab ) && array_key_exists( $tab, $tabs ) ? $tab : key( $tabs );
		$current_page = eac_get_input_var( 'page' );
		$page_name    = 'banking';

		include dirname( __FILE__ ) . '/views/admin-page.php';
	}

	/**
	 * Output the transactions tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_transactions_tab() {
		$action         = eac_get_input_var( 'action' );
		$transaction_id = eac_get_input_var( 'transaction_id' );
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/transactions/edit-transaction.php';
		} elseif ( 'view' === $action ) {
			include dirname( __FILE__ ) . '/views/transactions/view-transaction.php';
		} else {
			include dirname( __FILE__ ) . '/views/transactions/list-transactions.php';
		}
	}


	/**
	 * Output the accounts tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_accounts_tab() {
		$action     = eac_get_input_var( 'action' );
		$account_id = eac_get_input_var( 'account_id' );
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/accounts/edit-account.php';
		} elseif ( 'view' === $action ) {
			include dirname( __FILE__ ) . '/views/accounts/view-account.php';
		} else {
			include dirname( __FILE__ ) . '/views/accounts/list-accounts.php';
		}
	}

	/**
	 * Output the transfers tab.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function output_transfers_tab() {
		$action      = eac_get_input_var( 'action' );
		$transfer_id = eac_get_input_var( 'transfer_id' );
		if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
			include dirname( __FILE__ ) . '/views/transfers/edit-transfer.php';
		} elseif ( 'view' === $action ) {
			include dirname( __FILE__ ) . '/views/transfers/view-transfer.php';
		} else {
			include dirname( __FILE__ ) . '/views/transfers/list-transfers.php';
		}
	}
}
