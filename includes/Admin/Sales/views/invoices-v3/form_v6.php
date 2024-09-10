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
<form>
	<div class="tw-mb-4">
		<label class="tw-block tw-text-gray-700 tw-font-semibold mb-2" for="invoice-number">Invoice Number</label>
		<input id="invoice-number" type="text" class="tw-border tw-border-gray-300 tw-rounded-md tw-p-2 w-full" placeholder="Enter invoice number" required>
	</div>

	<div class="tw-mb-4">
		<label class="tw-block tw-text-gray-700 tw-font-semibold mb-2" for="date">Date</label>
		<input id="date" type="date" class="tw-border tw-border-gray-300 tw-rounded-md tw-p-2 w-full" required>
	</div>

	<h2 class="tw-text-xl tw-font-semibold mt-6 mb-4">Client Information</h2>
	<div class="tw-mb-4">
		<label class="tw-block tw-text-gray-700 tw-font-semibold mb-2" for="client-name">Client Name</label>
		<input id="client-name" type="text" class="tw-border tw-border-gray-300 tw-rounded-md tw-p-2 w-full" placeholder="Enter client name" required>
	</div>

	<div class="tw-mb-4">
		<label class="tw-block tw-text-gray-700 tw-font-semibold mb-2" for="client-email">Client Email</label>
		<input id="client-email" type="email" class="tw-border tw-border-gray-300 tw-rounded-md tw-p-2 w-full" placeholder="Enter client email" required>
	</div>

	<h2 class="tw-text-xl tw-font-semibold mt-6 mb-4">Item Details</h2>
	<div class="tw-mb-4">
		<label class="tw-block tw-text-gray-700 tw-font-semibold mb-2" for="item-description">Item Description</label>
		<input id="item-description" type="text" class="tw-border tw-border-gray-300 tw-rounded-md tw-p-2 w-full" placeholder="Enter item description" required>
	</div>

	<div class="tw-mb-4">
		<label class="tw-block tw-text-gray-700 tw-font-semibold mb-2" for="item-quantity">Quantity</label>
		<input id="item-quantity" type="number" class="tw-border tw-border-gray-300 tw-rounded-md tw-p-2 w-full" placeholder="Enter quantity" required>
	</div>

	<div class="tw-mb-4">
		<label class="tw-block tw-text-gray-700 tw-font-semibold mb-2" for="item-price">Price</label>
		<input id="item-price" type="number" step="0.01" class="tw-border tw-border-gray-300 tw-rounded-md tw-p-2 w-full" placeholder="Enter price per item" required>
	</div>

	<div class="tw-mb-4">
		<button type="submit" class="tw-bg-blue-500 tw-text-white tw-font-bold tw-py-2 tw-px-4 tw-rounded hover:tw-bg-blue-600">Add Item</button>
	</div>

	<h2 class="tw-text-xl tw-font-semibold mt-6 mb-4">Total Amount</h2>
	<div class="tw-flex justify-between items-center">
		<span>Total:</span>
		<input type="text" value="$0.00" readonly class="tw-bg-gray-200 tw-border tw-border-gray-300 tw-rounded-md tw-p-2 w-1/3 text-right">
	</div>

	<div class="tw-mt-6">
		<button type="submit" class="tw-bg-green-500 tw-text-white tw-font-bold tw-py-2 tw-px-4 tw-rounded hover:tw-bg-green-600">Generate Invoice</button>
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
