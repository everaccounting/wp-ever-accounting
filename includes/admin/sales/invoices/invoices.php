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
	} else { ?>
		<h1>
			<?php _e( 'Invoices', 'wp-ever-accounting' ); ?>
			<?php
			echo sprintf(
				'<a class="page-title-action" href="%s">%s</a>',
				$add_url,
				__( 'Add New', 'wp-ever-accounting' )
			);
			?>
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

		<form id="ea-accounts-filter" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-revenues' ); ?>

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
