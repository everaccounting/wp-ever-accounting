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
	'columns'            => $columns,
	'invoice'            => $document->to_array(),
	'is_calculating_tax' => $document->is_calculating_tax(),
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
	<tr>
		<td colspan="<?php echo count( $columns ); ?>">
			<?php esc_html_e( 'No items added yet.', 'wp-ever-accounting' ); ?>
		</td>
	</tr>
</script>

<script type="text/html" id="tmpl-eac-invoice-line">
	<td class="col-item">
		<select class="select-line-item eac_select2" data-action="eac_json_search" data-type="item" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
		<textarea class="line-description" placeholder="Item Description"></textarea>
	</td>
	<td class="col-quantity">
		<input type="number" class="add-line-item-quantity" min="1" value="1">
	</td>
	<td class="col-price">
		<input type="number" class="add-line-item-price" min="0" value="0">
	</td>
	<td class="col-tax">
		&mdash;
	</td>
	<td class="col-subtotal">
		&mdash;
	</td>
	<td class="col-action">
		<a href="#">
			<span class="dashicons dashicons-saved"></span>
		</a>
	</td>
</script>

<script type="text/html" id="tmpl-eac-invoice-actions">
	<tr>
		<td colspan="<?php echo count( $columns ) - 1 ; ?>">
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
			$0
		</td>
		<td class="col-action">&nbsp;</td>
	</tr>
</script>
