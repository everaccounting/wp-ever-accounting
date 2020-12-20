<?php
/**
 * Admin Payments Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Expenses/Payments
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();

function eaccounting_expenses_tab_payments() {
	if ( ! current_user_can( 'ea_manage_payment' ) ) {
		wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
	}
	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;
	if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
		include_once dirname( __FILE__ ) . '/edit-payment.php';

	} else {
		$add_url    = add_query_arg(
			array(
				'page'   => 'ea-expenses',
				'tab'    => 'payments',
				'action' => 'add',
			),
			admin_url( 'admin.php' )
		);
		$import_url = add_query_arg(
			array(
				'page' => 'ea-tools',
				'tab'  => 'import',
			),
			admin_url( 'admin.php' )
		);
		?>
		<h1>
			<?php _e( 'Payments', 'wp-ever-accounting' ); ?>
			<a href="<?php echo esc_url( $add_url ); ?>" class="page-title-action"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
			<a class="page-title-action" href=" <?php echo esc_url( $import_url ); ?>"><?php _e( 'Import', 'wp-ever-accounting' ); ?></a>
		</h1>
		<?php
		require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/list-table-payments.php';
		$list_table = new \EverAccounting\Admin\ListTables\List_Table_Payments();
		$list_table->prepare_items();

		/**
		 * Fires at the top of the admin payments page.
		 *
		 * Use this hook to add content to this section of payments.
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_payments_page_top' );

		?>
		<form id="ea-payments-table" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-payments' ); ?>

			<input type="hidden" name="page" value="ea-expenses"/>
			<input type="hidden" name="tab" value="payments"/>

			<?php $list_table->views(); ?>
			<?php $list_table->display(); ?>
		</form>
		<?php
		/**
		 * Fires at the bottom of the admin payments page.
		 *
		 * Use this hook to add content to this section of payments Tab.
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_payments_page_bottom' );
	}
}

add_action( 'eaccounting_expenses_tab_payments', 'eaccounting_expenses_tab_payments' );
