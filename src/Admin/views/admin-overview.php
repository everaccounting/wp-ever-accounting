<?php
/**
 * View: Admin Overview
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Overview
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

$payments = eac_get_payments(
	array(
		'limit' => 5,
	)
);
?>
<div class="wrap ea-page">
	<div class="eac-page__header eac-mb-20">
		<div class="eac-page__header-col">
			<h2 class="eac-page__title"><?php echo esc_html__( 'Overview', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-page__header-col">
		</div>
	</div>

	<div class="ea-overview-tiles">
		<?php
		/**
		 * Fires before the overview page content.
		 *
		 * @since 1.1.0
		 */
		do_action( 'eac_before_overview_page' );
		?>
	</div>

	<div class="ea-report-cards eac-mb-20">
		<div class="ea-report-card is--wp">
			<div class="ea-report-card__icon">
				<?php echo eac_get_svg_icon( 'category', 32 ); ?>
			</div>
			<div class="ea-report-card__body">
				<div class="ea-report-card__primary">
					<span class="ea-report-card__title">Sales</span>
					<span class="ea-report-card__value">৳410,569.10</span>
				</div>

				<div class="ea-report-card__secondary">
					<span class="ea-report-card__title">Estimated</span>
					<span class="ea-report-card__value">৳498.00</span>
				</div>

				<div class="ea-report-card__label">
					<?php echo wp_date( 'F Y' ); ?>
				</div>
			</div>
		</div>

		<div class="ea-report-card is--wp">
			<div class="ea-report-card__icon">
				<?php echo eac_get_svg_icon( 'calendar', 32 ); ?>
			</div>
			<div class="ea-report-card__body">
				<div class="ea-report-card__primary">
					<span class="ea-report-card__title">Expenses</span>
					<span class="ea-report-card__value">৳410,569.10</span>
				</div>

				<div class="ea-report-card__secondary">
					<span class="ea-report-card__title">Estimated</span>
					<span class="ea-report-card__value">৳498.00</span>
				</div>

				<div class="ea-report-card__label">
					<?php echo wp_date( 'F Y' ); ?>
				</div>
			</div>
		</div>

		<div class="ea-report-card is--wp">
			<div class="ea-report-card__icon">
				<?php echo eac_get_svg_icon( 'check', 32 ); ?>
			</div>
			<div class="ea-report-card__body">
				<div class="ea-report-card__primary">
					<span class="ea-report-card__title">Profit</span>
					<span class="ea-report-card__value">৳410,569.10</span>
				</div>

				<div class="ea-report-card__secondary">
					<span class="ea-report-card__title">Estimated</span>
					<span class="ea-report-card__value">৳498.00</span>
				</div>

				<div class="ea-report-card__label">
					<?php echo wp_date( 'F Y' ); ?>
				</div>
			</div>
		</div>

		<div class="ea-report-card is--wp">
			<div class="ea-report-card__icon">
				<?php echo eac_get_svg_icon( 'chevron_left', 32 ); ?>
			</div>
			<div class="ea-report-card__body">
				<div class="ea-report-card__primary">
					<span class="ea-report-card__title">Receivable</span>
					<span class="ea-report-card__value">৳410,569.10</span>
				</div>

				<div class="ea-report-card__secondary">
					<span class="ea-report-card__title">overdue</span>
					<span class="ea-report-card__value">৳498.00</span>
				</div>

				<div class="ea-report-card__label">
					<?php echo wp_date( 'F Y' ); ?>
				</div>
			</div>
		</div>

		<div class="ea-report-card is--wp">
			<div class="ea-report-card__icon">
				<?php echo eac_get_svg_icon( 'chevron_right', 32 ); ?>
			</div>
			<div class="ea-report-card__body">
				<div class="ea-report-card__primary">
					<span class="ea-report-card__title">Payable</span>
					<span class="ea-report-card__value">৳410,569.10</span>
				</div>

				<div class="ea-report-card__secondary">
					<span class="ea-report-card__title">overdue</span>
					<span class="ea-report-card__value">৳498.00</span>
				</div>

				<div class="ea-report-card__label">
					<?php echo wp_date( 'F Y' ); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="ea-overview-charts">
		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title">Cashflow</h3>
			</div>
			<div class="eac-card__body">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Maxime, voluptatum!</div>
		</div>
	</div>

	<div class="ea-overview-contents">
		<div class="eac-columns">
			<div class="eac-col-4">
				<div class="eac-card">
					<div class="eac-card__header">
						<h3 class="eac-card__title">Profit & Loss</h3>
					</div>
					<div class="eac-card__body">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Maxime, voluptatum!</div>
				</div>
			</div>
			<div class="eac-col-4">
				<div class="eac-card">
					<div class="eac-card__header">
						<h3 class="eac-card__title">Expenses By Category</h3>
					</div>
					<div class="eac-card__body">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Maxime, voluptatum!</div>
				</div>
			</div>
			<div class="eac-col-4">
				<div class="eac-card">
					<div class="eac-card__header">
						<h3 class="eac-card__title">Income By Category</h3>
					</div>
					<div class="eac-card__body">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Maxime, voluptatum!</div>
				</div>
			</div>
			<div class="eac-col-4">
				<div class="eac-card">
					<div class="eac-card__header">
						<h3 class="eac-card__title">Recent Payments</h3>
					</div>
					<div class="eac-card__body p-0">
						<?php if ( empty( $payments ) ) : ?>
							<p>No payments found.</p>
						<?php else : ?>
							<table class="widefat fixed striped">
								<thead>
								<tr>
									<th><?php esc_html_e( 'Payment', 'wp-ever-accounting' ); ?></th>
									<th><?php esc_html_e( 'Date', 'wp-ever-accounting' ); ?></th>
									<th><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?></th>
								</tr>
								</thead>
								<tbody>
								<?php foreach ( $payments as $payment ) : ?>
									<tr>
										<td><?php echo esc_html( $payment->get_transaction_number() ); ?></td>
										<td><?php echo esc_html( $payment->get_payment_date() ); ?></td>
										<td><?php echo esc_html( eac_price_to_default( $payment->get_amount(), $payment->get_currency_code(), $payment->get_currency_rate(), true ) ); ?></td>
									</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="eac-col-4">
				<div class="eac-card">
					<div class="eac-card__header">
						<h3 class="eac-card__title">Recent Expenses</h3>
					</div>
					<div class="eac-card__body">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Maxime, voluptatum!</div>
				</div>
			</div>
			<div class="eac-col-4">
				<div class="eac-card">
					<div class="eac-card__header">
						<h3 class="eac-card__title">Account Balances</h3>
					</div>
					<div class="eac-card__body">

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
