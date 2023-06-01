<?php
/**
 * View: Admin Page
 *
 * @since 1.0.0
 * @subpackage Admin/Views
 * @package EverAccounting
 * @var string $current_tab Current tab.
 * @var string $current_page Current page.
 * @var string $page_name Page name.
 * @var array  $tabs Tabs.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap eac-page">
	<?php if ( isset( $tabs ) && ! empty( $tabs ) ) : ?>
		<nav class="nav-tab-wrapper eac-page__nav">
			<?php
			foreach ( $tabs as $name => $label ) {
				echo sprintf(
					'<a href="%s" class="nav-tab %s">%s</a>',
					esc_url( admin_url( 'admin.php?page=' . $current_page . '&tab=' . $name ) ),
					esc_attr( $current_tab === $name ? 'nav-tab-active' : '' ),
					esc_html( $label )
				);
			}
			?>
			<?php
			/**
			 * Fires after the tabs on the settings page.
			 *
			 * @param string $current_tab Current tab..
			 * @param array  $tabs Tabs.
			 *
			 * @since 1.0.0
			 */
			do_action( 'ever_accounting_' . $page_name . '_nav_items', $current_tab, $tabs );
			?>
		</nav>
	<?php endif; ?>
	<hr class="wp-header-end">
	<?php
	if ( ! empty( $tabs ) && ! empty( $current_tab ) ) {
		/**
		 * Action: EverAccounting Admin Page Tab
		 *
		 * @param string $current_tab Current tab.
		 *
		 * @since 1.0.0
		 */
		do_action( 'ever_accounting_' . $page_name . '_tab_' . $current_tab, $current_tab );
	}

	/**
	 * Action: EverAccounting Admin Page
	 *
	 * @since 1.0.0
	 */
	do_action( 'ever_accounting_' . $page_name . '_content' );
	?>
</div>
<?php
