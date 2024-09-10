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
<form id="eac-invoice-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<div class="eac-poststuff">
		<div class="column-1">
			<table class="eac-invoice-table widefat">
				<thead class="eac-invoice-table__header">
				<tr>
					<?php foreach ( $columns as $key => $label ) : ?>
						<th class="col-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></th>
					<?php endforeach; ?>
				</tr>
				</thead>
			</table>
		</div>
	</div>
</form>


<script type="text/template" id="tmpl-eac-invoice-no-items">
	<tr>
		<td colspan="<?php echo count( $columns ); ?>">
			<?php esc_html_e( 'No line items.', 'wp-ever-accounting' ); ?>
		</td>
	</tr>
</script>

<script type="text/template" id="tmpl-eac-invoice-totals">
	<tr>
		<td class="col-summary" colspan="4">
			Subtotal
		</td>
		<td class="col-amount">
			0.00
		</td>
		<td class="col-actions"></td>
	</tr>
	<tr>
		<td class="col-summary" colspan="4">
			Shipping
		</td>
		<td class="col-amount">
			0.00
		</td>
		<td class="col-actions"></td>
	</tr>
	<tr>
		<td class="col-summary" colspan="4">
			Tax
		</td>
		<td class="col-amount">
			0.00
		</td>
		<td class="col-actions"></td>
	</tr>
	<tr>
		<td class="col-summary" colspan="4">
			Fee
		</td>
		<td class="col-amount">
			0.00
		</td>
		<td class="col-actions"></td>
	</tr>
	<tr>
		<td class="col-summary" colspan="4">
			Total
		</td>
		<td class="col-amount">
			0.00
		</td>
		<td class="col-actions"></td>
	</tr>
</script>

<script type="text/template" id="tmpl-eac-invoice-actions">
	<tr>
		<td colspan="<?php echo count( $columns ); ?>">
			<button class="button add-line-item" title="<?php esc_attr_e( 'Add Line Item', 'wp-ever-accounting' ); ?>">
				<?php esc_html_e( 'Add Line Item', 'wp-ever-accounting' ); ?>
			</button>
			<button class="button add-taxes" title="<?php esc_attr_e( 'Add Tax', 'wp-ever-accounting' ); ?>">
				<?php esc_html_e( 'Add Tax', 'wp-ever-accounting' ); ?>
			</button>
		</td>
	</tr>
</script>

<script type="text/template" id="tmpl-eac-add-line-item">
	<div class="eac-modal__overlay" tabindex="-1" data-micromodal-close>
		<div class="eac-modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">

			<div class="eac-form-field">
				<label for=""><?php esc_html_e( 'Item', 'wp-ever-accounting' ); ?></label>
				<select class="select-line-item eac_select2" data-action="eac_json_search" data-type="item" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
			</div>

			<div class="eac-form-field">
				<label for=""><?php esc_html_e( 'Quantity', 'wp-ever-accounting' ); ?></label>
				<input type="number" class="add-line-item-quantity" min="1" value="{{data.quantity}}">
			</div>

			<div class="eac-form-field">
				<label for=""><?php esc_html_e( 'Price', 'wp-ever-accounting' ); ?></label>
				<input type="number" class="add-line-item-price" min="0" value="{{data.price}}">
			</div>

			<div class="eac-form-field">
				<label for=""><?php esc_html_e( 'Taxes', 'wp-ever-accounting' ); ?></label>
				<select class="add-line-item-tax eac_select2" data-action="eac_json_search" data-type="tax" data-placeholder="<?php esc_attr_e( 'Select a tax', 'wp-ever-accounting' ); ?>" multiple></select>
			</div>
		</div>
	</div>
</script>
