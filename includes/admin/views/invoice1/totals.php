<?php
/**
 * Invoice Total Items
 *
 * @package eaccounting\Admin\Views
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<table class="ea-invoice-totals">
	<tbody>
	<tr class="ea-invoice-totals-subtotal">
		<td class="label">Items Subtotal:</td>
		<td width="1%">&nbsp;</td>
		<td class="value"><?php echo esc_html($invoice->get_formatted_subtotal());?></td>
	</tr>
	<tr class="ea-invoice-totals-discount">
		<td class="label">Total Discount:</td>
		<td width="1%">&nbsp;</td>
		<td class="value"><?php echo esc_html($invoice->get_formatted_total_discount());?></td>
	</tr>
	<tr class="ea-invoice-totals-tax">
		<td class="label">Total Tax:</td>
		<td width="1%">&nbsp;</td>
		<td class="value"><?php echo esc_html($invoice->get_formatted_total_tax());?></td>
	</tr>
	<tr class="ea-invoice-totals-total">
		<td class="label">Invoice Total:</td>
		<td width="1%">&nbsp;</td>
		<td class="value"><?php echo esc_html($invoice->get_formatted_total());?></td>
	</tr>
	</tbody>
</table>
