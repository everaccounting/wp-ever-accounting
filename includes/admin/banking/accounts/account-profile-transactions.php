<?php
/**
 * Account profile transactions
 * @since 1.1.0
 *
 * @subpackage  Admin/Banking/Accounts
 * @package     EverAccounting
 *
 */

defined( 'ABSPATH' ) || exit();

require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-transaction-list-table.php';
$args       = array(
	'display_args' => array(
		'columns_to_hide'      => array( 'actions', 'cb' ),
		'hide_extra_table_nav' => true,
	),
);
$list_table = new EAccounting_Transaction_List_Table( $args );
$list_table->prepare_items();
$list_table->views();
$list_table->display();
