<?php
/**
 * Add invoice view.
 *
 * @package EverAccounting
 * @var $invoice \EverAccounting\Models\Invoice
 */

defined( 'ABSPATH' ) || exit;
$invoice           = new \EverAccounting\Models\Invoice();
$columns           = eac_get_invoice_columns();
$columns['action'] = '&nbsp;';
$data              = array(
	'invoice'  => $invoice->to_array(),
	'settings' => array(
		'currency' => eac_get_currency( $invoice->currency_code ),
	),
);
wp_add_inline_script( 'eac-sales', 'var eac_invoice_edit_vars = ' . json_encode( $data ) . ';', 'after' );
?>
<form id="eac-invoice-form" class="eac-document-overview" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<div class="eac-poststuff">
		<div class="column-1">

			<div class="eac-card">
				<div class="eac-card__child has-2-cols tw-pt-[2em] tw-pl-[2em] tw-pr-[2em]">
					<div class="col-1">
						<?php
						eac_form_field(
							array(
								'label'            => __( 'Customer', 'wp-ever-accounting' ),
								'type'             => 'select',
								'name'             => 'contact_id',
								'options'          => array( $invoice->customer ),
								'value'            => $invoice->customer_id,
								'required'         => true,
								'class'            => 'eac_select2',
								'option_value'     => 'id',
								'option_label'     => 'formatted_name',
								'data-placeholder' => __( 'Select a customer', 'wp-ever-accounting' ),
								'data-action'      => 'eac_json_search',
								'data-type'        => 'customer',
								'suffix'           => sprintf(
									'<a class="addon" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
									esc_url( admin_url( 'admin.php?page=eac-sales&tab=customers&add=yes' ) ),
									__( 'Add Customer', 'wp-ever-accounting' )
								),
							)
						);
						?>
						<div class="billing-address"></div>
					</div><!-- .col-1 -->

					<div class="col-2 has-2-cols">
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
								'label'             => esc_html__( 'Reference', 'wp-ever-accounting' ),
								'name'              => 'reference',
								'value'             => $invoice->reference,
								'type'              => 'text',
								'placeholder'       => 'REF-0001',
								'attr-autocomplete' => 'off',
							)
						);
						?>
					</div><!-- .col-2 -->
				</div><!-- .eac-document-overview__section -->
				<div class="document-items tw-pt-[2em] tw-overflow-x-auto">
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
				</div><!-- .eac-document-overview__section -->
			</div><!-- .eac-card -->


		</div><!-- .column-1 -->
		<div class="column-2">

			<button type="submit" class="button button-primary button-large tw-w-full">
				<?php esc_html_e( 'Save Invoice', 'wp-ever-accounting' ); ?>
			</button>
			<hr>

			<?php
			eac_form_field(
				array(
					'label'            => esc_html__( 'Currency', 'wp-ever-accounting' ),
					'name'             => 'currency_code',
					'default'          => eac_get_base_currency(),
					'value'            => $invoice->currency_code,
					'type'             => 'select',
					'options'          => eac_get_currencies(),
					'option_value'     => 'code',
					'option_label'     => 'formatted_name',
					'placeholder'      => esc_html__( 'Select a currency', 'wp-ever-accounting' ),
					'class'            => 'eac_select2',
					'data-action'      => 'eac_json_search',
					'data-type'        => 'currency',
					'data-allow-clear' => 'false',
				)
			);

			eac_form_field(
				array(
					'label'   => esc_html__( 'VAT Exempt', 'wp-ever-accounting' ),
					'name'    => 'vat_exempt',
					'value'   => $invoice->vat_exempt,
					'type'    => 'select',
					'options' => array(
						'no'  => esc_html__( 'No', 'wp-ever-accounting' ),
						'yes' => esc_html__( 'Yes', 'wp-ever-accounting' ),
					),
				)
			);
			?>

			<div class="eac-form-field">
				<label for="discount_amount"><?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?></label>
				<div class="eac-input-group">
					<input type="number" name="discount_amount" id="discount_amount" placeholder="10" value="<?php echo esc_attr( $invoice->discount_amount ); ?>"/>
					<select name="discount_type" id="discount_type" class="addon" style="max-width: 80px;">
						<option value="fixed" <?php selected( 'fixed', $invoice->discount_type ); ?>><?php echo $invoice->currency ? esc_html( $invoice->currency->symbol ) : esc_html( '($)' ); ?></option>
						<option value="percentage" <?php selected( 'percentage', $invoice->discount_type ); ?>><?php echo esc_html( '(%)' ); ?></option>
					</select>
				</div>
			</div><!-- .eac-form-field -->

			<div class="eac-card">
				<div class="eac-card__header">
					<h3 class="eac-card__title">Receipt</h3>
				</div>
				<div class="eac-card__body">
					<div class="eac-attachment">
						<div class="eac-attachment__dropzone">
							<button class="eac-attachment__button" type="button">Upload Receipt</button>
						</div>
						<ul class="eac-attachment__list">
							<li class="eac-attachment__item">
								<div class="eac-attachment__icon"><img src="https://via.placeholder.com/150" alt="Attachment"></div>
								<div class="eac-attachment__info">
									<div class="eac-attachment__name">Receipt-0001.jpg</div>
									<div class="eac-attachment__size">1.2 MB</div>
								</div>
								<div class="eac-attachment__actions">
									<a href="#" class="eac-attachment__action">
										<span class="dashicons dashicons-download"></span>
									</a>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</div><!-- .eac-card -->

		</div><!-- .column-2 -->
	</div><!-- .eac-poststuff -->
</form>


<script type="text/html" id="tmpl-eac-invoice-billing-address">
	<table class="eac-document-address">
		<tbody>
		<# if ( data.name ) { #>
		<tr>
			<td class="name">
				<span>{{ data.name }}</span>
				<input type="hidden" name="address[name]" value="{{ data.name }}">
			</td>
		</tr>
		<# } #>

		<# if ( data.company ) { #>
		<tr>
			<td class="company">
				<span>{{ data.company }}</span>
				<input type="hidden" name="address[company]" value="{{ data.company }}">
			</td>
		</tr>
		<# } #>

		<tr>
			<td class="address">
				{{ data.address }}<br>
				{{ data.city }} {{ data.state }} {{ data.zip }}
				<input type="hidden" name="address[address]" value="{{ data.address }}">
				<input type="hidden" name="address[city]" value="{{ data.city }}">
				<input type="hidden" name="address[state]" value="{{ data.state }}">
				<input type="hidden" name="address[zip]" value="{{ data.zip }}">
			</td>
		</tr>

		<# if ( data.country ) { #>
		<tr>
			<td class="country">
				<span>{{ data.country }}</span>
				<input type="hidden" name="address[country]" value="{{ data.country }}">
			</td>
		</tr>
		<# } #>

		<# if ( data.phone || data.email ) { #>
		<tr>
			<td class="phone-email">
				<# if ( data.phone ) { #>
				<span class="phone">{{ data.phone }}</span>
				<input type="hidden" name="address[phone]" value="{{ data.phone }}">
				<# } #>

				<# if ( data.phone && data.email ) { #>
				<span class="separator"> | </span>
				<# } #>

				<# if ( data.email ) { #>
				<span class="email">{{ data.email }}</span>
				<input type="hidden" name="address[email]" value="{{ data.email }}">
				<# } #>
			</td>
		</tr>
		<# } #>

		</tbody>
	</table>
</script>
<script type="text/html" id="tmpl-eac-invoice-no-items">
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
					<input type="hidden" name="items[{{ data.id }}][type]" value="{{ data.type }}">
					<input type="hidden" name="items[{{ data.id }}][price]" value="{{ data.price }}">
					<input type="hidden" name="items[{{ data.id }}][quantity]" value="{{ data.quantity }}">
					<input type="hidden" name="items[{{ data.id }}][unit]" value="{{ data.unit }}">
					<input type="hidden" name="items[{{ data.id }}][total]" value="{{ data.total }}">
					<input class="item-name" type="text" name="items[{{ data.id }}][name]" value="{{ data.name }}" placeholder="<?php esc_attr_e( 'Item Name', 'wp-ever-accounting' ); ?>">
					<textarea class="item-description" name="items[{{ data.id }}][description]" placeholder="<?php esc_attr_e( 'Item Description', 'wp-ever-accounting' ); ?>">{{ data.description }}</textarea>
					<# if ( data.taxable && 'yes' === data.vat_exempt ) { #>
					<select class="item-taxes eac_select2" data-action="eac_json_search" data-type="tax" data-placeholder="<?php esc_attr_e( 'Select a tax rate', 'wp-ever-accounting' ); ?>" multiple>
						<# if ( data.taxes && data.taxes.length ) { #>
						<# _.each( data.taxes, function( taxes ) { #>
						<option value="{{ taxes.tax_id }}" selected>{{ taxes.formatted_name }}</option>
						<# } ); #>
						<# } #>
					</select>
					<# if ( data.taxes && data.taxes.length ) { #>
					<# _.each( data.taxes, function( taxes ) { #>
					<input type="hidden" name="items[{{ data.id }}][taxes]{{ taxes.id }}['id']" value="{{ taxes.id }}">
					<input type="hidden" name="items[{{ data.id }}][taxes]{{ taxes.id }}['tax_id']" value="{{ taxes.tax_id }}">
					<input type="hidden" name="items[{{ data.id }}][taxes]{{ taxes.id }}['name']" value="{{ taxes.name }}">
					<input type="hidden" name="items[{{ data.id }}][taxes]{{ taxes.id }}['rate']" value="{{ taxes.rate }}">
					<# } ); #>
					<# } #>
					<# } #>
					<?php
					break;

				case 'price':
					echo '<input type="number" class="item-price" name="items[{{ data.id }}][price]" min="0" value="{{ data.price }}">';
					break;

				case 'quantity':
					echo '<input type="number" class="item-quantity" name="items[{{ data.id }}][quantity]" min="0" value="{{ data.quantity }}">';
					break;

				case 'subtotal_tax':
					echo '<span class="item-tax">{{ data.subtotal_tax }}</span>';
					break;

				case 'subtotal':
					echo '<span class="item-subtotal">{{ accounting.formatMoney(data.subtotal) }}</span>';
					break;

				case 'action':
					?>
					<a href="#" class="remove-item-item">
						<span class="dashicons dashicons-trash"></span>
					</a>
					<?php
					break;

				default:
					break;
			}
			?>
		</td>
	<?php endforeach; ?>
</script>
<script type="text/html" id="tmpl-eac-invoice-items-actions">
	<tr>
		<td colspan="<?php echo count( $columns ) - 1; ?>">
			<select class="add-item eac_select2" data-action="eac_json_search" data-type="item" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
		</td>
		<td class="col-spinner">
			<# if ( data.is_fetching ) { #>
			<span class="spinner is-active"></span>
			<# } #>
		</td>
	</tr>
</script>
<script type="text/html" id="tmpl-eac-invoice-items-totals">
	<tr>
		<td class="col-total-label" colspan="<?php echo count( $columns ) - 2; ?>">
			<?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-total-amount">
			{{ data.subtotal || 0 }}
		</td>
		<td class="col-action">&nbsp;</td>
	</tr>
	<tr>
		<td class="col-total-label" colspan="<?php echo count( $columns ) - 2; ?>">
			<?php esc_html_e( 'Tax', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-total-amount">
			{{ data.tax_total || 0 }}
		</td>
		<td class="col-action">&nbsp;</td>
	</tr>
	<tr>
		<td class="col-total-label" colspan="<?php echo count( $columns ) - 2; ?>">
			<?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-total-amount">
			{{ data.discount_total || 0 }}
		</td>
		<td class="col-action">&nbsp;</td>
	</tr>
	<tr>
		<td class="col-total-label" colspan="<?php echo count( $columns ) - 2; ?>">
			<?php esc_html_e( 'Adjustment', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-total-amount">
			<input type="number" name="adjustment" value="{{ data.adjustment || 0 }}">
		</td>
		<td class="col-action">&nbsp;</td>
	</tr>
	<tr>
		<td class="col-total-label" colspan="<?php echo count( $columns ) - 2; ?>">
			<?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-total-amount">
			{{ data.total || 0 }}
		</td>
		<td class="col-action">&nbsp;</td>
	</tr>

</script>

