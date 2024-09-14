<?php
/**
 * Add invoice view.
 *
 * @package EverAccounting
 * @var $document \EverAccounting\Models\Invoice
 */

defined( 'ABSPATH' ) || exit;
$columns = eac_get_invoice_columns();
$columns['action'] = '&nbsp;';
$data              = array(
	'invoice'      => $document->to_array(),
	'settings'     => array(
		'columns'      => $columns,
		'currency' => eac_get_currency( $document->currency_code ),
		'tax_per_item' => true,
	),
);
wp_localize_script( 'eac-invoice', 'eac_invoice_form_vars', $data );
wp_enqueue_script( 'eac-invoice' );
?>
<form id="eac-invoice-form" class="eac-document-overview" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<div class="eac-poststuff">
		<div class="column-1">

			<div class="eac-card">
				<div class="eac-document-overview__section eac-card__faked">
					Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cumque, explicabo!
				</div>
				<div class="eac-document-overview__section document-summary">
					<table class="eac-document-summary">
						<thead class="eac-document-summary__head">
						<tr>
							<?php foreach ( $columns as $key => $label ) : ?>
								<th class="col-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></th>
							<?php endforeach; ?>
						</tr>
						</thead>
					</table>
				</div>
			</div><!-- .eac-card -->

		</div>
		<div class="column-2">
			<button type="submit" class="button button-primary button-large tw-w-full">
				<?php esc_html_e( 'Save Invoice', 'wp-ever-accounting' ); ?>
			</button>

			<hr>

			<?php
			eac_form_field(
				array(
					'label'            => __( 'Customer', 'wp-ever-accounting' ),
					'type'             => 'select',
					'name'             => 'contact_id',
					'value'            => $document->contact_id,
					'options'          => array( $document->customer ),
					'option_value'     => 'id',
					'option_label'     => 'formatted_name',
					'default'          => filter_input( INPUT_GET, 'customer_id', FILTER_SANITIZE_NUMBER_INT ),
					'disabled'         => $document->exists() && $document->contact_id,
					'data-placeholder' => __( 'Select customer', 'wp-ever-accounting' ),
					'data-action'      => 'eac_json_search',
					'data-type'        => 'customer',
					'class'            => 'eac_select2',
					'suffix'           => sprintf(
						'<a class="button" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
						esc_url( admin_url( 'admin.php?page=eac-sales&tab=customers&add=yes' ) ),
						__( 'Add customer', 'wp-ever-accounting' )
					),
				)
			);

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

			eac_form_field(
				array(
					'label'        => esc_html__( 'Currency', 'wp-ever-accounting' ),
					'name'         => 'currency_code',
					'value'        => $document->currency_code,
					'type'         => 'select',
					'options'      => eac_get_currencies(),
					'option_value' => 'code',
					'option_label' => 'formatted_name',
					'placeholder'  => esc_html__( 'Select a currency', 'wp-ever-accounting' ),
					'class'        => 'eac_select2',
					'data-action'  => 'eac_json_search',
					'data-type'    => 'currency',
				)
			);

			eac_form_field(
				array(
					'label'   => esc_html__( 'VAT Exempt', 'wp-ever-accounting' ),
					'name'    => 'vat_exempt',
					'value'   => $document->vat_exempt,
					'type'    => 'select',
					'options' => array(
						''    => esc_html__( 'Select an option', 'wp-ever-accounting' ),
						'yes' => esc_html__( 'Yes', 'wp-ever-accounting' ),
						'no'  => esc_html__( 'No', 'wp-ever-accounting' ),
					),
				)
			);
			?>
			<div class="eac-form-field">
				<label for="discount_amount"><?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?></label>
				<div class="eac-input-group">
					<input type="number" name="discount_amount" id="discount_amount" placeholder="10" value="<?php echo esc_attr( $document->discount_amount ); ?>"/>
					<select name="discount_type" id="discount_type" class="addon" style="width: 80px;">
						<option value="fixed" <?php selected( 'fixed', $document->discount_type ); ?>><?php echo $document->currency ? esc_html( $document->currency->symbol ) : esc_html( '($)' ); ?></option>
						<option value="percent" <?php selected( 'percent', $document->discount_type ); ?>><?php echo esc_html( '(%)' ); ?></option>
					</select>
				</div>
			</div>
		</div>
	</div>
</form>

<script type="text/html" id="tmpl-eac-invoice-no-line-items">
	<td colspan="<?php echo count( $columns ); ?>">
		<?php esc_html_e( 'No items added yet.', 'wp-ever-accounting' ); ?>
	</td>
</script>

<script type="text/html" id="tmpl-eac-invoice-line-item">
	<td class="col-item">
		<input class="line-name" type="text" placeholder="<?php esc_attr_e( 'Item Name', 'wp-ever-accounting' ); ?>" value="{{ data.name }}">
		<textarea class="line-description" placeholder="<?php esc_attr_e( 'Item Description', 'wp-ever-accounting' ); ?>">{{ data.description }}</textarea>
		<select class="line-taxes eac_select2" data-action="eac_json_search" data-type="tax" data-placeholder="<?php esc_attr_e( 'Select a tax rate', 'wp-ever-accounting' ); ?>" multiple>
			<# if ( data.taxes && data.taxes.length ) { #>
			<# _.each( data.taxes, function( taxes ) { #>
			<option value="{{ taxes.tax_id }}" selected>{{ taxes.name }}</option>
			<# } ); #>
			<# } #>
		</select>
		<# if ( data.taxes && data.taxes.length ) { #>
		<# _.each( data.taxes, function( taxes ) { #>
		<input type="hidden" name="lines[{{ data.id }}][taxes]{{ taxes.id }}['id']" value="{{ taxes.id }}">
		<input type="hidden" name="lines[{{ data.id }}][taxes]{{ taxes.id }}['name']" value="{{ taxes.name }}">
		<input type="hidden" name="lines[{{ data.id }}][taxes]{{ taxes.id }}['rate']" value="{{ taxes.rate }}">
		<# } ); #>
		<# } #>
	</td>
	<td class="col-price">
		<input type="number" class="line-price" min="0" value="{{ data.price }}">
	</td>
	<td class="col-quantity">
		<input type="number" class="line-quantity" min="0" value="{{ data.quantity }}">
	</td>
	<td class="col-tax">
		<span class="line-tax">{{ data.subtotal_tax }}</span>
	</td>
	<td class="col-subtotal">
		<span class="line-subtotal">{{ data.subtotal }}</span>
	</td>
	<td class="col-action">
		<a href="#" class="remove-line-item">
			<span class="dashicons dashicons-trash"></span>
		</a>
	</td>
</script>

<script type="text/html" id="tmpl-eac-invoice-actions">
	<tr>
		<td colspan="<?php echo count( $columns ) - 1; ?>">
			<select class="add-line-item eac_select2" data-action="eac_json_search" data-type="item" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
		</td>
		<td class="col-action">
			<span class="spinner"></span>
		</td>
	</tr>
</script>

<script type="text/html" id="tmpl-eac-invoice-totals">
	<tr>
		<td class="col-summary-label" colspan="<?php echo count( $columns ) - 2; ?>">
			<?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-summary-amount">
			{{ data.subtotal || 0 }}
		</td>
		<td class="col-action">&nbsp;</td>
	</tr>
	<tr>
		<td class="col-summary-label" colspan="<?php echo count( $columns ) - 2; ?>">
			<?php esc_html_e( 'Tax', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-summary-amount">
			{{ data.tax_total || 0 }}
		</td>
		<td class="col-action">&nbsp;</td>
	</tr>
	<tr>
		<td class="col-summary-label" colspan="<?php echo count( $columns ) - 2; ?>">
			<?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-summary-amount">
			{{ data.discount_total || 0 }}
		</td>
		<td class="col-action">&nbsp;</td>
	</tr>
	<tr>
		<td class="col-summary-label" colspan="<?php echo count( $columns ) - 2; ?>">
			<?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-summary-amount">
			{{ data.total || 0 }}
		</td>
		<td class="col-action">&nbsp;</td>
	</tr>
</script>
