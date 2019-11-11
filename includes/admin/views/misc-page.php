<?php
defined( 'ABSPATH' ) || exit();

/**
 * Render expense page
 * @return string
 * @since 1.0.0
 */
function eaccounting_misc_page() {
	ob_start();
	eaccounting_page_wrapper_open( 'misc-page' );
	$active_tab   = isset( $_GET['tab'] ) ? $_GET['tab'] : 'categories';
	$base         = admin_url( 'admin.php?page=eaccounting-misc' );
	$misc_tabs = apply_filters( 'eaccounting_misc_page_tabs', array(
		'categories' => __( 'Categories', 'wp-ever-accounting' ),
		'tax_rates'   => __( 'Rax Rates', 'wp-ever-accounting' ),
		'payment_methods'   => __( 'Payment Methods', 'wp-ever-accounting' ),
	) );

	echo '<h2 class="nav-tab-wrapper ea-tab-nav-wrapper">';

	foreach ( $misc_tabs as $tab_id => $label ) {
		$tab_url = add_query_arg( array(
			'tab' => $tab_id
		), $base );
		$active  = $active_tab == $tab_id ? ' nav-tab-active' : '';
		echo sprintf( '<a href="%s" class="nav-tab %s">%s</a>', $tab_url, $active, $label );
	}

	echo '</h2>';

	echo sprintf( '<div class="ea-tab-section-wrapper ea-misc-tab-section %s">', $active_tab );
	do_action( 'eaccounting_misc_tab_' . $active_tab );
	echo '</div>';
	eaccounting_page_wrapper_close();

	$output = ob_get_contents();
	ob_get_status();
	return $output;
}
