<?php
/**
 * Cashflow chart.
 */

defined( 'ABSPATH' ) || exit();

$datasets = array();
$years    = range( wp_date( 'Y' ), 2015 );
$year     = eac_get_input_var( 'year', wp_date( 'Y' ) );
$data     = eac_get_profit_report();
?>

<div class="eac-card">
	<div class="eac-card__header">
		<div>
			<?php esc_html_e( 'Cashflow', 'wp-ever-accounting' ); ?>
		</div>
		<div>
			<?php echo esc_html( wp_date( 'Y' ) ); ?>
		</div>
	</div>
	<div class="eac-card__body">
		<div class="eac-chart">
			<canvas id="eac-cashflow-chart" height="300"></canvas>
		</div>
	</div>
</div>

<script type="text/javascript">
	window.addEventListener( 'DOMContentLoaded', function () {
		var sales_expense = document.getElementById("eac-cashflow-chart").getContext("2d");
		new Chart(sales_expense, {
			type: 'bar',
			data: {
				labels: <?php echo wp_json_encode( array_keys( $data['payments'] ) ); ?>,
				datasets: [
					{
						label: "<?php esc_html_e( 'Sales', 'wp-ever-accounting' ); ?>",
						backgroundColor: "#3644ff",
						data: <?php echo wp_json_encode( array_values( $data['payments'] ) ); ?>
					},
					{
						label: "<?php esc_html_e( 'Expenses', 'wp-ever-accounting' ); ?>",
						backgroundColor: "#f2385a",
						data: <?php echo wp_json_encode( array_values( $data['expenses'] ) ); ?>
					},
					{
						type: 'line',
						fill: false,
						label: "<?php esc_html_e( 'Profit', 'wp-ever-accounting' ); ?>",
						backgroundColor: "#00d48f",
						borderColor: "#00d48f",
						data: <?php echo wp_json_encode( array_values( $data['profits'] ) ); ?>
					}
				]
			},
			options: {
				barValueSpacing: 20,
				scales: {
					xAxes: [{
						barThickness: 30
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
	} );
</script>
