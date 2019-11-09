<?php
/**
 * Render invoices tab contents
 */
defined( 'ABSPATH' ) || exit();

function eaccounting_tab_revenues(){
	require_once dirname( __FILE__ ) . '/edit-revenue.php';
}
add_action('eaccounting_income_tab_revenues', 'eaccounting_tab_revenues');
