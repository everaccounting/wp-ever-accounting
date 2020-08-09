<?php
/**
 * Admin Banking Page
 *
 * @package     EverAccounting
 * @subpackage  Admin/Banking
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();

require_once dirname( __FILE__ ) .'/accounts/accounts.php';


/**
 * render banking page.
 *
 * @since 1.0.2
 */
function eaccounting_admin_banking_page() {
	$tabs       = eaccounting_get_banking_tabs();
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
			 * the currently active banking tab.
			 *
			 * @since 1.0.2
			 */
			do_action( 'eaccounting_banking_tab_' . $active_tab );
			?>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}

/**
 * Retrieve banking tabs
 *
 * @return array $tabs
 * @since 1.0.2
 */
function eaccounting_get_banking_tabs() {
	$tabs             = array();
	$tabs['accounts']   = __( 'Accounts', 'wp-ever-accounting' );
	$tabs['transfers']  = __( 'Transfers', 'wp-ever-accounting' );
	//$tabs['currencies'] = __( 'Currencies', 'wp-ever-accounting' );

	return apply_filters( 'eaccounting_banking_tabs', $tabs );
}

/**
 * Setup banking pages.
 *
 * @since 1.0.2
 */
function eaccounting_load_banking_page() {
	$tab = eaccounting_get_current_tab();
	if ( empty( $tab ) ) {
		wp_redirect( add_query_arg( [ 'tab' => 'accounts' ] ) );
		exit();
	}

	do_action( 'eaccounting_load_banking_page_tab' . $tab );
}
