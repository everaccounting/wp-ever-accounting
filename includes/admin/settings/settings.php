<?php
/**
 * Admin Settings Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Settings
 * @since       1.0.2
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit();

require_once dirname( __FILE__ ) .'/currencies/currencies.php';
require_once dirname( __FILE__ ) .'/categories/categories.php';

/**
 * Options Page
 *
 * Renders the options page contents.
 *
 * @return void
 * @since 1.0.2
 */
function eaccounting_admin_settings_page() {
	$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], eaccounting_get_settings_tabs() ) ? $_GET['tab'] : 'general';
	global $wp_settings_fields;
	$page    = 'eaccounting_settings_' . $active_tab;
	$section = 'eaccounting_settings_' . $active_tab;
	?>
	<div class="wrap">
		<h2 class="nav-tab-wrapper">
			<?php eaccounting_navigation_tabs( eaccounting_get_settings_tabs(), $active_tab ); ?>
		</h2>
		<div id="tab_container">
			<?php if ( isset( $wp_settings_fields[ $page ][ $section ] ) ): ?>
				<form method="post" action="options.php">
					<table class="form-table">
						<?php
						settings_fields( 'eaccounting_settings' );
						do_settings_fields( 'eaccounting_settings_' . $active_tab, 'eaccounting_settings_' . $active_tab );
						?>
					</table>
					<?php submit_button(); ?>
				</form>
			<?php else: ?>
				<?php do_action( 'eaccounting_settings_tab_' . $active_tab ); ?>
			<?php endif; ?>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}


/**
 * Retrieves the settings tabs.
 *
 * @return array $tabs Settings tabs.
 * @since 1.0.2
 *
 */
function eaccounting_get_settings_tabs() {
	$tabs               = array();
	$tabs['general']    = __( 'General', 'wp-ever-accounting' );
	$tabs['categories'] = __( 'Categories', 'wp-ever-accounting' );
	$tabs['currencies'] = __( 'Currencies', 'wp-ever-accounting' );
//	$tabs['emails']     = __( 'Emails', 'wp-ever-accounting' );
//	$tabs['misc']       = __( 'Misc', 'wp-ever-accounting' );


	/**
	 * Filters the list of settings tabs.
	 *
	 * @param array $tabs Settings tabs.
	 *
	 * @since 1.0.2
	 *
	 */
	return apply_filters( 'eaccounting_settings_tabs', $tabs );
}
