<?php
defined( 'ABSPATH' ) || exit();
$active_tab   = isset( $_GET['tab'] ) ? $_GET['tab'] : 'income_summery';
$base         = admin_url( 'admin.php?page=eaccounting-reports' );
$reports_tabs = apply_filters( 'eaccounting_reports_page_tabs', array(
	'income_summery'         => __( 'Income Summery', 'wp-ever-accounting' ),
	'expense_summery'        => __( 'Expense Summery', 'wp-ever-accounting' ),
	'income_expense_summary' => __( 'Income vs Expense', 'wp-ever-accounting' ),
//	'profit_loss'            => __( 'Profit & Loss', 'wp-ever-accounting' ),
) );

?>
<div class="wrap ea-wrapper">
	<?php echo sprintf( '<h1 class="wp-heading-inline">%s</h1>', __('Reports', 'wp-ever-accounting') ); ?>
	<h2 class="nav-tab-wrapper ea-tab-nav-wrapper">
		<?php
		foreach ( $reports_tabs as $tab_id => $label ) {
			$tab_url = add_query_arg( array(
				'tab' => $tab_id
			), $base );
			$active  = $active_tab == $tab_id ? ' nav-tab-active' : '';
			echo sprintf( '<a href="%s" class="nav-tab %s">%s</a>', $tab_url, $active, $label );
		}
		?>
	</h2>
	<?php echo sprintf( '<div class="ea-tab-section-wrapper ea-reports-tab-section %s">', $active_tab ); ?>
	<?php do_action( 'eaccounting_reports_tab_' . $active_tab ); ?>
</div>
