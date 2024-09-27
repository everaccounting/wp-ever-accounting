<?php
/**
 * Recent payments
 *
 * @package WP Ever Accounting
 * @since   1.1.6
 */

defined( 'ABSPATH' ) || exit;

$payments = eac_get_payments(
	array(
		'limit'   => 5,
		'orderby' => 'date',
		'order'   => 'DESC',
	)
);
?>
<div class="eac-card">
	<div class="eac-card__header">
		<?php esc_html_e( 'Recent payments', 'wp-ever-accounting' ); ?>
		<?php if ( ! empty( $payments ) ) : ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=payments' ) ); ?>" class="eac-card__header__link"><?php esc_html_e( 'View all', 'wp-ever-accounting' ); ?></a>
		<?php endif; ?>
	</div>
	<div class="eac-card__body !tw-p-0">
		<table class="eac-table is--striped">
			<thead>
			<tr>
				<th><?php esc_html_e( '#', 'wp-ever-accounting' ); ?></th>
				<th><?php esc_html_e( 'Date', 'wp-ever-accounting' ); ?></th>
				<th><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php if ( ! empty( $payments ) ) : ?>
				<?php foreach ( $payments as $payment ) : ?>
					<tr>
						<td>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=payments&view=' . $payment->id ) ); ?>">
								<?php echo esc_html( $payment->number ); ?>
							</a>
						</td>
						<td><?php echo esc_html( $payment->date ); ?></td>
						<td><?php echo esc_html( $payment->formatted_amount ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="3"><?php esc_html_e( 'No payments found.', 'wp-ever-accounting' ); ?></td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
