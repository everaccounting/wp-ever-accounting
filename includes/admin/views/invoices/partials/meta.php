<?php
/**
 * Invoice Meta.
 *
 * @var $invoice \EverAccounting\Models\Invoice
 * @package EverAccounting\Admin
 */

defined( 'ABSPATH' ) || exit;
?>
<table class="ea-document__meta-table">
	<tr>
		<th class="ea-document__meta-label"><?php _e( 'Invoice Number', 'wp-ever-accounting' ); ?></th>
		<td class="spacer-col">&nbsp;</td>
		<td class="ea-document__meta-content">
			<?php echo empty( $invoice->get_invoice_number() ) ? '&mdash;' : esc_html( $invoice->get_invoice_number( 'view' ) ); ?>
		</td>
	</tr>
	<tr>
		<th class="ea-document__meta-label"><?php _e( 'Order Number', 'wp-ever-accounting' ); ?></th>
		<td class="spacer-col">&nbsp;</td>
		<td class="ea-document__meta-content">
			<?php echo empty( $invoice->get_order_number() ) ? '&mdash;' : esc_html( $invoice->get_order_number( 'view' ) ); ?>
		</td>
	</tr>
	<tr>
		<th class="ea-document__meta-label"><?php _e( 'Invoice Date', 'wp-ever-accounting' ); ?></th>
		<td class="spacer-col">&nbsp;</td>
		<td class="ea-document__meta-content">
			<?php echo empty( $invoice->get_issue_date() ) ? '&mdash;' : eaccounting_format_datetime( $invoice->get_issue_date(), 'M j, Y' ); ?>
		</td>
	</tr>
	<tr>
		<th class="ea-document__meta-label"><?php _e( 'Payment Date', 'wp-ever-accounting' ); ?></th>
		<td class="spacer-col">&nbsp;</td>
		<td class="ea-document__meta-content">
			<?php echo empty( $invoice->get_payment_date() ) ? '&mdash;' : eaccounting_format_datetime( $invoice->get_payment_date(), 'M j, Y' ); ?>
		</td>
	</tr>
	<tr>
		<th class="ea-document__meta-label"><?php _e( 'Due Date', 'wp-ever-accounting' ); ?></th>
		<td class="spacer-col">&nbsp;</td>
		<td class="ea-document__meta-content">
			<?php echo empty( $invoice->get_due_date() ) ? '&mdash;' : eaccounting_format_datetime( $invoice->get_due_date(), 'M j, Y' ); ?>
		</td>
	</tr>
	<tr>
		<th class="ea-document__meta-label"><?php _e( 'Invoice Status', 'wp-ever-accounting' ); ?></th>
		<td class="spacer-col">&nbsp;</td>
		<td class="ea-document__meta-content">
			<?php echo empty( $invoice->get_status() ) ? '&mdash;' : esc_html( $invoice->get_status_nicename() ); ?>
		</td>
	</tr>
</table>
