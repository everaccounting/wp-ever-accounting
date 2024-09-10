<?php
/**
 * Add invoice view.
 *
 * @package EverAccounting
 * @var $document \EverAccounting\Models\Invoice
 */

defined( 'ABSPATH' ) || exit;
$columns            = eac_get_invoice_columns();
$columns['actions'] = '&nbsp;';
if ( ! $document->is_calculating_tax() && isset( $columns['tax'] ) ) {
	unset( $columns['tax'] );
}
$data = array(
	'columns'            => $columns,
	'invoice'            => $document->to_array(),
	'is_calculating_tax' => $document->is_calculating_tax(),
);

wp_localize_script( 'eac-invoices', 'eac_invoices_vars', $data );
wp_enqueue_script( 'eac-invoices' );
?>

<form id="eac-invoice-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" class="eac-document">
	<div class="eac-poststuff">
		<div class="column-1">
			<table class="eac-invoice-table">
				<thead>
				<tr>
					<?php foreach ( $columns as $key => $label ) : ?>
						<th class="col-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></th>
					<?php endforeach; ?>
				</tr>
				</thead>
			</table>
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
		</div>

	</div>
</form>
<input class="my-input" />

<script type="text/template" id="tmpl-eac-invoice-item">
	<?php foreach ( $columns as $key => $label ) : ?>
			<td class="col-<?php echo esc_attr( $key ); ?>">
				<?php
				switch ( $key ) {
					case 'item':
						?>
						<input class="line-name" type="text" name="lines[{{ data.id }}][name]" value="{{ data.name }}" placeholder="<?php esc_attr_e( 'Name', 'wp-ever-accounting' ); ?>">
						<textarea class="line-description" name="lines[{{ data.id }}][description]" placeholder="<?php esc_attr_e( 'Description', 'wp-ever-accounting' ); ?>" maxlength="160">{{ data.description }}</textarea>
				        <select name="lines[{{ data.id }}][tax_ids]" class="line-taxes eac_select2 " data-action="eac_json_search" data-type="tax" data-placeholder="<?php esc_attr_e( 'Select a tax', 'wp-ever-accounting' ); ?>" multiple>
						</select>

						<input type="hidden" name="lines[{{ data.id }}][id]" value="{{ data.id }}">
						<input type="hidden" name="lines[{{ data.id }}][item_id]" value="{{ data.item_id }}">
						<?php
						break;
					case 'price':
						?>
						<div class="eac-input-group">
							<span class="addon"><?php echo esc_html( eac_get_currency_symbol( $document->currency_code ) ); ?></span>
							<input class="line-price eac_decimal_input" type="text" name="lines[{{ data.id }}][price]" value="{{ data.price }}" placeholder="<?php esc_attr_e( 'Price', 'wp-ever-accounting' ); ?>">
						</div>
						<?php
						break;
					case 'quantity':
						printf( '<input class="line-quantity eac_decimal_input" type="number" name="lines[%s][quantity]" value="%s" placeholder="%s" />', '{{ data.id }}', '{{ data.quantity }}', esc_attr__( 'Quantity', 'wp-ever-accounting' ) );
						printf( '<input type="hidden" name="lines[%s][quantity]" value="%s" />', '{{ data.id }}', '{{ data.quantity }}' );
						printf( '<input type="hidden" name="lines[%s][unit]" value="%s" />', '{{ data.id }}', '{{ data.unit }}' );
						break;
					case 'tax':
						printf( '<span class="line-tax">%s</span>', '{{ data.tax || 0 }}' );
						printf( '<input type="hidden" name="lines[%s][tax]" value="%s" />', '{{ data.id }}', '{{ data.tax }}' );
						break;
					case 'subtotal':
						printf('<span class="line-subtotal">%s</span>', '{{ data.subtotal || 0 }}');
						printf( '<input type="hidden" name="lines[%s][subtotal]" value="%s" />', '{{ data.id }}', '{{ data.subtotal }}' );
						break;
					case 'actions':
						echo '<a href="#" class="remove-line"><span class="dashicons dashicons-trash"></span></a>';
					default:
						// code...
						break;
				}

				?>

			</td>
	<?php endforeach; ?>
</script>

<script type="text/template" id="tmpl-eac-invoice-actions">
	<tr>
		<td colspan="2">
			<div class="eac-input-group">
				<select class="add-line-item eac_select2" data-action="eac_json_search" data-type="item" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=eac-items&add=yes' ) ); ?>" title="<?php esc_attr_e( 'Add New Item', 'wp-ever-accounting' ); ?>">
					<span class="dashicons dashicons-plus"></span>
				</a>
			</div>
		</td>
	</tr>
</script>

<script type="text/template" id="tmpl-eac-invoice-totals">
	<tr>
		<td colspan="2"></td>
		<td colspan="2">
			<span class="alignright">Subtotal</span>
		</td>
		<td>
			<span class="alignright">{{data.subtotal||0}}</span>
		</td>
	</tr>
</script>
