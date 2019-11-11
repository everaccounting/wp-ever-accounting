<?php
defined( 'ABSPATH' ) || exit();


function eaccounting_tab_tax_rates() {

	if ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'add_tax_rate' ) {
		require_once EACCOUNTING_ADMIN_ABSPATH . '/views/edit-tax-rate.php';
	} elseif ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'edit_tax_rate' ) {
		require_once EACCOUNTING_ADMIN_ABSPATH . '/views/edit-tax-rate.php';
	} else {
		require_once EACCOUNTING_ADMIN_ABSPATH . '/tables/class-tax-rates-list-table.php';
		$list_table = new EAccounting_Rax_Rates_List_Table();
		$list_table->prepare_items();
		$base_url = admin_url( 'admin.php?page=eaccounting-misc&tab=tax_rates' );
		?>

		<h1 class="wp-heading-inline"><?php _e( 'Tax Rates', 'wp-ever-accounting' ); ?></h1>
		<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting-action' => 'add_tax_rate' ), $base_url ) ); ?>"
		   class="page-title-action"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>

		<?php do_action( 'eaccounting_tax_rates_page_top' ); ?>
		<form method="get" action="<?php echo esc_url( $base_url ); ?>">
			<div class="ea-list-table">
				<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-accounts' ); ?>
				<input type="hidden" name="page" value="eaccounting-misc"/>
				<input type="hidden" name="tab" value="tax_rates"/>
				<?php $list_table->views() ?>
				<?php $list_table->display() ?>
			</div>
		</form>
		<?php
		do_action( 'eaccounting_tax_rates_page_bottom' );
	}
}

add_action( 'eaccounting_misc_tab_tax_rates', 'eaccounting_tab_tax_rates' );
