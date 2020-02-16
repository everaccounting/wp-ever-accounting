<?php
defined( 'ABSPATH' ) || exit();
?>
<div class="wrap ea-wrapper">
	<?php
	if ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'add_transfer' ) {
		eaccounting_get_views( 'banking/edit-transfer.php' );
	} elseif ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'edit_transfer' ) {
		eaccounting_get_views( 'banking/edit-transfer.php' );
	} else {
		require_once EACCOUNTING_ADMIN_ABSPATH . '/tables/class-ea-transfers-list-table.php';
		$list_table = new EAccounting_Transfers_List_Table();
		$list_table->prepare_items();
		$base_url = admin_url( 'admin.php?page=eaccounting-banking&tab=transfers' );
		?>
		<h1 class="wp-heading-inline"><?php _e( 'Transfers', 'wp-ever-accounting' ); ?></h1>
		<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting-action' => 'add_transfer' ), admin_url( 'admin.php?page=eaccounting-banking&tab=transfers' ) ) ); ?>"
		   class="page-title-action">
			<?php _e( 'Add Transfer', 'wp-ever-accounting' ); ?>
		</a>
		<div class="ea-mb-20"></div>
		<?php do_action( 'eaccounting_transfers_page_top' ); ?>
		<form method="get" action="<?php echo esc_url( $base_url ); ?>">
			<div class="ea-list-table">
				<input type="hidden" name="page" value="eaccounting-banking"/>
				<?php $list_table->views() ?>
				<?php $list_table->display() ?>
			</div>
		</form>
		<?php do_action( 'eaccounting_transfers_page_bottom' ); ?>
	<?php } ?>
</div>
