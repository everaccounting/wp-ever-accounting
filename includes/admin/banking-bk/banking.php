<?php
/**
 * Admin Tools Page
 *
 * @package     EverAccounting
 * @subpackage  Admin/Banking
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();

require_once __DIR__ . '/accounts/accounts.php';
require_once __DIR__ . '/transfers/transfers.php';
require_once __DIR__ . '/currencies/currencies.php';


/**
 * Add per page screen option to the Accounts list table
 *
 * @since 1.0.2
 */
function eaccounting_baking_screen_options() {
	$tab = eaccounting_get_current_tab();
//
//	if ( $tab !== 'accounts' || empty( $tab ) ) {
//		return;
//	}
//
	add_screen_option(
			'per_page',
			array(
					'label'   => __( 'Number of accounts per page:', 'wp-ever-accounting' ),
					'option'  => 'eaccounting_edit_accounts_per_page',
					'default' => 20,
			)
	);

	/*
	 * Instantiate the list table to make the columns array available to screen options.
	 *
	 * If the 'view_accounts' action is set, don't instantiate. Instantiating in sub-views
	 * creates conflicts in the screen option column controls if another list table is being
	 * displayed.
	 */
	//if ( empty( $_REQUEST['action'] ) || ( ! empty( $_REQUEST['action'] ) && 'view_accounts' !== $_REQUEST['action'] ) ) {
	new Currencies_List_Table();
	//}


//	do_action( 'eaccounting_accounts_screen_options' );
}

//add_action( 'eaccounting_baking_screen_options', 'eaccounting_baking_screen_options' );

/**
 * Render screen option for banking page.
 *
 * @since 1.0.2
 */
function eaccounting_load_baking_page() {
	$tab = eaccounting_get_current_tab();
	if ( empty( $tab ) ) {
		wp_redirect( add_query_arg( [ 'tab' => 'accounts' ] ) );
		exit();
	}

	do_action( 'eaccounting_banking_screen_options_tab_' . $tab );

//	switch ( $tab ) {
//		case 'accounts':
//			break;
//		case 'transfers':
//			break;
//		case 'currencies':
//			add_screen_option(
//					'per_page',
//					array(
//							'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
//							'option'  => 'eaccounting_edit_currencies_per_page',
//							'default' => 20,
//					)
//			);
//			new Currencies_List_Table();
//			break;
//		default:
//			do_action( 'eaccounting_load_baking_page_tab_' . $tab );
//	}
}

add_action( 'eaccounting_banking_screen_options_tab_currencies', function () {

	add_screen_option(
			'per_page',
			array(
					'label'   => __( 'Number of items per page:', 'wp-ever-accounting' ),
					'option'  => 'eaccounting_edit_currencies_per_page',
					'default' => 20,
			)
	);
	new Currencies_List_Table();
} );

/**
 * Retrieve banking tabs
 *
 * @return array $tabs
 * @since 1.0.2
 */
function eaccounting_get_banking_tabs() {
	$tabs               = array();
	$tabs['accounts']   = __( 'Accounts', 'wp-ever-accounting' );
	$tabs['transfers']  = __( 'Transfers', 'wp-ever-accounting' );
	$tabs['currencies'] = __( 'Currencies', 'wp-ever-accounting' );

	return apply_filters( 'eaccounting_banking_tabs', $tabs );
}


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


