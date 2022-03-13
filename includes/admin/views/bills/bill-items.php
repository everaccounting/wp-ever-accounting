<?php
/**
 * Bill items.
 *
 * @var $bill \Ever_Accounting\Bill
 * @var $mode    string
 * @package Ever_Accounting\Admin
 */

use Ever_Accounting\Helpers\Form;
use Ever_Accounting\Helpers\Price;
use Ever_Accounting\Helpers\Tax;

defined( 'ABSPATH' ) || exit;

$items          = $bill->get_items();
$item_label     = ever_accounting_get_option( 'bill_item_label', __( 'Item', 'wp-ever-accounting' ) );
$price_label    = ever_accounting_get_option( 'bill_price_label', __( 'Unit Price', 'wp-ever-accounting' ) );
$quantity_label = ever_accounting_get_option( 'bill_quantity_label', __( 'Quantity', 'wp-ever-accounting' ) );
?>
<div class="ea-document__items-wrapper">
	<div class="ea-document__items-top">
		<table cellpadding="0" cellspacing="0" class="ea-document__items">
			<thead>
			<tr>
				<th class="ea-document__line-actions">&nbsp;</th>
				<th class="ea-document__line-name" colspan="2"><?php echo esc_html( $item_label ); ?></th>
				<?php do_action( 'ever_accounting_bill_items_headers', $bill ); ?>
				<th class="ea-document__line-price"><?php echo esc_html( $price_label ); ?></th>
				<th class="ea-document__line-quantity"><?php echo esc_html( $quantity_label ); ?></th>
				<?php if ( Tax::tax_enabled() ) : ?>
					<th class="ea-document__line-tax"><?php esc_html_e( 'Tax(%)', 'wp-ever-accounting' ); ?></th>
				<?php endif; ?>
				<th class="ea-document__line-subtotal"><?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?></th>
			</tr>
			</thead>
			<tbody id="ea-document__line-items">
			<?php
			foreach ( $items as $item_id => $item ) {
				do_action( 'ever_accounting_before_bill_item_html', $item_id, $item, $bill );

				include __DIR__ . '/bill-item.php';

				do_action( 'ever_accounting_bill_item_html', $item_id, $item, $bill );
			}
			do_action( 'ever_accounting_bill_items_after_line_items', $bill );
			?>
			</tbody>
			<tbody>
			<script type="text/template" id="ea-bill-line-template">
				<?php
				$item_id = 9999;
				$item    = new \Ever_Accounting\Document_Item();
				include __DIR__ . '/bill-item.php';
				?>
			</script>
			<script type="text/template" id="ea-bill-item-selector">
				<?php
				Form::item_dropdown(
					array(
						'name'      => 'items[9999][item_id]',
						'class'     => 'select-item',
						'creatable' => true
					)
				);
				?>
			</script>
			</tbody>
		</table>
	</div>

	<div class="ea-document__data-row ea-document__actions">
		<div class="ea-document__actions-left">
			<button type="button" class="button add-line-item btn-secondary"><?php esc_html_e( 'Add Line Item', 'wp-ever-accounting' ); ?></button>
			<button type="button" class="button button-secondary add-discount"><?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?></button>
		</div>
		<div class="ea-document__actions-right">
			<button type="button" class="button button-secondary recalculate"><?php esc_html_e( 'Recalculate', 'wp-ever-accounting' ); ?></button>
		</div>
	</div>

	<div class="ea-document__data-row ea-bill__totals">
		<table class="ea-document__total-items">
			<tr>
				<td class="label"><?php esc_html_e( 'Items Subtotal:', 'wp-ever-accounting' ); ?></td>
				<td width="1%"></td>
				<td class="total">
					<?php echo Price::price( $bill->get_subtotal(), $bill->get_currency_code() ); ?>
				</td>
			</tr>

			<tr>
				<td class="label"><?php esc_html_e( 'Discount:', 'wp-ever-accounting' ); ?></td>
				<td width="1%"></td>
				<td class="total">-
					<?php echo Price::price( $bill->get_total_discount(), $bill->get_currency_code() ); ?>
				</td>
			</tr>

			<?php if ( Tax::tax_enabled() ) : ?>
				<?php if ( 'total' === ever_accounting_get_option( 'tax_display_totals', 'total' ) ) : ?>
					<tr>
						<td class="label"><?php esc_html_e( 'Tax', 'wp-ever-accounting' ); ?>:</td>
						<td width="1%"></td>
						<td class="total">
							<?php echo Price::price( $bill->get_total_tax(), $bill->get_currency_code() ); ?>
						</td>
					</tr>
				<?php else : ?>
					<?php foreach ( $bill->get_taxes() as $tax ) : ?>
						<tr>
							<td class="label"><?php echo esc_html( __( 'Tax', 'wp-ever-accounting' ) . '(' . number_format_i18n( $tax['rate'] ) . '%)' ); ?>:</td>
							<td width="1%"></td>
							<td class="total">
								<?php echo Price::price( $tax['amount'], $bill->get_currency_code() ); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endif; ?>

			<tr>
				<td class="label"><?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?>:</td>
				<td width="1%"></td>
				<td class="total">
					<?php echo Price::price( $bill->get_total(), $bill->get_currency_code() ); ?>
				</td>
			</tr>
			<?php if ( $bill->exists() ) : ?>
				<tr>
					<td class="label"><?php esc_html_e( 'Paid', 'wp-ever-accounting' ); ?>:</td>
					<td width="1%"></td>
					<td class="total">
						<?php echo Price::price( $bill->get_total_paid(), $bill->get_currency_code() ); ?>
					</td>
				</tr>
			<?php endif; ?>
			<?php if ( $bill->exists() && ! empty( $bill->get_total_due() ) ) : ?>
				<tr class="ea-document__due">
					<td class="label"><?php esc_html_e( 'Due', 'wp-ever-accounting' ); ?>:</td>
					<td width="1%"></td>
					<td class="total">
						<?php echo Price::price( abs( $bill->get_total_due() ), $bill->get_currency_code() ); ?>
					</td>
				</tr>
			<?php endif; ?>

		</table>

	</div>
</div>
