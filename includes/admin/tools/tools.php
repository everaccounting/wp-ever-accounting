<?php
/**
 * Admin Reports Page
 *
 * @package     EverAccounting
 * @subpackage  Admin/Reports
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();
/**
 * render tools page.
 *
 * @since 1.0.2
 */
function eaccounting_admin_tools_page() {
	$tabs       = eaccounting_get_tools_tabs();
	$active_tab = eaccounting_get_active_tab( $tabs, 'accounts' );

	ob_start();
	?>
	<div class="wrap">
		<h2 class="nav-tab-wrapper">
			<?php eaccounting_navigation_tabs( $tabs, $active_tab ); ?>
		</h2>
		<div id="tab_container">
			<?php
			/**
			 * Fires in the Tabs screen tab.
			 *
			 * The dynamic portion of the hook name, `$active_tab`, refers to the slug of
			 * the currently active tools tab.
			 *
			 * @since 1.0.2
			 */
			do_action( 'eaccounting_tools_tab_' . $active_tab );
			?>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}

/**
 * Retrieve tools tabs
 *
 * @return array $tabs
 * @since 1.0.2
 */
function eaccounting_get_tools_tabs() {
	$tabs                = array();
	$tabs['import']      = __( 'Import', 'wp-ever-accounting' );
	$tabs['export']      = __( 'Export', 'wp-ever-accounting' );
	$tabs['system_info'] = __( 'System Info', 'wp-ever-accounting' );

	return apply_filters( 'eaccounting_tools_tabs', $tabs );
}

/**
 * Setup tools pages.
 *
 * @since 1.0.2
 */
function eaccounting_load_tools_page() {
	$tab = eaccounting_get_current_tab();
	if ( empty( $tab ) ) {
		wp_redirect( add_query_arg( [ 'tab' => 'import' ] ) );
		exit();
	}

	do_action( 'eaccounting_load_tools_page_tab' . $tab );
}
