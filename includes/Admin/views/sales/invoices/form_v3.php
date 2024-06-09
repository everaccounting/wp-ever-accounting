<?php
/**
 * Admin Invoices Form.
 * Page: Sales
 * Tab: Invoices
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $document \EverAccounting\Models\Invoice Invoice object.
 */

defined( 'ABSPATH' ) || exit;

$columns = array(
	'item'     => __( 'Item', 'wp-ever-accounting' ),
	'price'    => __( 'Price', 'wp-ever-accounting' ),
	'quantity' => __( 'Quantity', 'wp-ever-accounting' ),
	'tax'      => __( 'Tax', 'wp-ever-accounting' ),
	'subtotal' => __( 'Subtotal', 'wp-ever-accounting' ),
	'actions'  => '&nbsp;',
);
?>
	<form id="eac-invoice-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" class="eac-document-form">

		<div class="eac-panel eac-p-0">

			<div class="eac-document-form__section document-info">
				<div class="document-info__left">
					<div class="eac-form-group tw-mt-0">
						<label for="contact_id"><?php esc_html_e( 'Customer', 'wp-ever-accounting' ); ?></label>
						<div class="eac-input-group">
							<select name="contact_id" id="contact_id" class="eac_select2" data-action="eac_json_search" data-type="customer" data-placeholder="<?php esc_attr_e( 'Select a customer', 'wp-ever-accounting' ); ?>">
								<?php if ( $document->contact_id ) : ?>
									<option value="<?php echo esc_attr( $document->contact_id ); ?>" selected="selected"><?php echo esc_html( $document->billing_name ); ?></option>
								<?php endif; ?>
							</select>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=customers&add=yes' ) ); ?>" target="_blank" class="addon" title="<?php esc_attr_e( 'Add New Customer', 'wp-ever-accounting' ); ?>">
								<span class="dashicons dashicons-plus"></span>
							</a>
						</div>
					</div>

					<h3>
						<?php esc_html_e( 'Billing', 'ever-accounting' ); ?>
						<a href="#" class="edit_billing_details"><?php esc_html_e( 'Edit', 'ever-accounting' ); ?></a>
						<span><a href="#" class="load_billing_address" style="display: none;"><?php esc_html_e( 'Load billing address', 'ever-accounting' ); ?></a></span>
					</h3>
					<div class="billing-data tw-box-sizing:border-box tw-flex-wrap:wrap tw-gap-x-[20px] tw-grid tw-grid-cols-2 tw-justify-between tw-items-self-start tw-content-flex-start">
						<div>
							<?php
							if ( $document->formatted_billing_address ) {
								echo wp_kses_post( $document->formatted_billing_address );
							} else {
								echo '<p><strong>' . esc_html__( 'Address:', 'wp-ever-accounting' ) . '</strong> ' . esc_html__( 'No address set.', 'wp-ever-accounting' ) . '</p>';
							}
							?>
						</div>
						<div>
							<?php
							if ( $document->billing_phone ) {
								echo '<p><strong>' . esc_html__( 'Phone:', 'wp-ever-accounting' ) . '</strong> ' . wp_kses_post( eac_make_phone_clickable( $document->billing_phone ) ) . '</p>';
							}
							if ( $document->billing_email ) {
								echo '<p><strong>' . esc_html__( 'Email:', 'wp-ever-accounting' ) . '</strong> ' . wp_kses_post( '<a href="mailto:' . $document->billing_email . '">' . $document->billing_email . '</a>' ) . '</p>';
							}
							if ( $document->billing_vat_number ) {
								echo '<p><strong>' . esc_html__( 'VAT:', 'wp-ever-accounting' ) . '</strong> ' . esc_html( $document->billing_vat_number ) . '</p>';
							}
							?>
						</div>
					</div>

				</div>
				<div class="document-info__right tw-box-sizing:border-box tw-flex-wrap:wrap tw-gap-x-[20px] tw-grid tw-grid-cols-2 tw-justify-between tw-items-self-start tw-content-flex-start">
					<div class="eac-form-group tw-mt-0">
						<label for="issue_date"><?php esc_html_e( 'Issue Date', 'wp-ever-accounting' ); ?>
							<abbr class="required" title="<?php esc_attr_e( 'required', 'wp-ever-accounting' ); ?>"></abbr>
						</label>
						<input type="text" name="issue_date" id="issue_date" class="eac_datepicker" placeholder="<?php esc_attr_e( 'YYYY-MM-DD', 'wp-ever-accounting' ); ?>" value="<?php echo esc_attr( $document->issue_date ); ?>" required/>
					</div>
					<div class="eac-form-group tw-mt-0">
						<label for="due_date"><?php esc_html_e( 'Due Date', 'wp-ever-accounting' ); ?></label>
						<input type="text" name="due_date" id="due_date" data-format="yy-mm-dd" class="eac_datepicker" placeholder="<?php esc_attr_e( 'YYYY-MM-DD', 'wp-ever-accounting' ); ?>" value="<?php echo esc_attr( $document->due_date ); ?>"/>
					</div>
					<div class="eac-form-group tw-mt-0">
						<label for="number"><?php esc_html_e( 'Invoice Number', 'wp-ever-accounting' ); ?></label>
						<input type="text" name="number" id="number" placeholder="INV-0001" value="<?php echo esc_attr( $document->number ); ?>"/>
					</div>
					<div class="eac-form-group tw-mt-0">
						<label for="reference"><?php esc_html_e( 'Reference', 'wp-ever-accounting' ); ?></label>
						<input type="text" name="reference" id="reference" placeholder="REF-0001" value="<?php echo esc_attr( $document->reference ); ?>"/>
					</div>
					<div class="eac-form-group tw-mt-0">
						<label for="currency_code"><?php esc_html_e( 'Currency', 'wp-ever-accounting' ); ?></label>
						<select name="currency_code" id="currency_code" class="eac_select eac_select2" data-action="eac_json_search" data-type="currency" data-placeholder="<?php esc_attr_e( 'Select a currency', 'wp-ever-accounting' ); ?>">
							<?php if ( $document->currency ) : ?>
								<option value="<?php echo esc_attr( $document->currency_code ); ?>" selected="selected"><?php echo esc_html( $document->currency->formatted_name ); ?></option>
							<?php endif; ?>
						</select>
					</div>
					<div class="eac-form-group tw-mt-0">
						<label for="discount_amount"><?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?></label>
						<div class="eac-input-group">
							<input type="number" name="discount_amount" id="discount_amount" placeholder=".05" value="<?php echo esc_attr( $document->discount_amount ); ?>"/>
							<select name="discount_type" id="discount_type" class="addon" style="width: 150px;">
								<option value="fixed" <?php selected( 'fixed', $document->discount_type ); ?>><?php esc_html_e( 'Fixed', 'wp-ever-accounting' ); ?></option>
								<option value="percentage" <?php selected( 'percentage', $document->discount_type ); ?>><?php esc_html_e( '(%)', 'wp-ever-accounting' ); ?></option>
							</select>
						</div>
					</div>
				</div>
			</div><!-- .document-info -->

			<div class="eac-document-form__section document-items">
				<table class="eac-document-form__items">
					<thead>
					<tr class="line-item">
						<?php foreach ( $columns as $key => $label ) : ?>
							<?php if ( 'item' === $key ) : ?>
								<th class="line-item__<?php echo esc_attr( $key ); ?>" colspan="2"><?php echo esc_html( $label ); ?></th>
							<?php else : ?>
								<th class="line-item__<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></th>
							<?php endif; ?>
						<?php endforeach; ?>
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
									printf( '<input class="line-item__name-input" type="text" name="items[%s][name]" value="%s" readonly/>', esc_attr( $item_key ), esc_attr( $item->name ) );
									printf( '<textarea class="line-item__description-input" name="items[%s][description]" placeholder="%s" maxlength="160">%s</textarea>', esc_attr( $item_key ), esc_attr__( 'Description', 'wp-ever-accounting' ), esc_textarea( $item->get_description ) );
									?>
									<?php if ( $item->taxable ) : ?>
										<select name="items[<?php echo esc_attr( $item_key ); ?>][taxes][]" class="line-item__taxes-input eac_select2 " data-action="eac_json_search" data-type="tax" data-placeholder="<?php esc_attr_e( 'Select a tax', 'wp-ever-accounting' ); ?>" multiple>
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
											printf( '<div class="eac-input-group"><span class="addon">%s</span> <input class="line-item__price-input" type="number" name="items[%s][price]" value="%s" placeholder="%s" /></div>', esc_html( eac_get_currency_symbol( $document->currency_code ) ), esc_attr( $item_key ), esc_attr( $item->price ), esc_attr__( 'Price', 'wp-ever-accounting' ) );
											break;
										case 'quantity':
											printf( '<input class="line-item__quantity-input" type="number" name="items[%s][quantity]" value="%s" placeholder="%s" />', esc_attr( $item_key ), esc_attr( $item->quantity ), esc_attr__( 'Quantity', 'wp-ever-accounting' ) );
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
							<div class="eac-input-group">
								<select class="add-line-item eac_select2" data-action="eac_json_search" data-type="item" name="items[<?php echo esc_attr( PHP_INT_MAX ); ?>][item_id]" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
								<a class="button" href="<?php echo esc_url( eac_action_url( 'action=get_html_response&html_type=edit_item' ) ); ?>" title="<?php esc_attr_e( 'Add New Item', 'wp-ever-accounting' ); ?>">
									<span class="dashicons dashicons-plus"></span>
								</a>
							</div>
						</td>
						<td class="line-item tw-text-right" colspan="<?php echo count( $columns ) - 1; ?>">
							<a class="button calculate_totals" type="button"><?php esc_html_e( 'Recalculate', 'wp-ever-accounting' ); ?></a>
						</td>
					</tr>
					</tbody>
				</table>
			</div><!-- .document-items -->

			<div class="eac-document-form__section document-totals">
				<div>
				</div>
				<div class="eac-document__totals">
					<div class="document-totals__subtotal">
						<span><?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?></span>
						<span><?php echo esc_html( $document->formatted_items_total ); ?></span>
					</div>
					<?php if ( ! empty( absint( $document->discount_total ) ) ) : ?>
						<div class="document-totals__discount">
							<span><?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?></span>
							<span>&minus;<?php echo esc_html( $document->formatted_discount_total ); ?></span>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( absint( $document->shipping_total ) ) ) : ?>
						<div class="document-totals__shipping">
							<span><?php esc_html_e( 'Shipping', 'wp-ever-accounting' ); ?></span>
							<span><?php echo esc_html( $document->formatted_shipping_total ); ?></span>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( absint( $document->fees_total ) ) ) : ?>
						<div class="document-totals__fees">
							<span><?php esc_html_e( 'Fees', 'wp-ever-accounting' ); ?></span>
							<span><?php echo esc_html( $document->formatted_fees_total ); ?></span>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( absint( $document->tax_total ) ) ) : ?>
						<?php if ( 'single' === get_option( 'eac_tax_display_totals' ) ) : ?>
							<div class="document-totals__tax">
								<span><?php esc_html_e( 'Tax', 'wp-ever-accounting' ); ?></span>
								<span><?php echo esc_html( $document->formatted_tax_total ); ?></span>
							</div>
						<?php else : ?>
							<?php foreach ( $document->formatted_itemized_taxes as $label => $amount ) : ?>
								<div class="document-totals__itemized-tax">
									<span><?php echo esc_html( $label ); ?></span>
									<span><?php echo esc_html( $amount ); ?></span>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					<?php endif; ?>
					<div class="document-totals__total">
						<span><?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?></span>
						<span><?php echo esc_html( $document->formatted_total ); ?></span>
					</div>
				</div>
			</div><!-- .document-totals -->

		</div>

		<div class="eac-modal" id="edit-billing-details" aria-hidden="true">
			<div class="eac-modal__overlay" tabindex="-1">
				<div class="eac-modal__container" role="dialog" aria-modal="true" aria-labelledby="edit-billing-details">
					<header class="eac-modal__header">
						<h3 class="eac-modal__title">
							<?php esc_html_e( 'Edit Billing Details', 'wp-ever-accounting' ); ?>
						</h3>
						<button class="eac-modal__close" data-micromodal-close="edit-billing-details"></button>
					</header>
					<div class="eac-modal__body tw-box-sizing:border-box tw-flex-wrap:wrap tw-gap-x-[20px] tw-grid tw-grid-cols-2 tw-justify-between tw-items-self-start tw-content-flex-start">
						<?php
						$fields = array(
							array(
								'type'          => 'hidden',
								'id'            => 'billing_name',
								'label'         => __( 'Name', 'wp-ever-accounting' ),
								'value'         => $document->billing_name,
								'placeholder'   => __( 'John Doe', 'wp-ever-accounting' ),
								'wrapper_class' => 'tw-mt-0',
							),
							array(
								'type'          => 'text',
								'id'            => 'billing_company',
								'label'         => __( 'Company', 'wp-ever-accounting' ),
								'value'         => $document->billing_company,
								'placeholder'   => __( 'XYZ Corp', 'wp-ever-accounting' ),
								'wrapper_class' => 'tw-mt-0',
							),
							array(
								'type'          => 'text',
								'id'            => 'billing_address_1',
								'label'         => __( 'Address 1', 'wp-ever-accounting' ),
								'value'         => $document->billing_address_1,
								'placeholder'   => __( '123 Main St', 'wp-ever-accounting' ),
								'wrapper_class' => 'tw-mt-0',
							),
							array(
								'type'          => 'text',
								'id'            => 'billing_address_2',
								'label'         => __( 'Address 2', 'wp-ever-accounting' ),
								'value'         => $document->billing_address_2,
								'placeholder'   => __( 'Suite 100', 'wp-ever-accounting' ),
								'wrapper_class' => 'tw-mt-0',
							),
							array(
								'type'          => 'text',
								'id'            => 'billing_city',
								'label'         => __( 'City', 'wp-ever-accounting' ),
								'value'         => $document->billing_city,
								'placeholder'   => __( 'New York', 'wp-ever-accounting' ),
								'wrapper_class' => 'tw-mt-0',
							),
							array(
								'type'          => 'text',
								'id'            => 'billing_state',
								'label'         => __( 'State', 'wp-ever-accounting' ),
								'value'         => $document->billing_state,
								'placeholder'   => __( 'NY', 'wp-ever-accounting' ),
								'wrapper_class' => 'tw-mt-0',
							),
							array(
								'type'          => 'text',
								'id'            => 'billing_postcode',
								'label'         => __( 'Postcode', 'wp-ever-accounting' ),
								'value'         => $document->billing_postcode,
								'placeholder'   => __( '10001', 'wp-ever-accounting' ),
								'wrapper_class' => 'tw-mt-0',
							),
							array(
								'type'          => 'select',
								'id'            => 'billing_country',
								'label'         => __( 'Country', 'wp-ever-accounting' ),
								'options'       => \EverAccounting\Utilities\I18n::get_countries(),
								'value'         => $document->billing_country,
								'placeholder'   => __( 'Select a country', 'wp-ever-accounting' ),
								'wrapper_class' => 'tw-mt-0',
							),
							array(
								'type'          => 'text',
								'id'            => 'billing_phone',
								'label'         => __( 'Phone', 'wp-ever-accounting' ),
								'value'         => $document->billing_phone,
								'placeholder'   => __( '555-555-5555', 'wp-ever-accounting' ),
								'wrapper_class' => 'tw-mt-0',
							),
							array(
								'type'          => 'email',
								'id'            => 'billing_email',
								'label'         => __( 'Email', 'wp-ever-accounting' ),
								'value'         => $document->billing_email,
								'placeholder'   => 'john@doe.com',
								'wrapper_class' => 'tw-mt-0',
							),
							// vat number.
							array(
								'type'          => 'text',
								'id'            => 'billing_vat_number',
								'label'         => __( 'VAT Number', 'wp-ever-accounting' ),
								'value'         => $document->billing_vat_number,
								'placeholder'   => __( 'VAT Number', 'wp-ever-accounting' ),
								'wrapper_class' => 'tw-mt-0',
							),
							// vat exempt.
							array(
								'type'          => 'select',
								'id'            => 'billing_vat_exempt',
								'label'         => __( 'VAT Exempt', 'wp-ever-accounting' ),
								'value'         => filter_var( $document->billing_vat_exempt, FILTER_VALIDATE_BOOLEAN ) ? 'yes' : '',
								'options'       => array(
									'yes' => __( 'Yes', 'wp-ever-accounting' ),
									''    => __( 'No', 'wp-ever-accounting' ),
								),
								'wrapper_class' => 'tw-mt-0',
								'input_class'   => 'trigger-update',
							),
						);
						foreach ( $fields as $field ) {
							eac_form_group( $field );
						}
						?>
					</div>
					<footer class="eac-modal__footer">
						<button type="button" class="button button-primary"><?php esc_html_e( 'Save', 'wp-ever-accounting' ); ?></button>
						<button type="button" class="button" data-micromodal-close="edit-billing-details"><?php esc_html_e( 'Cancel', 'wp-ever-accounting' ); ?></button>
					</footer>
				</div>
			</div>
		</div><!-- .eac-modal -->

		<?php wp_nonce_field( 'eac_edit_invoice' ); ?>
		<input type="hidden" name="billing_name"
		<input type="hidden" name="id" value="<?php echo esc_attr( $document->id ); ?>"/>
		<input type="hidden" name="action" value="eac_edit_invoice"/>
	</form>
<?php
