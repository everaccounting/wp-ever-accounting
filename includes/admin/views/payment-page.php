<?php
defined( 'ABSPATH' ) || exit();
?>
<div class="wrap ea-wrapper">
	<?php
	if ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'add_payment' ) {
		eaccounting_get_views( 'edit-payment.php' );
	} elseif ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'edit_payment' ) {
		eaccounting_get_views( 'edit-payment.php' );
	} else {
		require_once EACCOUNTING_ADMIN_ABSPATH . '/tables/class-ea-payments-list-table.php';
		$list_table = new EAccounting_Payments_List_Table();
		$list_table->prepare_items();
		$base_url = admin_url( 'admin.php?page=eaccounting-payments' );
		?>

		<h1 class="wp-heading-inline"><?php _e( 'Payments', 'wp-eaccounting' ); ?></h1>
		<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting-action' => 'add_payment' ), $base_url ) ); ?>"
		   class="page-title-action">
			<?php _e( 'Add New', 'wp-eaccounting' ); ?>
		</a>
		<?php do_action( 'eaccounting_payments_page_top' ); ?>
		<form method="get" action="<?php echo esc_url( $base_url ); ?>">
			<div class="ea-list-table">
				<?php $list_table->search_box( __( 'Search', 'wp-eaccounting' ), 'eaccounting-payments' ); ?>
				<input type="hidden" name="page" value="eaccounting-payments"/>
				<?php $list_table->views() ?>
				<?php $list_table->display() ?>
			</div>
		</form>
		<?php
		do_action( 'eaccounting_payments_page_bottom' );
	}
	?>
</div>
