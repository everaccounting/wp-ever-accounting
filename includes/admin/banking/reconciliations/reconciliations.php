<?php
/**
 * Admin Reconciliations Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Banking/Reconciliations
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();


function eaccounting_banking_tab_reconciliations() {
	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;

	if ( in_array( $action, [ 'edit', 'add' ] ) ) {
		include __DIR__ . '/edit-reconciliation.php';
	} else {
		?>
		<h1>
			<?php _e( 'Reconciliations', 'wp-ever-accounting' ); ?>
			<a class="page-title-action" href="<?php echo eaccounting_admin_url( array( 'tab' => 'reconciliations', 'action' => 'add' ) ); ?>"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
		</h1>
		<?php
		require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/list-table-reconciliations.php';
		$reconciliations_table = new \EverAccounting\Admin\ListTables\List_Table_Transfers();
		$reconciliations_table->prepare_items();
		?>
		<div class="wrap">
			<?php

			/**
			 * Fires at the top of the admin reconciliations page.
			 *
			 * Use this hook to add content to this section of reconciliations.
			 *
			 * @since 1.0.2
			 */
			do_action( 'eaccounting_reconciliations_page_top' );

			?>
			<form id="ea-reconciliations-filter" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
				<?php //$reconciliations_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-reconciliations' ); ?>

				<input type="hidden" name="page" value="ea-banking"/>
				<input type="hidden" name="tab" value="reconciliations"/>

				<?php $reconciliations_table->views() ?>
				<?php $reconciliations_table->display() ?>
			</form>
			<?php
			/**
			 * Fires at the bottom of the admin reconciliations page.
			 *
			 * Use this hook to add content to this section of reconciliations Tab.
			 *
			 * @since 1.0.2
			 */
			do_action( 'eaccounting_reconciliations_page_bottom' );
			?>
		</div>
		<?php
	}
}

add_action( 'eaccounting_banking_tab_reconciliations', 'eaccounting_banking_tab_reconciliations' );
