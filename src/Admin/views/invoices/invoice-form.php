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
$customers   = eac_get_customers( array( 'include' => $document->get_contact_id() ) );
$currencies  = eac_get_currencies( array( 'limit' => - 1 ) );
$columns     = array(
	'item'     => __( 'Item', 'wp-ever-accounting' ),
	'price'    => __( 'Price', 'wp-ever-accounting' ),
	'quantity' => __( 'Quantity', 'wp-ever-accounting' ),
	'tax'      => __( 'Tax', 'wp-ever-accounting' ),
	'subtotal' => __( 'Subtotal', 'wp-ever-accounting' ),
	'actions'  => '&nbsp;',
);

// If not collecting tax, remove the tax column.
if ( ! $document->is_calculating_tax() ) {
	unset( $columns['tax'] );
}
?>
<form id="eac-invoice-form" class="eac-document-form eac-ajax-form" method="post" autocomplete="off">

	<div class="eac-panel">
		<div class="eac-document-form__section document-info eac-row px-4 py-4">
			<div class="eac-col-6">
				<?php
				eac_form_field(
					array(
						'type'        => 'select',
						'id'          => 'customer_id',
						'name'        => 'contact_id',
						'label'       => __( 'Customer', 'ever-accounting' ),
						'options'     => wp_list_pluck( $customers, 'formatted_name', 'id' ),
						'value'       => $document->get_contact_id(),
						'placeholder' => __( 'Select a customer', 'ever-accounting' ),
						'input_class' => 'eac-select2',
						'attrs'       => 'data-action=eac_json_search&data-type=customer',
						'required'    => true,
						'suffix'      => sprintf(
							'<a class="button" href="%s" title="%s"><span class="dashicons dashicons-plus"></span></a>',
							esc_url( eac_action_url( 'action=get_html_response&html_type=edit_customer' ) ),
							__( 'Add Customer', 'wp-ever-accounting' )
						),
					)
				);
				?>
				<h3>
					<?php esc_html_e( 'Billing', 'ever-accounting' ); ?>
					<a href="#" class="edit_billing_address"><?php esc_html_e( 'Edit', 'ever-accounting' ); ?></a>
					<span>
						<a href="#" class="load_billing_address"
						   style="display: none;"><?php esc_html_e( 'Load billing address', 'ever-accounting' ); ?></a>
					</span>
				</h3>

				<div class="eac-columns billing-fields d-none">
					<?php
					eac_form_fields(
						array(
							array(
								'type'        => 'text',
								'id'          => 'billing_name',
								'label'       => __( 'Name', 'wp-ever-accounting' ),
								'value'       => $document->get_billing_name(),
								'placeholder' => __( 'John Doe', 'wp-ever-accounting' ),
								'class'       => 'eac-col-6',
							),
							array(
								'type'        => 'text',
								'id'          => 'billing_company',
								'label'       => __( 'Company', 'wp-ever-accounting' ),
								'value'       => $document->get_billing_company(),
								'placeholder' => __( 'XYZ Corp', 'wp-ever-accounting' ),
								'class'       => 'eac-col-6',
							),
							array(
								'type'        => 'text',
								'id'          => 'billing_address_1',
								'label'       => __( 'Address 1', 'wp-ever-accounting' ),
								'value'       => $document->get_billing_address_1(),
								'placeholder' => __( '123 Main St', 'wp-ever-accounting' ),
								'class'       => 'eac-col-6',
							),
							array(
								'type'        => 'text',
								'id'          => 'billing_address_2',
								'label'       => __( 'Address 2', 'wp-ever-accounting' ),
								'value'       => $document->get_billing_address_2(),
								'placeholder' => __( 'Suite 100', 'wp-ever-accounting' ),
								'class'       => 'eac-col-6',
							),
							array(
								'type'        => 'text',
								'id'          => 'billing_city',
								'label'       => __( 'City', 'wp-ever-accounting' ),
								'value'       => $document->get_billing_city(),
								'placeholder' => __( 'New York', 'wp-ever-accounting' ),
								'class'       => 'eac-col-6',
							),
							array(
								'type'        => 'text',
								'id'          => 'billing_state',
								'label'       => __( 'State', 'wp-ever-accounting' ),
								'value'       => $document->get_billing_state(),
								'placeholder' => __( 'NY', 'wp-ever-accounting' ),
								'class'       => 'eac-col-6',
							),
							array(
								'type'        => 'text',
								'id'          => 'billing_postcode',
								'label'       => __( 'Postcode', 'wp-ever-accounting' ),
								'value'       => $document->get_billing_postcode(),
								'placeholder' => __( '10001', 'wp-ever-accounting' ),
								'class'       => 'eac-col-6',
							),
							array(
								'type'        => 'select',
								'id'          => 'billing_country',
								'label'       => __( 'Country', 'wp-ever-accounting' ),
								'options'     => eac_get_countries(),
								'value'       => $document->get_billing_country(),
								'placeholder' => __( 'Select a country', 'wp-ever-accounting' ),
								'class'       => 'eac-col-6',
							),
							array(
								'type'        => 'text',
								'id'          => 'billing_phone',
								'label'       => __( 'Phone', 'wp-ever-accounting' ),
								'value'       => $document->get_billing_phone(),
								'placeholder' => __( '555-555-5555', 'wp-ever-accounting' ),
								'class'       => 'eac-col-6',
							),
							array(
								'type'        => 'email',
								'id'          => 'billing_email',
								'label'       => __( 'Email', 'wp-ever-accounting' ),
								'value'       => $document->get_billing_email(),
								'placeholder' => 'john@doe.com',
								'class'       => 'eac-col-6',
							),
							// vat number.
							array(
								'type'        => 'text',
								'id'          => 'billing_vat_number',
								'label'       => __( 'VAT Number', 'wp-ever-accounting' ),
								'value'       => $document->get_billing_vat_number(),
								'placeholder' => __( 'VAT Number', 'wp-ever-accounting' ),
								'class'       => 'eac-col-6',
							),
							// vat exempt.
							array(
								'type'        => 'select',
								'id'          => 'billing_vat_exempt',
								'label'       => __( 'VAT Exempt', 'wp-ever-accounting' ),
								'value'       => $document->get_vat_exempt(),
								'options'     => array(
									'1' => __( 'Yes', 'wp-ever-accounting' ),
									'0'  => __( 'No', 'wp-ever-accounting' ),
								),
								'class'       => 'eac-col-6',
								'input_class' => 'trigger-update',
							),
						)
					);
					?>
				</div>

				<div class="eac-columns billing-data">
					<div class="eac-col-6">
						<?php
						if ( $document->get_formatted_billing_address() ) {
							echo $document->get_formatted_billing_address();
						} else {
							echo '<p><strong>' . esc_html__( 'Address:', 'wp-ever-accounting' ) . '</strong> ' . esc_html__( 'No address set.', 'wp-ever-accounting' ) . '</p>';
						}
						?>
					</div>
					<div class="eac-col-6">
						<?php
						if ( $document->get_billing_phone() ) {
							echo '<p><strong>' . esc_html__( 'Phone:', 'wp-ever-accounting' ) . '</strong> ' . wp_kses_post( eac_make_phone_clickable( $document->get_billing_phone() ) ) . '</p>';
						}
						if ( $document->get_billing_email() ) {
							echo '<p><strong>' . esc_html__( 'Email:', 'wp-ever-accounting' ) . '</strong> ' . wp_kses_post( '<a href="mailto:' . $document->get_billing_email() . '">' . $document->get_billing_email() . '</a>' ) . '</p>';
						}
						if ( $document->get_billing_vat_number() ) {
							echo '<p><strong>' . esc_html__( 'VAT:', 'wp-ever-accounting' ) . '</strong> ' . esc_html( $document->get_billing_vat_number() ) . '</p>';
						}
						?>
					</div>
				</div>
			</div>
			<div class="eac-col-6">
				<div class="eac-row document-data">
					<?php
					eac_form_field(
						array(
							'data_type'   => 'date',
							'id'          => 'issued_at',
							'name'        => 'issued_at',
							'label'       => __( 'Issue Date', 'ever-accounting' ),
							'placeholder' => 'YYYY-MM-DD',
							'value'       => $document->get_issue_date(),
							'required'    => true,
							'class'       => 'eac-col-6',
						)
					);
					eac_form_field(
						array(
							'data_type'   => 'date',
							'id'          => 'due_at',
							'name'        => 'due_at',
							'label'       => __( 'Due Date', 'ever-accounting' ),
							'placeholder' => 'YYYY-MM-DD',
							'value'       => $document->get_due_date(),
							'required'    => true,
							'class'       => 'eac-col-6',
						)
					);
					eac_form_field(
						array(
							'id'          => 'document_number',
							'name'        => 'number',
							'label'       => __( 'Invoice Number', 'ever-accounting' ),
							'placeholder' => 'INV-0001',
							'value'       => $document->get_number(),
							'required'    => true,
							'class'       => 'eac-col-6',
						)
					);
					eac_form_field(
						array(
							'id'          => 'reference',
							'label'       => __( 'Reference', 'ever-accounting' ),
							'placeholder' => 'REF-0001',
							'value'       => $document->get_reference(),
							'class'       => 'eac-col-6',
							// limit to 20 characters.
							'attrs'       => array(
								'maxlength' => 15,
							),
						)
					);
					// discount.
					eac_form_field(
						array(
							'data_type'   => 'decimal',
							'id'          => 'discount_amount',
							'label'       => __( 'Discount', 'ever-accounting' ),
							'placeholder' => '0.00',
							'value'       => $document->get_discount_amount(),
							'class'       => 'eac-col-6',
							'input_class' => 'trigger-update',
						)
					);
					eac_form_field(
						array(
							'id'          => 'discount_type',
							'name'        => 'discount_type',
							'type'        => 'select',
							'label'       => __( 'Discount Type', 'ever-accounting' ),
							'options'     => array(
								'percent' => __( 'Percent (%)', 'ever-accounting' ),
								'fixed'   => __( 'Fixed Amount', 'ever-accounting' ),
							),
							'value'       => $document->get_discount_type(),
							'class'       => 'eac-col-6',
							'input_class' => 'trigger-update',
						)
					);
					// currency.
					eac_form_field(
						array(
							'type'        => 'select',
							'id'          => 'currency_code',
							'label'       => __( 'Currency', 'ever-accounting' ),
							'placeholder' => __( 'Select a currency', 'ever-accounting' ),
							'value'       => $document->get_currency_code(),
							'options'     => wp_list_pluck( $currencies, 'formatted_name', 'code' ),
							'required'    => true,
							'input_class' => 'eac-select2 trigger-update',
							'class'       => 'eac-col-6',
							'style'       => count( eac_get_currencies() ) > 1 ? '' : 'display:none',
						)
					);
					eac_form_field(
						array(
							'data_type'   => 'decimal',
							'name'        => 'exchange_rate',
							'label'       => __( 'Exchange Rate', 'wp-ever-accounting' ),
							'placeholder' => '1.00',
							'value'       => 1,
							'required'    => true,
							'class'       => 'eac-col-6 display-none',
							'prefix'      => sprintf( '1 %s =', eac_get_base_currency() ),
							'suffix'      => sprintf( '%s', $document->get_currency_code() ),
							'style'       => $document->get_currency_code() === eac_get_base_currency() ? 'display:none' : '',
							'input_class' => 'trigger-update',
						)
					);
					?>
				</div>
			</div>
		</div>
		<div class="eac-document-form__section document-items">
			<table class="eac-document-form__items">
				<thead>
				<tr>
					<?php foreach ( $columns as $key => $label ) : ?>
						<?php if ( 'item' === $key ) : ?>
							<th class="line-<?php echo esc_attr( $key ); ?>"
								colspan="2"><?php echo esc_html( $label ); ?></th>
						<?php else : ?>
							<th class="line-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></th>
						<?php endif; ?>
					<?php endforeach; ?>
				</thead>
				<tbody>
				<?php foreach ( $document->get_items() as $item_key => $item ) : ?>
				<tr>
					<?php foreach ( $columns as $key => $label ) : ?>
						<?php if ( 'item' === $key ) : ?>
							<td class="line-<?php echo esc_attr( $key ); ?>-name" colspan="2">
								<?php
								echo sprintf( '<input type="hidden" name="items[%s][id]" value="%s"/>', esc_attr( $item_key ), esc_attr( $item->get_id() ) );
								echo sprintf( '<input type="hidden" name="items[%s][item_id]" value="%s"/>', esc_attr( $item_key ), esc_attr( $item->get_item_id() ) );
								echo sprintf( '<input class="item-name" type="text" name="items[%s][name]" value="%s" readonly/>', esc_attr( $item_key ), esc_attr( $item->get_name() ) );
								echo sprintf( '<textarea class="item-description" name="items[%s][description]" placeholder="%s" maxlength="160">%s</textarea>', esc_attr( $item_key ), esc_attr__( 'Description', 'wp-ever-accounting' ), esc_textarea( $item->get_description() ) );
								?>
							</td>
						<?php else : ?>
							<td class="line-<?php echo esc_attr( $key ); ?>">
								<?php
								switch ( $key ) {
									case 'price':
										echo sprintf( '<input class="line-price trigger-update" type="number" name="items[%s][price]" value="%s" placeholder="%s" />', esc_attr( $item_key ), esc_attr( $item->get_price() ), esc_attr__( 'Price', 'wp-ever-accounting' ) );
										break;
									case 'quantity':
										echo sprintf( '<input class="line-quantity trigger-update" type="number" name="items[%s][quantity]" value="%s" placeholder="%s" />', esc_attr( $item_key ), esc_attr( $item->get_quantity() ), esc_attr__( 'Quantity', 'wp-ever-accounting' ) );
										break;
									case 'tax':
										echo sprintf( '<select class="item-tax trigger-update eac-select2" name="items[%s][tax_ids]" data-action="eac_json_search" data-type="tax" multiple="multiple" data-placeholder="%s">', esc_attr( $item_key ), esc_attr__( 'Select tax', 'wp-ever-accounting' ) );
										foreach ( $item->get_taxes() as $tax ) {
											printf( '<option value="%s" %s>%s</option>', esc_attr( $tax->get_tax_id() ), selected( $tax->get_id(), $tax->get_id(), false ), esc_html( $tax->get_name() ) );
										}
										echo '</select>';

										break;
									case 'subtotal':
										echo esc_html( eac_format_money( $item->get_subtotal(), $document->get_currency_code() ) );
										break;
									case 'actions':
										echo '<a href="#" class="remove-line-item"><span class="dashicons dashicons-trash"></span></a>';
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
					<td class="line-item" colspan="2">
						<div class="eac-form-field__group">
							<select class="trigger-update eac-select2" data-action="eac_json_search" data-type="item" name="items[<?php echo PHP_INT_MAX; ?>][item_id]" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>">
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
		<?php if ( $document->is_editable() ) : ?>
			<div class="eac-document-form__section document-actions bb px-4 py-2">
				<a class="button text-start add-line-item" type="button"><?php esc_html_e( 'Add Line Item', 'wp-ever-accounting' ); ?></a>
				<a class="button text-end calculate-totals" type="button"><?php esc_html_e( 'Recalculate', 'wp-ever-accounting' ); ?></a>
			</div>
		<?php endif; ?>

		<div class="eac-document-form__section document-totals">
			<div>
			</div>
			<div class="eac-document__totals">
				<div>
					<span><?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?></span>
					<span><?php echo esc_html( $document->get_formatted_items_total() ); ?></span>
				</div>
				<?php if ( ! empty( absint( $document->get_discount_total() ) ) ) : ?>
					<div>
						<span><?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?></span>
						<span>&minus;<?php echo esc_html( $document->get_formatted_discount_total() ); ?>
						</span>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( absint( $document->get_shipping_total() ) ) ) : ?>
					<div>
						<span><?php esc_html_e( 'Shipping', 'wp-ever-accounting' ); ?></span>
						<span><?php echo esc_html( $document->get_formatted_shipping_total() ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( absint( $document->get_fees_total() ) ) ) : ?>
					<div>
						<span><?php esc_html_e( 'Fees', 'wp-ever-accounting' ); ?></span>
						<span><?php echo esc_html( $document->get_formatted_fees_total() ); ?></span>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( absint( $document->get_tax_total() ) ) ) : ?>
					<?php if ( 'single' === get_option( 'eac_tax_display_totals' ) ) : ?>
						<div>
							<span><?php esc_html_e( 'Tax', 'wp-ever-accounting' ); ?></span>
							<span><?php echo esc_html( $document->get_formatted_tax_total() ); ?></span>
						</div>
					<?php else : ?>
						<?php foreach ( $document->get_formatted_itemized_taxes() as $label => $amount ) : ?>
							<div>
								<span><?php echo esc_html( $label ); ?></span>
								<span><?php echo esc_html( $amount ); ?></span>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endif; ?>
				<div>
					<span><?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?></span>
					<span><?php echo esc_html( $document->get_formatted_total() ); ?></span>
				</div>
			</div>
		</div>
	</div>

	<div class="eac-panel">
		<?php
		eac_form_field(
			array(
				'id'          => 'note',
				'label'       => __( 'Note', 'wp-ever-accounting' ),
				'type'        => 'textarea',
				'value'       => $document->get_note(),
				'placeholder' => __( 'Enter a note', 'wp-ever-accounting' ),
				'rows'        => 5,
			)
		)
		?>
	</div>

	<?php wp_nonce_field( 'eac_edit_invoice' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $document->get_id() ); ?>"/>
	<input type="hidden" name="type" value="<?php echo esc_attr( $document->get_type() ); ?>"/>
	<input type="hidden" name="action" value="eac_edit_invoice"/>
</form>
