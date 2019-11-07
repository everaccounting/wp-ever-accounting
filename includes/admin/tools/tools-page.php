<?php
defined( 'ABSPATH' ) || exit();

/**
 * Render expense page
 * @return string
 * @since 1.0.0
 */
function eaccounting_tools_page() {
	ob_start();
	eaccounting_page_wrapper_open( 'tools-page' );
	$active_tab   = isset( $_GET['tab'] ) ? $_GET['tab'] : 'import';
	$base         = admin_url( 'admin.php?page=eaccounting-tools' );
	$tools_tabs = apply_filters( 'eaccounting_tools_page_tabs', array(
		'import' => __( 'Import', 'wp-ever-accounting' ),
		'export'   => __( 'Export', 'wp-ever-accounting' ),
	) );

	echo '<hr class="wp-header-end">';
	echo '<h2 class="nav-tab-wrapper ea-tab-wrapper">';

	foreach ( $tools_tabs as $tab_id => $label ) {
		$tab_url = add_query_arg( array(
			'tab' => $tab_id
		), $base );
		$active  = $active_tab == $tab_id ? ' nav-tab-active' : '';
		echo sprintf( '<a href="%s" class="nav-tab %s">%s</a>', $tab_url, $active, $label );
	}

	echo '</h2>';

	echo sprintf( '<div class="ea-tools-tab-section %s">', $active_tab );
	do_action( 'eaccounting_tools_tab_' . $active_tab );
	echo '</div>';
	eaccounting_page_wrapper_close();

	$output = ob_get_contents();
	ob_get_status();
	return $output;
}
