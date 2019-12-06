<?php
$year = isset( $_REQUEST['year'] ) ? intval( $_REQUEST['year'] ) : '';
?>
	<div class="ea-card ea-report-card">
		<div class="ea-card-header">

			<form action="" class="ea-report-filter">
				<?php
				echo EAccounting_Form::year_dropdown( [
					'placeholder' => __( 'Year', 'wp-ever-accounting' ),
					'selected'    => $year
				] );
				echo EAccounting_Form::button( __( 'Filter', 'wp-ever-accounting' ) );
				?>

				<input type="hidden" name="page" value="eaccounting-reports">
				<input type="hidden" name="tab" value="income_expense_summary">
			</form>

		</div>


		<div class="ea-card-body">

			<div class="ea-report-graph">
				<canvas id="income-expense-summery-graph" height="300" style="width: 100%;"></canvas>
			</div>

			<div class="ea-table-report">
				<table class="ea-table">
					<thead>
					<tr>
						<th><?php _e( 'Categories', 'wp-ever-accounting' ); ?></th>
						<?php foreach ( $dates as $date ): ?>
							<th class="align-right"><?php echo $date; ?></th>
						<?php endforeach; ?>
					</tr>
					</thead>
					<tbody>

					<?php if ( ! empty( $compares ) ): ?>
						<?php foreach ( $compares as $type => $categories ): ?>
							<?php foreach ( $categories as $category_id => $category ): ?>
								<tr>
									<?php if ( 'income' == $type ): ?>
										<td><?php echo $income_categories[ $category_id ]; ?></td>
									<?php else: ?>
										<td><?php echo $expense_categories[ $category_id ]; ?></td>
									<?php endif; ?>
									<?php foreach ( $category as $item ): ?>

										<?php if ( 'income' == $type ): ?>
											<td class="align-right"><?php echo eaccounting_price( $item['amount'] ); ?></td>
										<?php else: ?>
											<td class="align-right">
												-<?php echo eaccounting_price( $item['amount'] ); ?></td>
										<?php endif; ?>
									<?php endforeach; ?>
								</tr>
							<?php endforeach; ?>
						<?php endforeach; ?>
					<?php else: ?>
						<tr>
							<td colspan="13">
								<h5 class="text-center"><?php _e( 'No records found', 'wp-ever-accounting' ); ?></h5>
							</td>
						</tr>
					<?php endif; ?>
					</tbody>

					<tfoot>
					<tr>
						<th><?php _e( 'Total', 'wp-ever-accounting' ); ?></th>
						<?php foreach ( $totals as $total ): ?>
							<th class="align-right"><?php echo eaccounting_price( $total['amount'] ); ?></th>
						<?php endforeach; ?>
					</tr>
					</tfoot>

				</table>
			</div>


		</div>
	</div>
<script>
	jQuery(document).ready(function ($) {
		var ctx = document.getElementById("income-expense-summery-graph");

		var data = {
			labels: <?php echo json_encode(array_values($dates), true );?>,
			datasets: [{
				fill: false,
				label: "Profit",
				lineTension: 0.1,
				borderColor: "#6da252",
				backgroundColor: "#6da252",
				data: <?php echo json_encode(array_values($profit_graph), true );?>,
			},
			]
		};

		var expenseGraph = new Chart(ctx, {
			type: 'line',
			data: data,
			options: {
				responsive: false,
				maintainAspectRatio: false,
				legend: {
					display: true,
					position: 'top'
				},
			}
		});

	});
</script>

