<?php
defined( 'ABSPATH' ) || exit();
$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'taxes';
$base       = admin_url( 'admin.php?page=eaccounting-misc' );
$misc_tabs  = apply_filters( 'eaccounting_misc_page_tabs', array(
	'taxes'      => __( 'Taxes', 'wp-ever-accounting' ),
	'categories' => __( 'Categories', 'wp-ever-accounting' )
) );

?>
<div class="wrap ea-wrapper">
	<h2 class="nav-tab-wrapper ea-tab-nav-wrapper">
		<?php
		foreach ( $misc_tabs as $tab_id => $label ) {
			$tab_url = add_query_arg( array(
				'tab' => $tab_id
			), $base );
			$active  = $active_tab == $tab_id ? ' nav-tab-active' : '';
			echo sprintf( '<a href="%s" class="nav-tab %s">%s</a>', $tab_url, $active, $label );
		}
		?>
	</h2>
	<div class="ea-tab-section-wrapper ea-misc-tab-section <?php echo sanitize_html_class( $active_tab ); ?>">
		<?php do_action( 'eaccounting_misc_tab_' . $active_tab ); ?>
	</div>
</div>
