<?php
/**
 * View: Admin dashboard
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Overview
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

$payments = eac_get_payments( array( 'limit' => 5 ) );
$expenses = eac_get_expenses( array( 'limit' => 5 ) );

?>
<div class="wrap eac-wrap">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Dashboard', 'wp-ever-accounting' ); ?>
	</h1>
	<hr class="wp-header-end">

	<div class="eac-metrics">
		<?php for ( $i = 1; $i <= 8; $i++ ) : ?>
		<div class="eac-metric">
			<span class="eac-metric__legend">Monthly</span>
			<h4 class="eac-metric__label"><?php esc_html_e( 'Total Payments', 'wp-ever-accounting' ); ?></h4>
			<div class="eac-metric__value">
				$<?php echo number_format( wp_rand( 1000, 1000000 ), 2 ); ?>
				<span class="eac-metric__delta eac-metric__delta--positive">
					+12%
				</span>
			</div>
		</div>
		<?php endfor; ?>
	</div>

	<?php require __DIR__ . '/dashboard/cashflow-chart.php'; ?>

	<div class="tw-grid tw-grid-cols-2 tw-gap-[30px]">
		<?php require __DIR__ . '/dashboard/payment-categories.php'; ?>
		<?php require __DIR__ . '/dashboard/expense-categories.php'; ?>
	</div>

	<div class="tw-grid tw-grid-cols-3 tw-gap-[30px]">
		<div>
			<?php require __DIR__ . '/dashboard/recent-payments.php'; ?>
		</div>
		<div>
			<?php require __DIR__ . '/dashboard/recent-expenses.php'; ?>
		</div>
		<div>
			<?php require __DIR__ . '/dashboard/account-balances.php'; ?>
		</div>
	</div>
</div>
