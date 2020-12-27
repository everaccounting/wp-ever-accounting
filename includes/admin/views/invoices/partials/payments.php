<?php
/**
 * Shows Invoice payments.
 * Used in view invoice page.
 *
 * @package EverAccounting\Admin
 * @var \EverAccounting\Models\Invoice $invoice The item being used
 */

$payments = $invoice->get_payments();
?>
<div class="ea-card" id="ea-invoice-payments">
	<div class="ea-card__header">
		<h3 class="ea-card__title">
			<?php esc_html_e( 'Payments Received', 'wp-ever-accounting' ); ?>
		</h3>
	</div>
	<?php if ( empty( $payments ) ) : ?>

		<div class="ea-card__inside">
			<?php echo sprintf( '<p>%s</p>', __( 'There are no payment received yet.', 'wp-ever-accounting' ) ); ?>
		</div>

	<?php else : ?>

		<table class="ea-card__body ea-invoice-payment widefat" style="border: 0;">
			<thead>
			<tr>
				<th><?php esc_html_e( 'Date', 'wp-ever-accounting' ); ?></th>
				<th><?php esc_html_e( 'Payment', 'wp-ever-accounting' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $payments as $payment ) : ?>
				<tr>
					<td><?php echo esc_html( $payment->get_payment_date() ); ?></td>
					<td>
						<abbr title="<?php echo esc_attr( eaccounting_price( $payment->get_amount(), $payment->get_currency_code() ) ); ?>">
							<?php echo esc_html( eaccounting_price( eaccounting_price_convert_between( $payment->get_amount(), $payment->get_currency_code(), $payment->get_currency_rate(), $invoice->get_currency_code(), $invoice->get_currency_rate() ), $invoice->get_currency_code() ) ); ?>
						</abbr>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>

</div>
