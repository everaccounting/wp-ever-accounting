<?php
defined( 'ABSPATH' ) || exit();
?>
<div class="wrap ea-wrapper">
	<h1 class="wp-heading-inline ea-mb-10"><?php _e( 'Dashboard', 'wp-ever-accounting' ); ?></h1>

	<div class="ea-row">


		<div class="ea-col-4">
			<div class="ea-summery-box income">
				<a href="#">
					<span class="ea-summery-box-icon">
						<i class="fa fa-money"></i>
					</span>
				</a>

				<div class="ea-summery-box-content">
					<span class="ea-summery-box-text">Total Incomes</span>
					<span
						class="ea-summery-box-number"><?php echo eaccounting_price( eaccounting_get_total_income() ); ?></span>
				</div>

			</div>
		</div>

		<div class="ea-col-4">
			<div class="ea-summery-box expense">
				<a href="#">
					<span class="ea-summery-box-icon">
						<i class="fa fa-shopping-cart"></i>
					</span>
				</a>

				<div class="ea-summery-box-content">
					<span class="ea-summery-box-text">Total Expenses</span>
					<span
						class="ea-summery-box-number"><?php echo eaccounting_price( eaccounting_get_total_expense() ); ?></span>
				</div>

			</div>
		</div>

		<div class="ea-col-4">
			<div class="ea-summery-box profit">
				<a href="#">
					<span class="ea-summery-box-icon">
						<i class="fa fa-heart"></i>
					</span>
				</a>

				<div class="ea-summery-box-content">
					<span class="ea-summery-box-text">Total Profit</span>
					<span
						class="ea-summery-box-number"><?php echo eaccounting_price( eaccounting_get_total_profit() ); ?></span>
				</div>

			</div>
		</div>

		<div class="ea-col-6">
			<div class="ea-card">
				<div class="ea-card-header">
					<h3 class="ea-card-title"><?php _e( 'Expense By Categories', 'wp-ever-accounting' ); ?></h3>
				</div>
				<div class="ea-card-body">
					<canvas id="ea-expense-by-categories"></canvas>
				</div>
			</div>
		</div>

		<div class="ea-col-6">
			<div class="ea-card">
				<div class="ea-card-header">
					<h3 class="ea-card-title"><?php _e( 'Income By Categories', 'wp-ever-accounting' ); ?></h3>
				</div>
				<div class="ea-card-body">
					<canvas id="ea-income-by-categories"></canvas>
				</div>
			</div>
		</div>

		<div class="ea-col-6">
			<div class="ea-card">
				<div class="ea-card-header">
					<h3 class="ea-card-title"><?php _e( 'Latest Expense', 'wp-ever-accounting' ); ?></h3>
				</div>
				<div class="ea-card-body">
					<?php
					$payments = eaccounting_get_payments( [ 'per_page' => 5 ] );
					if ( empty( $payments ) ) {
						echo sprintf( '<p>%s</p>', __( 'No expenses found', 'wp-ever-accounting' ) );
					} else {
						?>
						<table class="ea-table">
							<thead>
							<tr>
								<th><?php _e( 'Date', 'wp-ever-accounting' ); ?></th>
								<th><?php _e( 'Category', 'wp-ever-accounting' ); ?></th>
								<th><?php _e( 'Amount', 'wp-ever-accounting' ); ?></th>
							</tr>
							</thead>
							<?php
							foreach ( $payments as $payment ) {
								$item = new EAccounting_Payment( $payment );
								echo sprintf( '<tr><td>%1$s</td><td>%2$s</td><td>%3$s</td></tr>', $item->get_paid_at(), $item->get_category( 'view' ), $item->get_amount( 'view' ) );
							} ?>
							<tbody>

							</tbody>

						</table>
						<?php
					} ?>
				</div>
			</div>
		</div>

		<div class="ea-col-6">
			<div class="ea-card">
				<div class="ea-card-header">
					<h3 class="ea-card-title"><?php _e( 'Latest Income', 'wp-ever-accounting' ); ?></h3>
				</div>
				<div class="ea-card-body">
					<?php
					$incomes = eaccounting_get_revenues( [ 'per_page' => 5 ] );
					if ( empty( $incomes ) ) {
						echo sprintf( '<p>%s</p>', __( 'No incomes found', 'wp-ever-accounting' ) );
					} else {
						?>
						<table class="ea-table">
							<thead>
							<tr>
								<th><?php _e( 'Date', 'wp-ever-accounting' ); ?></th>
								<th><?php _e( 'Category', 'wp-ever-accounting' ); ?></th>
								<th><?php _e( 'Amount', 'wp-ever-accounting' ); ?></th>
							</tr>
							</thead>
							<?php
							foreach ( $incomes as $income ) {
								$item = new EAccounting_Revenue( $income );
								echo sprintf( '<tr><td>%1$s</td><td>%2$s</td><td>%3$s</td></tr>', $item->get_paid_at(), $item->get_category( 'view' ), $item->get_amount( 'view' ) );
							} ?>
							<tbody>

							</tbody>

						</table>
						<?php
					} ?>
				</div>
			</div>
		</div>


	</div>


</div>

<script>
	<?php
	$expenses = eaccounting_get_expense_by_categories();
	$expenses_labels = [];
	$expenses_colors = [];
	$expenses_data = [];
	foreach ( $expenses as $expense ) {
		$expenses_labels[] = sprintf( "%s - %s", html_entity_decode( eaccounting_price( $expense['total'] ) ), $expense['name'] );
		$expenses_colors[] = $expense['color'];
		$expenses_data[]   = $expense['total'];
	}

	$incomes = eaccounting_get_income_by_categories();
	$incomes_labels = [];
	$incomes_colors = [];
	$incomes_data = [];
	foreach ( $incomes as $income ) {
		$incomes_labels[] = sprintf( "%s - %s", html_entity_decode( eaccounting_price( $income['total'] ) ), $income['name'] );
		$incomes_colors[] = $income['color'];
		$incomes_data[]   = $income['total'];
	}

	?>
	jQuery(document).ready(function ($) {
		var ea_expenses = document.getElementById('ea-expense-by-categories');
		new Chart(ea_expenses, {
			type: 'doughnut',
			data: {
				labels: <?php echo json_encode( $expenses_labels );?>,
				datasets: [{
					data: <?php echo json_encode( $expenses_data );?>,
					backgroundColor: <?php echo json_encode( $expenses_colors );?>,
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				legend: {
					display: true,
					fullWidth: true,
					position: 'right',
				},
				tooltips: {
					callbacks: {
						label: function (tooltipItem, data) {
							var allData = data.datasets[tooltipItem.datasetIndex].data;
							var tooltipLabel = data.labels[tooltipItem.index];
							var tooltipData = allData[tooltipItem.index];
							var total = 0;

							var label = tooltipLabel.split(" - ");

							for (var i in allData) {
								total += allData[i];
							}

							var tooltipPercentage = Math.round((tooltipData / total) * 100);

							return label[1] + ': ' + label[0] + ' (' + tooltipPercentage + '%)';
						}
					}
				},
			}
		});

		var ea_incomes = document.getElementById('ea-income-by-categories');
		new Chart(ea_incomes, {
			type: 'doughnut',
			data: {
				labels: <?php echo json_encode( $incomes_labels );?>,
				datasets: [{
					data: <?php echo json_encode( $incomes_data );?>,
					backgroundColor: <?php echo json_encode( $incomes_colors );?>,
				}]
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				legend: {
					display: true,
					fullWidth: true,
					position: 'right',
				},
				tooltips: {
					callbacks: {
						label: function (tooltipItem, data) {
							var allData = data.datasets[tooltipItem.datasetIndex].data;
							var tooltipLabel = data.labels[tooltipItem.index];
							var tooltipData = allData[tooltipItem.index];
							var total = 0;

							var label = tooltipLabel.split(" - ");

							for (var i in allData) {
								total += allData[i];
							}

							var tooltipPercentage = Math.round((tooltipData / total) * 100);

							return label[1] + ': ' + label[0] + ' (' + tooltipPercentage + '%)';
						}
					}
				},
			}
		});


	})
</script>
