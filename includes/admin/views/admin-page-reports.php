<?php
/**
 * Admin View: Page - Reports
 *
 * @var array  $tabs
 * @var string $current_tab
 * @var array $sections
 * @var string $current_section
 */
?>
<div class="wrap eaccounting ea-reports">
	<nav class="nav-tab-wrapper ea-nav-tab-wrapper">
		<?php
		foreach ( $tabs as $name => $label ) {
			echo '<a href="' . admin_url( 'admin.php?page=ea-reports&tab=' . $name ) . '" class="nav-tab ';
			if ( $current_tab === $name ) {
				echo 'nav-tab-active';
			}
			echo '">' . esc_html( $label ) . '</a>';
		}
		?>
	</nav>
	<?php if ( sizeof( $sections ) > 1 ) : ?>
		<ul class="subsubsub">
			<li>
				<?php

				$links = array();

				foreach ( $sections as $section_id => $current_title ) {
					$link = '<a href="admin.php?page=ea-reports&tab=' . urlencode( $current_tab ) . '&amp;section=' . urlencode( $section_id ) . '" class="';

					if ( $section_id === $current_section ) {
						$link .= 'current';
					}

					$link .= '">' . esc_html( $current_title ) . '</a>';

					$links[] = $link;
				}

				echo implode( ' | </li><li>', $links );

				?>
			</li>
		</ul>
		<br class="clear"/>
	<?php endif; ?>
	<br class="clear"/>

	<h1 class="screen-reader-text"><?php echo esc_html( $tabs[ $current_tab ] ); ?></h1>
	<div class="ea-admin-page">
		<?php do_action( 'eaccounting_reports_tab_' . $current_tab ); ?>
	</div>
</div>
