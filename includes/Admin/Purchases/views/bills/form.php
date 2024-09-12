<?php
/**
 * Admin Bills Form.
 * Page: Expenses
 * Tab: Bills
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $bill \EverAccounting\Models\Bill Bill object.
 */

defined( 'ABSPATH' ) || exit;
$columns           = eac_get_bill_columns();
$columns['action'] = '&nbsp;';
$document          = new \EverAccounting\Models\Document();
$data              = $document->to_array();
$data['settings']  = array(
	'columns'  => $columns,
	'currency' => eac_get_currency( $document->currency_code ),
);
wp_enqueue_script( 'eac-bill-form' );
wp_localize_script( 'eac-bill-form', 'eac_bill_form_vars', $data );
?>
<form id="eac-bill-form" class="eac-document-overview" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<div class="eac-poststuff">
		<div class="column-1">

			<div class="eac-card">
				<div class="eac-document-overview__section eac-card__child document-details has-2-cols">
					<div class="col-1">
						<?php
						eac_form_field(
							array(
								'label'            => __( 'Vendor', 'wp-ever-accounting' ),
								'type'             => 'select',
								'name'             => 'vendor_id',
								'options'          => array( $document->vendor ),
								'value'            => $document->vendor_id,
								'required'         => true,
								'class'            => 'eac_select2',
								'option_value'     => 'id',
								'option_label'     => 'formatted_name',
								'data-placeholder' => __( 'Select a vendor', 'wp-ever-accounting' ),
								'data-action'      => 'eac_json_search',
								'data-type'        => 'vendor',
								'suffix'           => sprintf(
									'<a class="addon" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
									esc_url( admin_url( 'admin.php?page=eac-purchases&tab=vendors&add=yes' ) ),
									__( 'Add Vendor', 'wp-ever-accounting' )
								),
							)
						);
						?>

						<div class="vendor-address"></div>

					</div><!-- .col-1 -->

					<div class="col-2 has-2-cols">
						<?php
						eac_form_field(
							array(
								'label'       => esc_html__( 'Issue Date', 'wp-ever-accounting' ),
								'name'        => 'issue_date',
								'value'       => $document->issue_date,
								'type'        => 'text',
								'placeholder' => 'YYYY-MM-DD',
								'required'    => true,
								'class'       => 'eac_datepicker',
							)
						);
						eac_form_field(
							array(
								'label'       => esc_html__( 'Bill Number', 'wp-ever-accounting' ),
								'name'        => 'number',
								'value'       => $document->number,
								'type'        => 'text',
								'placeholder' => 'BILL-0001',
								'required'    => true,
							)
						);
						eac_form_field(
							array(
								'label'       => esc_html__( 'Due Date', 'wp-ever-accounting' ),
								'name'        => 'due_date',
								'value'       => $document->due_date,
								'type'        => 'text',
								'placeholder' => 'YYYY-MM-DD',
								'class'       => 'eac_datepicker',
							)
						);
						eac_form_field(
							array(
								'label'       => esc_html__( 'Reference', 'wp-ever-accounting' ),
								'name'        => 'reference',
								'value'       => $document->reference,
								'type'        => 'text',
								'placeholder' => 'REF-0001',
							)
						);
						?>

					</div><!-- .col-2 -->


				</div>

				<div class="eac-document-overview__section document-summary">

				</div>

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
					'value'            => $document->currency_code,
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
					'value'   => $document->vat_exempt,
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
					<input type="number" name="discount_amount" id="discount_amount" placeholder="10" value="<?php echo esc_attr( $document->discount_amount ); ?>"/>
					<select name="discount_type" id="discount_type" class="addon" style="max-width: 80px;">
						<option value="fixed" <?php selected( 'fixed', $document->discount_type ); ?>><?php echo $document->currency ? esc_html( $document->currency->symbol ) : esc_html( '($)' ); ?></option>
						<option value="percentage" <?php selected( 'percentage', $document->discount_type ); ?>><?php echo esc_html( '(%)' ); ?></option>
					</select>
				</div>
			</div>

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
			</div>

		</div><!-- .column-2 -->
	</div><!-- .eac-poststuff -->
</form>

<script type="text/html" id="tmpl-eac-bill-vendor-address">
	<table class="eac-document-overview__address">
		<tbody>
		<# if ( data.billing_name ) { #>
		<tr>
			<td class="name">
				<span>{{ data.billing_name }}</span>
				<input type="hidden" name="billing_name" value="{{ data.billing_name }}">
			</td>
		</tr>
		<# } #>
		<# if ( data.billing_address ) { #>
		<tr>
			<td class="address">
				{{ data.billing_address }}
				{{ data.billing_city }}, {{ data.billing_state }} {{ data.postcode }}
				<input type="hidden" name="billing_address" value="{{ data.billing_address }}">
				<input type="hidden" name="billing_city" value="{{ data.billing_city }}">
				<input type="hidden" name="billing_state" value="{{ data.billing_state }}">
				<input type="hidden" name="billing_zip_code" value="{{ data.billing_postcode }}">
			</td>
		</tr>
		<# } #>

		<# if ( data.billing_country ) { #>
		<tr>
			<td class="country">
				{{ data.billing_country }}
				<input type="hidden" name="billing_country" value="{{ data.billing_country }}">
			</td>
		</tr>
		<# } #>

		<# if ( data.billing_vat ) { #>
		<tr>
			<td class="tax-number">
				<?php esc_html_e( 'Tax Number:', 'wp-ever-accounting' ); ?>
				{{ data.billing_vat }}
				<input type="hidden" name="billing_vat" value="{{ data.billing_vat }}">
			</td>
		</tr>
		<# } #>

		<# if ( data.billing_phone || data.billing_email ) { #>
		<tr>
			<td class="phone-email">
				<# if ( data.billing_phone ) { #>
				<span class="phone">
								{{ data.billing_phone }}
								<input type="hidden" name="billing_phone" value="{{ data.billing_phone }}">
							</span>
				<# } #>

				<# if ( data.billing_phone && data.billing_email ) { #>
				<span class="separator"> | </span>
				<# } #>

				<# if ( data.billing_email ) { #>
				<span class="email">
								{{ data.billing_email }}
								<input type="hidden" name="billing_email" value="{{ data.billing_email }}">
							</span>
				<# } #>
			</td>
		</tr>
		<# } #>
	</table>
</script>
<script type="text/html" id="tmpl-eac-bill-summary-head">
	<tr>
		<?php foreach ( $columns as $key => $label ) : ?>
			<th class="col-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></th>
		<?php endforeach; ?>
	</tr>
</script>
<script type="text/html" id="tmpl-eac-bill-summary-item-empty">
	<td colspan="<?php echo count( $columns ); ?>">
		<?php esc_html_e( 'No items added yet.', 'wp-ever-accounting' ); ?>
	</td>
</script>
<script type="text/html" id="tmpl-eac-bill-summary-item">
	<?php foreach ( $columns as $key => $label ) : ?>
		<td class="col-<?php echo esc_attr( $key ); ?>">
			<?php
			switch ( $key ) {
				case 'item':
					?>
					<input type="hidden" name="items[{{ data.id }}][id]" value="{{ data.id }}">
					<input type="hidden" name="items[{{ data.id }}][type]" value="{{ data.type }}">
					<input type="hidden" name="items[{{ data.id }}][unit_price]" value="{{ data.unit_price }}">
					<input type="hidden" name="items[{{ data.id }}][quantity]" value="{{ data.quantity }}">
					<input type="hidden" name="items[{{ data.id }}][taxes]" value="{{ data.taxes }}">
					<input type="hidden" name="items[{{ data.id }}][total]" value="{{ data.total }}">
					<input class="line-name" type="text" name="items[{{ data.id }}][name]" value="{{ data.name }}" placeholder="<?php esc_attr_e( 'Item Name', 'wp-ever-accounting' ); ?>">
					<textarea class="line-description" name="items[{{ data.id }}][description]" placeholder="<?php esc_attr_e( 'Item Description', 'wp-ever-accounting' ); ?>">{{ data.description }}</textarea>
					<# if ( data.taxable ) { #>
					<select class="line-taxes eac_select2" data-action="eac_json_search" data-type="tax" data-placeholder="<?php esc_attr_e( 'Select a tax rate', 'wp-ever-accounting' ); ?>" multiple>
						<# if ( data.taxes && data.taxes.length ) { #>
						<# _.each( data.taxes, function( taxes ) { #>
						<option value="{{ taxes.tax_id }}" selected>{{ taxes.name }}</option>
						<# } ); #>
						<# } #>
					</select>
					<# if ( data.taxes && data.taxes.length ) { #>
					<# _.each( data.taxes, function( taxes ) { #>
					<input type="hidden" name="items[{{ data.id }}][taxes]{{ taxes.id }}['id']" value="{{ taxes.id }}">
					<input type="hidden" name="items[{{ data.id }}][taxes]{{ taxes.id }}['name']" value="{{ taxes.name }}">
					<input type="hidden" name="items[{{ data.id }}][taxes]{{ taxes.id }}['rate']" value="{{ taxes.rate }}">
					<# } ); #>
					<# } #>
					<# } #>
					<?php
					break;

				case 'price':
					echo '<input type="number" class="line-price" name="items[{{ data.id }}][price]" min="0" value="{{ data.price }}">';
					break;

				case 'quantity':
					echo '<input type="number" class="line-quantity" name="items[{{ data.id }}][quantity]" min="0" value="{{ data.quantity }}">';
					break;

				case 'subtotal_tax':
						echo '<span class="line-tax">{{ data.subtotal_tax }}</span>';
					break;

				case 'subtotal':
					echo '<span class="line-subtotal">{{ accounting.formatMoney(data.subtotal) }}</span>';
					break;

				case 'action':
					?>
					<a href="#" class="remove-line-item">
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
<script type="text/html" id="tmpl-eac-bill-summary-actions">
	<tr>
		<td colspan="<?php echo count( $columns ) - 1; ?>">
			<select class="add-line-item eac_select2" data-action="eac_json_search" data-type="item" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
		</td>
		<td class="col-spinner">
			<# if ( data.is_fetching ) { #>
			<span class="spinner is-active"></span>
			<# } #>
		</td>
	</tr>
</script>
