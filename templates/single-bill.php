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
<?php eaccounting_get_template( 'global/head.php' ); ?>
<?php do_action( 'eaccounting_before_bill_top' ); ?>
<?php do_action( 'eaccounting_bill_top', $bill ); ?>
<?php do_action( 'eaccounting_after_bill_top' ); ?>

<div class="ea-card">
	<div class="ea-card__inside">
		<?php do_action( 'eacounting_before_bill_content', $bill ); ?>
		<?php do_action( 'eaccounting_bill_content', $bill ); ?>
		<?php do_action( 'eacounting_after_bill_content', $bill ); ?>
	</div>
</div>
<!-- /.ea-card -->
<?php eaccounting_get_template( 'global/footer.php' ); ?>


