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
				eaccounting_contact_dropdown( array(
								'wrapper_class' => 'ea-col-6',
								'label'         => __( 'Customer', 'wp-ever-accounting' ),
								'name'          => 'contact_id',
								'id'            => 'customer_id',
								'placeholder'   => __( 'Select Customer', 'wp-ever-accounting' ),
								'value'         => $invoice->get_contact_id(),
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
				eaccounting_text_input( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Invoice Date', 'wp-ever-accounting' ),
						'name'          => 'invoiced_at',
						'value'         => $invoice->get_invoiced_at() ? $invoice->get_invoiced_at() : null,
						'required'      => true,
						'data_type'     => 'date'
				) );
				eaccounting_text_input( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Due Date', 'wp-ever-accounting' ),
						'name'          => 'due_at',
						'value'         => $invoice->get_due_at() ? $invoice->get_due_at() : null,
						'required'      => true,
						'data_type'     => 'date'
				) );
				eaccounting_text_input( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Invoice Number', 'wp-ever-accounting' ),
						'name'          => 'invoice_number',
						'value'         => $invoice->get_invoice_number(),
						'required'      => true,
				) );
				?>
				<div class="ea-form-field ea-col-12 ea-invoice-table-wrap">
					<label class="ea-label" for="items"><?php _e( 'Items', 'wp-ever-accounting' ); ?></label>
					<table cellpadding="0" cellspacing="0" class="ea-invoice-items-table">
						<thead class="ea-invoice-items-table-head">
						<tr>
							<th class="ea-invoice-item"><?php _e( 'Item', 'wp-ever-accounting' ); ?></th>
							<th class="ea-invoice-item-quantity"><?php _e( 'Quantity', 'wp-ever-accounting' ) ?></th>
							<th colspan="ea-invoice-item-price"><?php _e( 'Price', 'wp-ever-accounting' ) ?></th>
							<th colspan="ea-invoice-item-actions"></th>
						</tr>
						</thead>
						<tbody class="ea-invoice-items-table-body">
						<tr class="ea-invoice-items-line-items">
							<td class="ea-invoice-item"><?php _e( 'Item 1', 'wp-ever-accounting' ); ?></td>
							<td class="ea-invoice-item-quantity"><?php _e( '1', 'wp-ever-accounting' ) ?></td>
							<td colspan="ea-invoice-item-price"><?php _e( '$1000.00', 'wp-ever-accounting' ) ?></td>
							<td colspan="ea-invoice-item-actions">
								<span class="dashicons dashicons-edit-page"></span>
								<span class="dashicons dashicons-trash"></span>
							</td>
						</tr>
						</tbody>
					</table>
					<div class="ea-invoice-items-total">
						<div class="ea-row">
							<div class="ea-col-6"></div>
							<div class="ea-col-6">
								<table class="ea-invoice-total text-right">
									<tbody>
									<tr class="ea-totals-subtotal">
										<td class="label"><?php _e( 'Items Subtotal:','wp-ever-accounting' ); ?></td>
										<td width="1%"></td>
										<td class="value">$1000.00</td>
									</tr>
									<tr class="ea-totals-discount">
										<td class="label"><?php _e( 'Discount:','wp-ever-accounting' ); ?></td>
										<td width="1%"></td>
										<td class="value">$0.00</td>
									</tr>
									<tr class="ea-totals-shipping">
										<td class="label"><?php _e( 'Shipping:','wp-ever-accounting' ); ?></td>
										<td width="1%"></td>
										<td class="value">$0.00</td>
									</tr>
									<tr class="ea-totals-shipping">
										<td class="label"><?php _e( 'Shipping:','wp-ever-accounting' ); ?></td>
										<td width="1%"></td>
										<td class="value">$0.00</td>
									</tr>
									<tr class="ea-totals-total">
										<td class="label"><?php _e( 'Items Total:','wp-ever-accounting' ); ?></td>
										<td width="1%"></td>
										<td class="value">$1000.00</td>
									</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="ea-invoice-items-actions">
							<div class="ea-row">
								<div class="ea-col-8 text-left">
									<button type="button" class="button ea-add-invoice-item btn-secondary"><?php _e('Add Invoice Items','wp-ever-accounting');?></button>
									<button type="button" class="button ea-create-invoice-item btn-secondary"><?php _e('Create Invoice Items','wp-ever-accounting');?></button>
								</div>
								<div class="ea-col-4 text-right">
									<button type="button" class="button ea-invoice-recalculate button-primary"><?php _e('Recalculate Totals','wp-ever-accounting');?></button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
				eaccounting_text_input( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Order Number', 'wp-ever-accounting' ),
						'name'          => 'order_number',
						'value'         => $invoice->get_order_number(),
						'required'      => false,
				) );
				eaccounting_textarea( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Notes', 'wp-ever-accounting' ),
						'name'          => 'note',
						'value'         => $invoice->get_note(),
						'required'      => false,
				) );
				eaccounting_textarea( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Footer', 'wp-ever-accounting' ),
						'name'          => 'footer',
						'value'         => $invoice->get_footer(),
						'required'      => false,
				) );
				eaccounting_category_dropdown( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Category', 'wp-ever-accounting' ),
						'name'          => 'category_id',
						'value'         => $invoice->get_category_id(),
						'required'      => true,
						'type'          => 'expense',
						'creatable'     => true,
				) );
				eaccounting_file_input( array(
						'label'         => __( 'Attachments', 'wp-ever-accounting' ),
						'name'          => 'attachment',
						'value'         => $invoice->get_attachment(),
						'required'      => false,
						'wrapper_class' => 'ea-col-6',
						'placeholder'   => __( 'Upload File', 'wp-ever-accounting' ),
				) );
				?>
			</div>
			<?php
			wp_nonce_field( 'ea_edit_invoice' );
			submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
			?>
		</form>
	</div>
</div>
