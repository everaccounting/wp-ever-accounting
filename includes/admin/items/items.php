<?php
/**
 * Admin Items Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Items
 * @since       1.1.0
 */
defined( 'ABSPATH' ) || exit();

require_once dirname( __FILE__ ) . '/items/items.php';


/**
 * render expenses page.
 *
 * @since 1.1.0
 */
function eaccounting_admin_items_page() {
	$tabs       = eaccounting_get_items_tabs();
	$active_tab = eaccounting_get_active_tab( $tabs, 'items' );

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
			 * @since 1.1.0
			 */
			do_action( 'eaccounting_items_tab_' . $active_tab );
			eaccounting_get_categories();
			eaccounting_get_categories();
			eaccounting_get_categories();
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
function eaccounting_get_items_tabs() {
	$tabs          = array();
	$tabs['items'] = __( 'Items', 'wp-ever-accounting' );

	return apply_filters( 'eaccounting_items_tabs', $tabs );
}

/**
 * Setup expenses pages.
 *
 * @since 1.0.2
 */
function eaccounting_load_items_page() {
	$tab  = eaccounting_get_current_tab();
	$tabs = eaccounting_get_items_tabs();
	if ( empty( $tab ) && $tabs ) {
		wp_redirect( add_query_arg( array( 'tab' => current( array_keys( $tabs ) ) ) ) );
		exit();
	}

	do_action( 'eaccounting_load_items_page_tab' . $tab );
}
