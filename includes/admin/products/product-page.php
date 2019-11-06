<?php
defined('ABSPATH') || exit();


/**
 * Renders the Accounts Pages Admin Page
 * since 1.0.0
 */
function eaccounting_products_page() {
	wp_enqueue_script('eaccounting-products');

	eaccounting_page_wrapper_open('products-page');
	if ( isset( $_GET['eaccounting_action'] ) && $_GET['eaccounting_action'] == 'edit_product' ) {
		require_once EVER_ACCOUNTING_ABSPATH . '/includes/admin/products/edit-product.php';
	} elseif ( isset( $_GET['eaccounting_action'] ) && $_GET['eaccounting_action'] == 'add_product' ) {
		require_once EVER_ACCOUNTING_ABSPATH . '/includes/admin/products/add-product.php';
	} else {
		require_once EVER_ACCOUNTING_ABSPATH . '/includes/admin/products/class-products-table.php';
		$products_table = new EAccounting_Products_Table();
		$products_table->prepare_items();
		?>

		<h1><?php _e( 'Products', 'wp-ever-accounting' ); ?><a href="<?php echo esc_url( add_query_arg( array( 'eaccounting_action' => 'add_product' ), admin_url('admin.php?page=eaccounting-accounts') ) ); ?>" class="add-new-h2"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a></h1>
		<?php do_action( 'eaccounting_products_page_top' ); ?>
		<form id="eaccounting-products-filter" method="get" action="<?php echo admin_url( 'admin.php?page=eaccounting-accounts' ); ?>">
			<?php $products_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-accounts' ); ?>

			<input type="hidden" name="page" value="eaccounting-products" />

			<?php $products_table->views() ?>
			<?php $products_table->display() ?>
		</form>
		<?php
		do_action( 'eaccounting_product_page_bottom' );
	}
	eaccounting_page_wrapper_close();
}
