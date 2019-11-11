<?php
defined('ABSPATH') || exit();


/**
 * Renders the Accounts Pages Admin Page
 * since 1.0.0
 */
function eaccounting_categories_tab() {
	if ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'add_category' ) {
		require_once EACCOUNTING_ADMIN_ABSPATH . '/views/edit-category.php';
	} elseif ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'edit_category' ) {
		require_once EACCOUNTING_ADMIN_ABSPATH . '/views/edit-category.php';
	} else {
		require_once EACCOUNTING_ADMIN_ABSPATH . '/tables/class-categories-list-table.php';
		$list_table = new EAccounting_Categories_List_Table();
		$list_table->prepare_items();
		$base_url = admin_url( 'admin.php?page=eaccounting-misc&tab=categories' );
		?>

		<h1 class="wp-heading-inline"><?php _e( 'Categories', 'wp-eaccounting' ); ?></h1>
		<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting-action' => 'add_category' ), $base_url ) ); ?>" class="page-title-action">
			<?php _e( 'Add New', 'wp-eaccounting' ); ?>
		</a>

		<?php do_action( 'eaccounting_categories_page_top' ); ?>
		<form method="get" action="<?php echo esc_url( $base_url ); ?>">
			<div class="ea-list-table">
				<?php $list_table->search_box( __( 'Search', 'wp-eaccounting' ), 'eaccounting-categories' ); ?>
				<input type="hidden" name="page" value="eaccounting-categories"/>
				<input type="hidden" name="tab" value="categories"/>
				<?php $list_table->views() ?>
				<?php $list_table->display() ?>
			</div>
		</form>
		<?php
		do_action( 'eaccounting_categories_page_bottom' );
	}
}

add_action('eaccounting_misc_tab_categories', 'eaccounting_categories_tab');
