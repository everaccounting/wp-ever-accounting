<?php
/**
 * Admin Expenses Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Expenses
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();

require_once dirname( __FILE__ ) .'/payments/payments.php';
require_once dirname( __FILE__ ) .'/vendors/vendors.php';

/**
 * render expenses page.
 *
 * @since 1.0.2
 */
function eaccounting_admin_expenses_page() {
	$tabs       = eaccounting_get_expenses_tabs();
	$active_tab = eaccounting_get_active_tab( $tabs, 'payments' );

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
			 * the currently active expenses tab.
			 *
			 * @since 1.0.2
			 */
			do_action( 'eaccounting_expenses_tab_' . $active_tab );
			?>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}

/**
 * Retrieve expenses tabs
 *
 * @return array $tabs
 * @since 1.0.2
 */
function eaccounting_get_expenses_tabs() {
	$tabs = array();
	//$tabs['bills']    = __( 'Bills', 'wp-ever-accounting' );
	$tabs['payments'] = __( 'Payments', 'wp-ever-accounting' );
	$tabs['vendors']  = __( 'Vendors', 'wp-ever-accounting' );

	return apply_filters( 'eaccounting_expenses_tabs', $tabs );
}

/**
 * Setup expenses pages.
 *
 * @since 1.0.2
 */
function eaccounting_load_expenses_page() {
	$tab = eaccounting_get_current_tab();
	if ( empty( $tab ) ) {
		wp_redirect( add_query_arg( [ 'tab' => 'revenues' ] ) );
		exit();
	}

	do_action( 'eaccounting_load_expenses_page_tab' . $tab );
}
