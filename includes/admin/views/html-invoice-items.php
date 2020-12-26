<?php
/**
 * Invoice items.
 *
 * @package EverAccounting\Admin
 */

defined( 'ABSPATH' ) || exit;

$line_items = $invoice->get_line_items();

?>
<div class="ea-invoice__items-wrapper">
	<table cellpadding="0" cellspacing="0" class="ea-invoice__items">
		<thead>
		<tr>
			<?php if ( $invoice->is_editable() ) : ?>
				<th class="ea-invoice__line-actions">&nbsp;</th>
			<?php endif; ?>
			<th class="ea-invoice__line-name" colspan="2"><?php esc_html_e( 'Item', 'wp-ever-accounting' ); ?></th>
			<?php do_action( 'eaccounting_invoice_items_headers', $invoice ); ?>
			<th class="ea-invoice__line-price"><?php esc_html_e( 'Unit Price', 'wp-ever-accounting' ); ?></th>
			<th class="ea-invoice__line-quantity"><?php esc_html_e( 'Quantity', 'wp-ever-accounting' ); ?></th>
			<?php if ( eaccounting_tax_enabled() ) : ?>
				<th class="ea-invoice__line-tax"><?php esc_html_e( 'Tax(%)', 'wp-ever-accounting' ); ?></th>
			<?php endif; ?>
			<th class="ea-invoice__line-subtotal"><?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?></th>
		</tr>
		</thead>
		<tbody id="ea-invoice__line-items">
		<?php
		foreach ( $line_items as $item_id => $item ) {
			do_action( 'eaccounting_before_invoice_item_html', $item_id, $item, $invoice );

			include __DIR__ . '/html-invoice-item.php';

			do_action( 'eaccounting_invoice_item_html', $item_id, $item, $invoice );
		}
		do_action( 'eaccounting_invoice_items_after_line_items', $invoice );
		?>
		</tbody>

		<tbody>
		<script type="text/template" id="ea-invoice-line-template">
			<?php
			eaccounting_get_admin_template(
					'html-invoice-item',
					array(
							'invoice' => $invoice,
							'item_id' => '999',
							'item'    => new \EverAccounting\Models\DocumentItem(),
					)
			);
			?>
		</script>
		<script type="text/template" id="ea-invoice-item-selector">
			<?php
			eaccounting_item_dropdown(
					array(
							'name'  => 'line_items[9999][item_id]',
							'class' => 'select-item',
					)
			);
			?>
		</script>
		</tbody>

	</table>

	<?php if ( $invoice->is_editable() ) : ?>
		<div class="ea-invoice__data-row ea-invoice__actions">
			<div class="ea-invoice__actions-left">
				<button type="button" class="button add-line-item btn-secondary"><span class="dashicons dashicons-plus">&nbsp;</span><?php esc_html_e( 'Line Item', 'wp-ever-accounting' ); ?></button>
			</div>
			<div class="ea-invoice__actions-right">
				<button type="button" class="button button-primary recalculate"><?php esc_html_e( 'Recalculate', 'wp-ever-accounting' ); ?></button>
			</div>
		</div>
	<?php endif; ?>

	<div class="ea-invoice__data-row ea-invoice__totals">
		<table class="ea-invoice__total-items">
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

			<tr>
				<td class="label"><?php esc_html_e( 'Invoice Total', 'wp-ever-accounting' ); ?>:</td>
				<td width="1%"></td>
				<td class="total">
					<?php echo eaccounting_price( $invoice->get_total(), $invoice->get_currency_code() ); ?>
				</td>
			</tr>

		</table>

		<div class="clear"></div>

		<?php if ( $invoice->exists() ) : ?>

			<table class="ea-invoice__total-items" style="border-top: 1px solid #999; margin-top:12px; padding-top:12px">

				<?php if ( $invoice->get_total_paid() ) : ?>
					<tr>
						<td class="label"><?php esc_html_e( 'Paid', 'wp-ever-accounting' ); ?>:</td>
						<td width="1%"></td>
						<td class="total">
							<?php echo eaccounting_price( $invoice->get_total_paid(), $invoice->get_currency_code() ); ?>
						</td>
					</tr>
				<?php endif; ?>

				<?php if ( ! $invoice->is_status( 'paid' ) && $invoice->get_total_paid() < $invoice->get_total() ) : ?>
					<tr>
						<td class="label"><?php esc_html_e( 'Due', 'wp-ever-accounting' ); ?>:</td>
						<td width="1%"></td>
						<td class="total">
							<?php echo eaccounting_price( $invoice->get_total_due(), $invoice->get_currency_code() ); ?>
						</td>
					</tr>
				<?php endif; ?>

			</table>
		<?php endif; ?>

	</div>


</div>
