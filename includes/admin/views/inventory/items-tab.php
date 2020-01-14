<?php
if ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'add_item' ) {
	eaccounting_get_views( 'inventory/edit-product.php' );
} elseif ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'add_item' ) {
	eaccounting_get_views( 'inventory/edit-product.php' );
} else {
	require_once EACCOUNTING_ADMIN_ABSPATH . '/tables/class-ea-products-list-table.php';
	$list_table = new EAccounting_Products_List_Table();

	$action = $list_table->current_action();

	$redirect_to = admin_url( 'admin.php?page=eaccounting-inventory&tab=items' );

	if ( $action && check_admin_referer( 'bulk-products' ) ) {

		$ids = isset( $_GET['item'] ) ? $_GET['item'] : false;

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}
		$ids = array_map( 'intval', $ids );
		foreach ( $ids as $id ) {
			switch ( $action ) {
				case 'activate':
					eaccounting_insert_product( [ 'id' => $id, 'status' => 'active' ] );
					break;
				case 'deactivate':
					eaccounting_insert_product( [ 'id' => $id, 'status' => 'inactive' ] );
					break;
				case 'delete':
					eaccounting_delete_product( $id );
					break;
			}
		}

		wp_redirect( $redirect_to );
		exit();
	}

	$list_table->prepare_items();

	?>

	<h1 class="wp-heading-inline"><?php _e( 'Items', 'wp-ever-accounting' ); ?></h1>
	<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting-action' => 'add_item' ), $redirect_to ) ); ?>"
	   class="page-title-action">
		<?php _e( 'Add New', 'wp-ever-accounting' ); ?>
	</a>
	<?php do_action( 'eaccounting_items_page_top' ); ?>
	<form method="get" action="<?php echo esc_url( $redirect_to ); ?>">
		<div class="ea-list-table">
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-inventory' ); ?>
			<input type="hidden" name="page" value="eaccounting-inventory"/>
			<input type="hidden" name="tab" value="items"/>
			<?php $list_table->views() ?>
			<?php $list_table->display() ?>
		</div>
	</form>
	<?php
	do_action( 'eaccounting_items_page_bottom' );
}
