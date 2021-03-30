<?php

?>
<div id="eaccounting-invoice-payment" class="ea-card ea-noprint">
	<div class="ea-card__inside">
		<h3><?php esc_attr_e( 'Payment', 'wp-ever-accounting' ); ?></h3>
		<form id="eaccounting_payment_form" class="eaccounting-form" action="<?php echo esc_url(eaccounting_get_url('checkout'));?>" method="get">
			<ul class="eaccounting-payment-methods">
				<?php foreach ( $gateways as $gateway ) : ?>
					<li class="eaccounting-payment-method payment_method_<?php echo esc_attr( $gateway->id ); ?>">
						<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio"
							   class="input-radio" name="payment_method"
							   value="<?php echo esc_attr( $gateway->id ); ?>"
						<?php checked( $gateway->id, $default_gateway ); ?>"/>

						<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>">
							<?php echo $gateway->get_method_title(); /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php do_action( 'eaccounting_invoice_payment_before_submit' ); ?>
			<input type="hidden" name="invoice_id" value="<?php echo esc_attr( $invoice->get_id() ); ?>">
			<input type="hidden" name="checkout_key" value="<?php echo esc_attr( $invoice->get_key() ); ?>">
			<?php wp_nonce_field( 'eaccounting_invoice_payment' ); ?>
			<input type="submit" class="button alt" name="eaccounting_invoice_payment" id="invoice_payment"
				   value="<?php echo esc_html( $pay_button_text ); ?>"/>
			<?php do_action( 'eaccounting_invoice_payment_after_submit' ); ?>
		</form>
	</div>
</div>
