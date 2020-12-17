<?php
/**
 * Admin Invoice Edit Page.
 *
 * @since       1.1.0
 * @subpackage  Admin/Sales/Invoices
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

$invoice_id = isset( $_REQUEST['invoice_id'] ) ? absint( $_REQUEST['invoice_id'] ) : null;
try {
	$invoice = new \EverAccounting\Models\Invoice( $invoice_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
$back_url = remove_query_arg( array( 'action', 'invoice_id' ) );
?>
<div class="ea-card">
	<div class="ea-card__header">
		<h3 class="ea-card__title">
			<?php echo $invoice->exists() ? __( 'Update Invoice', 'wp-ever-accounting' ) : __( 'Add Invoice', 'wp-ever-accounting' ); ?>
		</h3>

		<a href="<?php echo $back_url; ?>" class="button button-secondary"><span class="dashicons dashicons-arrow-left-alt">&nbsp;</span><?php _e( 'Back', 'wp-ever-accounting' ); ?></a>
	</div>

	<div class="ea-card__inside">
		<form id="ea-invoice-form" method="post">
			<div class="ea-row">
			<?php
			eaccounting_customer_dropdown(
				array(
					'wrapper_class' => 'ea-col-6',
					'label'         => __( 'Customer', 'wp-ever-accounting' ),
					'name'          => 'customer_id',
					'placeholder'   => __( 'Select Customer', 'wp-ever-accounting' ),
					'value'         => $invoice->get_customer_id(),
					'required'      => true,
					'disabled'      => ! $invoice->is_editable(),
					'type'          => 'customer',
					'creatable'     => true,
				)
			);

			eaccounting_currency_dropdown(
				array(
					'wrapper_class' => 'ea-col-6',
					'label'         => __( 'Currency', 'wp-ever-accounting' ),
					'name'          => 'currency_code',
					'value'         => $invoice->get_currency_code(),
					'disabled'      => ! $invoice->is_editable(),
					'required'      => true,
					'creatable'     => true,
				)
			);
			eaccounting_text_input(
				array(
					'wrapper_class' => 'ea-col-6',
					'label'         => __( 'Invoice Date', 'wp-ever-accounting' ),
					'name'          => 'issue_date',
					'value'         => $invoice->get_issue_date() ? eaccounting_format_datetime( $invoice->get_issue_date(), 'Y-m-d' ) : null,
					'required'      => true,
					'disabled'      => ! $invoice->is_editable(),
					'data_type'     => 'date',
				)
			);
			eaccounting_text_input(
				array(
					'wrapper_class' => 'ea-col-6',
					'label'         => __( 'Due Date', 'wp-ever-accounting' ),
					'name'          => 'due_date',
					'value'         => $invoice->get_due_date() ? eaccounting_format_datetime( $invoice->get_due_date(), 'Y-m-d' ) : null,
					'required'      => true,
					'disabled'      => ! $invoice->is_editable(),
					'data_type'     => 'date',
				)
			);
			eaccounting_text_input(
				array(
					'wrapper_class' => 'ea-col-6',
					'label'         => __( 'Invoice Number', 'wp-ever-accounting' ),
					'name'          => 'invoice_number',
					'value'         => $invoice->get_invoice_number(),
					'disabled'      => ! $invoice->is_editable(),
					'required'      => true,
				)
			);
			eaccounting_text_input(
				array(
					'wrapper_class' => 'ea-col-6',
					'label'         => __( 'Order Number', 'wp-ever-accounting' ),
					'name'          => 'order_number',
					'value'         => $invoice->get_order_number(),
					'disabled'      => ! $invoice->is_editable(),
					'required'      => false,
				)
			);

			eaccounting_get_admin_template( 'invoice/items', array( 'invoice' => $invoice ) );

			eaccounting_textarea(
				array(
					'wrapper_class' => 'ea-col-6',
					'label'         => __( 'Notes', 'wp-ever-accounting' ),
					'name'          => 'note',
					'value'         => $invoice->get_note(),
					'disabled'      => ! $invoice->is_editable(),
					'required'      => false,
				)
			);
			eaccounting_textarea(
				array(
					'wrapper_class' => 'ea-col-6',
					'label'         => __( 'Terms', 'wp-ever-accounting' ),
					'name'          => 'footer',
					'value'         => $invoice->get_footer(),
					'disabled'      => ! $invoice->is_editable(),
					'required'      => false,
				)
			);
			eaccounting_category_dropdown(
				array(
					'wrapper_class' => 'ea-col-6',
					'label'         => __( 'Category', 'wp-ever-accounting' ),
					'name'          => 'category_id',
					'value'         => $invoice->get_category_id(),
					'required'      => true,
					'type'          => 'income',
					'disabled'      => ! $invoice->is_editable(),
					'creatable'     => true,
					'ajax_action'   => 'eaccounting_get_income_categories',
					'modal_id'      => 'ea-modal-add-income-category',
				)
			);
			eaccounting_file_input(
				array(
					'label'         => __( 'Attachments', 'wp-ever-accounting' ),
					'name'          => 'attachment_id',
					'value'         => $invoice->get_attachment(),
					'wrapper_class' => 'ea-col-6',
					'placeholder'   => __( 'Upload File', 'wp-ever-accounting' ),
				)
			);
			eaccounting_hidden_input(
				array(
					'name'  => 'id',
					'value' => $invoice->get_id(),
				)
			);

			eaccounting_hidden_input(
				array(
					'name'  => 'action',
					'value' => 'eaccounting_edit_invoice',
				)
			);
			?>
			</div>
			<?php
			wp_nonce_field( 'ea_edit_invoice' );
			submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
			?>
		</form>
	</div>
</div>
