<?php
/**
 * Render Transfers list table
 *
 * Page: Banking
 * Tab: Accounts
 * Section: Transfer
 *
 * @since       1.0.2
 * @subpackage  Admin/Views/Accounts
 * @package     Ever_Accounting
 */

use Ever_Accounting\Models\Account;

defined( 'ABSPATH' ) || exit();

require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-transfer-list-table.php';
$args = array(
	'display_args' => array(
		'columns_to_hide'   => array( 'actions', 'cb', 'from_account_id' ),
		'hide_bulk_options' => true,
	),
	'query_args'   => array(
		'from_account_id' => $account->get_id(),
	),
);

$list_table = new Ever_Accounting_Transfer_List_Table( $args );
$list_table->prepare_items();
$list_table->views();
$list_table->display();
