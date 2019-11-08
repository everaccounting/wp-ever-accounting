<?php
defined('ABSPATH') || exit();


/**
 * Renders the Accounts Pages Admin Page
 * since 1.0.0
 */
function eaccount_taxes_page() {
	wp_enqueue_script('eaccounting-taxes');
	eaccounting_page_wrapper_open('taxes-page');
	if ( isset( $_GET['eaccounting_action'] ) && $_GET['eaccounting_action'] == 'edit_tax' ) {
		require_once EACCOUNTING_ABSPATH . '/includes/admin/taxes/edit-taxes.php';
	} elseif ( isset( $_GET['eaccounting_action'] ) && $_GET['eaccounting_action'] == 'add_tax' ) {
		require_once EACCOUNTING_ABSPATH . '/includes/admin/taxes/add-tax.php';
	} else {
		require_once EACCOUNTING_ABSPATH . '/includes/admin/taxes/class-taxes-table.php';
		$taxes_table = new EAccounting_Taxes_Table();
		$taxes_table->prepare_items();
		?>

		<h1><?php _e( 'Taxes', 'wp-ever-accounting' ); ?><a href="<?php echo esc_url( add_query_arg( array( 'eaccounting_action' => 'add_tax' ), admin_url('admin.php?page=eaccounting-taxes') ) ); ?>" class="add-new-h2"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a></h1>
		<?php do_action( 'eaccounting_taxes_page_top' ); ?>
		<form id="eaccounting-taxes-filter" method="get" action="<?php echo admin_url( 'admin.php?page=eaccounting-taxes' ); ?>">
			<?php $taxes_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-taxes' ); ?>

			<input type="hidden" name="page" value="eaccounting-taxes" />

			<?php $taxes_table->views() ?>
			<?php $taxes_table->display() ?>
		</form>
		<?php
		do_action( 'eaccounting_taxes_page_bottom' );
	}
	eaccounting_page_wrapper_close();
}
