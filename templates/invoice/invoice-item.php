<?php
/**
 * Displays invoice item.
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/invoice/invoice-item.php.
 *
 * @var $invoice Invoice
 * @var $item_id int
 * @var $item Document_Item
 * @version 1.1.0
 * @package EAccounting
 */

use EAccounting\Models\Invoice;
use EAccounting\Models\Document_Item;

defined( 'ABSPATH' ) || exit;
?>
<tr class="ea-document__line" data-item_id="<?php echo esc_attr( $item_id ); ?>">

	<td class="ea-document__line-name" colspan="2">
		<div class="view">
			<?php echo esc_html( $item->get_item_name( 'view' ) ); ?>
		</div>
	</td>
	<?php do_action( 'eaccounting_invoice_item_values', $item_id, $item, $invoice ); ?>

	<td class="ea-document__line-price" width="1%" data-value="<?php echo esc_attr( $item->get_price() ); ?>">
		<div class="view">
			<?php echo esc_html( eaccounting_price( $item->get_price(), $invoice->get_currency_code() ) ); ?>
		</div>
	</td>

	<td class="ea-document__line-quantity" width="1%" data-value="">
		<div class="view">
			<?php echo '<small class="times">&times;</small> ' . esc_html( $item->get_quantity() ); ?>
		</div>
	</td>
	<?php if ( eaccounting_tax_enabled() ) : ?>
		<td class="ea-document__line-tax" width="1%">
			<div class="view">
				<abbr title="<?php echo esc_html( eaccounting_price( $item->get_tax(), $invoice->get_currency_code() ) ); ?>"><?php echo esc_html( number_format( $item->get_tax_rate(), 2 ) ); ?><small>%</small></abbr>
			</div>
		</td>
	<?php endif; ?>

	<td class="ea-document__line-subtotal" width="1%">
		<div class="view">
			<span class="line_item_subtotal"><?php echo esc_html( eaccounting_format_price( $item->get_subtotal(), $invoice->get_currency_code() ) ); ?></span>
		</div>
	</td>

</tr>
