<?php
/**
 * Displays an invoice.
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/invoice/invoice.php.
 *
 * @var $invoice Invoice
 * @version 1.1.0
 */

use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit;
?>

<?php do_action( 'eaccounting_before_invoice', $invoice ); ?>
<div id="ea-invoice" class="ea-invoice">
	<?php do_action( 'eaccounting_invoice_header', $invoice ); ?>
	<?php do_action( 'eaccounting_invoice_details', $invoice ); ?>
	<?php do_action( 'eaccounting_invoice_items', $invoice ); ?>
	<?php do_action( 'eaccounting_invoice_totals', $invoice ); ?>
	<?php do_action( 'eaccounting_invoice_footer', $invoice ); ?>
</div>
<?php do_action( 'eaccounting_after_invoice', $invoice ); ?>
