<?php
defined('ABSPATH') || exit();


/**
 * Renders the Accounts Pages Admin Page
 * since 1.0.0
 */
function eaccounting_products_page() {
	eaccounting_page_wrapper_open();
	if ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'add_product' ) {
		require_once dirname( __FILE__ ) . '/edit-product.php';
	} elseif ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'edit_product' ) {
		require_once dirname( __FILE__ ) . '/edit-product.php';
	} else {
		require_once dirname( __FILE__ ) . '/class-product-list-table.php';
		$list_table = new EAccounting_Products_List_Table();
		$list_table->prepare_items();
		$base_url = admin_url( 'admin.php?page=eaccounting-products' );
		?>

		<h1 class="wp-heading-inline"><?php _e( 'Products', 'wp-eaccounting' ); ?></h1>
		<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting-action' => 'add_product' ), $base_url ) ); ?>" class="page-title-action">
			<?php _e( 'Add New', 'wp-eaccounting' ); ?>
		</a>

		<?php do_action( 'eaccounting_products_page_top' ); ?>
		<form method="get" action="<?php echo esc_url( $base_url ); ?>">
			<div class="ea-list-table">
				<?php $list_table->search_box( __( 'Search', 'wp-eaccounting' ), 'eaccounting-products' ); ?>
				<input type="hidden" name="page" value="eaccounting-products"/>
				<?php $list_table->views() ?>
				<?php $list_table->display() ?>
			</div>
		</form>
		<?php
		do_action( 'eaccounting_products_page_bottom' );
	}
	eaccounting_page_wrapper_close();
}
