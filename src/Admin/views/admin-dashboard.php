<?php
/**
 * View: Admin dashboard
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Overview
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();
$profit_report = eac_get_profit_report();
$payments      = eac_get_payments( array( 'limit' => 5 ) );
$expenses      = eac_get_expenses( array( 'limit' => 5 ) );
$accounts      = eac_get_accounts(
	array(
		'type'  => 'bank',
		'limit' => 5,
	)
);
?>

<div class="eac-page-section">
	<h2><?php esc_html_e( 'Overview', 'wp-ever-accounting' ); ?></h2>
</div>

<?php require __DIR__ . '/dashboard/summaries.php'; ?>

<?php require __DIR__ . '/dashboard/cashflow-chart.php'; ?>

<div class="eac-columns">
	<div class="eac-col-6">
		<?php require __DIR__ . '/dashboard/payment-categories.php'; ?>
	</div>
	<div class="eac-col-6">
		<?php require __DIR__ . '/dashboard/expense-categories.php'; ?>
	</div>
</div>
<div class="eac-columns">
	<div class="eac-col-4">
		<div class="eac-card">
			<div class="eac-card__header">
				<?php esc_html_e( 'Recent payments', 'wp-ever-accounting' ); ?>
				<?php if ( ! empty( $payments ) ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=payments' ) ); ?>" class="eac-card__header__link"><?php esc_html_e( 'View all', 'wp-ever-accounting' ); ?></a>
				<?php endif; ?>
			</div>
			<div class="eac-card__body padding-0">
				<table class="widefat striped border-0">
					<thead>
					<tr>
						<th><?php esc_html_e( 'Date', 'wp-ever-accounting' ); ?></th>
						<th><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?></th>
						<th><?php esc_html_e( 'Category', 'wp-ever-accounting' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php if ( ! empty( $payments ) ) : ?>
						<?php foreach ( $payments as $payment ) : ?>
							<tr>
								<td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $payment->date ) ) ); ?></td>
								<td><?php echo esc_html( eac_format_money( $payment->amount ) ); ?></td>
								<td>
									<?php
									$category      = eac_get_category( $payment->get_category_id() );
									$category_name = $category && $category->get_name() ? $category->get_name() : __( 'Uncategorized', 'wp-ever-accounting' );
									echo esc_html( $category_name );
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="3"><?php esc_html_e( 'No payment found.', 'wp-ever-accounting' ); ?></td>
						</tr>
					<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="eac-col-4">
		<div class="eac-card">
			<div class="eac-card__header">
				<?php esc_html_e( 'Recent Expenses', 'wp-ever-accounting' ); ?>
				<?php if ( ! empty( $expenses ) ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-purchases&tab=expenses' ) ); ?>" class="eac-card__header__link"><?php esc_html_e( 'View all', 'wp-ever-accounting' ); ?></a>
				<?php endif; ?>
			</div>
			<div class="eac-card__body padding-0">
				<table class="widefat striped border-0">
					<thead>
					<tr>
						<th><?php esc_html_e( 'Date', 'wp-ever-accounting' ); ?></th>
						<th><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?></th>
						<th><?php esc_html_e( 'Category', 'wp-ever-accounting' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php if ( ! empty( $expenses ) ) : ?>
						<?php foreach ( $expenses as $expense ) : ?>
							<tr>
								<td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $expense->get_date() ) ) ); ?></td>
								<td><?php echo esc_html( $expense->get_formatted_amount() ); ?></td>
								<td>
									<?php
									$category      = eac_get_category( $expense->get_category_id() );
									$category_name = $category && $category->get_name() ? $category->get_name() : __( 'Uncategorized', 'wp-ever-accounting' );
									echo esc_html( $category_name );
									?>
								</td>

							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="3"><?php esc_html_e( 'No expense found.', 'wp-ever-accounting' ); ?></td>
						</tr>
					<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="eac-col-4">
		<div class="eac-card">
			<div class="eac-card__header">
				<?php esc_html_e( 'Account Balances', 'wp-ever-accounting' ); ?>
				<?php if ( ! empty( $accounts ) ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-banking&tab=accounts' ) ); ?>" class="eac-card__header__link"><?php esc_html_e( 'View all', 'wp-ever-accounting' ); ?></a>
				<?php endif; ?>
			</div>
			<div class="eac-card__body padding-0">
				<table class="widefat striped border-0">
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
								<td><?php echo esc_html( eac_format_money( $account->balance ) ); ?></td>
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
