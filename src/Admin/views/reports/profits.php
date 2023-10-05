<?php
/**
 * Sales profits page.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin
 */

defined( 'ABSPATH' ) || exit;

$datasets = array();
$currency = eac_get_currency();
$years    = range( wp_date( 'Y' ), 2015 );
$year     = eac_get_input_var( 'year', wp_date( 'Y' ) );
$data     = eac_get_profit_report( $year );
$labels   = array_keys( $data['profits'] );
$datasets = array(
	'profits' => array(
		'type'            => 'bar',
		'label'           => esc_html__( 'Profits', 'wp-ever-accounting' ),
		'backgroundColor' => eac_get_random_color( 'profits' ),
		'data'            => array_values( $data['profits'] ),
	),
);
?>

<div class="eac-panel align-items-center d-flex justify-content-between">
	<h3 class="eac-panel__title">
		<?php echo esc_html__( 'Profit Report', 'wp-ever-accounting' ); ?>
	</h3>
	<form class="ea-report-filters" method="get" action="">
		<select name="year" class="eac-select">
			<?php foreach ( $years as $y ) : ?>
				<option value="<?php echo esc_attr( $y ); ?>" <?php selected( $y, $year ); ?>>
					<?php echo esc_html( $y ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<button type="submit" class="button">
			<?php echo esc_html__( 'Submit', 'wp-ever-accounting' ); ?>
		</button>
		<input hidden="hidden" name="page" value="eac-reports"/>
		<input hidden="hidden" name="tab" value="profits"/>
	</form>
</div>

<ul class="eac-summaries">
	<li class="eac-summary">
		<div class="eac-summary__label"><?php esc_html_e( 'Total Profit', 'wp-ever-accounting' ); ?></div>
		<div class="eac-summary__data">
			<div class="eac-summary__value"><?php echo esc_html( eac_format_money( $data['total_profit'] ) ); ?></div>
		</div>
	</li>
	<li class="eac-summary">
		<div class="eac-summary__label"><?php esc_html_e( 'Per Month', 'wp-ever-accounting' ); ?></div>
		<div class="eac-summary__data">
			<div class="eac-summary__value"><?php echo esc_html( eac_format_money( $data['month_avg'] ) ); ?></div>
		</div>
	</li>
	<li class="eac-summary">
		<div class="eac-summary__label"><?php esc_html_e( 'Per Day', 'wp-ever-accounting' ); ?></div>
		<div class="eac-summary__data">
			<div class="eac-summary__value"><?php echo esc_html( eac_format_money( $data['daily_avg'] ) ); ?></div>
		</div>
	</li>
</ul>

<div class="eac-card">
	<div class="eac-card__header">
		<h3 class="eac-card__title"><?php esc_html_e( 'Chart', 'wp-ever-accounting' ); ?></h3>
	</div>
	<div class="eac-card__body">
		<div class="eac-chart">
			<canvas id="eac-profit-chart" style="min-height: 300px;"></canvas>
		</div>
	</div>
</div>


<div class="eac-card">
	<div class="eac-card__header">
		<h3 class="eac-card__title"><?php esc_html_e( 'Profits by Months', 'wp-ever-accounting' ); ?></h3>
	</div>
	<div class="eac-card__body padding-0">
		<div class="eac-overflow-x">
			<table class="widefat striped eac-report-table border-0">
				<thead>
				<tr>
					<th><?php esc_html_e( 'Month', 'wp-ever-accounting' ); ?></th>
					<?php foreach ( array_keys( $data['profits'] ) as $label ) : ?>
						<th><?php echo esc_html( $label ); ?></th>
					<?php endforeach; ?>
				</tr>
				</thead>
				<tbody>
				<tr>
					<th><?php esc_html_e( 'Payments', 'wp-ever-accounting' ); ?></th>
					<?php foreach ( $data['payments'] as $value ) : ?>
						<td><?php echo esc_html( eac_format_money( $value ) ); ?></td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Expenses', 'wp-ever-accounting' ); ?></th>
					<?php foreach ( $data['expenses'] as $value ) : ?>
						<td><?php echo esc_html( eac_format_money( "$value" ) ); ?></td>
					<?php endforeach; ?>
				</tr>
				</tbody>
				<tfoot>
				<tr>
					<th><?php esc_html_e( 'Profit', 'wp-ever-accounting' ); ?></th>
					<?php foreach ( $data['profits'] as $value ) : ?>
						<th><?php echo esc_html( eac_format_money( $value ) ); ?></th>
					<?php endforeach; ?>
				</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>


<script type="text/javascript">
	window.onload = function () {
		var ctx = document.getElementById("eac-profit-chart").getContext('2d');
		var symbol = "<?php echo esc_html( $currency ? $currency->get_symbol() : '' ); ?>";
		var myChart = new Chart(ctx, {
			type: 'bar',
			minHeight: 500,
			data: {
				labels: <?php echo wp_json_encode( array_values( $labels ) ); ?>,
				datasets:[
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
						label: "<?php esc_html_e( 'Profit', 'wp-ever-accounting' ); ?>",
						backgroundColor: "#00d48f",
						data: <?php echo wp_json_encode( array_values( $data['profits'] ) ); ?>
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
							return datasetLabel + ': ' + value.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + symbol;
						}
					}
				},
				scales: {
					xAxes: [{
						stacked: false,
						gridLines: {
							display: true,
						}
					}],
					yAxes: [{
						stacked: false,
						ticks: {
							beginAtZero: true,
							callback: function (value, index, ticks) {
								return Number(value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + symbol;
							}
						},
						type: 'linear',
						barPercentage: 0.4
					}]
				},
				responsive: true,
				maintainAspectRatio: false,
				legend: {position: 'top'},
			}
		});
	}
</script>
