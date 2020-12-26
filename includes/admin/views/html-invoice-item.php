<?php
/**
 * Shows an Invoice item
 *
 * @package EverAccounting\Admin
 * @var Invoice      $invoice The item being displayed
 * @var DocumentItem $item    The item being displayed
 * @var int          $item_id The id of the item being displayed
 */

use EverAccounting\Models\DocumentItem;
use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit;

?>
<tr class="ea-invoice__line" data-item_id="<?php echo esc_attr( $item_id ); ?>">
	<?php if ( $invoice->is_editable() ) : ?>
		<td class="ea-invoice__line-actions" width="1%">
			<a class="save-invoice-item tips" href="#" data-tip="<?php esc_attr_e( 'Save item', 'wp-ever-accounting' ); ?>"><span class="dashicons dashicons-yes">&nbsp;</span></a>
			<a class="edit-invoice-item tips" href="#" data-tip="<?php esc_attr_e( 'Edit item', 'wp-ever-accounting' ); ?>"><span class="dashicons dashicons-edit">&nbsp;</span></a>
			<a class="delete-invoice-item tips" href="#" data-tip="<?php esc_attr_e( 'Delete item', 'wp-ever-accounting' ); ?>"><span class="dashicons dashicons-no">&nbsp;</span></a>
		</td>
	<?php endif; ?>

	<td class="ea-invoice__line-name" colspan="2">
		<input type="hidden" class="invoice_item_id" name="line_items[<?php echo absint( $item_id ); ?>][item_id]" value="<?php echo esc_attr( $item->get_item_id() ); ?>"/>
		<div class="view">
			<?php echo esc_html( $item->get_item_name( 'view' ) ); ?>
		</div>
		<div class="edit" style="display: none;">
			<input type="text" class="invoice_item_name" name="line_items[<?php echo absint( $item_id ); ?>][item_name]" value="<?php echo esc_attr( $item->get_item_name() ); ?>" required/>
		</div>
	</td>
	<?php do_action( 'eaccounting_invoice_item_values', $item_id, $item, $invoice ); ?>

	<td class="ea-invoice__line-price" width="1%" data-value="<?php echo $item->get_unit_price(); ?>">
		<div class="view">
			<?php echo esc_html( eaccounting_price( $item->get_unit_price(), $invoice->get_currency_code() ) ); ?>
		</div>
		<div class="edit" style="display: none;">
			<input type="text" class="invoice_unit_price" name="line_items[<?php echo $item_id; ?>][unit_price]" value="<?php echo esc_attr( $item->get_unit_price() ); ?>" required/>
		</div>
	</td>

	<td class="ea-invoice__line-quantity" width="1%" data-value="">
		<div class="view">
			<?php echo '<small class="times">&times;</small> ' . esc_html( $item->get_quantity() ); ?>
		</div>
		<div class="edit" style="display: none;">
			<input type="number" step="1" min="1" autocomplete="off" name="line_items[<?php echo absint( $item_id ); ?>][quantity]" placeholder="0" value="<?php echo esc_attr( $item->get_quantity() ); ?>" size="4" class="invoice_item_quantity" required/>
		</div>
	</td>
	<?php if ( eaccounting_tax_enabled() ) : ?>
	<td class="ea-invoice__line-tax" width="1%">
		<div class="view">
			<abbr title="<?php echo esc_html( eaccounting_price($item->get_total_tax(), $invoice->get_currency_code()) ); ?>"><?php echo esc_html( $item->get_tax_rate() ); ?><small>%</small></abbr>
		</div>
		<div class="edit" style="display: none;">
			<input type="text" class="invoice_item_tax" name="line_items[<?php echo absint( $item_id ); ?>][tax_rate]" value="<?php echo esc_attr( $item->get_tax_rate() ); ?>" required>
		</div>
	</td>
	<?php endif; ?>

	<td class="ea-invoice__line-subtotal" width="1%">
		<div class="view">
			<span class="invoice_item_subtotal"><?php echo esc_html( eaccounting_format_price( $item->get_subtotal(), $invoice->get_currency_code() ) ); ?></span>
		</div>
	</td>

</tr>
