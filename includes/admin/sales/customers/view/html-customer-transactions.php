<?php
require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-income-list-table.php';
$list_table = new EAccounting_Income_List_Table(
	array(
		'display_args' => array(
			'columns_to_hide' => array( 'actions', 'cb' ),
			'hide_extra_table_nav'  => true,
		),
	)
);
$list_table->table_classes = array('no-border');
$list_table->prepare_items();
$list_table->views();
$list_table->display();
