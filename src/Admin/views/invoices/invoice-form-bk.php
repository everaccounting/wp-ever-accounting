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
$columns     = array(
	'item'     => __( 'Item', 'wp-ever-accounting' ),
	'price'    => __( 'Price', 'wp-ever-accounting' ),
	'quantity' => __( 'Quantity', 'wp-ever-accounting' ),
	'tax'      => __( 'Taxes', 'wp-ever-accounting' ),
	'subtotal' => __( 'Subtotal', 'wp-ever-accounting' ),
	'actions'  => '&nbsp;',
);
$tax_enabled = $document->is_calculating_tax();
?>
<table cellpadding="0" cellspacing="0" class="eac-document__items">
	<thead>
	<tr>
		<th class="item"><?php esc_html_e( 'Item', 'wp-ever-accounting' ); ?></th>
		<th class="price"><?php esc_html_e( 'Price', 'wp-ever-accounting' ); ?></th>
		<th class="quantity"><?php esc_html_e( 'Quantity', 'wp-ever-accounting' ); ?></th>
		<?php if ( $tax_enabled ) : ?>
			<th class="tax"><?php esc_html_e( 'Taxes', 'wp-ever-accounting' ); ?></th>
		<?php endif; ?>
		<th class="subtotal"><?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?></th>
		<th class="actions" width="1%">&nbsp;</th>
	</tr>
	</thead>
	<tbody>
	<?php if ( $document->get_items() ) :; ?>
		<?php foreach ( $document->get_items() as $item ) : ?>
			<tr class="item" data-item_id="<?php esc_attr_e( $item->get_id() ); ?>">

			</tr>
		<?php endforeach; ?>
	<?php else :; ?>
	</tbody>
</table>


<form id="eac-invoice-form" class="eac-invoice-form eac-document-form">
	<div class="eac-card">
		<div class="eac-card__header">
			<div class="eac-card__title"><?php esc_html_e( 'Invoice details', 'wp-ever-accounting' ); ?></div>
		</div>
		<div class="eac-card__section">
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
					<h3>
						<?php esc_html_e( 'Billing', 'wp-ever-accounting' ); ?>
						<?php if ( $document->is_editable() ) : ?>
							<a href="#" class="billing-edit"><?php esc_html_e( 'Edit', 'wp-ever-accounting' ); ?></a>
						<?php endif; ?>
					</h3>
					<div class="billing-data">
						<?php
						// Display values.
						if ( $document->get_formatted_billing_address() ) {
							echo '<p>' . wp_kses( $document->get_formatted_billing_address(), array( 'br' => array() ) ) . '</p>';
						} else {
							echo '<p><strong>' . esc_html__( 'Address:', 'wp-ever-accounting' ) . '</strong> ' . esc_html__( 'No billing address set.', 'wp-ever-accounting' ) . '</p>';
						}
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
					<div class="billing-fields" style="display: none;">
						<div class="eac-columns">
							<?php foreach ( $fields['billing'] as $field ) : ?>
								<?php
								$getter = "get_billing_{$field['name']}";
								eac_input_field(
									array_merge(
										$field,
										array(
											'name'     => "billing_{$field['name']}",
											'value'    => is_callable( array( $document, $getter ) ) ? $document->$getter( 'edit' ) : '',
											'disabled' => ! $document->is_editable(),
											'class'    => 'eac-col-6',
										)
									)
								);
								?>
							<?php endforeach; ?>
							<?php do_action( $document->get_hook_prefix() . '_after_billing_fields', $document ); ?>
						</div>
					</div>
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
								// 'value'       => $document->exists() ? $document->get_number( 'edit' ) : $document->get_next_number(),
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
								'name'        => 'reference',
								'label'       => __( 'Order Number', 'wp-ever-accounting' ),
								'type'        => 'text',
								'placeholder' => __( '123456789', 'wp-ever-accounting' ),
								'value'       => $document->get_reference( 'edit' ),
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
				<?php foreach ( $document->get_items() as $key => $item ) : ?>
					<tr>
						<?php foreach ( $columns as $column => $label ) : ?>
							<td class="eac-document__line-<?php echo esc_attr( $column ); ?>">
								<?php
								switch ( $column ) {
									case 'item':
										eac_input_field(
											array(
												'type'     => 'text',
												'name'     => sprintf( 'items[%s][name]', $key ),
												'value'    => $item->get_name(),
												'readonly' => true,
											)
										);
										eac_input_field(
											array(
												'type'  => 'textarea',
												'name'  => sprintf( 'items[%s][description]', $key ),
												'value' => $item->get_description(),
												'label' => __( 'Description', 'wp-ever-accounting' ),
												'placeholder' => __( 'Enter description (optional)', 'wp-ever-accounting' ),
												'input_style' => 'min-height: unset;',
											)
										);
										eac_input_field(
											array(
												'type'  => 'hidden',
												'name'  => sprintf( 'items[%s][id]', $key ),
												'value' => $item->get_id(),
											)
										);
										eac_input_field(
											array(
												'type'  => 'hidden',
												'name'  => sprintf( 'items[%s][item_id]', $key ),
												'value' => $item->get_item_id(),
											)
										);
										break;
									case 'quantity':
										eac_input_field(
											array(
												'name'    => sprintf( 'items[%s][quantity]', $key ),
												'value'   => $item->get_quantity(),
												'wrapper' => false,
											)
										);
										break;
									case 'price':
										eac_input_field(
											array(
												'name'    => sprintf( 'items[%s][price]', $key ),
												'value'   => $item->get_price(),
												'wrapper' => false,
											)
										);
										break;
									case 'tax':
										eac_input_field(
											array(
												'type'     => 'tax',
												'name'     => sprintf( 'items[%d][tax_ids]', $key ),
												'value'    => $item->get_tax_ids(),
												'multiple' => true,
											)
										);
										break;
									case 'subtotal':
										echo esc_html( $item->get_formatted_subtotal() );
										break;
									case 'actions':
										echo '<a href="#" class="remove_line_item" title="' . esc_attr__( 'Remove', 'wp-ever-accounting' ) . '"><span class="dashicons dashicons-trash"></span></a>';
										break;
								}
								?>
							</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
				<tr class="new-line-item-row">
					<td class="eac-document__line-item">
						<div class="eac-field__group">
							<select class="eac-field__select eac-select-item" id="add_line_item" data-eac-select2="item" data-placeholder="<?php esc_attr_e( 'Select Item to Add', 'wp-ever-accounting' ); ?>">
								<option value=""><?php esc_html_e( 'Select Item', 'wp-ever-accounting' ); ?></option>
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
		<div class="eac-card__section">
			<a href="#" class="button" id="recalculate"><?php esc_html_e( 'Recalculate', 'wp-ever-accounting' ); ?></a>
		</div>
	</div>

	<?php wp_nonce_field( 'eac_edit_invoice' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $document->get_id() ); ?>"/>
	<input type="hidden" name="type" value="<?php echo esc_attr( $document->get_type() ); ?>"/>
	<input type="hidden" name="action" value="eac_edit_invoice"/>
</form>
