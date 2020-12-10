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
		<form id="ea-invoice-form" class="ea-ajax-form" method="post">
			<div class="ea-row">
				<?php
				eaccounting_contact_dropdown(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Customer', 'wp-ever-accounting' ),
						'name'          => 'customer_id',
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
						'value'         => $invoice->get_issue_date() ? eaccounting_format_datetime( $invoice->get_issue_date(), 'Y-m-d' ) : null,
						'required'      => true,
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
				$items = $invoice->get_items( true );
				?>

				<div class="ea-invoice-items-wrapper">
					<table cellpadding="0" cellspacing="0" class="ea-invoice-items">
						<thead>
						<tr>
							<th class="line-actions">&nbsp;</th>
							<th class="line-name" colspan="2"><?php esc_html_e( 'Item', 'wp-ever-accounting' ); ?></th>
							<th class="line-price"><?php esc_html_e( 'Price', 'wp-ever-accounting' ); ?></th>
							<th class="line-quantity"><?php esc_html_e( 'Quantity', 'wp-ever-accounting' ); ?></th>
							<th class="line-tax"><?php esc_html_e( 'Tax(%)', 'wp-ever-accounting' ); ?></th>
							<th class="line-vat"><?php esc_html_e( 'Vat(%)', 'wp-ever-accounting' ); ?></th>
							<th class="line-subtotal"><?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?></th>
						</tr>
						</thead>
						<tbody class="ea-invoice-line-items">
						<?php foreach ( $items as $index => $item ) : ?>
							<tr class="ea-invoice-line">
								<td class="line-actions">
									<a href="#" class="edit-line"><span class="dashicons dashicons-edit">&nbsp;</span></a>
									<a href="#" class="delete-line"><span class="dashicons dashicons-no">&nbsp;</span></a>
								</td>
								<td class="line-name" colspan="2">
									<input type="hidden" class="item-id" name="items[<?php echo $index; ?>][id]" value="<?php echo esc_html( $item->get_item_id() ); ?>">
									<div class="edit">
										<input type="text" class="item-name" name="items[<?php echo $index; ?>][item_name]" value="<?php echo esc_html( $item->get_item_name() ); ?>" placeholder="<?php esc_html_e( 'Search item', 'wp-ever-accounting' ); ?>" required>
									</div>
									<div class="view">
										<?php echo esc_html( $item->get_item_name() ); ?>
									</div>
								</td>
								<td class="line-price">
									<div class="edit">
										<input type="text" class="item-price" name="items[<?php echo $index; ?>][item_price]" value="<?php echo esc_html( eaccounting_format_price( $item->get_item_price(), $invoice->get_currency_code() ) ); ?>" required>
									</div>
									<div class="view">
										<?php echo esc_html( eaccounting_format_price( $item->get_item_price(), $invoice->get_currency_code() ) ); ?>
									</div>
								</td>
								<td class="line-quantity">
									<div class="edit">
										<input type="text" class="item-quantity" name="items[<?php echo $index; ?>][item_quantity]" value="<?php echo esc_html( $item->get_quantity() ); ?>" required>
									</div>
									<div class="view">
										<small class="times">×</small>
										<?php echo esc_html( $item->get_quantity() ); ?>
									</div>
								</td>
								<td class="line-tax">
									<div class="edit">
										<input type="text" class="item-vat" name="items[<?php echo $index; ?>][item_vat_rate]" value="<?php echo esc_html( $item->get_tax_rate() ); ?>" required>
									</div>
									<div class="view">
										<?php echo esc_html( $item->get_tax_rate() ); ?>
									</div>
								</td>
								<td class="line-vat">
									<div class="edit">
										<input type="text" class="item-tax" name="items[<?php echo $index; ?>][item_tax_rate]" value="<?php echo esc_html( $item->get_vat_rate() ); ?>">
									</div>
									<div class="view">
										<?php echo esc_html( $item->get_vat_rate() ); ?>
									</div>
								</td>
								<td class="line-subtotal">
									<div class="view">
										<span class="item-subtotal"><?php echo esc_html( eaccounting_format_price( $item->get_subtotal(), $invoice->get_currency_code() ) ); ?></span>
									</div>
								</td>
							</tr>

						<?php endforeach; ?>

						<tr class="ea-invoice-line add-line-item">
							<td colspan="8" class="add-item-column">
								<button type="button" class="button ea-add-invoice-item btn-secondary"><span class="dashicons dashicons-plus"></span> Item</button>
							</td>
						</tr>

						</tbody>
					</table>
					<div class="ea-invoice-data-row ea-invoice-total-items">
						<table class="ea-invoice-totals">
							<tbody>
							<tr class="ea-invoice-totals-subtotal">
								<td class="label">Items Subtotal:</td>
								<td width="1%">&nbsp;</td>
								<td class="value"><span class="ea-invoice-currency__symbol">$</span>0.00</td>
							</tr>
							<tr class="ea-invoice-totals-discount">
								<td class="label">Total Discount:</td>
								<td width="1%">&nbsp;</td>
								<td class="value"><span class="ea-invoice-currency__symbol">$</span>0.00</td>
							</tr>
							<tr class="ea-invoice-totals-tax">
								<td class="label">Total Tax:</td>
								<td width="1%">&nbsp;</td>
								<td class="value"><span class="ea-invoice-currency__symbol">$</span>0.00</td>
							</tr>
							<tr class="ea-invoice-totals-total">
								<td class="label">Invoice Total:</td>
								<td width="1%">&nbsp;</td>
								<td class="value"><span class="ea-invoice-currency__symbol">$</span>0.00</td>
							</tr>
							</tbody>
						</table>
					</div>
					<div class="ea-invoice-data-row ea-invoice-tools">
						<button type="button" class="button button-primary recalculate-totals-button">Recalculate Totals</button>
					</div>
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
