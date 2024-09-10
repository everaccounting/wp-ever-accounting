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

wp_localize_script( 'eac-invoices', 'eac_invoice_vars', $data );
wp_enqueue_script( 'eac-invoices' );
?>
<form id="eac-invoice-form" class="eac-invoice-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<div class="eac-poststuff">
		<div class="eac-card">

			<div class="eac-invoice-form__section eac-card__faked">
				Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eius, quod.
			</div>
			<div class="eac-invoice-form__section invoice-summary ">
				<table class="eac-invoice-summary">
					<thead class="eac-invoice-summary__head">
					<tr>
						<?php foreach ( $columns as $key => $label ) : ?>
							<th class="col-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></th>
						<?php endforeach; ?>
					</tr>
					</thead>
					<tbody class="eac-invoice-summary__items">
					<tr>
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
					</tr>
					</tbody>

					<tbody class="eac-invoice-summary__actions">
					<tr>
						<td colspan="<?php echo count( $columns ); ?>">
							<select class="select-line-item eac_select2" data-action="eac_json_search" data-type="item" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>

						</td>
					</tr>
					</tbody>

					<tbody class="eac-invoice-summary__totals">
					<tr>
						<td colspan="<?php echo count( $columns ); ?>"></td>
					</tr>
					<tr>
						<td class="col-summary-label" colspan="<?php echo count( $columns ) - 2; ?>">
							<?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?>
						</td>
						<td class="col-summary-amount">
							$0
						</td>
						<td class="col-action">&nbsp;</td>
					</tr>
					<tr>
						<td class="col-summary-label" colspan="<?php echo count( $columns ) - 2; ?>">
							<?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?>
						</td>
						<td class="col-summary-amount">
							$0
						</td>
						<td class="col-action">&nbsp;</td>
					</tr>
					<tr>
						<td class="col-add-tax" colspan="<?php echo count( $columns ) - 1; ?>">
							<a href="#" class="add-line-item">
								Add Tax +
							</a>
						</td>
						<td class="col-action">&nbsp;</td>
					</tr>
					<tr>
						<td class="col-summary-label" colspan="<?php echo count( $columns ) - 2; ?>">
							<?php esc_html_e( 'Adjustment', 'wp-ever-accounting' ); ?>
						</td>
						<td class="col-summary-amount">
							<input type="number" class="add-line-item-price" min="0" value="0">
						</td>
						<td class="col-action">&nbsp;</td>
					</tr>
					<tr>
						<td class="col-summary-label" colspan="<?php echo count( $columns ) - 2; ?>">
							<?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?>
						</td>
						<td class="col-summary-amount">
							$0
						</td>
						<td class="col-action">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="<?php echo count( $columns ); ?>"></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</form>
