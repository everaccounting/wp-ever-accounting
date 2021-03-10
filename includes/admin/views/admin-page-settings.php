<?php
/**
 * Admin View: Page - Settings
 *
 * @var array  $tabs
 * @var string $active_tab
 * @var string $active_section
 * @var string $active_tab_label
 * @var array  $sections
 */
defined( 'ABSPATH' ) || exit;
$section_keys = array_keys( $sections );
?>
<div class="wrap ea-settings">
	<nav class="nav-tab-wrapper ea-tab-wrapper">
		<?php
		foreach ( $tabs as $slug => $label ) {
			echo '<a href="' . esc_html( admin_url( 'admin.php?page=ea-settings&tab=' . esc_attr( $slug ) ) ) . '" class="nav-tab ' . ( $active_tab === $slug ? 'nav-tab-active' : '' ) . '">' . esc_html( $label ) . '</a>';
		}
		?>
	</nav>

	<?php if ( sizeof( $sections ) > 1 ) : ?>
		<ul class="subsubsub">
			<?php
			$links = array();
			foreach ( array_filter( $sections ) as $key => $section_title ) {
				$link = '<a href="admin.php?page=ea-settings&tab=' . urlencode( $active_tab ) . '&amp;section=' . urlencode( $key ) . '" class="';
				if ( $key === $active_section ) {
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
	<h1 class="screen-reader-text"><?php echo esc_html( $active_tab_label ); ?></h1>

	<?php
	do_action( 'eaccounting_settings_top', $active_tab, $active_section );

	if ( has_action( 'eaccounting_settings_tab_' . $active_tab ) ) {
		do_action( 'eaccounting_settings_tab_' . $active_tab );
	} elseif ( $active_section && has_action( 'eaccounting_settings_tab_' . $active_tab . '_section_' . $active_section ) ) {
		do_action( 'eaccounting_settings_tab_' . $active_tab . '_section_' . $active_section );
	} else {
		if ( empty( $active_section ) ) {
			$active_section = 'main';
		}
		global $wp_settings_sections, $wp_settings_fields;
		print_r($wp_settings_sections);
		?>
		<form method="post" id="mainform" action="options.php" enctype="multipart/form-data">
			<table class="form-table">
				<?php settings_errors(); ?>
				<?php settings_fields( 'eaccounting_settings' ); ?>
				<?php do_settings_sections( 'eaccounting_settings_' . $active_tab . '_' . $active_section ); ?>
			</table>

			<?php if ( empty( $GLOBALS['hide_save_button'] ) ) : ?>
				<?php submit_button(); ?>
			<?php endif; ?>
		</form>
		<?php
	}
	?>
</div>
