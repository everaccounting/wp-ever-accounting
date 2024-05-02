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
$accounts = eac_get_accounts(
	array(
		'type'  => 'bank',
		'limit' => 5,
	)
);

?>
<div class="wrap bkit-wrap">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Dashboard', 'wp-ever-accounting' ); ?>
	</h1>
	<hr class="wp-header-end">

	<?php //require __DIR__ . '/dashboard/summaries.php'; ?>
	<?php include __DIR__ . '/dashboard/cashflow-chart.php'; ?>

	<div class="tw-grid tw-grid-cols-2 tw-gap-[30px]">
		<?php include __DIR__ . '/dashboard/revenue-categories.php'; ?>
		<?php include __DIR__ . '/dashboard/expense-categories.php'; ?>
	</div>

	<div class="tw-grid tw-grid-cols-3 tw-gap-[30px]">
		<div>1</div>
		<div>2</div>
		<div class="bkit-card">
			<div class="bkit-card__header">
				<?php esc_html_e( 'Account Balances', 'wp-ever-accounting' ); ?>
				<?php if ( ! empty( $accounts ) ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-banking&tab=accounts' ) ); ?>" class="bkit-card__header__link"><?php esc_html_e( 'View all', 'wp-ever-accounting' ); ?></a>
				<?php endif; ?>
			</div>
			<div class="bkit-card__body !tw-p-0">
				<table class="eac-table is--striped">
					<thead>
					<tr>
						<th><?php esc_html_e( 'Account', 'wp-ever-accounting' ); ?></th>
						<th><?php esc_html_e( 'Balance', 'wp-ever-accounting' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php if ( ! empty( $accounts ) ) : ?>
						<?php foreach ( $accounts as $account ) : ?>
							<tr>
								<td><?php echo esc_html( $account->name ); ?></td>
								<td><?php echo esc_html( $account->formatted_balance ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="2"><?php esc_html_e( 'No account found.', 'wp-ever-accounting' ); ?></td>
						</tr>
					<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
