<?php
/**
 * Customer profile notes
 */
defined( 'ABSPATH' ) || exit();

require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-note-list-table.php';
$args       = array(
	'display_args' => array(
		'columns_to_hide'      => array( 'cb','name' ),
		'hide_bulk_options' => true,
	),
);
$list_table = new EAccounting_Note_List_Table( $args );
$list_table->prepare_items();
$list_table->views();
$list_table->display();
