<?php
/**
 * View: Admin Overview
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Overview
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();
$profit_report     = eac_get_profit_report();
$recent_payments    = eac_get_payments( array( 'limit' => 5 ) );
$recent_expenses   = eac_get_expenses( array( 'limit' => 5 ) );
$bank_accounts     = eac_get_accounts(
	array(
		'type'  => 'bank',
		'limit' => 5,
	)
);
?>

<div class="eac-page-section">
	<h2><?php esc_html_e( 'Overview', 'wp-ever-accounting' ); ?></h2>
</div>

<?php include __DIR__ . '/overview/summaries.php'; ?>

<?php include __DIR__ . '/overview/cashflow-chart.php'; ?>

<div class="eac-columns">
	<div class="eac-col-6">
		<div class="eac-card">
			<div class="eac-card__header"><?php esc_html_e( 'Profit & Loss', 'wp-ever-accounting' ); ?></div>
			<div class="eac-card__body">
				Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem dicta eos minima nulla soluta ut. Aspernatur cum dicta ex, iste laudantium minima numquam odio ratione repudiandae voluptatibus? Ad facere incidunt itaque iure iusto molestiae natus necessitatibus odio! At autem blanditiis cum debitis dicta dolorem doloribus eligendi eum ex expedita fuga, harum hic ipsum iure labore laboriosam magni maiores minus mollitia necessitatibus neque nostrum nulla odio pariatur placeat
				possimus, quae quam quas quidem quo rerum, ullam unde ut veniam vero voluptatem voluptates. Aliquam amet beatae et, expedita fuga inventore itaque iusto laboriosam minus nobis placeat porro praesentium reprehenderit sit veniam, voluptatum.
			</div>
		</div>
	</div>
	<div class="eac-col-6">
		<?php include __DIR__ . '/overview/expense-chart.php'; ?>
	</div>
</div>
<div class="eac-columns">
	<div class="eac-col-4">
		<div class="eac-card">
			<div class="eac-card__header"><?php esc_html_e( 'Recent payments', 'wp-ever-accounting' ); ?></div>
			<div class="eac-card__body is--padding-0">
				<table class="widefat striped is--border-0">
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
								<td><?php echo esc_html( $payment->category_name ); ?></td>
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
			<div class="eac-card__header"><?php esc_html_e( 'Recent Expenses', 'wp-ever-accounting' ); ?></div>
			<div class="eac-card__body is--padding-0">
				<table class="widefat striped is--border-0">
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
								<td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $expense->date ) ) ); ?></td>
								<td><?php echo esc_html( eac_format_money( $expense->amount ) ); ?></td>
								<td><?php echo esc_html( $expense->category_name ); ?></td>
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
			<div class="eac-card__header"><?php esc_html_e( 'Account Balances', 'wp-ever-accounting' ); ?></div>
			<div class="eac-card__body is--padding-0">
				<table class="widefat striped is--border-0">
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
<script type="text/javascript">
	window.onload = function () {
		var sales_expense = document.getElementById( "eac-sales-expenses-chart" ).getContext( "2d" );
		new Chart(sales_expense, {
			type: 'bar',
			data: {
				labels: <?php echo wp_json_encode( array_keys( $profit_report['payments'] ) ); ?>,
				datasets: [
					{
						label: "<?php esc_html_e( 'Sales', 'wp-ever-accounting' ); ?>",
						backgroundColor: "#3644ff",
						data: <?php echo wp_json_encode( array_values( $profit_report['payments'] ) ); ?>
					},
					{
						label: "<?php esc_html_e( 'Expenses', 'wp-ever-accounting' ); ?>",
						backgroundColor: "#f2385a",
						data: <?php echo wp_json_encode( array_values( $profit_report['expenses'] ) ); ?>
					},
					{
						type: 'line',
						fill: false,
						label: "<?php esc_html_e( 'Profit', 'wp-ever-accounting' ); ?>",
						backgroundColor: "#00d48f",
						data: <?php echo wp_json_encode( array_values( $profit_report['profits'] ) ); ?>
					}
				]
			},
			options: {
				barValueSpacing: 20,
				scales: {
					xAxes: [{
						barThickness : 30
					}],
					yAxes: [{
						ticks: {
							min: 0,
						}
					}]
				},
				responsive: true,
				maintainAspectRatio: false,
				legend: {position: 'top'},
			}
		});
	}

</script>
