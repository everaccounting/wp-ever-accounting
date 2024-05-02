<?php
/**
 * Sales payments page.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin
 * @subpackage Admin/Views/Reports
 */

defined( 'ABSPATH' ) || exit;

$datasets = array();
$currency = eac_get_currency();
$years    = range( wp_date( 'Y' ), 2015 );

// TODO: Need to update these bellow two php variables with the dynamic values.
$year     = wp_date( 'Y' ); // eac_get_input_var( 'year', wp_date( 'Y' ) );
// $data     = eac_get_payment_report( $year );
$data = array(
	"total_amount" => 0,
	"total_count" => 0,
	"daily_avg" => 0,
	"month_avg" => 0,
	"date_count" => 122,
	"months" => array(
		"Jan, 24" => 0,
		"Feb, 24" => 0,
		"Mar, 24" => 0,
		"Apr, 24" => 0,
		"May, 24" => 0,
	),
	"categories" => array(),
);

$labels   = array_keys( $data['months'] );
$datasets['total'] = array(
	'type'            => 'line',
	'fill'            => false,
	'label'           => esc_html__( 'Total', 'wp-ever-accounting' ),
	'backgroundColor' => '#3644ff',
	'borderColor'     => '#3644ff',
	'data'            => array_values( $data['months'] ),
);
?>

<div class="bkit-panel">
	<div class="bkit-panel-inner tw-flex tw-justify-between tw-items-center">
		<h3 class="bkit-panel__title">
			<?php echo esc_html__( 'Taxes Report', 'wp-ever-accounting' ); ?>
		</h3>
		<form class="eac-report-filters" method="get" action="">
			<select name="year" class="bkit-select">
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
			<input hidden="hidden" name="tab" value="payments"/>
		</form>
	</div>
</div>

<ul class="eac-summaries">
	<li class="eac-summary">
		<div class="eac-summary__label"><?php esc_html_e( 'Total Taxes', 'wp-ever-accounting' ); ?></div>
		<div class="eac-summary__data">
			<div class="eac-summary__value"><?php echo esc_html( eac_format_amount( $data['total_amount'] ) ); ?></div>
		</div>
	</li>
	<li class="eac-summary">
		<div class="eac-summary__label"><?php esc_html_e( 'Per Month', 'wp-ever-accounting' ); ?></div>
		<div class="eac-summary__data">
			<div class="eac-summary__value"><?php echo esc_html( eac_format_amount( $data['month_avg'] ) ); ?></div>
		</div>
	</li>
	<li class="eac-summary">
		<div class="eac-summary__label"><?php esc_html_e( 'Per Day', 'wp-ever-accounting' ); ?></div>
		<div class="eac-summary__data">
			<div class="eac-summary__value"><?php echo esc_html( eac_format_amount( $data['daily_avg'] ) ); ?></div>
		</div>
	</li>
</ul>

<div class="bkit-card">
	<div class="bkit-card__header">
		<h3 class="bkit-card__title"><?php esc_html_e( 'Chart', 'wp-ever-accounting' ); ?></h3>
	</div>
	<div class="bkit-card__body">
		<div class="eac-chart">
			<canvas id="eac-payment-chart" style="min-height: 300px;"></canvas>
		</div>
	</div>
</div>


<div class="bkit-card">
	<div class="bkit-card__header">
		<h3 class="bkit-card__title"><?php esc_html_e( 'Taxes by Months', 'wp-ever-accounting' ); ?></h3>
	</div>
	<div class="bkit-card__body padding-0">
		<div class="bkit-overflow-x">
			<table class="widefat striped eac-report-table border-0">
				<thead>
				<tr>
					<th><?php esc_html_e( 'Month', 'wp-ever-accounting' ); ?></th>
					<?php foreach ( array_keys( $data['months'] ) as $label ) : ?>
						<th><?php echo esc_html( $label ); ?></th>
					<?php endforeach; ?>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td colspan="<?php echo count( $data['months'] ) + 1; ?>">
						<?php esc_html_e( 'No data found', 'wp-ever-accounting' ); ?>
					</td>
				</tr>
				</tbody>
				<tfoot>
				<tr>
					<th><?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?></th>
					<?php foreach ( $data['months'] as $value ) : ?>
						<th><?php echo esc_html( eac_format_amount( $value ) ); ?></th>
					<?php endforeach; ?>
				</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>


<script type="text/javascript">
	window.onload = function () {
		var ctx = document.getElementById("eac-payment-chart").getContext('2d');
		var symbol = "<?php echo esc_html( $currency ? $currency->get_symbol() : '' ); ?>";
		var myChart = new Chart(ctx, {
			type: 'bar',
			minHeight: 500,
			data: {
				labels: <?php echo wp_json_encode( array_values( $labels ) ); ?>,
				datasets: <?php echo wp_json_encode( array_values( $datasets ) ); ?>
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
				legend: {display: false},
			}
		});
	}
</script>
