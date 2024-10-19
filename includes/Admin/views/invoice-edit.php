<?php
/**
 * Edit invoice view.
 *
 * @since 1.0.0
 * @package EverAccounting
 */

use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit;

$id      = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
$invoice = Invoice::make( $id );

$columns  = EAC()->invoices->get_columns();
$is_taxed = 'yes' === get_option( 'eac_tax_enabled', 'no' ) || $invoice->tax > 0;
// if tax is not enabled and invoice has no tax, remove the tax column.
if ( ! $is_taxed ) {
	unset( $columns['tax'] );
}


$data = $invoice->to_array();
foreach ( $invoice->items as $item ) {
	$_item = $item->to_array();
	if ( $is_taxed ) {
		foreach ( $item->taxes as $tax ) {
			$_item['taxes'][] = $tax->to_array();
		}
	}
	$data['items'][] = $_item;
}
wp_add_inline_script(
	'eac-admin',
	'var eac_invoice_vars = ' . wp_json_encode( $data ) . ';',
	'before'
);

?>
<div class="eac-section-header">
	<h1 class="wp-heading-inline">
		<?php if ( $invoice->exists() ) : ?>
			<?php esc_html_e( 'Edit Invoice', 'wp-ever-accounting' ); ?>
		<?php else : ?>
			<?php esc_html_e( 'Add Invoice', 'wp-ever-accounting' ); ?>
		<?php endif; ?>
		<a href="<?php echo esc_attr( remove_query_arg( array( 'action', 'id' ) ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
			<span class="dashicons dashicons-undo"></span>
		</a>
	</h1>

	<?php if ( $invoice->exists() ) : ?>
		<a class="button" href="<?php echo esc_url( add_query_arg( array( 'action' => 'view' ) ) ); ?>">
			<?php esc_html_e( 'View Invoice', 'wp-ever-accounting' ); ?>
		</a>
	<?php endif; ?>
</div>

<form id="eac-edit-invoice" name="invoice" method="post">
	<div class="eac-poststuff">
		<div class="column-1">

			<div class="eac-card eac-document-overview">

				<div class="eac-card__child document-details eac-grid cols-2">
					<div>
						<?php
						eac_form_field(
							array(
								'label'            => __( 'Customer', 'wp-ever-accounting' ),
								'type'             => 'select',
								'name'             => 'contact_id',
								'options'          => array( $invoice->customer ),
								'value'            => $invoice->customer_id,
								'required'         => true,
								'readonly'         => true,
								'class'            => 'eac_select2',
								'option_value'     => 'id',
								'option_label'     => 'formatted_name',
								'data-placeholder' => __( 'Select a customer', 'wp-ever-accounting' ),
								'data-action'      => 'eac_json_search',
								'data-type'        => 'customer',
							)
						);
						?>

						<div class="billing-address"></div>

					</div>
					<div class="eac-grid cols-2">
						<?php
						eac_form_field(
							array(
								'label'             => esc_html__( 'Issue Date', 'wp-ever-accounting' ),
								'name'              => 'issue_date',
								'value'             => $invoice->issue_date,
								'type'              => 'text',
								'placeholder'       => 'YYYY-MM-DD',
								'required'          => true,
								'class'             => 'eac_datepicker',
								'attr-autocomplete' => 'off',
							)
						);
						eac_form_field(
							array(
								'label'             => esc_html__( 'Invoice Number', 'wp-ever-accounting' ),
								'name'              => 'number',
								'value'             => $invoice->number,
								'default'           => $invoice->get_next_number(),
								'type'              => 'text',
								'placeholder'       => 'INV-0001',
								'required'          => true,
								'readonly'          => true,
								'attr-autocomplete' => 'off',
							)
						);
						eac_form_field(
							array(
								'label'             => esc_html__( 'Due Date', 'wp-ever-accounting' ),
								'name'              => 'due_date',
								'value'             => $invoice->due_date,
								'type'              => 'text',
								'placeholder'       => 'YYYY-MM-DD',
								'class'             => 'eac_datepicker',
								'attr-autocomplete' => 'off',
							)
						);
						eac_form_field(
							array(
								'label'             => esc_html__( 'Order Number', 'wp-ever-accounting' ),
								'name'              => 'order_number',
								'value'             => $invoice->reference,
								'type'              => 'text',
								'placeholder'       => 'REF-0001',
								'attr-autocomplete' => 'off',
							)
						);
						eac_form_field(
							array(
								'label'           => esc_html__( 'Currency', 'wp-ever-accounting' ),
								'name'            => 'currency',
								'default'         => eac_base_currency(),
								'value'           => $invoice->currency,
								'type'            => 'select',
								'options'         => eac_get_currencies(),
								'option_value'    => 'code',
								'option_label'    => 'formatted_name',
								'placeholder'     => esc_html__( 'Select a currency', 'wp-ever-accounting' ),
								'class'           => 'eac_select2',
								'data-allowClear' => 'false',
								'required'        => true,
							)
						);
						// exchange rate.
						eac_form_field(
							array(
								'label'       => esc_html__( 'Exchange Rate', 'wp-ever-accounting' ),
								'name'        => 'exchange_rate',
								'value'       => $invoice->exchange_rate,
								'type'        => 'number',
								'placeholder' => '1.00',
								'attr-step'   => 'any',
								'prefix'      => '1 ' . eac_base_currency() . ' = ',
								'required'    => true,
							)
						);

						?>
					</div>
				</div><!-- .document-details -->


				<div class="document-items">
					<table class="eac-document-items">
						<thead class="eac-document-items__head">
						<tr>
							<?php foreach ( $columns as $key => $label ) : ?>
								<th class="col-<?php echo esc_attr( $key ); ?>">
									<?php echo esc_html( $label ); ?>
								</th>
							<?php endforeach; ?>
						</tr>
						</thead>
					</table>
				</div><!-- .document-items -->


			</div><!-- .eac-card -->

		</div><!-- .column-1 -->
		<div class="column-2">

			<div class="eac-card">
				<div class="eac-card__header">
					<h3 class="eac-card__title"><?php esc_html_e( 'Save', 'wp-ever-accounting' ); ?></h3>
				</div>
				<div class="eac-card__body">
					<?php
					eac_form_field(
						array(
							'label'       => __( 'Status', 'wp-ever-accounting' ),
							'type'        => 'select',
							'id'          => 'status',
							'options'     => EAC()->invoices->get_statuses(),
							'value'       => $invoice->status,
							'placeholder' => __( 'Select status', 'wp-ever-accounting' ),
							'required'    => true,
						)
					);

					/**
					 * Fires to add custom actions.
					 *
					 * @param Invoice $invoice Invoice object.
					 *
					 * @since 2.0.0
					 */
					do_action( 'eac_invoice_edit_misc_actions', $invoice );
					?>
				</div>

				<div class="eac-card__footer">
					<?php if ( $invoice->exists() ) : ?>
						<a class="del del_confirm" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', $invoice->get_edit_url() ), 'bulk-invoices' ) ); ?>">
							<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
						</a>
						<button class="button button-primary"><?php esc_html_e( 'Update Invoice', 'wp-ever-accounting' ); ?></button>
					<?php else : ?>
						<button class="button button-primary button-large tw-w-full"><?php esc_html_e( 'Add Invoice', 'wp-ever-accounting' ); ?></button>
					<?php endif; ?>
				</div>
			</div><!-- .eac-card -->

			<?php
			/**
			 * Fires action to inject custom meta boxes in the side column.
			 *
			 * @param Invoice $invoice Invoice object.
			 *
			 * @since 1.0.0
			 */
			do_action( 'eac_invoice_edit_side_meta_boxes', $invoice );
			?>

		</div><!-- .column-2 -->
	</div><!-- .eac-poststuff -->

	<input type="hidden" name="action" value="eac_edit_invoice"/>
	<input type="hidden" name="id" value="<?php echo esc_attr( $invoice->id ); ?>"/>
	<?php wp_nonce_field( 'eac_edit_invoice' ); ?>
</form>

<script type="text/html" id="tmpl-eac-invoice-billing-addr">
	<table class="document-address">
		<tbody>
		<# if ( data.contact_name ) { #>
		<tr>
			<td class="name">
				<span>{{ data.contact_name }}</span>
				<input type="hidden" name="contact_name" value="{{ data.contact_name }}">
			</td>
		</tr>
		<# } #>

		<# if ( data.contact_company ) { #>
		<tr>
			<td class="company">
				<span>{{ data.contact_company }}</span>
				<input type="hidden" name="contact_company" value="{{ data.contact_company }}">
			</td>
		</tr>
		<# } #>

		<tr>
			<td class="address">
				{{ data.contact_address }}<br>
				{{ data.contact_city }} {{ data.contact_state }} {{ data.contact_zip }}
				<input type="hidden" name="contact_address" value="{{ data.contact_address }}">
				<input type="hidden" name="contact_city" value="{{ data.contact_city }}">
				<input type="hidden" name="contact_state" value="{{ data.contact_state }}">
				<input type="hidden" name="contact_zip" value="{{ data.contact_zip }}">
			</td>
		</tr>

		<# if ( data.contact_country ) { #>
		<tr>
			<td class="country">
				<span>{{ data.contact_country }}</span>
				<input type="hidden" name="contact_country" value="{{ data.contact_country }}">
			</td>
		</tr>
		<# } #>

		<# if ( data.contact_phone || data.contact_email ) { #>
		<tr>
			<td class="phone-email">
				<# if ( data.contact_phone ) { #>
				<span class="phone">{{ data.contact_phone }}</span>
				<input type="hidden" name="contact_phone" value="{{ data.contact_phone }}">
				<# } #>

				<# if ( data.contact_phone && data.contact_email ) { #>
				<span class="separator"> | </span>
				<# } #>

				<# if ( data.contact_email ) { #>
				<span class="email">{{ data.contact_email }}</span>
				<input type="hidden" name="contact_email" value="{{ data.contact_email }}">
				<# } #>
			</td>
		</tr>
		<# } #>

		</tbody>
	</table>
</script>
<script type="text/html" id="tmpl-eac-invoice-empty">
	<td colspan="<?php echo count( $columns ); ?>">
		<?php esc_html_e( 'No items added yet.', 'wp-ever-accounting' ); ?>
	</td>
</script>
<script type="text/html" id="tmpl-eac-invoice-item">
	<?php foreach ( $columns as $key => $label ) : ?>
		<td class="col-<?php echo esc_attr( $key ); ?>">
			<?php
			switch ( $key ) {
				case 'item':
					?>
					<input type="hidden" name="items[{{ data.id }}][id]" value="{{ data.id }}">
					<input type="hidden" name="items[{{ data.id }}][item_id]" value="{{ data.item_id }}">
					<input type="hidden" name="items[{{ data.id }}][type]" value="{{ data.type }}">
					<input type="hidden" name="items[{{ data.id }}][unit]" value="{{ data.unit }}">
					<input type="hidden" name="items[{{ data.id }}][discount]" value="{{ data.discount }}">
					<input class="item-name" type="text" name="items[{{ data.id }}][name]" value="{{ data.name }}" readonly>
					<textarea class="item-description" name="items[{{ data.id }}][description]" placeholder="<?php esc_attr_e( 'Item Description', 'wp-ever-accounting' ); ?>">{{ data.description }}</textarea>
					<?php if ( $is_taxed ) : ?>
					<select class="item-taxes eac_select2" data-action="eac_json_search" data-type="tax" data-placeholder="<?php esc_attr_e( 'Select a tax rate', 'wp-ever-accounting' ); ?>" multiple>
						<# if ( data.taxes && data.taxes.length ) { #>
						<# _.each( data.taxes, function( taxes ) { #>
						<option value="{{ taxes.tax_id }}" selected>{{ taxes.formatted_name }}</option>
						<# } ); #>
						<# } #>
					</select>
					<# if ( data.taxes && data.taxes.length ) { #>
					<# _.each( data.taxes, function( taxes ) { #>
					<input type="hidden" name="items[{{ data.id }}][taxes][{{ taxes.id }}][id]" value="{{ taxes.id }}">
					<input type="hidden" name="items[{{ data.id }}][taxes][{{ taxes.id }}][tax_id]" value="{{ taxes.tax_id }}">
					<input type="hidden" name="items[{{ data.id }}][taxes][{{ taxes.id }}][name]" value="{{ taxes.name }}">
					<input type="hidden" name="items[{{ data.id }}][taxes][{{ taxes.id }}][rate]" value="{{ taxes.rate }}">
					<input type="hidden" name="items[{{ data.id }}][taxes][{{ taxes.id }}][compound]" value="{{ taxes.compound }}">
					<input type="hidden" name="items[{{ data.id }}][taxes][{{ taxes.id }}][amount]" value="{{ taxes.amount }}">
					<# } ); #>
					<# } #>
				<?php endif; ?>
					<?php
					break;

				case 'price':
					echo '<input type="number" class="item-price" name="items[{{ data.id }}][price]" min="0" step="any" value="{{ data.price }}">';
					break;

				case 'quantity':
					echo '<input type="number" class="item-quantity" name="items[{{ data.id }}][quantity]" min="0" step="any" value="{{ data.quantity }}">';
					break;

				case 'tax':
					echo '<span class="item-tax">{{ data.formatted_tax}}</span>';
					break;

				case 'subtotal':
					echo '<span class="item-subtotal">{{ data.formatted_subtotal }}</span>';
					echo '<a href="#" class="remove-item"><span class="dashicons dashicons-trash"></span></a>';
					break;

				default:
					break;
			}
			?>
		</td>
	<?php endforeach; ?>
</script>
<script type="text/html" id="tmpl-eac-invoice-toolbar">
	<tr>
		<td colspan="<?php echo count( $columns ); ?>">
			<select class="add-item eac_select2" data-action="eac_json_search" data-type="item" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
		</td>
	</tr>
</script>
<script type="text/html" id="tmpl-eac-invoice-totals">
	<tr>
		<td class="col-label" colspan="<?php echo count( $columns ) - 1; ?>">
			<?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-amount">
			<div class="eac-input-group">
				<select name="discount_type" id="discount_type" class="addon">
					<option value="fixed"
					<# if ( 'fixed' === data.discount_type ) { #>selected="selected"<# } #>>($)</option>
					<option value="percent"
					<# if ( 'percent' === data.discount_type ) { #>selected="selected"<# } #>>(%)</option>
				</select>
				<input type="number" name="discount_value" id="discount_value" placeholder="10" style="text-align: right;width: auto;" value="{{data.discount_value}}"/>
			</div>
		</td>
	</tr>
	<tr>
		<td class="col-label" colspan="<?php echo count( $columns ) - 1; ?>">
			<?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-amount">
			{{ data.formatted_subtotal || 0 }}
		</td>
	</tr>
	<tr>
		<td class="col-label" colspan="<?php echo count( $columns ) - 1; ?>">
			<?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-amount">
			{{ data.formatted_discount || 0 }}
		</td>
	</tr>

	<?php if ( $is_taxed ) : ?>
		<tr>
			<td class="col-label" colspan="<?php echo count( $columns ) - 1; ?>">
				<?php esc_html_e( 'Tax', 'wp-ever-accounting' ); ?>
			</td>
			<td class="col-amount">
				{{ data.formatted_tax || 0 }}
			</td>
		</tr>
	<?php endif; ?>
	<tr>
		<td class="col-label" colspan="<?php echo count( $columns ) - 1; ?>">
			<?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-amount">
			{{ data.formatted_total || 0 }}
		</td>
	</tr>
</script>
