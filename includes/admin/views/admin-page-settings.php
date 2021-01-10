<?php
/**
 * Admin View: Page - Settings
 *
 * @var array  $tabs
 * @var string $current_tab
 * @var string $current_section
 * @var string $current_tab_label
 * @var array  $sections
 */
defined( 'ABSPATH' ) || exit;
$section_keys = array_keys( $sections );
?>
<div class="wrap ea-settings">
	<nav class="nav-tab-wrapper ea-tab-wrapper">
		<?php
		foreach ( $tabs as $slug => $label ) {
			echo '<a href="' . esc_html( admin_url( 'admin.php?page=ea-settings&tab=' . esc_attr( $slug ) ) ) . '" class="nav-tab ' . ( $current_tab === $slug ? 'nav-tab-active' : '' ) . '">' . esc_html( $label ) . '</a>';
		}
		?>
	</nav>

	<?php if ( sizeof( $sections ) > 1 ) : ?>
		<ul class="subsubsub">
			<?php
			$links = array();
			foreach ( $sections as $key => $section_title ) {
				$link = '<a href="admin.php?page=ea-settings&tab=' . urlencode( $current_tab ) . '&amp;section=' . urlencode( $key ) . '" class="';
				if ( $key === $current_section ) {
					$link .= 'current';
				}
				$link   .= '">' . esc_html( $section_title ) . '</a>';
				$links[] = $link;
			}
			echo implode( ' | </li><li>', $links );
			?>
		</ul>
	<?php endif; ?>
	<br class="clear"/>
	<h1 class="screen-reader-text"><?php echo esc_html( $current_tab_label ); ?></h1>

	<?php
	do_action( 'eaccounting_settings_top', $current_tab, $current_section );

	if ( has_action( 'eaccounting_settings_tab_' . $current_tab ) ) {
		do_action( 'eaccounting_settings_tab_' . $current_tab );
	} elseif ( $current_section && has_action( 'eaccounting_settings_tab_' . $current_tab . '_section_' . $current_section ) ) {
		do_action( 'eaccounting_settings_tab_' . $current_tab . '_section_' . $current_section );
	} else {
		?>
		<form method="post" id="mainform" action="options.php" enctype="multipart/form-data">
			<table class="form-table">
				<?php settings_errors(); ?>
				<?php settings_fields( 'eaccounting_settings' ); ?>
				<?php do_settings_fields( 'eaccounting_settings_' . $current_tab, $current_section ); ?>
			</table>

			<?php if ( empty( $GLOBALS['hide_save_button'] ) ) : ?>
				<?php submit_button(); ?>
			<?php endif; ?>
		</form>
		<?php
	}
	?>
</div>
