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

use Ever_Accounting\Helpers\Template;

defined( 'ABSPATH' ) || exit();

if ( empty( $key ) || empty( $bill_id ) ) {
	Template::get_template( 'unauthorized.php' );
	exit();
}
$bill = \Ever_Accounting\Documents::get_bill( $bill_id );
if ( empty( $bill ) || ! $bill->is_key_valid( $key ) ) {
	Template::get_template( 'unauthorized.php' );
	exit();
}
?>


<?php do_action( 'ever_accounting_public_before_bill', $bill ); ?>
<div class="ea-card">
	<div class="ea-card__inside">
		<?php Template::get_template( 'bill/bill.php', array( 'bill' => $bill ) ); ?>
	</div>
</div>
<?php do_action( 'ever_accounting_public_after_bill', $bill ); ?>
