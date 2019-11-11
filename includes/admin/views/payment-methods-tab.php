<?php
defined('ABSPATH') || exit();


/**
 * Renders the Accounts Pages Admin Page
 * since 1.0.0
 */
function eaccounting_payment_methods_tab() {
	if ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'add_payment_method' ) {
		require_once EACCOUNTING_ADMIN_ABSPATH . '/views/edit-payment-method.php';
	} elseif ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'edit_payment_method' ) {
		require_once EACCOUNTING_ADMIN_ABSPATH . '/views/edit-payment-method.php';
	} else {
		require_once EACCOUNTING_ADMIN_ABSPATH . '/tables/class-payment-methods-list-table.php';
		$list_table = new EAccounting_Payment_Methods_List_Table();
		$list_table->prepare_items();
		$base_url = admin_url( 'admin.php?page=eaccounting-misc&tab=payment_methods' );
		?>

		<h1 class="wp-heading-inline"><?php _e( 'Payment Methods', 'wp-eaccounting' ); ?></h1>
		<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting-action' => 'add_payment_method' ), $base_url ) ); ?>" class="page-title-action">
			<?php _e( 'Add New', 'wp-eaccounting' ); ?>
		</a>

		<?php do_action( 'eaccounting_payment_methods_page_top' ); ?>
		<form method="get" action="<?php echo esc_url( $base_url ); ?>">
			<div class="ea-list-table">
				<?php $list_table->search_box( __( 'Search', 'wp-eaccounting' ), 'eaccounting-payment-methods' ); ?>
				<input type="hidden" name="page" value="eaccounting-misc"/>
				<input type="hidden" name="tab" value="payment_methods"/>
				<?php $list_table->views() ?>
				<?php $list_table->display() ?>
			</div>
		</form>
		<?php
		do_action( 'eaccounting_payment_methods_page_bottom' );
	}
}

add_action('eaccounting_misc_tab_payment_methods', 'eaccounting_payment_methods_tab');
