<?php
defined( 'ABSPATH' ) || exit();


function eaccounting_tab_categories() {
	if ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'edit_category' ) {
		require_once dirname( __FILE__ ) . '/edit-category.php';
	} elseif ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'edit_category' ) {
		require_once dirname( __FILE__ ) . '/edit-category.php';
	} else {
		require_once dirname( __FILE__ ) . '/class-categories-list-table.php';
		$accounts_table = new EAccounting_Categories_List_Table();
		$accounts_table->prepare_items();
		$base_url = admin_url( 'admin.php?page=eaccounting-misc&tab=categories' );
		?>

		<h1 class="wp-heading-inline"><?php _e( 'Categories', 'wp-ever-accounting' ); ?></h1>
		<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting-action' => 'add_category' ), $base_url ) ); ?>"
		   class="page-title-action"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>

		<?php do_action( 'eaccounting_categories_page_top' ); ?>
		<form method="get" action="<?php echo esc_url( $base_url ); ?>">
			<?php $accounts_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-accounts' ); ?>

			<input type="hidden" name="page" value="eaccounting-misc"/>
			<input type="hidden" name="tab" value="categories"/>

			<?php $accounts_table->views() ?>
			<?php $accounts_table->display() ?>
		</form>
		<?php
		do_action( 'eaccounting_categories_page_bottom' );
	}
}

add_action( 'eaccounting_misc_tab_categories', 'eaccounting_tab_categories' );
