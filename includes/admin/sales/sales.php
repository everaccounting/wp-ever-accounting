<?php
/**
 * Admin Sales Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Sales
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();

require_once dirname( __FILE__ ) . '/revenues/revenues.php';
require_once dirname( __FILE__ ) . '/customers/customers.php';
require_once dirname( __FILE__ ) . '/invoices/invoices.php';

/**
 * render sales page.
 *
 * @since 1.0.2
 */
function eaccounting_admin_sales_page() {
	$tabs       = eaccounting_get_sales_tabs();
	$active_tab = eaccounting_get_active_tab( $tabs, 'invoices' );

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
			 * the currently active sales tab.
			 *
			 * @since 1.0.2
			 */
			do_action( 'eaccounting_sales_tab_' . $active_tab );
			?>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}

/**
 * Retrieve sales tabs
 *
 * @return array $tabs
 * @since 1.0.2
 */
function eaccounting_get_sales_tabs() {
	$tabs              = array();
	$tabs['invoices']  = __( 'Invoices', 'wp-ever-accounting' );
	$tabs['revenues']  = __( 'Revenues', 'wp-ever-accounting' );
	$tabs['customers'] = __( 'Customers', 'wp-ever-accounting' );

	return apply_filters( 'eaccounting_sales_tabs', $tabs );
}

/**
 * Setup sales pages.
 *
 * @since 1.0.2
 */
function eaccounting_load_sales_page() {
	$tab  = eaccounting_get_current_tab();
	$tabs = eaccounting_get_sales_tabs();
	if ( empty( $tab ) && $tabs ) {
		wp_redirect( add_query_arg( array( 'tab' => current( array_keys( $tabs ) ) ) ) );
		exit();
	}
	do_action( 'eaccounting_load_sales_page_tab' . $tab );
}
