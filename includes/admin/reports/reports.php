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
 * render reports page.
 *
 * @since 1.0.2
 */
function eaccounting_admin_reports_page() {
	$tabs       = eaccounting_get_reports_tabs();
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
			 * the currently active reports tab.
			 *
			 * @since 1.0.2
			 */
			do_action( 'eaccounting_reports_tab_' . $active_tab );
			?>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}

/**
 * Retrieve reports tabs
 *
 * @return array $tabs
 * @since 1.0.2
 */
function eaccounting_get_reports_tabs() {
	$tabs                    = array();
	$tabs['income_summery']  = __( 'Income Summery', 'wp-ever-accounting' );
	$tabs['expense_summery'] = __( 'Expense Summery', 'wp-ever-accounting' );
	$tabs['income_expense']  = __( 'Income vs Expense', 'wp-ever-accounting' );

	return apply_filters( 'eaccounting_reports_tabs', $tabs );
}

/**
 * Setup reports pages.
 *
 * @since 1.0.2
 */
function eaccounting_load_reports_page() {
	$tab = eaccounting_get_current_tab();
	if ( empty( $tab ) ) {
		wp_redirect( add_query_arg( [ 'tab' => 'income_summery' ] ) );
		exit();
	}

	do_action( 'eaccounting_load_reports_page_tab' . $tab );
}
