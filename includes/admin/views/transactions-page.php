<?php
defined( 'ABSPATH' ) || exit();
?>
<div class="wrap ea-wrapper">
	<?php
	require_once EACCOUNTING_ADMIN_ABSPATH . '/tables/class-ea-transactions-list-table.php';
	$list_table = new EAccounting_Transactions_List_Table();
	$list_table->prepare_items();
	$base_url = admin_url( 'admin.php?page=eaccounting-transactions' );
	?>

	<h1 class="wp-heading-inline"><?php _e( 'Transactions', 'wp-ever-accounting' ); ?></h1>
	<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting-action' => 'add_revenue' ), admin_url( 'admin.php?page=eaccounting-revenues' ) ) ); ?>"
	   class="page-title-action">
		<?php _e( 'Add Revenue', 'wp-ever-accounting' ); ?>
	</a>

	<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting-action' => 'add_payment' ), admin_url( 'admin.php?page=eaccounting-payments' ) ) ); ?>" class="page-title-action">
		<?php _e( 'Add Payment', 'wp-ever-accounting' ); ?>
	</a>

	<?php do_action( 'eaccounting_transactions_page_top' ); ?>
	<form method="get" action="<?php echo esc_url( $base_url ); ?>">
		<div class="ea-list-table">
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-contacts' ); ?>
			<input type="hidden" name="page" value="eaccounting-transactions"/>
			<?php $list_table->views() ?>
			<?php $list_table->display() ?>
		</div>
	</form>
	<?php
	do_action( 'eaccounting_transactions_page_bottom' );
	?>
</div>
