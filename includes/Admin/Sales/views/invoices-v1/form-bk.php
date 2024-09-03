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

$document = new \EverAccounting\Models\Invoice();

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
		<div class="eac-poststuff">

			<div class="column-1">
				<div class="eac-panel eac-p-0">

					<div class="eac-document-form__section document-info">
						<div class="eac-document-info__column">

							<div class="eac-form-group tw-mt-0">
								<label for="contact_id"><?php esc_html_e( 'Customer', 'wp-ever-accounting' ); ?></label>
								<div class="eac-input-group">
									<select name="contact_id" id="contact_id" class="eac_select2" data-action="eac_json_search" data-type="customer" data-placeholder="<?php esc_attr_e( 'Select a customer', 'wp-ever-accounting' ); ?>">
										<option></option>
									</select>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=customers&add=yes' ) ); ?>" target="_blank" class="addon" title="<?php esc_attr_e( 'Add New Customer', 'wp-ever-accounting' ); ?>">
										<span class="dashicons dashicons-plus"></span>
									</a>
								</div>
							</div>

							<?php if ( $document->contact_id ) : ?>
								<h3>
									<?php esc_html_e( 'Billing', 'ever-accounting' ); ?>
									<a href="#" class="edit_billing_address"><?php esc_html_e( 'Edit', 'ever-accounting' ); ?></a>
									<span><a href="#" class="load_billing_address" style="display: none;"><?php esc_html_e( 'Load billing address', 'ever-accounting' ); ?></a></span>
								</h3>

								<div class="tw-box-sizing:border-box tw-flex-wrap:wrap tw-gap-x-[20px] tw-grid tw-grid-cols-2 tw-justify-between tw-items-self-start tw-content-flex-start">
									<?php
									$fields = array(
										array(
											'type'        => 'text',
											'id'          => 'billing_name',
											'label'       => __( 'Name', 'wp-ever-accounting' ),
											'value'       => $document->billing_name,
											'placeholder' => __( 'John Doe', 'wp-ever-accounting' ),
											'wrapper_class' => 'tw-mt-0',
										),
										array(
											'type'        => 'text',
											'id'          => 'billing_company',
											'label'       => __( 'Company', 'wp-ever-accounting' ),
											'value'       => $document->billing_company,
											'placeholder' => __( 'XYZ Corp', 'wp-ever-accounting' ),
											'wrapper_class' => 'tw-mt-0',
										),
										array(
											'type'        => 'text',
											'id'          => 'billing_address_1',
											'label'       => __( 'Address 1', 'wp-ever-accounting' ),
											'value'       => $document->billing_address_1,
											'placeholder' => __( '123 Main St', 'wp-ever-accounting' ),
											'wrapper_class' => 'tw-mt-0',
										),
										array(
											'type'        => 'text',
											'id'          => 'billing_address_2',
											'label'       => __( 'Address 2', 'wp-ever-accounting' ),
											'value'       => $document->billing_address_2,
											'placeholder' => __( 'Suite 100', 'wp-ever-accounting' ),
											'wrapper_class' => 'tw-mt-0',
										),
										array(
											'type'        => 'text',
											'id'          => 'billing_city',
											'label'       => __( 'City', 'wp-ever-accounting' ),
											'value'       => $document->billing_city,
											'placeholder' => __( 'New York', 'wp-ever-accounting' ),
											'wrapper_class' => 'tw-mt-0',
										),
										array(
											'type'        => 'text',
											'id'          => 'billing_state',
											'label'       => __( 'State', 'wp-ever-accounting' ),
											'value'       => $document->billing_state,
											'placeholder' => __( 'NY', 'wp-ever-accounting' ),
											'wrapper_class' => 'tw-mt-0',
										),
										array(
											'type'        => 'text',
											'id'          => 'billing_postcode',
											'label'       => __( 'Postcode', 'wp-ever-accounting' ),
											'value'       => $document->billing_postcode,
											'placeholder' => __( '10001', 'wp-ever-accounting' ),
											'wrapper_class' => 'tw-mt-0',
										),
										array(
											'type'        => 'select',
											'id'          => 'billing_country',
											'label'       => __( 'Country', 'wp-ever-accounting' ),
											'options'     => \EverAccounting\Utilities\I18n::get_countries(),
											'value'       => $document->billing_country,
											'placeholder' => __( 'Select a country', 'wp-ever-accounting' ),
											'wrapper_class' => 'tw-mt-0',
										),
										array(
											'type'        => 'text',
											'id'          => 'billing_phone',
											'label'       => __( 'Phone', 'wp-ever-accounting' ),
											'value'       => $document->billing_phone,
											'placeholder' => __( '555-555-5555', 'wp-ever-accounting' ),
											'wrapper_class' => 'tw-mt-0',
										),
										array(
											'type'        => 'email',
											'id'          => 'billing_email',
											'label'       => __( 'Email', 'wp-ever-accounting' ),
											'value'       => $document->billing_email,
											'placeholder' => 'john@doe.com',
											'wrapper_class' => 'tw-mt-0',
										),
										// vat number.
										array(
											'type'        => 'text',
											'id'          => 'billing_vat_number',
											'label'       => __( 'VAT Number', 'wp-ever-accounting' ),
											'value'       => $document->billing_vat_number,
											'placeholder' => __( 'VAT Number', 'wp-ever-accounting' ),
											'wrapper_class' => 'tw-mt-0',
										),
										// vat exempt.
										array(
											'type'        => 'select',
											'id'          => 'billing_vat_exempt',
											'label'       => __( 'VAT Exempt', 'wp-ever-accounting' ),
											'value'       => filter_var( $document->billing_vat_exempt, FILTER_VALIDATE_BOOLEAN ) ? '1' : '0',
											'options'     => array(
												'1' => __( 'Yes', 'wp-ever-accounting' ),
												'0' => __( 'No', 'wp-ever-accounting' ),
											),
											'wrapper_class' => 'tw-mt-0',
											'input_class' => 'trigger-update',
										),
									);
									foreach ( $fields as $field ) {
										eac_form_field( $field );
									}
									?>
								</div>
								<div class="tw-box-sizing:border-box tw-flex-wrap:wrap tw-gap-x-[20px] tw-grid tw-grid-cols-2 tw-justify-between tw-items-self-start tw-content-flex-start">
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
							<?php endif; ?>
						</div>
						<div class="eac-document-info__column tw-box-sizing:border-box tw-flex-wrap:wrap tw-gap-x-[20px] tw-grid tw-grid-cols-2 tw-justify-between tw-items-self-start tw-content-flex-start">
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
								<label for="invoice_number"><?php esc_html_e( 'Invoice Number', 'wp-ever-accounting' ); ?></label>
								<input type="text" name="invoice_number" id="invoice_number" placeholder="INV-0001" value="<?php echo esc_attr( $document->number ); ?>"/>
							</div>
							<div class="eac-form-group tw-mt-0">
								<label for="reference"><?php esc_html_e( 'Reference', 'wp-ever-accounting' ); ?></label>
								<input type="text" name="reference" id="reference" placeholder="REF-0001" value="<?php echo esc_attr( $document->reference ); ?>"/>
							</div>
							<div class="eac-form-group tw-mt-0">
								<label for="discount"><?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?></label>
								<input type="number" name="discount" id="discount" placeholder=".05" value="<?php echo esc_attr( $document->discount_amount ); ?>"/>
							</div>
							<div class="eac-form-group tw-mt-0">
								<label for="discount_type"><?php esc_html_e( 'Discount Type', 'wp-ever-accounting' ); ?></label>
								<select name="discount_type" id="discount_type">
									<option value="fixed" <?php selected( 'fixed', $document->discount_type ); ?>><?php esc_html_e( 'Fixed Amount', 'wp-ever-accounting' ); ?></option>
									<option value="percentage" <?php selected( 'percentage', $document->discount_type ); ?>><?php esc_html_e( 'Percentage (%)', 'wp-ever-accounting' ); ?></option>
								</select>
							</div>
						</div>
					</div>

					<div class="eac-document-form__section document-items">
						<table class="eac-document-form__items">
							<thead>
							<tr>
								<?php foreach ( $columns as $key => $label ) : ?>
									<?php if ( 'item' === $key ) : ?>
										<th class="line-<?php echo esc_attr( $key ); ?>" colspan="2"><?php echo esc_html( $label ); ?></th>
									<?php else : ?>
										<th class="line-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></th>
									<?php endif; ?>
								<?php endforeach; ?>
							</thead>
							<tbody>
							<?php foreach ( $document->items as $item_key => $item ) : ?>
							<tr>
								<?php foreach ( $columns as $key => $label ) : ?>
									<?php if ( 'item' === $key ) : ?>
										<td class="line-<?php echo esc_attr( $key ); ?>-name" colspan="2">
											<?php
											printf( '<input type="hidden" name="items[%s][id]" value="%s"/>', esc_attr( $item_key ), esc_attr( $item->id ) );
											printf( '<input type="hidden" name="items[%s][item_id]" value="%s"/>', esc_attr( $item_key ), esc_attr( $item->item_id ) );
											printf( '<input class="item-name" type="text" name="items[%s][name]" value="%s" readonly/>', esc_attr( $item_key ), esc_attr( $item->name ) );
											printf( '<textarea class="item-description" name="items[%s][description]" placeholder="%s" maxlength="160">%s</textarea>', esc_attr( $item_key ), esc_attr__( 'Description', 'wp-ever-accounting' ), esc_textarea( $item->get_description ) );
											?>
										</td>
									<?php else : ?>
										<td class="line-<?php echo esc_attr( $key ); ?>">
											<?php
											switch ( $key ) {
												case 'price':
													printf( '<input class="line-price trigger-update" type="number" name="items[%s][price]" value="%s" placeholder="%s" />', esc_attr( $item_key ), esc_attr( $item->price ), esc_attr__( 'Price', 'wp-ever-accounting' ) );
													break;
												case 'quantity':
													printf( '<input class="line-quantity trigger-update" type="number" name="items[%s][quantity]" value="%s" placeholder="%s" />', esc_attr( $item_key ), esc_attr( $item->quantity ), esc_attr__( 'Quantity', 'wp-ever-accounting' ) );
													break;
												case 'tax':
													printf( '<select class="item-tax trigger-update eac-select2" name="items[%s][tax_ids]" data-action="eac_json_search" data-type="tax" multiple="multiple" data-placeholder="%s">', esc_attr( $item_key ), esc_attr__( 'Select tax', 'wp-ever-accounting' ) );
													foreach ( $item->taxes as $tax ) {
														printf( '<option value="%s" %s>%s</option>', esc_attr( $tax->tax_id ), selected( $tax->id, $tax->id, false ), esc_html( $tax->name ) );
													}
													echo '</select>';

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
								<td class="line-item" colspan="3">
									<div class="eac-input-group">
										<select class="trigger-update eac_select2" data-action="eac_json_search" data-type="item" name="items[<?php echo esc_attr( PHP_INT_MAX ); ?>][item_id]" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>">
											<option></option>
										</select>
										<a class="button" href="<?php echo esc_url( eac_action_url( 'action=get_html_response&html_type=edit_item' ) ); ?>" title="<?php esc_attr_e( 'Add New Item', 'wp-ever-accounting' ); ?>">
											<span class="dashicons dashicons-plus"></span>
										</a>
									</div>
								</td>
							</tr>
							</tbody>
						</table>
					</div>

					<?php // if ( $document->is_editable ) : ?>
					<div class="eac-document-form__section document-actions">
						<a class="button text-start add-line-item" type="button"><?php esc_html_e( 'Add Line Item', 'wp-ever-accounting' ); ?></a>
						<a class="button text-end calculate-totals" type="button"><?php esc_html_e( 'Recalculate', 'wp-ever-accounting' ); ?></a>
					</div>
					<?php // endif; ?>
					<div class="eac-document-form__section document-totals">
						<div>
						</div>
						<div class="eac-document__totals">
							<div>
								<span><?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?></span>
								<span><?php echo esc_html( $document->formatted_items_total ); ?></span>
							</div>
							<?php if ( ! empty( absint( $document->discount_total ) ) ) : ?>
								<div>
									<span><?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?></span>
									<span>&minus;<?php echo esc_html( $document->formatted_discount_total ); ?></span>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( absint( $document->shipping_total ) ) ) : ?>
								<div>
									<span><?php esc_html_e( 'Shipping', 'wp-ever-accounting' ); ?></span>
									<span><?php echo esc_html( $document->formatted_shipping_total ); ?></span>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( absint( $document->fees_total ) ) ) : ?>
								<div>
									<span><?php esc_html_e( 'Fees', 'wp-ever-accounting' ); ?></span>
									<span><?php echo esc_html( $document->formatted_fees_total ); ?></span>
								</div>
							<?php endif; ?>
							<?php if ( ! empty( absint( $document->tax_total ) ) ) : ?>
								<?php if ( 'single' === get_option( 'eac_tax_display_totals' ) ) : ?>
									<div>
										<span><?php esc_html_e( 'Tax', 'wp-ever-accounting' ); ?></span>
										<span><?php echo esc_html( $document->formatted_tax_total ); ?></span>
									</div>
								<?php else : ?>
									<?php foreach ( $document->formatted_itemized_taxes as $label => $amount ) : ?>
										<div>
											<span><?php echo esc_html( $label ); ?></span>
											<span><?php echo esc_html( $amount ); ?></span>
										</div>
									<?php endforeach; ?>
								<?php endif; ?>
							<?php endif; ?>
							<div>
								<span><?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?></span>
								<span><?php echo esc_html( $document->formatted_total ); ?></span>
							</div>
						</div>
					</div>
				</div>

			</div><!-- .column-1 -->

			<div class="column-2">
				<div class="eac-card">
					<div class="eac-card__header">
						<h2 class="eac-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
					</div>
					<div class="eac-card__footer">
						<?php if ( $document->exists() ) : ?>
							<input type="hidden" name="id" value="<?php echo esc_attr( $document->id ); ?>"/>
						<?php endif; ?>
						<input type="hidden" name="action" value="eac_edit_invoice"/>
						<?php wp_nonce_field( 'eac_edit_invoice' ); ?>
						<?php if ( $document->exists() ) : ?>
							<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-sales&tab=invoices&id=' . $document->id ) ), 'bulk-items' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
						<?php endif; ?>
						<?php if ( $document->exists() ) : ?>
							<button class="button button-primary"><?php esc_html_e( 'Update Invoice', 'wp-ever-accounting' ); ?></button>
						<?php else : ?>
							<button class="button button-primary eac-w-100"><?php esc_html_e( 'Add Invoice', 'wp-ever-accounting' ); ?></button>
						<?php endif; ?>
					</div>
				</div>
			</div><!-- .column-2 -->

		</div><!-- .eac-poststuff -->
	</form>
<?php
