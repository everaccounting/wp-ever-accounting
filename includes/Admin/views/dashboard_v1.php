<?php
/**
 * View: Admin dashboard
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Overview
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

$revenues = eac_get_revenues( array( 'limit' => 5 ) );
$expenses = eac_get_expenses( array( 'limit' => 5 ) );

?>
<div class="wrap bkit-wrap">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Dashboard', 'wp-ever-accounting' ); ?>
	</h1>
	<hr class="wp-header-end">

	<?php require __DIR__ . '/dashboard/summaries.php'; ?>
	<?php require __DIR__ . '/dashboard/cashflow-chart.php'; ?>

	<div class="tw-grid tw-grid-cols-2 tw-gap-[30px]">
		<?php require __DIR__ . '/dashboard/revenue-categories.php'; ?>
		<?php require __DIR__ . '/dashboard/expense-categories.php'; ?>
	</div>

	<div class="tw-grid tw-grid-cols-3 tw-gap-[30px]">
		<div>
			<?php require __DIR__ . '/dashboard/recent-revenues.php'; ?>
		</div>
		<div>
			<?php require __DIR__ . '/dashboard/recent-expenses.php'; ?>
		</div>
		<div>
			<?php require __DIR__ . '/dashboard/account-balances.php'; ?>
		</div>
	</div>
</div>
