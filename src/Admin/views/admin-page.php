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

// Check if we have a variable called $sections.
$sections        = apply_filters( "ever_accounting_{$page_name}_{$current_tab}_sections", array() );
$section         = eac_get_input_var( 'section' );
$current_section = ! empty( $section ) && array_key_exists( $section, $sections ) ? $section : key( $sections );
$section_keys    = array_keys( $sections );
?>

	<div class="wrap eac-admin-page">
		<?php if ( isset( $tabs ) && ! empty( $tabs ) && count( $tabs ) > 1 ) : ?>
			<nav class="nav-tab-wrapper eac-admin-page__nav">
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
		<?php if ( count( $sections ) > 1 ) : ?>
			<ul class="subsubsub" style="float: none;">
				<?php
				foreach ( $sections as $section_id => $section_label ) {
					$url       = esc_url( admin_url( 'admin.php?page=' . $current_page . '&tab=' . $current_tab . '&section=' . $section_id ) );
					$class     = ( $current_section === $section_id ? 'current' : '' );
					$separator = ( end( $section_keys ) === $section_id ? '' : '|' );
					$text      = esc_html( $section_label );
					echo "<li><a href='$url' class='$class'>$text</a> $separator </li>"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				/**
				 * Fires after the sections on the settings page.
				 *
				 * @param string $current_tab Current tab.
				 * @param string $current_section Current section.
				 * @param array  $sections Sections.
				 *
				 * @since 1.0.0
				 */
				do_action( "ever_accounting_{$page_name}_{$current_tab}_{$current_section}_subsub_nav_items", $current_tab, $current_section, $sections );
				?>
			</ul>
			<br class="clear">
		<?php endif; ?>

		<hr class="wp-header-end">
		<?php
		if ( ! empty( $tabs ) && ! empty( $current_tab ) && ! empty( $sections ) && ! empty( $current_section ) ) {
			/**
			 * Action: EverAccounting Admin Page Tab Section
			 *
			 * @param string $current_tab Current tab.
			 * @param string $current_section Current section.
			 *
			 * @since 1.0.0
			 */
			do_action( "ever_accounting_{$page_name}_{$current_tab}_{$current_section}_content", $current_tab, $current_section );
		}

		if ( ! empty( $tabs ) && ! empty( $current_tab ) ) {
			/**
			 * Action: EverAccounting Admin Page Tab
			 *
			 * @param string $current_tab Current tab.
			 *
			 * @since 1.0.0
			 */
			do_action( "ever_accounting_{$page_name}_{$current_tab}_content", $current_tab );
		}

		/**
		 * Action: EverAccounting Admin Page
		 *
		 * @since 1.0.0
		 */
		do_action( "ever_accounting_{$page_name}_content", $current_tab, $current_section );
		?>
	</div>
<?php
