<?php
/**
 * Admin Invoices Page.
 *
 * @since       1.1.0
 * @subpackage  Admin/Sales/Invoices
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();

function eaccounting_sales_tab_invoices() {
	if ( ! current_user_can( 'ea_manage_invoice' ) ) {
		wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
	}

	$action  = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;
	$add_url = esc_url(
		add_query_arg(
			array(
				'tab'    => 'invoices',
				'action' => 'add',
				'page'   => 'ea-sales',
			),
			admin_url( 'admin.php' )
		)
	);

	if ( in_array( $action, array( 'add', 'edit' ), true ) ) {
		include_once dirname( __FILE__ ) . '/edit-invoice.php';
	} elseif ( 'view' === $action && ! empty( $_GET['invoice_id'] ) ) {
		include_once dirname( __FILE__ ) . '/view-invoice.php';
	} else {
		$add_new_url = add_query_arg(
			array(
				'page'   => 'ea-sales',
				'tab'    => 'invoices',
				'action' => 'add',
			),
			admin_url( 'admin.php' )
		);
		?>
		<h1>
			<?php _e( 'Invoices', 'wp-ever-accounting' ); ?>
			<a href="<?php echo esc_url( $add_url ); ?>" class="page-title-action"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
		</h1>
		<?php
		require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/list-table-invoices.php';
		$list_table = new EverAccounting\Admin\ListTables\List_Table_Invoices();
		$list_table->prepare_items();
		/**
		 * Fires at the top of the admin invoices page.
		 *
		 * Use this hook to add content to this section of revenues.
		 *
		 * @since 1.1.0
		 */
		do_action( 'eaccounting_invoices_page_top' );
		?>

		<form id="ea-invoices-table" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-invoices' ); ?>

			<input type="hidden" name="page" value="ea-sales"/>
			<input type="hidden" name="tab" value="invoices"/>

			<?php $list_table->views(); ?>
			<?php $list_table->display(); ?>
		</form>
		<?php
		/**
		 * Fires at the bottom of the admin invoices page.
		 *
		 * Use this hook to add content to this section of revenues Tab.
		 *
		 * @since 1.1.0
		 */
		do_action( 'eaccounting_invoices_page_bottom' );
	}
}

add_action( 'eaccounting_sales_tab_invoices', 'eaccounting_sales_tab_invoices' );

/**
 * Handle invoice actions.
 *
 * @since 1.1.0
 */
function eaccounting_handle_invoice_action() {
	$action     = eaccounting_clean( wp_unslash( $_POST['invoice_action'] ) );
	$invoice_id = absint( wp_unslash( $_POST['invoice_id'] ) );
	$invoice    = eaccounting_get_invoice( $invoice_id );

	if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'ea_invoice_action' ) || ! current_user_can( 'ea_manage_invoice' ) || ! $invoice->exists() ) {
		wp_die( 'no cheatin!' );
	}

	if ( ! did_action( 'eaccounting_invoice_action_' . sanitize_title( $action ) ) ) {
		do_action( 'eaccounting_invoice_action_' . sanitize_title( $action ), $invoice );
	}

	wp_redirect( add_query_arg( array( 'action' => 'view' ), eaccounting_clean( $_REQUEST['_wp_http_referer'] ) ) );
}
add_action( 'admin_post_eaccounting_invoice_action', 'eaccounting_handle_invoice_action' );
