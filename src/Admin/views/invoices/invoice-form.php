<?php
/**
 * View: Invoice Form
 *
 * @since    1.1.6
 * @package     EverAccounting
 * @subpackage  Admin/View/Vendors
 * @var Invoice $document Invoice object.
 */

use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit();
$tax_enabled = $document->is_calculating_tax();
?>
<form id="eac-invoice-form" class="eac-invoice-form eac-document" method="post">

	<div class="eac-panel">
		<div class="eac-columns">
			<div class="eac-col-6">
				<div class="eac-form-field">
					<label class="eac-form-field__label" for="customer_id">
						<?php esc_html_e( 'Customer', 'ever-accounting' ); ?>
						<abbr title="required">*</abbr>
					</label>
					<div class="eac-form-field__group">
						<select class="eac_ajax_search" name="customer_id" id="customer_id" data-placeholder="<?php esc_attr_e( 'Select a customer', 'wp-ever-accounting' ); ?>" data-allow_clear="true" data-type="customer" required>
							<?php if ( $document->get_contact_id() ) : ?>
								<option value="<?php echo esc_attr( $document->get_contact_id() ); ?>" selected="selected"><?php echo esc_html( $document->get_billing_name() ); ?></option>
							<?php endif; ?>
						</select>
						<a class="button" href="<?php esc_attr( eac_action_url( 'action=get_html_response&html_type=edit_customer' ) ); ?>" title="<?php esc_attr_e( 'Add customer', 'wp-ever-accounting' ); ?>">
							<span class="dashicons dashicons-plus"></span>
						</a>
					</div>
				</div>
				<div class="eac-columns billing-data">
					<div class="eac-form-field eac-col-6">
						<label for="billing_name" class="eac-form-field__label">
							<?php esc_html_e( 'Name', 'wp-ever-accounting' ); ?>
						</label>
						<input type="text" name="billing_name" id="billing_name" value="<?php echo esc_attr( $document->get_billing_name() ); ?>"/>
					</div>
					<div class="eac-form-field eac-col-6">
						<label for="billing_email" class="eac-form-field__label">
							<?php esc_html_e( 'Email', 'wp-ever-accounting' ); ?>
						</label>
						<input type="email" name="billing_email" id="billing_email" value="<?php echo esc_attr( $document->get_billing_email() ); ?>"/>
					</div>
					<div class="eac-form-field eac-col-6">
						<label for="billing_phone" class="eac-form-field__label">
							<?php esc_html_e( 'Phone', 'wp-ever-accounting' ); ?>
						</label>
						<input type="text" name="billing_phone" id="billing_phone" value="<?php echo esc_attr( $document->get_billing_phone() ); ?>"/>
					</div>
					<div class="eac-form-field eac-col-6">
						<label for="billing_vat_number" class="eac-form-field__label">
							<?php esc_html_e( 'Vat Number', 'wp-ever-accounting' ); ?>
						</label>
						<input type="text" name="billing_vat_number" id="billing_vat_number" value="<?php echo esc_attr( $document->get_billing_vat_number() ); ?>"/>
					</div>
					<div class="eac-form-field eac-col-6">
						<label for="billing_address_1" class="eac-form-field__label">
							<?php esc_html_e( 'Address', 'wp-ever-accounting' ); ?>
						</label>
						<input type="text" name="billing_address_1" id="billing_address_1" value="<?php echo esc_attr( $document->get_billing_address_1() ); ?>"/>
					</div>
					<div class="eac-form-field eac-col-6">
						<label for="billing_address_2" class="eac-form-field__label">
							<?php esc_html_e( 'Address', 'wp-ever-accounting' ); ?>
						</label>
						<input type="text" name="billing_address_2" id="billing_address_2" value="<?php echo esc_attr( $document->get_billing_address_2() ); ?>"/>
					</div>
					<div class="eac-form-field eac-col-6">
						<label for="billing_city" class="eac-form-field__label">
							<?php esc_html_e( 'City', 'wp-ever-accounting' ); ?>
						</label>
						<input type="text" name="billing_city" id="billing_city" value="<?php echo esc_attr( $document->get_billing_city() ); ?>"/>
					</div>
					<div class="eac-form-field eac-col-6">
						<label for="billing_state" class="eac-form-field__label">
							<?php esc_html_e( 'State', 'wp-ever-accounting' ); ?>
						</label>
						<input type="text" name="billing_state" id="billing_state" value="<?php echo esc_attr( $document->get_billing_state() ); ?>"/>
					</div>
					<div class="eac-form-field eac-col-6">
						<label for="billing_postcode" class="eac-form-field__label">
							<?php esc_html_e( 'Postcode', 'wp-ever-accounting' ); ?>
						</label>
						<input type="text" name="billing_postcode" id="billing_postcode" value="<?php echo esc_attr( $document->get_billing_postcode() ); ?>"/>
					</div>
					<div class="eac-form-field eac-col-6">
						<label for="billing_country" class="eac-form-field__label">
							<?php esc_html_e( 'Country', 'wp-ever-accounting' ); ?>
						</label>
						<select class="eac-select2" name="billing_country" id="billing_country" data-placeholder="<?php esc_attr_e( 'Select a country', 'wp-ever-accounting' ); ?>">
							<option value=""></option>
							<?php foreach ( eac_get_countries() as $key => $value ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $document->get_billing_country(), $key ); ?>><?php echo esc_html( $value ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>
			<div class="eac-col-6">
				<div class="eac-columns document-data">
					<div class="eac-form-field eac-col-6">
						<label for="issued_at" class="eac-form-field__label">
							<?php esc_html_e( 'Issue Date', 'wp-ever-accounting' ); ?>
							<abbr title="required">*</abbr>
						</label>
						<input type="text" class="eac-datepicker" name="issued_at" id="issued_at" value="<?php echo esc_attr( $document->get_issued_at() ); ?>" required/>
					</div>
					<div class="eac-form-field eac-col-6">
						<label for="due_at" class="eac-form-field__label">
							<?php esc_html_e( 'Due Date', 'wp-ever-accounting' ); ?>
							<abbr title="required">*</abbr>
						</label>
						<input type="text" class="eac-datepicker" name="due_at" id="due_at" value="<?php echo esc_attr( $document->get_due_at() ); ?>" required/>
					</div>
					<div class="eac-form-field eac-col-6">
						<label for="number" class="eac-form-field__label">
							<?php esc_html_e( 'Invoice Number', 'wp-ever-accounting' ); ?>
							<abbr title="required">*</abbr>
						</label>
						<input type="text" name="number" id="number" value="<?php echo esc_attr( $document->get_number() ); ?>" required/>
					</div>
					<div class="eac-form-field eac-col-6">
						<label for="reference" class="eac-form-field__label">
							<?php esc_html_e( 'Reference', 'wp-ever-accounting' ); ?>
						</label>
						<input type="text" name="reference" id="reference" value="<?php echo esc_attr( $document->get_reference() ); ?>"/>
					</div>
					<div class="eac-form-field eac-col-6">
						<label for="currency_code" class="eac-form-field__label">
							<?php esc_html_e( 'Currency', 'wp-ever-accounting' ); ?>
							<abbr title="required">*</abbr>
						</label>
						<select class="eac-select2" name="currency_code" id="currency_code" required>
							<?php foreach ( eac_get_currencies() as $currency ) : ?>
								<option value="<?php echo esc_attr( $currency['code'] ); ?>" <?php selected( $currency['code'], $document->get_currency_code() ); ?>>
									<?php echo esc_html( $currency['formatted_name'] ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="eac-panel overflow-x margin-bottom-0 padding-0 border-0">
		<table class="eac-document__items" cellpadding="0" cellspacing="0">
			<thead>
			<tr>
				<th class="line-item" colspan="2"><?php esc_html_e( 'Item', 'wp-ever-accounting' ); ?></th>
				<th class="line-price" width="10%"><?php esc_html_e( 'Price', 'wp-ever-accounting' ); ?></th>
				<th class="line-quantity" width="10%"><?php esc_html_e( 'Quantity', 'wp-ever-accounting' ); ?></th>
				<?php if ( $tax_enabled ) : ?>
					<th class="line-taxes" width="20%"><?php esc_html_e( 'Taxes', 'wp-ever-accounting' ); ?></th>
				<?php endif; ?>
				<th class="line-subtotal" width="10%"><?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?></th>
				<th class="line-actions" width="1%">&nbsp</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $document->get_items() as $key => $item ) : ?>
				<tr data-item_id="<?php echo esc_attr( $item->get_id() ); ?>">
					<td class="line-item" colspan="2">
						<input type="hidden" name="items[<?php echo esc_attr( $key ); ?>][id]" value="<?php echo esc_attr( $item->get_id() ); ?>"/>
						<input type="hidden" name="items[<?php echo esc_attr( $key ); ?>][product_id]" value="<?php echo esc_attr( $item->get_product_id() ); ?>"/>
						<input class="item-name" type="text" name="items[<?php echo esc_attr( $key ); ?>][name]" value="<?php echo esc_attr( $item->get_name() ); ?>" readonly/>
						<textarea class="item-description" name="items[<?php echo esc_attr( $key ); ?>][description]" placeholder="<?php esc_attr_e( 'Description', 'wp-ever-accounting' ); ?>" maxlength="160"><?php echo esc_textarea( $item->get_description() ); ?></textarea>
					</td>
					<td class="line-price">
						<input class="item-price" type="number" name="items[<?php echo esc_attr( $key ); ?>][price]" value="<?php echo esc_attr( eac_sanitize_money( $item->get_price(), $document->get_currency_code() ) ); ?>"/>
					</td>
					<td class="line-quantity">
						<div class="eac-field__group">
							<input class="item-quantity" type="number" name="items[<?php echo esc_attr( $key ); ?>][quantity]" value="<?php echo esc_attr( eac_sanitize_number( $item->get_quantity(), 2 ) ); ?>"/>
						</div>
					</td>
					<?php if ( $tax_enabled ) : ?>
						<td class="line-taxes">
							<select class="item-taxes eac_ajax_search" name="items[<?php echo esc_attr( $key ); ?>][tax_ids]" data-eac-select2="tax" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select taxes', 'wp-ever-accounting' ); ?>" data-type="tax">
								<?php foreach ( $item->get_taxes() as $item_tax ) : ?>
									<option value="<?php echo esc_attr( $item_tax->get_tax_id() ); ?>" selected="selected"><?php echo esc_html( $item_tax->get_name() ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					<?php endif; ?>
					<td class="line-subtotal">
						<?php echo esc_html( eac_format_money( $item->get_subtotal(), $document->get_currency_code() ) ); ?>
					</td>
					<td class="line-actions">
						<a class="remove-line-item" data-item_id="<?php echo esc_attr( $item->get_id() ); ?>">
							<span class="dashicons dashicons-trash"></span>
						</a>
					</td>
				</tr>
			<?php endforeach; ?>
			<tr id="new-line" class="<?php echo empty( $document->get_items() ) ? '' : 'display-none'; ?>">
				<td class="line-item" colspan="2">
					<div class="eac-form-field__group">
						<select class="select-new-item eac_ajax_search" data-tyoe="item" data-placeholder="<?php esc_attr_e( 'Select line item', 'wp-ever-accounting' ); ?>">
							<option></option>
						</select>
						<a class="button" href="<?php echo esc_url( eac_action_url( 'action=get_html_response&html_type=edit_product' ) ); ?>" title="<?php esc_attr_e( 'Add New Item', 'wp-ever-accounting' ); ?>">
							<span class="dashicons dashicons-plus"></span>
						</a>
					</div>
				</td>
			</tr>
			</tbody>
		</table>
	</div>

	<div class="eac-panel align-items-center display-flex justify-content-between border-top-0 margin-0">
		<button class="button add-line-item"><?php esc_html_e( 'Add Line Item', 'wp-ever-accounting' ); ?></button>
		<button class="button calculate-totals"><?php esc_html_e( 'Recalculate', 'wp-ever-accounting' ); ?></button>
	</div>

	<div class="eac-panel margin-0 border-top-0">
		<table class="eac-document__totals" cellpadding="0" cellspacing="0">
			<tbody>
			<tr>
				<td class="total-label"><?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?></td>
				<td class="total-value"><?php echo esc_html( eac_format_money( $document->get_subtotal(), $document->get_currency_code() ) ); ?></td>
			</tr>

			<tr>
				<td class="total-label">
					<?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?>
					<div class="eac-form-field__group display-inline-flex" style="max-width: 120px; margin-left: 10px; margin-bottom: 0; font-weight: normal;color: inherit;">
						<select class="discount-type form-field__addon" name="discount_type" style="max-width: 50px;">
							<option value="fixed" <?php selected( $document->get_discount_type(), 'fixed' ); ?>>$</option>
							<option value="percentage" <?php selected( $document->get_discount_type(), 'percentage' ); ?>>%</option>
						</select>
						<input class="discount_amount" type="number" name="discount_amount" value="<?php echo esc_attr( $document->get_discount_amount() ); ?>" style="max-width: 70px;"/>
					</div>
				</td>
				<td class="total-value">
					<?php echo esc_html( eac_format_money( $document->get_discount_total(), $document->get_currency_code() ) ); ?>
				</td>
			</tr>

			<tr>
				<td class="total-label">
					<?php esc_html_e( 'Shipping', 'wp-ever-accounting' ); ?>
					<input class="shipping_amount" type="number" name="shipping_amount" value="<?php echo esc_attr( $document->get_shipping_amount() ); ?>" style="max-width: 120px; margin-left: 10px;"/>
				</td>
				<td class="total-value"><?php echo esc_html( eac_format_money( $document->get_shipping_total(), $document->get_currency_code() ) ); ?></td>
			</tr>

			<tr>
				<td class="total-label">
					<?php esc_html_e( 'Fees', 'wp-ever-accounting' ); ?>
					<input class="fees_amount" type="number" name="fees_amount" value="<?php echo esc_attr( $document->get_fees_amount() ); ?>" style="max-width: 120px; margin-left: 10px;"/>
				</td>
				<td class="total-value"><?php echo esc_html( eac_format_money( $document->get_fees_total(), $document->get_currency_code() ) ); ?></td>
			</tr>

			<?php if ( $tax_enabled ) : ?>
				<?php foreach ( $document->get_taxes() as $tax ) : ?>
					<tr>
						<td class="total-label"><?php echo esc_html( $tax->get_name() ); ?></td>
						<td class="total-value"><?php echo esc_html( eac_format_money( $tax->get_amount(), $document->get_currency_code() ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			<tr>
				<td class="total-label"><?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?></td>
				<td class="total-value"><?php echo esc_html( eac_format_money( $document->get_total(), $document->get_currency_code() ) ); ?></td>
			</tr>
			</tbody>
		</table>
	</div>

	<div class="eac-panel">
		<div class="eac-form-field">
			<label class="eac-form-field__label" for="document_note"><?php esc_html_e( 'Notes', 'wp-ever-accounting' ); ?></label>
			<textarea name="document_note" id="document_note" rows="3"><?php echo esc_html( $document->get_document_note() ); ?></textarea>
		</div>
	</div>

	<?php wp_nonce_field( 'eac_edit_invoice' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $document->get_id() ); ?>"/>
	<input type="hidden" name="type" value="<?php echo esc_attr( $document->get_type() ); ?>"/>
	<input type="hidden" name="action" value="eac_edit_invoice"/>
</form>
