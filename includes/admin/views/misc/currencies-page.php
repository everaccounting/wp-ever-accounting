<?php
defined( 'ABSPATH' ) || exit();

if ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'add_tax' ) {
	eaccounting_get_views( 'misc/edit-tax.php' );
} elseif ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'edit_tax' ) {
	eaccounting_get_views( 'misc/edit-tax.php' );
} else {
	require_once EACCOUNTING_ADMIN_ABSPATH . '/tables/class-ea-currencies-list-table.php';
	$list_table = new EAccounting_Currencies_List_Table();

	$action = $list_table->current_action();

	$redirect_to = admin_url( 'admin.php?page=eaccounting-misc&tab=currencies' );

	if ( $action && check_admin_referer( 'bulk-taxes' ) ) {

		$ids = isset( $_GET['tax'] ) ? $_GET['tax'] : false;

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}
		$ids = array_map( 'intval', $ids );
		foreach ( $ids as $id ) {
			switch ( $action ) {
				case 'activate':
					eaccounting_insert_tax( [ 'id' => $id, 'status' => 'active' ] );
					break;
				case 'deactivate':
					eaccounting_insert_tax( [ 'id' => $id, 'status' => 'inactive' ] );
					break;
				case 'delete':
					eaccounting_delete_tax( $id );
					break;
			}
		}

		wp_redirect( $redirect_to );
		exit();
	}

	$list_table->prepare_items();

	?>

	<h1 class="wp-heading-inline"><?php _e( 'Currencies', 'wp-ever-accounting' ); ?></h1>
	<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting-action' => 'add_currency' ), $redirect_to ) ); ?>"
	   class="page-title-action">
		<?php _e( 'Add New', 'wp-ever-accounting' ); ?>
	</a>
	<?php do_action( 'eaccounting_taxes_page_top' ); ?>
	<form method="get" action="<?php echo esc_url( $redirect_to ); ?>">
		<div class="ea-list-table">
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-taxes' ); ?>
			<input type="hidden" name="page" value="eaccounting-misc"/>
			<input type="hidden" name="tab" value="currencies"/>
			<?php $list_table->views() ?>
			<?php $list_table->display() ?>
		</div>
	</form>
	<?php
	do_action( 'eaccounting_taxes_page_bottom' );
}
