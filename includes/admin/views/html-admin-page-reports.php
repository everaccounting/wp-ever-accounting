<?php
/**
 * Admin View: Page - Reports
 *
 * @var array  $tabs
 * @var string $current_tab
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="wrap eaccounting ea-reports">
	<nav class="nav-tab-wrapper ea-nav-tab-wrapper">
		<?php
		foreach ( $tabs as $key => $tab ) {
			echo '<a href="' . admin_url( 'admin.php?page=ea-reports&tab=' . urlencode( $key ) ) . '" class="nav-tab ';
			if ( $current_tab === $key ) {
				echo 'nav-tab-active';
			}
			echo '">' . esc_html( $tab ) . '</a>';
		}
		?>
	</nav>
	<div class="ea-card">
		<div class="ea-card__header">
			<h3 class="ea-card__title">Sales Report</h3>
			<div>
				<select name="" id="">
					<option value="">By Date</option>
					<option value="">By Category</option>
				</select>
			</div>
		</div>
	</div>
	<?php
	if ( has_action( 'eaccounting_render_report_' . $current_tab ) ) {
		do_action( 'eaccounting_render_report_' . $current_tab );
	}
	?>
</div>
