<?php
/**
 * Shows notes
 *
 * @package EverAccounting\Admin
 * @var \EverAccounting\Models\Invoice $invoice The item being used
 */

$payments = $invoice->get_payments();
?>

<?php if ( empty( $payments ) ) : ?>
	<?php echo sprintf( '<p>%s</p>', __( 'There are no payment received yet.', 'wp-ever-accounting' ) ); ?>
<?php else : ?>
<table class="ea-invoice-payment widefat" style="border: 0;">
	<thead>
		<tr>
			<th>Date</th>
			<th>Payment</th>
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
	<?php
endif;
