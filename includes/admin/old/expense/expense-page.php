<?php
defined( 'ABSPATH' ) || exit();

/**
 * Render expense page
 * @return string
 * @since 1.0.0
 */
function eaccounting_expense_page() {
	ob_start();
	eaccounting_page_wrapper_open( 'expense-page' );
	$active_tab   = isset( $_GET['tab'] ) ? $_GET['tab'] : 'payments';
	$base         = admin_url( 'admin.php?page=eaccounting-expense' );
	$expense_tabs = apply_filters( 'eaccounting_expense_page_tabs', array(
		'payments' => __( 'Payments', 'wp-ever-accounting' ),
		'bills'   => __( 'Bills', 'wp-ever-accounting' ),
		'vendors'   => __( 'Vendors', 'wp-ever-accounting' ),
	) );

	echo '<hr class="wp-header-end">';
	echo '<h2 class="nav-tab-wrapper ea-tab-wrapper">';

	foreach ( $expense_tabs as $tab_id => $label ) {
		$tab_url = add_query_arg( array(
			'tab' => $tab_id
		), $base );
		$active  = $active_tab == $tab_id ? ' nav-tab-active' : '';
		echo sprintf( '<a href="%s" class="nav-tab %s">%s</a>', $tab_url, $active, $label );
	}

	echo '</h2>';

	echo sprintf( '<div class="ea-expense-tab-section %s">', $active_tab );
	do_action( 'eaccounting_expense_tab_' . $active_tab );
	echo '</div>';
	eaccounting_page_wrapper_close();

	$output = ob_get_contents();
	ob_get_status();
	return $output;
}
