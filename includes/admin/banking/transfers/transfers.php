<?php
/**
 * Admin Transfers Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Banking/Transfers
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();


function eaccounting_banking_tab_transfers() {
	if ( ! current_user_can( 'ea_manage_transfer' ) ) {
		wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
	}
	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;
	if ( in_array( $action, array( 'edit', 'add' ), true ) ) {
		include __DIR__ . '/edit-transfer.php';
	} else {
		$add_url = add_query_arg(
			array(
				'page'   => 'ea-banking',
				'tab'    => 'transfers',
				'action' => 'add',
			),
			admin_url( 'admin.php' )
		);
		?>
		<h1>
			<?php _e( 'Transfers', 'wp-ever-accounting' ); ?>
			<a href="<?php echo esc_url( $add_url ); ?>" class="page-title-action"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
		</h1>
		<?php
		require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-transfer-list-table.php';
		$transfers_table = new EAccounting_Transfer_List_Table();
		$transfers_table->prepare_items();
		?>
			<?php

			/**
			 * Fires at the top of the admin transfers page.
			 *
			 * Use this hook to add content to this section of transfers.
			 *
			 * @since 1.0.2
			 */
			do_action( 'eaccounting_transfers_page_top' );

			?>
			<form id="ea-transfers-table" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
				<?php // $transfers_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-transfers' ); ?>

				<input type="hidden" name="page" value="ea-banking"/>
				<input type="hidden" name="tab" value="transfers"/>

				<?php $transfers_table->views(); ?>
				<?php $transfers_table->display(); ?>
			</form>
			<?php
			/**
			 * Fires at the bottom of the admin transfers page.
			 *
			 * Use this hook to add content to this section of transfers Tab.
			 *
			 * @since 1.0.2
			 */
			do_action( 'eaccounting_transfers_page_bottom' );
			?>
		<?php
	}
}

add_action( 'eaccounting_banking_tab_transfers', 'eaccounting_banking_tab_transfers' );
