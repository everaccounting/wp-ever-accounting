<?php
/**
 * Admin Invoices Form Main
 * Page: Sales
 * Tab: Invoices
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $document \EverAccounting\Models\Invoice Invoice object.
 */

defined( 'ABSPATH' ) || exit;

$columns= eac_get_invoice_columns();
$columns['actions'] = '&nbsp;';
if ( ! $document->is_calculating_tax() && isset( $columns['tax'] ) ) {
	unset( $columns['tax'] );
}

?>
<div class="bkit-panel tw-p-0">
	<div class="eac-document-form__section document-info">
		<div class="document-info__column document-info__billing">
			<h3>
				<?php esc_html_e( 'Billing Details', 'ever-accounting' ); ?>
			</h3>
			<?php if ( $document->contact_id ) : ?>
				<?php echo wp_kses_post( wpautop( $document->formatted_billing_address ) ); ?>
			<?php else : ?>
				<div class="tw-h-4 tw-bg-gray-200 tw-rounded tw-w-40 tw-mb-2"></div>
				<div class="tw-h-4 tw-bg-gray-200 tw-rounded tw-w-60"></div>
				<div class="tw-h-4 tw-bg-gray-200 tw-rounded tw-w-[80%] tw-mt-2"></div>
			<?php endif; ?>
		</div>
		<div class="document-info__column document-info__meta">
			<div class="bkit-form-group tw-mt-0">
				<label for="number"><?php esc_html_e( 'Invoice Number', 'wp-ever-accounting' ); ?></label>
				<input type="text" name="number" id="number" placeholder="INV-0001" value="<?php echo esc_attr( $document->number ); ?>" readonly/>
			</div>
			<div class="bkit-form-group tw-mt-0">
				<label for="reference"><?php esc_html_e( 'Reference', 'wp-ever-accounting' ); ?></label>
				<input type="text" name="reference" id="reference" placeholder="REF-0001" value="<?php echo esc_attr( $document->reference ); ?>"/>
			</div>
			<div class="bkit-form-group tw-mt-0">
				<label for="issue_date"><?php esc_html_e( 'Issue Date', 'wp-ever-accounting' ); ?>
					<abbr class="required" title="<?php esc_attr_e( 'required', 'wp-ever-accounting' ); ?>"></abbr>
				</label>
				<input type="text" name="issue_date" id="issue_date" class="eac_datepicker" placeholder="<?php esc_attr_e( 'YYYY-MM-DD', 'wp-ever-accounting' ); ?>" value="<?php echo esc_attr( $document->issue_date ); ?>" required/>
			</div>
			<div class="bkit-form-group tw-mt-0">
				<label for="due_date"><?php esc_html_e( 'Due Date', 'wp-ever-accounting' ); ?></label>
				<input type="text" name="due_date" id="due_date" data-format="yy-mm-dd" class="eac_datepicker" placeholder="<?php esc_attr_e( 'YYYY-MM-DD', 'wp-ever-accounting' ); ?>" value="<?php echo esc_attr( $document->due_date ); ?>"/>
			</div>
		</div>
	</div><!-- .document-info -->
	<div class="eac-document-form__section document-items">
		<table class="document-items__table">
			<thead>
			<tr class="line-item">
				<?php foreach ( $columns as $key => $label ) : ?>
					<?php if ( 'item' === $key ) : ?>
						<th class="line-item__<?php echo esc_attr( $key ); ?>" colspan="2"><?php echo esc_html( $label ); ?></th>
					<?php else : ?>
						<th class="line-item__<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></th>
					<?php endif; ?>
				<?php endforeach; ?>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $document->get_items() as $item_key => $item ) : ?>
			<tr class="line-item" data-item-id="<?php echo esc_attr( $item->item_id ); ?>">
				<?php foreach ( $columns as $key => $label ) : ?>
					<?php if ( 'item' === $key ) : ?>
						<td class="line-<?php echo esc_attr( $key ); ?>__item" colspan="2">
							<?php
							printf( '<input type="hidden" name="items[%s][id]" value="%s"/>', esc_attr( $item_key ), esc_attr( $item->id ) );
							printf( '<input type="hidden" name="items[%s][item_id]" value="%s"/>', esc_attr( $item_key ), esc_attr( $item->item_id ) );
							printf( '<input class="line-item__name" type="text" name="items[%s][name]" value="%s" readonly/>', esc_attr( $item_key ), esc_attr( $item->name ) );
							printf( '<textarea class="line-item__description" name="items[%s][description]" placeholder="%s" maxlength="160">%s</textarea>', esc_attr( $item_key ), esc_attr__( 'Description', 'wp-ever-accounting' ), esc_textarea( $item->get_description ) );
							?>
							<?php if ( $document->is_calculating_tax() && $item->taxable ) : ?>
								<select name="items[<?php echo esc_attr( $item_key ); ?>][taxes][]" class="line-item__taxes eac_select2 " data-action="eac_json_search" data-type="tax" data-placeholder="<?php esc_attr_e( 'Select a tax', 'wp-ever-accounting' ); ?>" multiple>
									<?php foreach ( $item->get_taxes() as $tax ) : ?>
										<option value="<?php echo esc_attr( $tax->tax_id ); ?>" selected="selected"><?php echo esc_html( $tax->name ); ?></option>
									<?php endforeach; ?>
								</select>
							<?php endif; ?>
						</td>
					<?php else : ?>
						<td class="line-item__<?php echo esc_attr( $key ); ?>">
							<?php
							switch ( $key ) {
								case 'price':
									printf( '<div class="bkit-input-group"><span class="addon">%s</span> <input class="line-item__price eac_decimal_input" type="text" name="items[%s][price]" value="%s" placeholder="%s" /></div>', esc_html( eac_get_currency_symbol( $document->currency_code ) ), esc_attr( $item_key ), esc_attr( $item->price ), esc_attr__( 'Price', 'wp-ever-accounting' ) );
									break;
								case 'quantity':
									printf( '<input class="line-item__quantity eac_decimal_input" type="number" name="items[%s][quantity]" value="%s" placeholder="%s" />', esc_attr( $item_key ), esc_attr( $item->quantity ), esc_attr__( 'Quantity', 'wp-ever-accounting' ) );
									break;
								case 'tax':
									echo esc_html( eac_format_amount( $item->tax_total, $document->currency_code ) );
									break;
								case 'subtotal':
									echo esc_html( eac_format_amount( $item->subtotal, $document->currency_code ) );
									break;
								case 'actions':
									echo '<a href="#" class="remove-line-item"><span class="dashicons dashicons-trash"></span></a>';
									break;
								default:
									// code...
									break;
							}
							?>
						</td>
					<?php endif; ?>
				<?php endforeach; ?>
				<?php endforeach; ?>
			</tr>
			<tr>
				<td colspan="2">
					<div class="bkit-input-group">
						<select class="add-line-item eac_select2" data-action="eac_json_search" data-type="item" name="items[<?php echo esc_attr( PHP_INT_MAX ); ?>][item_id]" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
						<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=eac-items&add=yes' ) ); ?>" title="<?php esc_attr_e( 'Add New Item', 'wp-ever-accounting' ); ?>">
							<span class="dashicons dashicons-plus"></span>
						</a>
					</div>
				</td>
				<td class="line-item tw-text-right" colspan="<?php echo count( $columns ) - 1; ?>">
					<?php if ( $document->get_items() ) : ?>

					<?php endif; ?>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	<div class="eac-document-form__section document-totals">
		<div></div>
		<table class="document-totals__table">
			<tbody>
			<tr>
				<td class="document-totals__label is--subtotal"><?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?></td>
				<td class="document-total__value is--subtotal"><?php echo esc_html( eac_format_amount( $document->items_total, $document->currency_code ) ); ?></td>
			</tr>
			<?php if ( ! empty( absint( $document->discount_total ) ) ) : ?>
				<tr>
					<td class="document-totals__label is--discount"><?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?></td>
					<td class="document-total__value is--discount"><?php echo esc_html( eac_format_amount( $document->discount_total, $document->currency_code ) ); ?></td>
				</tr>
			<?php endif; ?>
			<?php if ( ! empty( absint( $document->shipping_total ) ) ) : ?>
				<tr>
					<td class="document-totals__label is--shipping"><?php esc_html_e( 'Shipping', 'wp-ever-accounting' ); ?></td>
					<td class="document-total__value is--shipping"><?php echo esc_html( eac_format_amount( $document->shipping_total, $document->currency_code ) ); ?></td>
				</tr>
			<?php endif; ?>
			<?php if ( ! empty( absint( $document->fees_total ) ) ) : ?>
				<tr>
					<td class="document-totals__label is--fees"><?php esc_html_e( 'Fees', 'wp-ever-accounting' ); ?></td>
					<td class="document-total__value is--fees"><?php echo esc_html( eac_format_amount( $document->fees_total, $document->currency_code ) ); ?></td>
				</tr>
			<?php endif; ?>
			<?php if ( $document->is_calculating_tax() && ! empty( absint( $document->tax_total ) ) ) : ?>
				<?php if ( 'single' === get_option( 'eac_tax_display_totals' ) ) : ?>
					<tr>
						<td class="document-totals__label is--tax"><?php esc_html_e( 'Tax', 'wp-ever-accounting' ); ?></td>
						<td class="document-total__value is--tax"><?php echo esc_html( eac_format_amount( $document->tax_total, $document->currency_code ) ); ?></td>
					</tr>
				<?php else : ?>
					<?php foreach ( $document->formatted_itemized_taxes as $label => $amount ) : ?>
						<tr>
							<td class="document-totals__label is--tax is--itemized"><?php echo esc_html( $label ); ?></td>
							<td class="document-total__value is--tax is--itemized"><?php echo esc_html( $amount ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endif; ?>
			<tr>
				<td class="document-totals__label  is--total"><?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?></td>
				<td class="document-total__value  is--total"><?php echo esc_html( eac_format_amount( $document->total, $document->currency_code ) ); ?></td>
			</tr>
			</tbody>
		</table>
	</div>
</div>
