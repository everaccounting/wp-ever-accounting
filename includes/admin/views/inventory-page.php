<?php
defined( 'ABSPATH' ) || exit();
$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'items';
$base       = admin_url( 'admin.php?page=eaccounting-inventory' );
$tabs       = apply_filters( 'eaccounting_inventory_page_tabs', array(
	'items' => __( 'Items', 'wp-ever-accounting' ),
) );

?>
<div class="wrap ea-wrapper">
	<?php if ( count( $tabs ) > 1 ): ?>
		<h2 class="nav-tab-wrapper ea-tab-nav-wrapper">
			<?php
			foreach ( $tabs as $tab_id => $label ) {
				$tab_url = add_query_arg( array(
					'tab' => $tab_id
				), $base );
				$active  = $active_tab == $tab_id ? ' nav-tab-active' : '';
				echo sprintf( '<a href="%s" class="nav-tab %s">%s</a>', $tab_url, $active, $label );
			}
			?>
		</h2>
	<?php endif; ?>
	<div class="ea-tab-section-wrapper ea-misc-tab-section <?php echo sanitize_html_class( $active_tab ); ?>">
		<?php do_action( 'eaccounting_inventory_tab_' . $active_tab ); ?>
	</div>
</div>
