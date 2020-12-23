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
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-transfer-list-table.php';
$args       = array(
	'display_args' => array(
		'columns_to_hide'      => array( 'actions', 'cb' ),
		'hide_bulk_options' => true,
	),
);
$list_table = new EAccounting_Transfer_List_Table( $args );
$list_table->prepare_items();
$list_table->views();
$list_table->display();
