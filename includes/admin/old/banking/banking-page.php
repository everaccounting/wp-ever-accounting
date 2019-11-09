<?php
defined( 'ABSPATH' ) || exit();

/**
 * Render banking page
 * @return string
 * @since 1.0.0
 */
function eaccounting_banking_page() {
	ob_start();
	eaccounting_page_wrapper_open( 'banking-page' );
	$active_tab   = isset( $_GET['tab'] ) ? $_GET['tab'] : 'accounts';
	$base         = admin_url( 'admin.php?page=eaccounting-banking' );
	$banking_tabs = apply_filters( 'eaccounting_banking_page_tabs', array(
		'accounts' => __( 'Accounts', 'wp-ever-accounting' ),
		'transfers'   => __( 'Transfers', 'wp-ever-accounting' ),
		'reconciliations'   => __( 'Reconciliations', 'wp-ever-accounting' ),
	) );

	//echo '<hr class="wp-header-end">';
	echo '<h2 class="nav-tab-wrapper ea-tab-wrapper">';

	foreach ( $banking_tabs as $tab_id => $label ) {
		$tab_url = add_query_arg( array(
			'tab' => $tab_id
		), $base );
		$active  = $active_tab == $tab_id ? ' nav-tab-active' : '';
		echo sprintf( '<a href="%s" class="nav-tab %s">%s</a>', $tab_url, $active, $label );
	}

	echo '</h2>';

	echo sprintf( '<div class="ea-banking-tab-section %s">', $active_tab );
	do_action( 'eaccounting_banking_tab_' . $active_tab );
	echo '</div>';
	eaccounting_page_wrapper_close();

	$output = ob_get_contents();
	ob_get_status();
	return $output;
}
