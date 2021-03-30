<?php
/**
 * Payment form.
 *
 * This template can be overridden by copying it to yourtheme/eaccounting/payment-form.php.
 *
 * @version 1.1.0
 * @var Invoice $invoice
 * @var Gateway $gateways
 * @var string $selected
 */

use EverAccounting\Abstracts\Gateway;
use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || exit();

?>
<div id="eaccounting_checkout_wrap" class="ea-noprint">
	<div class="ea-card">
		<div class="ea-card__inside">
			<h3><?php esc_attr_e( 'Payment', 'wp-ever-accounting' ); ?></h3>

			<?php do_action( 'eaccounting_before_checkout_form', $invoice ); ?>

			<form id="eaccounting_checkout_form" class="eaccounting-form" action="" method="POST">
				<?php do_action( 'eaccounting_checkout_form_top', $invoice  ); ?>

				<ul class="eaccounting-gatways">
					<?php foreach ( $gateways as $gateway ) : ?>
						<li class="eaccounting-gateways gateway_<?php echo esc_attr( $gateway->id ); ?>">
							<input id="gateway_<?php echo esc_attr( $gateway->id ); ?>" type="radio"
							       class="input-radio" name="gateway"
							       value="<?php echo esc_attr( $gateway->id ); ?>"
							<?php checked( $gateway->id, $selected ); ?>"/>

							<label for="gateway_<?php echo esc_attr( $gateway->id ); ?>">
								<?php echo $gateway->get_method_title(); ?>
							</label>

						</li>
					<?php endforeach; ?>
				</ul>

				<div id="eaccounting_checkout_form_fields">
					<?php do_action( 'eaccounting_checkout_form_fields', $invoice  ); ?>
					<?php do_action( 'eaccounting_checkout_form_fields_'. $selected, $invoice  ); ?>
				</div>


				<?php do_action( 'eaccounting_checkout_form_bottom', $invoice  ); ?>


			</form>
			<?php do_action( 'eaccounting_after_checkout_form', $invoice  ); ?>
		</div>
	</div>
</div>
