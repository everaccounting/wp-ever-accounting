<?php
/**
 * Sales expenses page.
 *
 * @since 1.0.0
 * @package EverAccounting
 * @subpackage Admin/Views/Reports
 */

defined( 'ABSPATH' ) || exit;

$datasets = array();
$currency = eac_get_currency();
$years    = range( wp_date( 'Y' ), 2015 );

// TODO: Need to update these bellow php variable.
// $year     = eac_get_input_var( 'year', wp_date( 'Y' ) );
$year     = wp_date( 'Y' );

$data     = eac_get_expenses_report( $year );
$labels   = array_keys( $data['months'] );
foreach ( $data['categories'] as $category_id => $datum ) {
	if ( ! isset( $datasets[ $category_id ] ) ) {
		$term                     = eac_get_category( $category_id );
		$term_name                = $term && $term->get_name() ? esc_html( $term->get_name() ) : __( 'Uncategorized', 'wp-ever-accounting' );
		$datasets[ $category_id ] = array(
			'label'           => $term_name,
			'backgroundColor' => eac_get_random_color( $term_name ),
		);
	}
	$datasets[ $category_id ]['data'] = array_values( $datum );
}
// push a line chart for the total expenses.
$datasets['total'] = array(
	'type'            => 'line',
	'fill'            => false,
	'label'           => esc_html__( 'Total', 'wp-ever-accounting' ),
	'backgroundColor' => '#f2385a',
	'borderColor'     => '#f2385a',
	'data'            => array_values( $data['months'] ),
);
?>

<div class="eac-panel">
	<div class="eac-panel-inner tw-flex tw-justify-between tw-items-center">
		<h3 class="eac-panel__title">
			<?php echo esc_html__( 'Expense Report', 'wp-ever-accounting' ); ?>
		</h3>
		<form class="eac-report-filters" method="get" action="">
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
			<input hidden="hidden" name="tab" value="expenses"/>
		</form>
	</div>
</div>

<ul class="eac-summaries">
	<li class="eac-summary">
		<div class="eac-summary__label"><?php esc_html_e( 'Total Expense', 'wp-ever-accounting' ); ?></div>
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

<div class="eac-card">
	<div class="eac-card__header">
		<h3 class="eac-card__title"><?php esc_html_e( 'Expense Chart', 'wp-ever-accounting' ); ?></h3>
	</div>
	<div class="eac-card__body">
		<div class="eac-chart">
			<canvas id="eac-sales-chart" style="min-height: 300px;"></canvas>
		</div>
	</div>
</div>


<div class="eac-card">
	<div class="eac-card__header">
		<h3 class="eac-card__title"><?php esc_html_e( 'Sales by Months', 'wp-ever-accounting' ); ?></h3>
	</div>
	<div class="eac-card__body padding-0">
		<div class="eac-overflow-x">
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
				<?php if ( ! empty( $data['categories'] ) ) : ?>
					<?php foreach ( $data['categories'] as $category_id => $datum ) : ?>
					<tr>
						<td>
							<?php
							$term      = eac_get_category( $category_id );
							$term_name = $term && $term->get_name() ? esc_html( $term->get_name() ) : esc_html__( 'Uncategorized', 'wp-ever-accounting' );
							echo esc_html( $term_name );
							?>
						</td>
						<?php foreach ( $datum as $value ) : ?>
							<td><?php echo esc_html( eac_format_amount( $value ) ); ?></td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="<?php echo count( $data['months'] ) + 1; ?>">
							<?php esc_html_e( 'No data found.', 'wp-ever-accounting' ); ?>
						</td>
					</tr>
				<?php endif; ?>
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
		var ctx = document.getElementById("eac-sales-chart").getContext('2d');
		var symbol = "<?php echo esc_html( $currency ? $currency->get_symbol() : '' ); ?>";
		new Chart(ctx, {
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
							return datasetLabel + ': ' + value.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')+symbol;
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
						type: 'linear',
						barPercentage: 0.4,
						ticks: {
							beginAtZero: true,
							callback: function (value, index, ticks) {
								return Number(value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + symbol;
							}
						}
					}]
				},
				responsive: true,
				maintainAspectRatio: false,
				legend: {display: false},
			}
		});
	}
</script>
