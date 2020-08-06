<?php
/**
 * Admin Purchases Page
 *
 * @package     EverAccounting
 * @subpackage  Admin/Purchases
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();
/**
 * render purchases page.
 *
 * @since 1.0.2
 */
function eaccounting_admin_purchases_page() {
	$tabs       = eaccounting_get_purchases_tabs();
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
			 * the currently active purchases tab.
			 *
			 * @since 1.0.2
			 */
			do_action( 'eaccounting_purchases_tab_' . $active_tab );
			?>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}

/**
 * Retrieve purchases tabs
 *
 * @return array $tabs
 * @since 1.0.2
 */
function eaccounting_get_purchases_tabs() {
	$tabs             = array();
	$tabs['bills']    = __( 'Bills', 'wp-ever-accounting' );
	$tabs['payments'] = __( 'Payments', 'wp-ever-accounting' );
	$tabs['vendors']  = __( 'Vendors', 'wp-ever-accounting' );

	return apply_filters( 'eaccounting_purchases_tabs', $tabs );
}

/**
 * Setup purchases pages.
 *
 * @since 1.0.2
 */
function eaccounting_load_purchases_page() {
	$tab = eaccounting_get_current_tab();
	if ( empty( $tab ) ) {
		wp_redirect( add_query_arg( [ 'tab' => 'revenues' ] ) );
		exit();
	}

	do_action( 'eaccounting_load_purchases_page_tab' . $tab );
}
