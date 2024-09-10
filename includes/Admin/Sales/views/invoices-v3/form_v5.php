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

wp_localize_script( 'eac-invoices', 'eac_invoice_vars', $data );
wp_enqueue_script( 'eac-invoices' );
?>
<form id="eac-invoice-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<div class="eac-poststuff">
		<div class="column-1">
			<button class="add-line-item button" title="<?php esc_attr_e( 'Add Line Item', 'wp-ever-accounting' ); ?>">
				<?php esc_html_e( 'Add Line Item', 'wp-ever-accounting' ); ?>
			</button>
			<button class="add-taxes button" title="<?php esc_attr_e( 'Add Tax', 'wp-ever-accounting' ); ?>">
				<?php esc_html_e( 'Add Tax', 'wp-ever-accounting' ); ?>
			</button>
		</div>
	</div>
</form>

<script type="text/template" id="tmpl-eac-modal-add-line-item">
	<div class="eac-modal-header">
		<h2>Add Item</h2>
	</div>
	<form id="eac-form-invoice-add-line-item">
		{{JSON.stringify(data)}}
		<div class="eac-form-field">
			<label for=""><?php esc_html_e( 'Item', 'wp-ever-accounting' ); ?></label>
			<select class="select-line-item eac_select2" data-action="eac_json_search" data-type="item" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
		</div>

		<div class="eac-form-field">
			<label for=""><?php esc_html_e( 'Quantity', 'wp-ever-accounting' ); ?></label>
			<input type="number" class="add-line-item-quantity" min="1" value="{{data.quantity||1}}">
		</div>

		<div class="eac-form-field">
			<label for=""><?php esc_html_e( 'Price', 'wp-ever-accounting' ); ?></label>
			<input type="number" class="add-line-item-price" min="0" value="{{data.price||0}}">
		</div>

		<div class="eac-form-field">
			<label for=""><?php esc_html_e( 'Taxes', 'wp-ever-accounting' ); ?></label>
			<select class="add-line-item-tax eac_select2" data-type="tax" data-placeholder="<?php esc_attr_e( 'Select a tax', 'wp-ever-accounting' ); ?>" multiple>
				<# _.each( data.taxes, function( tax ) { #>
				<option value="{{tax.id}}" selected>{{tax.name}}</option>
				<# } ); #>
			</select>
		</div>
		<button class="button button-primary button-large tw-w-full <# {data.quantity ? '' : 'disabled'} #>">
			<?php esc_html_e( 'Add Item', 'wp-ever-accounting' ); ?>
		</button>
	</form>
</script>
