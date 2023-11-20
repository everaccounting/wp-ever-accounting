<?php
/**
 * Admin View: Settings
 *
 * @since   1.0.0
 * @var string $current_section Current section.
 * @var string $page Current page.
 * @var array  $tabs Tabs.
 * @var array  $notices Notices.
 *
 * @var string $current_tab Current tab.
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit;

$current_tab_label = isset( $tabs[ $current_tab ] ) ? $tabs[ $current_tab ] : '';
?>
<div class="wrap eac-admin-page">
	<nav class="nav-tab-wrapper eac-admin-page__nav">
		<?php foreach ( $tabs as $slug => $label ) : ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . esc_attr( $page ) . '&tab=' . esc_attr( $slug ) ) ); ?>" class="nav-tab <?php echo $current_tab === $slug ? 'nav-tab-active' : ''; ?>">
				<?php echo esc_html( $label ); ?>
			</a>
		<?php endforeach; ?>
		<?php
		/**
		 * Fires after the tabs on the settings page.
		 *
		 * @param string $current_tab Current tab.
		 * @param array  $tabs Tabs.
		 *
		 * @since 1.0.0
		 */
		do_action( 'ever_accounting_settings_nav_items', $current_tab, $tabs );
		?>
	</nav>
	<?php if ( count( $notices ) > 0 ) : ?>
		<?php foreach ( $notices as $notice ) : ?>
			<div id="message" class="notice notice-<?php echo esc_attr( $notice['type'] ); ?> inline"><p><strong><?php echo esc_html( $notice['message'] ); ?></strong></p></div>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php
	/**
	 * Fires before the settings page.
	 *
	 * @param string $current_tab Current tab.
	 * @param string $current_section Current section.
	 *
	 * @since 1.0.0
	 */
	do_action( 'ever_accounting_settings_sections_' . $current_tab );
	?>
	<hr class="wp-header-end">
	<?php
	/**
	 * Fires before the settings tab content.
	 *
	 * @param string $current_tab Current tab.
	 * @param string $current_section Current section.
	 *
	 * @since 1.0.0
	 */
	do_action( 'ever_accounting_before_settings_tab_' . $current_tab, $current_section );

	/**
	 * Fires before the settings page.
	 *
	 * @param string $current_tab Current tab.
	 * @param string $current_section Current section.
	 *
	 * @since 1.0.0
	 */
	do_action( 'ever_accounting_settings_tab_' . $current_tab, $current_section );

	/**
	 * Fires after the settings page.
	 *
	 * @param string $current_tab Current tab.
	 * @param string $current_section Current section.
	 *
	 * @since 1.0.0
	 */
	do_action( 'ever_accounting_after_settings_tab_' . $current_tab, $current_section );
	?>
</div>
