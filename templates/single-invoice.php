<?php
/**
 * Single invoice page.
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/single-invoice.php.
 *
 * @version 1.1.0
 * @var \EverAccounting\Models\Invoice $invoice
 */

defined( 'ABSPATH' ) || exit();

?>
<?php do_action( 'eaccounting_page_invoice_before_content', $invoice ); ?>

<div class="ea-card">
	<div class="ea-card__inside">
		<?php eaccounting_get_template( 'invoice/invoice.php', array( 'invoice' => $invoice ) ); ?>
	</div>
</div>

<?php do_action( 'eaccounting_page_invoice_after_content', $invoice ); ?>
