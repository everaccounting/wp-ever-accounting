<?php
$year        = isset( $_REQUEST['year'] ) ? intval( $_REQUEST['year'] ) : '';
$category_id = isset( $_REQUEST['category_id'] ) ? intval( $_REQUEST['category_id'] ) : '';
$account_id  = isset( $_REQUEST['account_id'] ) ? intval( $_REQUEST['account_id'] ) : '';
$customer_id = isset( $_REQUEST['customer_id'] ) ? intval( $_REQUEST['customer_id'] ) : '';
?>
<div class="ea-card ea-report-card">
	<div class="ea-card-header">

		<form action="" class="ea-report-filter">
			<?php
			echo EAccounting_Form::year_dropdown( [
				'placeholder' => __( 'Year', 'wp-ever-accounting' ),
				'selected'    => $year
			] );
			echo EAccounting_Form::accounts_dropdown( [
				'placeholder' => __( 'Account', 'wp-ever-accounting' ),
				'name'        => 'account_id',
				'selected'    => $account_id
			] );
			echo EAccounting_Form::customer_dropdown( [
				'placeholder' => __( 'Customer', 'wp-ever-accounting' ),
				'name'        => 'customer_id',
				'selected'    => $customer_id
			] );
			echo EAccounting_Form::categories_dropdown( [
				'placeholder' => __( 'Category', 'wp-ever-accounting' ),
				'type'        => 'income',
				'name'        => 'category_id',
				'selected'    => $category_id
			] );
			echo EAccounting_Form::button( __( 'Filter', 'wp-ever-accounting' ) );
			?>

			<input type="hidden" name="page" value="eaccounting-reports">
			<input type="hidden" name="tab" value="income_summery">
		</form>

	</div>

	<div class="ea-card-body">

		<div class="ea-report-graph">
			<canvas id="income-summer-graph" height="300" style="width: 100%;"></canvas>
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

				<?php if ( ! empty( $incomes ) ): ?>
					<?php foreach ( $incomes as $category_id => $category ): ?>
						<tr>
							<td><?php echo $categories[ $category_id ]; ?></td>
							<?php foreach ( $category as $item ): ?>
								<td class="align-right"><?php echo eaccounting_price( $item['amount'] ); ?></td>
							<?php endforeach; ?>
						</tr>
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
		var ctx = document.getElementById("income-summer-graph");

		var data = {
			labels: <?php echo json_encode(array_values($dates), true );?>,
			datasets: [{
					fill: false,
					label: "Income",
					lineTension: 0.1,
					borderColor: "#00c0ef",
					backgroundColor: "#00c0ef",
					data: <?php echo json_encode(array_values($incomes_graph), true );?>,
				},
			]
		};

		var incomeGraph = new Chart(ctx, {
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
