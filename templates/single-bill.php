<?php
/**
 * Single bill page.
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/single-bill.php.
 *
 * @version 1.1.0
 * @var int $bill_id
 * @var string $key
 */

if ( empty( $key ) || empty( $bill_id ) ) {
	eaccounting_get_template( 'unauthorized.php' );
	exit();
}
$bill = eaccounting_get_bill( $bill_id );
if ( empty( $bill ) || $key !== $bill->get_key() ) {
	eaccounting_get_template( 'unauthorized.php' );
	exit();
}
?>
<div class="ea-container">
	<?php eaccounting_get_template( 'global/header.php', array( 'bill' => $bill ) ); ?>
	<div class="ea-document_description">
		<div class="ea-row">
			<div class="ea-col-12">
				<div class="ea-card">
					<div class="ea-card__inside">
						<?php eaccounting_get_template( 'bill/bill.php', array( 'bill' => $bill ) ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php eaccounting_get_template( 'global/footer.php', array( 'bill' => $bill ) ); ?>


