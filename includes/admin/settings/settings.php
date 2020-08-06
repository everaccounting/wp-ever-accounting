<?php
/**
 * Admin Options Page
 *
 * @package     EverAccounting
 * @subpackage  Admin/Settings
 * @since       1.0.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

	ob_start();
	?>
	<div class="wrap">
		<h2 class="nav-tab-wrapper">
			<?php eaccounting_navigation_tabs( eaccounting_get_settings_tabs(), $active_tab ); ?>
		</h2>
		<div id="tab_container">
			<form method="post" action="options.php">
				<table class="form-table">
					<?php
					settings_fields( 'eaccounting_settings' );
					do_settings_fields( 'eaccounting_settings_' . $active_tab, 'eaccounting_settings_' . $active_tab );
					?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}


/**
 * Retrieves the settings tabs.
 *
 * @return array $tabs Settings tabs.
 * @since 1.0
 *
 */
function eaccounting_get_settings_tabs() {
	$tabs                    = array();
	$tabs['general']         = __( 'General', 'wp-ever-accounting' );
	$tabs['integrations']    = __( 'Integrations', 'wp-ever-accounting' );
	$tabs['opt_in_forms']    = __( 'Opt-In Form', 'wp-ever-accounting' );
	$tabs['emails']          = __( 'Emails', 'wp-ever-accounting' );
	$tabs['misc']            = __( 'Misc', 'wp-ever-accounting' );
	$tabs['payouts_service'] = __( 'Payouts Service', 'wp-ever-accounting' );

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
