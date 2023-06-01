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

$fields      = array(
	'billing' => array(
		array(
			'name'        => 'name',
			'label'       => __( 'Name', 'wp-ever-accounting' ),
			'type'        => 'text',
			'placeholder' => __( 'John Doe', 'wp-ever-accounting' ),
			'required'    => true,
		),
		array(
			'name'        => 'company',
			'label'       => __( 'Company', 'wp-ever-accounting' ),
			'type'        => 'text',
			'placeholder' => __( 'XYZ Corp', 'wp-ever-accounting' ),
		),
		array(
			'name'        => 'address_1',
			'label'       => __( 'Address 1', 'wp-ever-accounting' ),
			'type'        => 'text',
			'placeholder' => __( '123 Main St', 'wp-ever-accounting' ),
		),
		array(
			'name'        => 'address_2',
			'label'       => __( 'Address 2', 'wp-ever-accounting' ),
			'type'        => 'text',
			'placeholder' => __( 'Suite 100', 'wp-ever-accounting' ),
		),
		array(
			'name'        => 'city',
			'label'       => __( 'City', 'wp-ever-accounting' ),
			'type'        => 'text',
			'placeholder' => __( 'New York', 'wp-ever-accounting' ),
		),
		array(
			'name'        => 'state',
			'label'       => __( 'State', 'wp-ever-accounting' ),
			'type'        => 'text',
			'placeholder' => __( 'NY', 'wp-ever-accounting' ),
		),
		array(
			'name'        => 'postcode',
			'label'       => __( 'Postcode', 'wp-ever-accounting' ),
			'type'        => 'text',
			'placeholder' => __( '10001', 'wp-ever-accounting' ),
		),
		array(
			'name'        => 'country',
			'label'       => __( 'Country', 'wp-ever-accounting' ),
			'type'        => 'country',
			'placeholder' => __( 'Select a country', 'wp-ever-accounting' ),
		),
		array(
			'name'        => 'phone',
			'label'       => __( 'Phone', 'wp-ever-accounting' ),
			'type'        => 'text',
			'placeholder' => __( '123-456-7890', 'wp-ever-accounting' ),
		),
		array(
			'name'        => 'email',
			'label'       => __( 'Email', 'wp-ever-accounting' ),
			'type'        => 'text',
			'placeholder' => __( 'john@doe.com', 'wp-ever-accounting' ),
		),
		array(
			'name'        => 'vat_number',
			'label'       => __( 'VAT Number', 'wp-ever-accounting' ),
			'type'        => 'text',
			'placeholder' => __( '123456789', 'wp-ever-accounting' ),
		),
	),
);
$tax_enabled = $document->is_calculating_tax();
?>
<form id="eac-invoice-form" class="eac-invoice-form eac-document" method="post">

	<div class="eac-document__data header">
		<div class="eac-columns">
			<div class="eac-col-6">
				<?php
				eac_input_field(
					array(
						'name'        => 'contact_id',
						'label'       => __( 'Customer', 'wp-ever-accounting' ),
						'type'        => 'customer',
						'placeholder' => __( 'Select a customer', 'wp-ever-accounting' ),
						'value'       => $document->get_contact_id(),
						'input_class' => 'eac-select-contact',
						'required'    => true,
						'disabled'    => ! $document->is_editable(),
						'default'     => eac_get_input_var( 'customer_id' ),
						'suffix'      => sprintf(
							'<a class="button" href="%s" title="%s"><span class="dashicons dashicons-plus"></span></a>',
							esc_url( eac_action_url( 'action=get_html_response&html_type=edit_customer' ) ),
							__( 'Add Customer', 'wp-ever-accounting' )
						),
					)
				);
				?>
			</div>
			<div class="eac-col-6">
				<div class="eac-columns">
					<?php
					eac_input_field(
						array(
							'name'        => 'issued_at',
							'label'       => __( 'Issue Date', 'wp-ever-accounting' ),
							'type'        => 'date',
							'placeholder' => __( 'YYYY-MM-DD', 'wp-ever-accounting' ),
							'value'       => $document->get_issued_at( 'edit' ),
							'class'       => 'eac-col-6',
							'required'    => true,
							'disabled'    => ! $document->is_editable(),
						)
					);
					eac_input_field(
						array(
							'name'        => 'invoice_number',
							'label'       => __( 'Invoice Number', 'wp-ever-accounting' ),
							'type'        => 'text',
							'placeholder' => __( '123456789', 'wp-ever-accounting' ),
							'value'       => $document->get_document_number(),
							'readonly'    => true,
							'required'    => true,
							'disabled'    => ! $document->is_editable(),
							'class'       => 'eac-col-6',
						)
					);
					eac_input_field(
						array(
							'name'        => 'due_at',
							'label'       => __( 'Due Date', 'wp-ever-accounting' ),
							'type'        => 'date',
							'placeholder' => __( 'YYYY-MM-DD', 'wp-ever-accounting' ),
							'value'       => $document->get_due_at( 'edit' ),
							'class'       => 'eac-col-6',
							'disabled'    => ! $document->is_editable(),
							'required'    => true,
						)
					);
					eac_input_field(
						array(
							'name'        => 'order_number',
							'label'       => __( 'Order Number', 'wp-ever-accounting' ),
							'type'        => 'text',
							'placeholder' => __( '123456789', 'wp-ever-accounting' ),
							'value'       => $document->get_order_number( 'edit' ),
							'class'       => 'eac-col-6',
						)
					);
					eac_input_field(
						array(
							'name'        => 'currency_code',
							'label'       => __( 'Currency', 'wp-ever-accounting' ),
							'type'        => 'currency',
							'placeholder' => __( 'Select Currency', 'wp-ever-accounting' ),
							'value'       => $document->get_currency_code( 'edit' ),
							'class'       => 'eac-col-6',
							'required'    => true,
							'disabled'    => ! $document->is_editable(),
						)
					);
					?>
				</div>
			</div>
		</div>
	</div>

	<div class="eac-document__data items">
		<table cellpadding="0" cellspacing="0" class="eac-document__items">
			<thead class="eac-document__items-head">
			<tr>
				<th class="line-item" colspan="2"><?php esc_html_e( 'Item', 'wp-ever-accounting' ); ?></th>
				<th class="line-price" width="10%"><?php esc_html_e( 'Price', 'wp-ever-accounting' ); ?></th>
				<th class="line-quantity" width="10%"><?php esc_html_e( 'Quantity', 'wp-ever-accounting' ); ?></th>
				<?php if ( $tax_enabled ) : ?>
					<th class="line-taxes" width="20%"><?php esc_html_e( 'Taxes', 'wp-ever-accounting' ); ?></th>
				<?php endif; ?>
				<th class="line-subtotal" width="10%"><?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?></th>
				<th class="line-actions" width="1%">&nbsp;</th>
			</tr>
			</thead>
			<tbody class="eac-document__items-lines">
			<?php foreach ( $document->get_items() as $key => $item ) : ?>
				<tr class="eac-document__items-line" data-item_id="<?php echo esc_attr( $item->get_id() ); ?>">
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
							<select class="item-taxes" name="items[<?php echo esc_attr( $key ); ?>][tax_ids]" data-eac-select2="tax" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select taxes', 'wp-ever-accounting' ); ?>">
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
			<tr class="eac-document__items-line eac-document__items-new-line" style="<?php echo ( count( $document->get_items() ) > 0 ) ? 'display: none;' : ''; ?>">
				<td class="line-item" colspan="2">
					<div class="eac-field__group">
						<select class="select-new-item" data-eac-select2="product" data-placeholder="<?php esc_attr_e( 'Select line item', 'wp-ever-accounting' ); ?>">
							<option></option>
						</select>
						<a class="button" href="<?php echo esc_url( eac_action_url( 'action=get_html_response&html_type=edit_product' ) ); ?>" title="<?php esc_attr_e( 'Add New Product', 'wp-ever-accounting' ); ?>">
							<span class="dashicons dashicons-plus"></span>
						</a>
					</div>
				</td>
			</tr>
			</tbody>
		</table>
	</div>

	<div class="eac-document__data buttons">
		<button class="button add-line-item"><?php esc_html_e( 'Add Line Item', 'wp-ever-accounting' ); ?></button>
		<button class="button calculate-totals"><?php esc_html_e( 'Recalculate', 'wp-ever-accounting' ); ?></button>
	</div>

	<div class="eac-document__data totals">
		<table class="eac-document__totals">
			<tr>
				<th><?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?></th>
				<th>
					<?php echo esc_html( $document->get_formatted_subtotal() ); ?>
				</th>
			</tr>
			<?php if ( ! empty( $document->get_discount_total() ) ) : ?>
				<tr>
					<th><?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?></th>
					<th>
						<?php echo esc_html( eac_format_money( $document->get_discount_total(), $document->get_currency_code() ) ); ?>
					</th>
				</tr>
			<?php endif; ?>
			<?php if ( ! empty( $document->get_shipping_total() ) ) : ?>
				<tr>
					<th><?php esc_html_e( 'Shipping', 'wp-ever-accounting' ); ?></th>
					<th>
						<?php echo esc_html( eac_format_money( $document->get_shipping_total(), $document->get_currency_code() ) ); ?>
					</th>
				</tr>
			<?php endif; ?>
			<?php if ( ! empty( $document->get_fees_total() ) ) : ?>
				<tr>
					<th><?php esc_html_e( 'Fees', 'wp-ever-accounting' ); ?></th>
					<th>
						<?php echo esc_html( eac_format_money( $document->get_fees_total(), $document->get_currency_code() ) ); ?>
					</th>
				</tr>
			<?php endif; ?>
			<?php if ( $document->is_calculating_tax() ) : ?>
				<?php foreach ( $document->get_taxes() as $tax ) : ?>
					<tr>
						<th><?php echo esc_html( $tax->get_name() ); ?></th>
						<th>
							<?php echo esc_html( eac_format_money( $tax->get_amount(), $document->get_currency_code() ) ); ?>
						</th>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			<tr>
				<th><?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?></th>
				<th>
					<?php echo esc_html( eac_format_money( $document->get_total(), $document->get_currency_code() ) ); ?>
				</th>
			</tr>
		</table>
	</div>

	<div class="eac-document__data additional">
		<div class="eac-columns">
			<div class="eac-col-12">
				<?php
				eac_input_field(
					array(
						'type'        => 'textarea',
						'name'        => 'notes',
						'value'       => $document->get_document_note(),
						'label'       => __( 'Notes', 'wp-ever-accounting' ),
						'placeholder' => __( 'Enter notes', 'wp-ever-accounting' ),
					)
				);
				?>
			</div>
		</div>
	</div>
	<?php wp_nonce_field( 'eac_edit_invoice' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $document->get_id() ); ?>"/>
	<input type="hidden" name="type" value="<?php echo esc_attr( $document->get_type() ); ?>"/>
	<input type="hidden" name="action" value="eac_edit_invoice"/>
</form>
