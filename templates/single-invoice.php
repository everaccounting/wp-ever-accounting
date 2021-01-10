<?php
/**
 * Single invoice page.
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/single-invoice.php.
 *
 * @version 1.1.0
 * @var int $invoice_id
 * @var string $key
 */

if ( empty( $key ) || empty( $invoice_id ) ) {
	eaccounting_get_template( 'unauthorized.php' );
	exit();
}
$invoice = eaccounting_get_invoice( $invoice_id );
if ( empty( $invoice ) || $key !== $invoice->get_key() ) {
	eaccounting_get_template( 'unauthorized.php' );
	exit();
}
?>
<?php eaccounting_get_template( 'global/head.php' ); ?>
<?php do_action( 'eaccounting_before_invoice_top' ); ?>
<?php do_action( 'eaccounting_invoice_top', $invoice ); ?>
<?php do_action( 'eaccounting_after_invoice_top' ); ?>
<div class="ea-card">
	<div class="ea-card__inside">
		<?php do_action( 'eacounting_before_invoice_content', $invoice ); ?>
		<?php do_action( 'eaccounting_invoice_content', $invoice ); ?>
		<?php do_action( 'eacounting_after_invoice_content', $invoice ); ?>
	</div>
</div>
<!-- /.ea-card -->
<?php eaccounting_get_template( 'global/footer.php' ); ?>



