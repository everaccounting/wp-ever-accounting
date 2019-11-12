<?php
defined('ABSPATH') || exit();


/**
 * Renders the Income Pages Admin Page
 * since 1.0.0
 */
function eaccounting_revenues_tab() {
	if ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'add_revenue' ) {
		require_once EACCOUNTING_ADMIN_ABSPATH . '/views/edit-revenue.php';
	} elseif ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'edit_revenue' ) {
		require_once EACCOUNTING_ADMIN_ABSPATH . '/views/edit-revenue.php';
	} else {
		require_once EACCOUNTING_ADMIN_ABSPATH . '/tables/class-revenues-list-table.php';
		$list_table = new EAccounting_Revenues_List_Table();
		$list_table->prepare_items();
		$base_url = admin_url( 'admin.php?page=eaccounting-income&tab=revenues' );
		?>

		<h1 class="wp-heading-inline"><?php _e( 'Payment Methods', 'wp-eaccounting' ); ?></h1>
		<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting-action' => 'add_revenue' ), $base_url ) ); ?>" class="page-title-action">
			<?php _e( 'Add New', 'wp-eaccounting' ); ?>
		</a>

		<?php do_action( 'eaccounting_revenues_page_top' ); ?>
		<form method="get" action="<?php echo esc_url( $base_url ); ?>">
			<div class="ea-list-table">
				<?php $list_table->search_box( __( 'Search', 'wp-eaccounting' ), 'eaccounting-revenues' ); ?>
				<input type="hidden" name="page" value="eaccounting-income"/>
				<input type="hidden" name="tab" value="revenues"/>
				<?php $list_table->views() ?>
				<?php $list_table->display() ?>
			</div>
		</form>
		<?php
		do_action( 'eaccounting_revenues_page_bottom' );
	}
}

add_action('eaccounting_income_tab_revenues', 'eaccounting_revenues_tab');
