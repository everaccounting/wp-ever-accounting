<?php
/**
 * Admin Invoices Form Body
 * Page: Sales
 * Tab: Invoices
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $document \EverAccounting\Models\Invoice Invoice object.
 */

defined( 'ABSPATH' ) || exit;

$columns            = eac_get_invoice_columns();
$columns['actions'] = '&nbsp;';
if ( ! $document->is_calculating_tax() && isset( $columns['tax'] ) ) {
	unset( $columns['tax'] );
}
?>
<div class="eac-panel tw-p-0">
	<div class="eac-document-form__section document-info">
		<div class=" document-info__billing">
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
	</div><!-- .document-info -->

	<div class="eac-document-form__section document-lines">
		<table class="document-lines__table">
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
			<tbody>
			<?php foreach ( $document->lines as $line_key => $line ) : ?>
			<tr class="line-item" data-item-id="<?php echo esc_attr( $line->item_id ); ?>">
				<?php foreach ( $columns as $key => $label ) : ?>
					<?php if ( 'item' === $key ) : ?>
						<td class="line-<?php echo esc_attr( $key ); ?>__item" colspan="2">
							<?php
							printf( '<input type="hidden" name="lines[%s][id]" value="%s"/>', esc_attr( $line_key ), esc_attr( $line->id ) );
							printf( '<input type="hidden" name="lines[%s][item_id]" value="%s"/>', esc_attr( $line_key ), esc_attr( $line->item_id ) );
							printf( '<input class="line-item__name" type="text" name="lines[%s][name]" value="%s" readonly/>', esc_attr( $line_key ), esc_attr( $line->name ) );
							printf( '<textarea class="line-item__description" name="lines[%s][description]" placeholder="%s" maxlength="160">%s</textarea>', esc_attr( $line_key ), esc_attr__( 'Description', 'wp-ever-accounting' ), esc_textarea( $line->get_description ) );
							?>
							<?php if ( $document->is_calculating_tax() && $line->taxable ) : ?>
								<select name="lines[<?php echo esc_attr( $line_key ); ?>][tax_ids]" class="line-item__taxes eac_select2 " data-action="eac_json_search" data-type="tax" data-placeholder="<?php esc_attr_e( 'Select a tax', 'wp-ever-accounting' ); ?>" multiple>
									<?php foreach ( $line->taxes as $tax ) : ?>
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
									printf( '<div class="eac-input-group"><span class="addon">%s</span> <input class="line-item__price eac_decimal_input" type="text" name="lines[%s][price]" value="%s" placeholder="%s" /></div>', esc_html( eac_get_currency_symbol( $document->currency_code ) ), esc_attr( $line_key ), esc_attr( $line->price ), esc_attr__( 'Price', 'wp-ever-accounting' ) );
									break;
								case 'quantity':
									printf( '<input class="line-item__quantity eac_decimal_input" type="number" name="lines[%s][quantity]" value="%s" placeholder="%s" />', esc_attr( $line_key ), esc_attr( $line->quantity ), esc_attr__( 'Quantity', 'wp-ever-accounting' ) );
									break;
								case 'tax':
									echo esc_html( eac_format_amount( $line->tax_total, $document->currency_code ) );
									break;
								case 'subtotal':
									echo esc_html( eac_format_amount( $line->subtotal, $document->currency_code ) );
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
					<div class="eac-input-group">
						<select class="add-line-item eac_select2" data-action="eac_json_search" data-type="item" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
						<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=eac-items&add=yes' ) ); ?>" title="<?php esc_attr_e( 'Add New Item', 'wp-ever-accounting' ); ?>">
							<span class="dashicons dashicons-plus"></span>
						</a>
					</div>
				</td>
				<td class="line-item tw-text-right" colspan="<?php echo count( $columns ) - 1; ?>">
					<?php if ( $document->lines ) : ?>

					<?php endif; ?>
				</td>
			</tr>
			</tbody>
		</table>
	</div><!-- .document-lines -->
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