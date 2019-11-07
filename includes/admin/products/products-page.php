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
		require_once dirname( __FILE__ ) . '/add-product.php';
	} elseif ( isset( $_GET['eaccounting_action'] ) && $_GET['eaccounting_action'] == 'add_product' ) {
		require_once dirname( __FILE__ ) . '/add-product.php';
	} else {
		require_once dirname( __FILE__ ) . '/class-products-table.php';
		$products_table = new EAccounting_Products_Table();
		$products_table->prepare_items();
		$base_url = admin_url('admin.php?page=eaccounting-products');
		?>

		<h1 class="wp-heading-inline"><?php _e( 'Products', 'wp-ever-accounting' ); ?></h1>
		<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting_action' => 'add_product' ), $base_url ) ); ?>" class="page-title-action"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
		<hr class="wp-header-end">


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
