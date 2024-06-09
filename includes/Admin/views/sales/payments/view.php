<?php
/**
 * Admin Veiw Payment.
 * Page: Sales
 * Tab: Payment
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $payment \EverAccounting\Models\Payment Payment object.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="eac-columns">
	<div class="eac-col-9">
		<div class="eac-panel">
			<?php echo do_shortcode( '[eac_payment id=' . $payment->id . ']' ); ?>
		</div>
	</div>
	<div class="eac-col-3"></div>
</div>


