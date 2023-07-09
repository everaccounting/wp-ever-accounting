<?php
/**
 * Cashflow chart.
 */

defined( 'ABSPATH' ) || exit();

$datasets = array();
$years    = range( wp_date( 'Y' ), 2015 );
$year     = eac_get_input_var( 'year', wp_date( 'Y' ) );
$data     = eac_get_profit_report();
$currency = eac_get_currency();
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
	window.addEventListener('DOMContentLoaded', function () {
		var sales_expense = document.getElementById("eac-cashflow-chart").getContext("2d");
		var symbol = "<?php echo esc_html( $currency ? $currency->get_symbol() : '' ); ?>";
		new Chart(sales_expense, {
			type: 'line',
			data: {
				labels: <?php echo wp_json_encode( array_keys( $data['payments'] ) ); ?>,
				datasets: [
					{
						label: "<?php esc_html_e( 'Incoming', 'wp-ever-accounting' ); ?>",
						backgroundColor: "#3644ff",
						data: <?php echo wp_json_encode( array_values( $data['payments'] ) ); ?>
					},
					{
						label: "<?php esc_html_e( 'Outgoing', 'wp-ever-accounting' ); ?>",
						backgroundColor: "#f2385a",
						data: <?php echo wp_json_encode( array_values( $data['expenses'] ) ); ?>
					},
					{
						type: 'line',
						// fill: false,
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
						stacked: true,
						barThickness: 30
					}],
					yAxes: [{
						stacked: true,
						barThickness: 30,
						ticks: {
							callback: function (value, index, ticks) {
								return Number(value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+ symbol;
							}
						}
					}]
				},
				tooltips: {
					displayColors: true,
					YrPadding: 12,
					backgroundColor: "#000000",
					bodyFontColor: "#e5e5e5",
					bodySpacing: 4,
					intersect: 0,
					mode: "nearest",
					position: "nearest",
					titleFontColor: "#ffffff",
					callbacks: {
						label: function (tooltipItem, data) {
							let value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
							let datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
							return datasetLabel + ': ' + value.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+symbol;
						}
					}
				},
				responsive: true,
				maintainAspectRatio: false,
				legend: {position: 'top'},
			}
		});
	});
</script>
