<?php
/**
 * Admin Tools Page
 *
 * @package     EverAccounting
 * @subpackage  Admin/Tools
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();

/**
 * Render screen option for banking page.
 *
 * @since 1.0.2
 */
function eaccounting_baking_screen_options() {
	$tab = eaccounting_get_current_tab();
	if ( empty( $tab ) ) {
		wp_redirect( add_query_arg( [ 'tab' => 'accounts' ] ) );
		exit();
	}

	$action = 'eaccounting_baking_screen_options_tab_' . sanitize_key( $tab );
	do_action( $action );
}

/**
 * Retrieve banking tabs
 *
 * @return array $tabs
 * @since 1.0.2
 */
function eaccounting_get_banking_tabs() {
	$tabs              = array();
	$tabs['accounts']  = __( 'Accounts', 'wp-ever-accounting' );
	$tabs['transfers'] = __( 'Transfers', 'wp-ever-accounting' );

	return apply_filters( 'eaccounting_banking_tabs', $tabs );
}


/**
 * render banking page.
 *
 * @since 1.0.2
 */
function eaccounting_admin_banking() {
	$tabs       = eaccounting_get_banking_tabs();
	$active_tab = eaccounting_get_active_tab( $tabs, 'accounts' );

	ob_start();
	?>
	<div class="wrap">
		<h2 class="nav-tab-wrapper">
			<?php eaccounting_navigation_tabs( $tabs, $active_tab, array() ); ?>
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
