<?php
/**
 * Add invoice view.
 *
 * @package EverAccounting
 * @var $document \EverAccounting\Models\Invoice
 */

defined( 'ABSPATH' ) || exit;
$columns = eac_get_invoice_columns();
if ( ! $document->is_calculating_tax() && isset( $columns['tax'] ) ) {
	unset( $columns['tax'] );
}
$columns['action'] = '&nbsp;';
$data              = array(
	'columns'      => $columns,
	'invoice'      => $document->to_array(),
	'tax_per_item' => true,
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
			<option value="{{ taxes.id }}" selected>{{ taxes.name }}</option>
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
		<span class="line-tax">{{ data.tax }}</span>
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
			{{ data.subtotal }}
		</td>
		<td class="col-action">&nbsp;</td>
	</tr>
	<tr>
		<td class="col-summary-label" colspan="<?php echo count( $columns ) - 2; ?>">
			<?php esc_html_e( 'Tax', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-summary-amount">
			{{ data.tax }}
		</td>
		<td class="col-action">&nbsp;</td>
	</tr>
	<tr>
		<td class="col-summary-label" colspan="<?php echo count( $columns ) - 2; ?>">
			<?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-summary-amount">
			{{ data.total }}
		</td>
		<td class="col-action">&nbsp;</td>
	</tr>
</script>
