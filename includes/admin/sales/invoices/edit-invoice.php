<?php
/**
 * Admin Invoice Edit Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Sales/Invoices
 * @since       1.1.0
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

<div class="ea-form-card">
	<div class="ea-card ea-form-card__header is-compact">
		<h3 class="ea-form-card__header-title"><?php echo $invoice->exists() ? __( 'Update Invoice', 'wp-ever-accounting' ) : __( 'Add Invoice', 'wp-ever-accounting' ); ?></h3>
		<a href="<?php echo $back_url; ?>" class="button button-secondary"><span class="dashicons dashicons-arrow-left-alt"></span><?php _e( 'Back', 'wp-ever-accounting' ); ?></a>
	</div>

	<div class="ea-card">
		<form id="ea-category-form" class="ea-ajax-form" method="post">
			<div class="ea-row">
				<?php
				eaccounting_contact_dropdown(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Customer', 'wp-ever-accounting' ),
						'name'          => 'contact_id',
						'id'            => 'customer_id',
						'placeholder'   => __( 'Select Customer', 'wp-ever-accounting' ),
						'value'         => $invoice->get_customer_id(),
						'required'      => true,
						'type'          => 'customer',
						'creatable'     => true,
					)
				);
				eaccounting_currency_dropdown(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Account Currency', 'wp-ever-accounting' ),
						'name'          => 'currency_code',
						'value'         => $invoice->get_currency_code(),
						'required'      => true,
						'creatable'     => true,
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Invoice Date', 'wp-ever-accounting' ),
						'name'          => 'invoiced_at',
						'value'         => $invoice->get_issued_at() ? eaccounting_format_datetime( $invoice->get_issued_at(), 'Y-m-d' ) : null,
						'required'      => true,
						'data_type'     => 'date',
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Due Date', 'wp-ever-accounting' ),
						'name'          => 'due_at',
						'value'         => $invoice->get_due_at() ? eaccounting_format_datetime( $invoice->get_due_at(), 'Y-m-d' ) : null,
						'required'      => true,
						'data_type'     => 'date',
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Invoice Number', 'wp-ever-accounting' ),
						'name'          => 'invoice_number',
						'value'         => $invoice->get_invoice_number(),
						'required'      => true,
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Order Number', 'wp-ever-accounting' ),
						'name'          => 'order_number',
						'value'         => $invoice->get_order_number(),
						'required'      => false,
					)
				);
				?>
				<div class="ea-form-field ea-col-12 ea-invoice-table-wrap">
					<label class="ea-label" for="items"><?php _e( 'Items', 'wp-ever-accounting' ); ?></label>
					<table cellpadding="0" cellspacing="0" class="ea-invoice-items-table">
						<thead class="ea-invoice-items-table-head">
						<tr>
							<th class="ea-invoice-item"><?php _e( 'Item Name', 'wp-ever-accounting' ); ?></th>
							<th colspan="ea-invoice-item-price"><?php _e( 'Price', 'wp-ever-accounting' ); ?></th>
							<th class="ea-invoice-item-quantity"><?php _e( 'Quantity', 'wp-ever-accounting' ); ?></th>
							<th colspan="ea-invoice-item-tax"><?php _e( 'Tax', 'wp-ever-accounting' ); ?></th>
							<th colspan="ea-invoice-item-total"><?php _e( 'Total', 'wp-ever-accounting' ); ?></th>
							<th colspan="ea-invoice-item-actions">&nbsp;</th>
						</tr>
						</thead>
						<tbody class="ea-invoice-items-table-body">
						<tr class="ea-invoice-items-line-items">
							<td class="ea-invoice-item"><?php _e( 'Item 1', 'wp-ever-accounting' ); ?></td>
							<td class="ea-invoice-item-price"><?php _e( '$1000.00', 'wp-ever-accounting' ); ?></td>
							<td class="ea-invoice-item-quantity"><?php _e( '1', 'wp-ever-accounting' ); ?></td>
							<td class="ea-invoice-item-tax"><?php _e( '2.32', 'wp-ever-accounting' ); ?></td>
							<td class="ea-invoice-item-total"><?php _e( '$1000.00', 'wp-ever-accounting' ); ?></td>
							<td class="ea-invoice-item-actions">
								<div class="ea-invoice-item-edit-actions">
									<a href="#" class="edit-items"><span class="dashicons dashicons-edit"></span></a>
									<a href="#" class="delete-items"><span class="dashicons dashicons-no"></span></a>
								</div>
							</td>
						</tr>
						<tr class="ea-invoice-items-line-items">
							<td class="ea-invoice-item"><?php _e( 'Item 1', 'wp-ever-accounting' ); ?></td>
							<td class="ea-invoice-item-price"><?php _e( '$1000.00', 'wp-ever-accounting' ); ?></td>
							<td class="ea-invoice-item-quantity"><?php _e( '1', 'wp-ever-accounting' ); ?></td>
							<td class="ea-invoice-item-tax"><?php _e( '2.32', 'wp-ever-accounting' ); ?></td>
							<td class="ea-invoice-item-total"><?php _e( '$1000.00', 'wp-ever-accounting' ); ?></td>
							<td class="ea-invoice-item-actions">
								<div class="ea-invoice-item-edit-actions">
									<a href="#" class="edit-items"><span class="dashicons dashicons-edit"></span></a>
									<a href="#" class="delete-items"><span class="dashicons dashicons-no"></span></a>
								</div>
							</td>
						</tr>
						<tr class="ea-invoice-items-total">
							<td colspan="2"></td>
							<td colspan="3">
								<div class="subtotal">
									<span class="label"><?php _e( 'Subtotal:', 'wp-ever-accounting' ); ?></span>
									<span class="value align-right"><?php _e( '$1000.00', 'wp-ever-accounting' ); ?></span>
								</div>
								<div class="discount">
									<span class="label"><?php _e( 'Discount:', 'wp-ever-accounting' ); ?></span>
									<span class="value align-right"><?php _e( '$0.00', 'wp-ever-accounting' ); ?></span>
								</div>
								<div class="shipping">
									<span class="label"><?php _e( 'Shipping:', 'wp-ever-accounting' ); ?></span>
									<span class="value align-right"><?php _e( '$0.00', 'wp-ever-accounting' ); ?></span>
								</div>
								<div class="total">
									<span class="label"><?php _e( 'Total:', 'wp-ever-accounting' ); ?></span>
									<span class="value align-right"><?php _e( '$1000.00', 'wp-ever-accounting' ); ?></span>
								</div>
							</td>
							<td colspan="2"></td>
						</tr>
						</tbody>
						<tfoot class="ea-invoice-items-table-footer">
							<tr class="ea-invoice-items-actions">
								<td colspan="3">
									<button type="button" class="button ea-add-invoice-item btn-secondary"><?php _e( 'Add Item', 'wp-ever-accounting' ); ?></button>
									<button type="button" class="button ea-add-discount btn-secondary"><?php _e( 'Add Discounts', 'wp-ever-accounting' ); ?></button>
									<button type="button" class="button ea-add-shipping btn-secondary"><?php _e( 'Add Shipping', 'wp-ever-accounting' ); ?></button>
								</td>
								<td colspan="3" class="align-right">
									<button type="button" class="button ea-invoice-recalculate button-primary"><?php _e( 'Recalculate Totals', 'wp-ever-accounting' ); ?></button>
								</td>
							</tr>

						</tfoot>
					</table>
				</div>
				<?php
				eaccounting_textarea(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Notes', 'wp-ever-accounting' ),
						'name'          => 'note',
						'value'         => $invoice->get_note(),
						'required'      => false,
					)
				);
				eaccounting_textarea(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Footer', 'wp-ever-accounting' ),
						'name'          => 'footer',
						'value'         => $invoice->get_footer(),
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
						'type'          => 'expense',
						'creatable'     => true,
					)
				);
				eaccounting_file_input(
					array(
						'label'         => __( 'Attachments', 'wp-ever-accounting' ),
						'name'          => 'attachment',
						'value'         => $invoice->get_attachment_id(),
						'required'      => false,
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
