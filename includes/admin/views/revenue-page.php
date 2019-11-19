<?php
defined( 'ABSPATH' ) || exit();
?>
<div class="wrap ea-wrapper">
	<?php
	if ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'add_revenue' ) {
		eaccounting_get_views( 'edit-revenue.php' );
	} elseif ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'edit_revenue' ) {
		eaccounting_get_views( 'edit-revenue.php' );
	} else {
		require_once EACCOUNTING_ADMIN_ABSPATH . '/tables/class-ea-revenues-list-table.php';
		$list_table = new EAccounting_Revenues_List_Table();
		$list_table->prepare_items();
		$base_url = admin_url( 'admin.php?page=eaccounting-revenues' );
		?>

		<h1 class="wp-heading-inline"><?php _e( 'Revenues', 'wp-ever-accounting' ); ?></h1>
		<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting-action' => 'add_revenue' ), $base_url ) ); ?>"
		   class="page-title-action">
			<?php _e( 'Add New', 'wp-ever-accounting' ); ?>
		</a>
		<?php do_action( 'eaccounting_revenues_page_top' ); ?>
		<form method="get" action="<?php echo esc_url( $base_url ); ?>">
			<div class="ea-list-table">
				<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-revenues' ); ?>
				<input type="hidden" name="page" value="eaccounting-revenues"/>
				<?php $list_table->views() ?>
				<?php $list_table->display() ?>
			</div>
		</form>
		<?php
		do_action( 'eaccounting_revenues_page_bottom' );
	}
	?>
</div>
