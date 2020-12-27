<?php
/**
 * Invoice items.
 *
 * @var $invoice \EverAccounting\Models\Invoice
 * @var $mode    string
 * @package EverAccounting\Admin
 */

defined( 'ABSPATH' ) || exit;

$edit_mode = isset( $mode ) && $mode === 'edit';
$items     = $invoice->get_items();
?>
<div class="ea-document__items-wrapper">
	<h3 class="ea-document__items-title"><?php esc_html_e( 'Line Items', 'wp-ever-accounting' ); ?></h3>
	<table cellpadding="0" cellspacing="0" class="ea-document__items">
		<thead>
		<tr>
			<?php if ( $invoice->is_editable() && $edit_mode ) : ?>
				<th class="ea-document__line-actions">&nbsp;</th>
			<?php endif; ?>
			<th class="ea-document__line-name" colspan="2"><?php esc_html_e( 'Item', 'wp-ever-accounting' ); ?></th>
			<?php do_action( 'eaccounting_invoice_items_headers', $invoice ); ?>
			<th class="ea-document__line-price"><?php esc_html_e( 'Unit Price', 'wp-ever-accounting' ); ?></th>
			<th class="ea-document__line-quantity"><?php esc_html_e( 'Quantity', 'wp-ever-accounting' ); ?></th>
			<?php if ( eaccounting_tax_enabled() ) : ?>
				<th class="ea-document__line-tax"><?php esc_html_e( 'Tax(%)', 'wp-ever-accounting' ); ?></th>
			<?php endif; ?>
			<th class="ea-document__line-subtotal"><?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?></th>
		</tr>
		</thead>
		<tbody id="ea-document__line-items">
		<?php
		foreach ( $items as $item_id => $item ) {
			do_action( 'eaccounting_before_invoice_item_html', $item_id, $item, $invoice );

			include __DIR__ . '/item.php';

			do_action( 'eaccounting_invoice_item_html', $item_id, $item, $invoice );
		}
		do_action( 'eaccounting_invoice_items_after_line_items', $invoice );
		?>
		</tbody>
		<?php if ( $invoice->is_editable() && $edit_mode ) : ?>
			<tbody>
			<script type="text/template" id="ea-invoice-line-template">
				<?php
				$item_id = 9999;
				$item    = new \EverAccounting\Models\DocumentItem();
				include __DIR__ . '/item.php';
				?>
			</script>
			<script type="text/template" id="ea-invoice-item-selector">
				<?php
				eaccounting_item_dropdown(
					array(
						'name'  => 'items[9999][item_id]',
						'class' => 'select-item',
					)
				);
				?>
			</script>
			</tbody>
		<?php endif; ?>
	</table>
	<?php if ( $invoice->is_editable() && $edit_mode ) : ?>
		<div class="ea-document__data-row ea-document__actions">
			<div class="ea-document__actions-left">
				<button type="button" class="button add-line-item btn-secondary"><?php esc_html_e( 'Add Line Item', 'wp-ever-accounting' ); ?></button>
			</div>
			<div class="ea-document__actions-right">
				<button type="button" class="button button-secondary add-discount"><?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?></button>
				<button type="button" class="button button-primary recalculate"><?php esc_html_e( 'Recalculate', 'wp-ever-accounting' ); ?></button>
			</div>
		</div>
	<?php endif; ?>

	<div class="ea-document__data-row ea-invoice__totals">
		<table class="ea-document__total-items">
			<tr>
				<td class="label"><?php esc_html_e( 'Items Subtotal:', 'wp-ever-accounting' ); ?></td>
				<td width="1%"></td>
				<td class="total">
					<?php echo eaccounting_price( $invoice->get_subtotal(), $invoice->get_currency_code() ); ?>
				</td>
			</tr>

			<tr>
				<td class="label"><?php esc_html_e( 'Discount:', 'wp-ever-accounting' ); ?></td>
				<td width="1%"></td>
				<td class="total">-
					<?php echo eaccounting_price( $invoice->get_total_discount(), $invoice->get_currency_code() ); ?>
				</td>
			</tr>

			<?php if ( eaccounting_tax_enabled() ) : ?>
				<tr>
					<td class="label"><?php esc_html_e( 'Tax', 'wp-ever-accounting' ); ?>:</td>
					<td width="1%"></td>
					<td class="total">
						<?php echo eaccounting_price( $invoice->get_total_tax(), $invoice->get_currency_code() ); ?>
					</td>
				</tr>
			<?php endif; ?>
			<?php if ( $invoice->exists() ) : ?>
			<tr>
				<td class="label"><?php esc_html_e( 'Invoice Total', 'wp-ever-accounting' ); ?>:</td>
				<td width="1%"></td>
				<td class="total">
					<?php echo eaccounting_price( $invoice->get_total(), $invoice->get_currency_code() ); ?>
				</td>
			</tr>
			<tr>
				<td class="label"><?php esc_html_e( 'Paid', 'wp-ever-accounting' ); ?>:</td>
				<td width="1%"></td>
				<td class="total">
					<?php echo eaccounting_price( $invoice->get_total_paid(), $invoice->get_currency_code() ); ?>
				</td>
			</tr>
			<?php endif; ?>
			<?php if ( $invoice->exists() && ! empty( $invoice->get_total_due() ) ) : ?>
				<tr>
					<td class="label"><?php esc_html_e( 'Due', 'wp-ever-accounting' ); ?>:</td>
					<td width="1%"></td>
					<td class="total">
						<?php echo eaccounting_price( $invoice->get_total_due(), $invoice->get_currency_code() ); ?>
					</td>
				</tr>
			<?php endif; ?>

		</table>

	</div>
</div>
