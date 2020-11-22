<?php
/**
 * Admin Settings Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Settings
 * @package     EverAccounting
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit();


/**
 * Options Page
 *
 * Renders the options page contents.
 *
 * @since 1.0.2
 * @return void
 */
function eaccounting_admin_settings_page() {
	$active_tab = eaccounting_get_current_tab();
	$tabs       = eaccounting_get_settings_tabs();
	if ( empty( $active_tab ) && $tabs ) {
		wp_redirect( add_query_arg( [ 'tab' => current( array_keys( $tabs ) ) ] ) );
		exit();
	}
	
	global $wp_settings_fields;
	$page    = 'eaccounting_settings_' . $active_tab;
	$section = 'eaccounting_settings_' . $active_tab;
	?>
	<div class="wrap">
		<?php if ( count( eaccounting_get_settings_tabs() ) > 1 ) : ?>
			<h2 class="nav-tab-wrapper">
				<?php eaccounting_navigation_tabs( eaccounting_get_settings_tabs(), $active_tab ); ?>
			</h2>
		<?php else : ?>
			<h1><?php _e( 'Settings', 'wp-ever-accounting' ); ?></h1>
		<?php endif; ?>

		<div id="tab_container">
			<?php if ( isset( $wp_settings_fields[ $page ][ $section ] ) ) : ?>
				<form method="post" action="options.php">
					<table class="form-table">
						<?php
						settings_errors();
						settings_fields( 'eaccounting_settings' );
						do_settings_fields( 'eaccounting_settings_' . $active_tab, 'eaccounting_settings_' . $active_tab );
						?>
					</table>
					<?php submit_button(); ?>
				</form>
			<?php else : ?>
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
 * @since 1.0.2
 *
 * @return array $tabs Settings tabs.
 */
function eaccounting_get_settings_tabs() {
	$tabs = array();
	if ( current_user_can( 'ea_manage_options' ) ) {
		$tabs['general'] = __( 'Settings', 'wp-ever-accounting' );
	}
	// $tabs['emails']     = __( 'Emails', 'wp-ever-accounting' );
	// $tabs['misc']       = __( 'Misc', 'wp-ever-accounting' );

	/**
	 * Filters the list of settings tabs.
	 *
	 * @since 1.0.2
	 *
	 * @param array $tabs Settings tabs.
	 */
	return apply_filters( 'eaccounting_settings_tabs', $tabs );
}
