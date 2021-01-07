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
<div class="ea-container">
	<div class="ea-row">
		<div class="ea-col-12">
			<div class="ea-card">
				<div class="ea-card__inside">
					<?php eaccounting_get_template( 'invoice/invoice.php', array( 'invoice' => $invoice ) ); ?>
				</div>
			</div>
		</div>
	</div>
</div>
