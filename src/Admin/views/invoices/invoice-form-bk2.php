<?php
/**
 * View: Invoice Form
 *
 * @since    1.1.6
 * @package     EverAccounting
 * @subpackage  Admin/View/Vendors
 * @var Invoice $invoice Invoice object.
 */

use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit();

$fields  = array(
	'billing'  => array(
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
			'name'        => 'street',
			'label'       => __( 'Street', 'wp-ever-accounting' ),
			'type'        => 'text',
			'placeholder' => __( '123 Main St', 'wp-ever-accounting' ),
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
			'name'        => 'zip',
			'label'       => __( 'Zip', 'wp-ever-accounting' ),
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
		array(
			'name'        => 'tax_number',
			'label'       => __( 'Tax Number', 'wp-ever-accounting' ),
			'type'        => 'text',
			'placeholder' => __( '123456789', 'wp-ever-accounting' ),
		),
	),
	'shipping' => array(
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
			'name'        => 'street',
			'label'       => __( 'Street', 'wp-ever-accounting' ),
			'type'        => 'text',
			'placeholder' => __( '123 Main St', 'wp-ever-accounting' ),
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
			'name'        => 'zip',
			'label'       => __( 'Zip', 'wp-ever-accounting' ),
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
	),
);
$columns = array(
	'actions'  => '&nbsp;',
	'item'     => __( 'Item', 'wp-ever-accounting' ),
	'quantity' => __( 'Quantity', 'wp-ever-accounting' ),
	'tax'      => __( 'Tax', 'wp-ever-accounting' ),
	'price'    => __( 'Price', 'wp-ever-accounting' ),
	'subtotal' => __( 'Subtotal', 'wp-ever-accounting' ),
);
?>

<form id="eac-document" class="eac-document">
	<div class="eac-card">
		<div class="eac-card__header">
			<div class="eac-card__title"><?php esc_html_e( 'Invoice details', 'wp-ever-accounting' ); ?></div>
		</div>
		<div class="eac-card__section">
			<div class="eac-columns">
				<div class="eac-document__customer-data eac-col-6">
					<div class="eac-columns">
						<div class="eac-col-12">
							<?php
							eac_input_field(
								array(
									'name'        => 'contact_id',
									'label'       => __( 'Customer', 'wp-ever-accounting' ),
									'type'        => 'customer',
									'placeholder' => __( 'Select a customer', 'wp-ever-accounting' ),
									'value'       => $invoice->get_contact_id(),
									'input_class' => 'eac-select-contact',
									'required'    => true,
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
							<h3>
								<?php esc_html_e( 'Billing', 'wp-ever-accounting' ); ?>
								<a href="#" class="eac-document__edit-address"><?php esc_html_e( 'Edit', 'wp-ever-accounting' ); ?></a>
							</h3>
							<div class="eac-document__address">
								<?php
								// Display values.
								if ( $invoice->get_billing_address() ) {
									echo '<p>' . wp_kses( $invoice->get_billing_address(), array( 'br' => array() ) ) . '</p>';
								} else {
									echo '<p class="none_set"><strong>' . esc_html__( 'Address:', 'wp-ever-accounting' ) . '</strong> ' . esc_html__( 'No billing address set.', 'wp-ever-accounting' ) . '</p>';
								}
								?>
							</div>
							<div class="eac-document__address-editor" style="display: none;">
								<?php foreach ( $fields['billing'] as $field ) : ?>
									<?php
									$getter = "get_billing_{$field['name']}";
									eac_input_field(
										array_merge(
											$field,
											array(
												'name'  => "billing_{$field['name']}",
												'value' => is_callable( array( $invoice, $getter ) ) ? $invoice->$getter( 'edit' ) : '',
											)
										)
									);
									?>
								<?php endforeach; ?>
								<?php do_action( $invoice->get_hook_prefix() . '_after_billing_fields', $invoice ); ?>
							</div>
						</div>

						<div class="eac-col-6">
							<h3>
								<?php esc_html_e( 'Shipping', 'wp-ever-accounting' ); ?>
								<a href="#" class="eac-document__edit-address"><?php esc_html_e( 'Edit', 'wp-ever-accounting' ); ?></a>
							</h3>
							<div class="eac-document__address">
								<?php
								// Display values.
								if ( $invoice->get_shipping_address() ) {
									echo '<p>' . wp_kses( $invoice->get_shipping_address(), array( 'br' => array() ) ) . '</p>';
								} else {
									echo '<p class="none_set"><strong>' . esc_html__( 'Address:', 'wp-ever-accounting' ) . '</strong> ' . esc_html__( 'No shipping address set.', 'wp-ever-accounting' ) . '</p>';
								}
								?>
							</div>
							<div class="eac-document__address-editor" style="display: none;">
								<?php foreach ( $fields['shipping'] as $field ) : ?>
									<?php
									$getter = "get_shipping_{$field['name']}";
									eac_input_field(
										array_merge(
											$field,
											array(
												'name'  => "shipping_{$field['name']}",
												'value' => is_callable( array( $invoice, $getter ) ) ? $invoice->$getter( 'edit' ) : '',
											)
										)
									);
									?>
								<?php endforeach; ?>
								<?php do_action( $invoice->get_hook_prefix() . '_after_shipping_fields', $invoice ); ?>
							</div>
						</div>
					</div>
				</div>
				<div class="eac-document__document-data eac-col-6">
					<div class="eac-columns">
						<?php
						eac_input_field(
							array(
								'name'        => 'invoice_number',
								'label'       => __( 'Invoice Number', 'wp-ever-accounting' ),
								'type'        => 'text',
								'placeholder' => __( '123456789', 'wp-ever-accounting' ),
								'value'       => $invoice->exists() ? $invoice->get_document_number( 'edit' ) : $invoice->get_next_document_number(),
								'readonly'    => true,
								'required'    => true,
								'class'       => 'eac-col-6',
							)
						);
						eac_input_field(
							array(
								'name'        => 'order_number',
								'label'       => __( 'Order Number', 'wp-ever-accounting' ),
								'type'        => 'text',
								'placeholder' => __( '123456789', 'wp-ever-accounting' ),
								'value'       => $invoice->get_order_number( 'edit' ),
								'class'       => 'eac-col-6',
							)
						);
						eac_input_field(
							array(
								'name'        => 'issued_at',
								'label'       => __( 'Issue Date', 'wp-ever-accounting' ),
								'type'        => 'date',
								'placeholder' => __( 'YYYY-MM-DD', 'wp-ever-accounting' ),
								'value'       => $invoice->get_issued_at( 'edit' ),
								'class'       => 'eac-col-6',
								'required'    => true,
							)
						);
						eac_input_field(
							array(
								'name'        => 'due_at',
								'label'       => __( 'Due Date', 'wp-ever-accounting' ),
								'type'        => 'date',
								'placeholder' => __( 'YYYY-MM-DD', 'wp-ever-accounting' ),
								'value'       => $invoice->get_due_at( 'edit' ),
								'class'       => 'eac-col-6',
								'required'    => true,
							)
						);
						eac_input_field(
							array(
								'name'        => 'currency_code',
								'label'       => __( 'Currency', 'wp-ever-accounting' ),
								'type'        => 'currency',
								'placeholder' => __( 'Select Currency', 'wp-ever-accounting' ),
								'value'       => $invoice->get_currency_code( 'edit' ),
								'class'       => 'eac-col-6',
								'required'    => true,
							)
						);
						?>
					</div>
				</div>
			</div>
		</div>

		<div class="eac-card__separator"></div>
		<div class="eac-card__section eac-p-0">
			<table class="eac-document__items" cellspacing="0" cellpadding="0">
				<thead>
				<tr>
					<?php foreach ( $columns as $column => $label ) : ?>
						<th class="eac-document__line-<?php echo esc_attr( $column ); ?>"><?php echo esc_html( $label ); ?></th>
					<?php endforeach; ?>
				</tr>
				</thead>
				<tbody>
				<?php foreach ( $invoice->get_items( 'item' ) as $item ) : ?>
				<?php endforeach; ?>
				<?php for ( $i = 0; $i < 5; $i ++ ) : ?>
					<tr>
						<td class="eac-document__line-actions">
							<a href="#" class="eac-document__line-del"><span class="dashicons dashicons-trash">&nbsp;</span></a>
						</td>
						<td class="eac-document__line-item">
							<?php
							eac_input_field(
								array(
									'type' => 'item',
								)
							);
							?>
							<textarea name="description" placeholder="<?php esc_attr_e( 'Description', 'wp-ever-accounting' ); ?>"></textarea>
						</td>
						<td class="eac-document__line-quantity">
							<input type="number" name="quantity" placeholder="<?php esc_attr_e( 'Quantity', 'wp-ever-accounting' ); ?>" value=""/>
						</td>
						<td class="eac-document__line-price">
							<input type="number" name="unit_price" placeholder="<?php esc_attr_e( 'Unit Price', 'wp-ever-accounting' ); ?>" value=""/>
						</td>
						<td class="eac-document__line-tax">
							<input type="number" name="tax" placeholder="<?php esc_attr_e( 'Tax', 'wp-ever-accounting' ); ?>" value=""/>
						</td>
						<td class="eac-document__line-subtotal">
							<input type="number" name="amount" placeholder="<?php esc_attr_e( 'Amount', 'wp-ever-accounting' ); ?>" value=""/>
						</td>
					</tr>
				<?php endfor; ?>
				<tr>
					<td></td>
					<td class="eac-document__line-empty">
						<?php
						eac_input_field(
							array(
								'type'   => 'item',
								'class'  => 'eac-col-12',
								'suffix' => 'Add Item',
							)
						);
						?>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="eac-card__section">
			<a href="#" class="button">Add Item</a>
		</div>
		<div class="eac-card__section">
			<div class="eac-columns">
				<div class="eac-col-6">
					<?php
					eac_input_field(
						array(
							'name'        => 'notes',
							'label'       => __( 'Notes', 'wp-ever-accounting' ),
							'type'        => 'textarea',
							'placeholder' => __( 'Enter notes', 'wp-ever-accounting' ),
							'value'       => $invoice->get_notes( 'edit' ),
						)
					)
					?>
				</div>
				<div class="eac-col-6">
					<table class="eac-document__totals">
						<tr>
							<td class="label"><?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?></td>
							<td width="1%">:</td>
							<td class="total">
								<?php echo esc_html( eac_format_money( $invoice->get_total() ) ); ?>
							</td>
						</tr>
						<tr>
							<td class="label"><?php esc_html_e( 'Fees', 'wp-ever-accounting' ); ?></td>
							<td width="1%">:</td>
							<td>
								<input type="number" class="eac-document__fees" name="fees" value="0" style="max-width: 100px;"/>
							</td>
						</tr>
						<tr>
							<td class="label"><?php esc_html_e( 'Shipping', 'wp-ever-accounting' ); ?></td>
							<td width="1%">:</td>
							<td>
								<input type="number" class="eac-document__shipping" name="shipping" value="0" style="max-width: 100px;"/>
							</td>
						</tr>
						<tr>
							<td class="label">
								<?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?>
							</td>
							<td width="1%">:</td>
							<td>
								<div class="eac-field__group">
									<input type="number" class="eac-document__fees" name="fees" value="0" style="max-width: 100px;"/>
									<span>
										<select name="" id="">
										<option value="percent">%</option>
										<option value="fixed">$</option>
									</select>
									</span>
								</div>
							</td>
						</tr>
						<tr>
							<td class="label"><?php esc_html_e( 'Tax', 'wp-ever-accounting' ); ?></td>
							<td width="1%">:</td>
							<td class="total">
								<?php echo esc_html( $invoice->get_total_tax() ); ?>
							</td>
						</tr>
						<tr>
							<td class="label"><?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?></td>
							<td width="1%">:</td>
							<td class="total">
								<?php echo esc_html( eac_format_money( $invoice->get_total() ) ); ?>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</form>
