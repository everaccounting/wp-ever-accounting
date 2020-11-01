<?php
/**
 * Admin Misc Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Misc
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();

require_once dirname( __FILE__ ) . '/currencies/currencies.php';
require_once dirname( __FILE__ ) . '/categories/categories.php';

/**
 * render banking page.
 *
 * @since 1.0.2
 */
function eaccounting_admin_misc_page() {
	$tabs       = eaccounting_get_misc_tabs();
	$active_tab = eaccounting_get_active_tab( $tabs, 'categories' );

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
			do_action( 'eaccounting_misc_tab_' . $active_tab );
			?>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}

/**
 * Retrieve misc tabs
 *
 * @since 1.0.2
 * @return array $tabs
 */
function eaccounting_get_misc_tabs() {
	$tabs = array();
	if ( current_user_can( 'ea_manage_category' ) ) {
		$tabs['categories'] = __( 'Categories', 'wp-ever-accounting' );
	}
	if ( current_user_can( 'ea_manage_currency' ) ) {
		$tabs['currencies'] = __( 'Currencies', 'wp-ever-accounting' );
	}
	if ( current_user_can( 'ea_manage_currency' ) ) {
		$tabs['taxes'] = __( 'Taxes', 'wp-ever-accounting' );
	}

	return apply_filters( 'eaccounting_misc_tabs', $tabs );
}

/**
 * Setup misc pages.
 *
 * @since 1.0.2
 */
function eaccounting_load_misc_page() {
	$tab  = eaccounting_get_current_tab();
	$tabs = eaccounting_get_misc_tabs();
	if ( empty( $tab ) && $tabs ) {
		wp_redirect( add_query_arg( array( 'tab' => current( array_keys( $tabs ) ) ) ) );
		exit();
	}
	do_action( 'eaccounting_load_misc_page_tab' . $tab );
}
