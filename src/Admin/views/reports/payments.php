<?php
/**
 * Sales payments page.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get random color for chart.
 * It accepts a key and returns a color.
 * Then saved the color in a global variable.
 * If same key is passed again, it will return the same color.
 *
 * @param string $key Key to get color.
 *
 * @since 1.0.0
 *
 * @return string
 */
$datasets = array();
$years    = range( wp_date( 'Y' ), 2015 );
$year     = eac_get_input_var( 'year', wp_date( 'Y' ) );
$data     = eac_get_payment_report( $year );
$labels   = array_keys( $data['months'] );
foreach ( $data['categories'] as $category_id => $datum ) {
	if ( ! isset( $datasets[ $category_id ] ) ) {
		$term                     = eac_get_category( $category_id );
		$term_name                = $term && $term->get_name() ? esc_html( $term->get_name() ) : esc_html__( 'Uncategorized', 'wp-ever-accounting' );
		$datasets[ $category_id ] = array(
			'label'           => $term_name,
			'backgroundColor' => eac_get_random_color( $term_name ),
		);
	}
	$datasets[ $category_id ]['data'] = array_values( $datum );
}
?>

<div class="eac-panel is--space-between">
	<h3 class="eac-panel__title">
		<?php echo esc_html__( 'Payment Report', 'wp-ever-accounting' ); ?>
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
		<input hidden="hidden" name="tab" value="payments"/>
	</form>
</div>

<ul class="eac-summaries">
	<li class="eac-summary">
		<div class="eac-summary__label"><?php esc_html_e( 'Total Payment', 'wp-ever-accounting' ); ?></div>
		<div class="eac-summary__data">
			<div class="eac-summary__value"><?php echo esc_html( eac_format_money( $data['total_amount'] ) ); ?></div>
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
			<canvas id="eac-payment-chart" style="min-height: 300px;"></canvas>
		</div>
	</div>
</div>


<div class="eac-card">
	<div class="eac-card__header">
		<h3 class="eac-card__title"><?php esc_html_e( 'Payments by Months', 'wp-ever-accounting' ); ?></h3>
	</div>
	<div class="eac-card__body is--padding-0">
		<div class="eac-overflow-x">
			<table class="widefat striped fixed is--border-0">
				<thead>
				<tr>
					<th><?php esc_html_e( 'Month', 'wp-ever-accounting' ); ?></th>
					<?php foreach ( array_keys( $data['months'] ) as $label ) : ?>
						<th><?php echo esc_html( $label ); ?></th>
					<?php endforeach; ?>
				</tr>
				</thead>
				<tbody>
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
							<td><?php echo esc_html( eac_format_money( $value ) ); ?></td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot>
				<tr>
					<th><?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?></th>
					<?php foreach ( $data['months'] as $value ) : ?>
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
		var ctx = document.getElementById("eac-payment-chart").getContext('2d');
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
							let label = data.labels[tooltipItem.index];
							let value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
							console.log(label);
							console.log(value);
							// return tooltipItem.yLabel.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
							// return label + ': ' + value.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
							return tooltipItem.yLabel;
						}
					}
				},
				scales: {
					xAxes: [{
						stacked: true,
						gridLines: {
							display: false,
						}
					}],
					yAxes: [{
						stacked: true,
						ticks: {
							beginAtZero: true,
						},
						type: 'linear',
					}]
				},
				responsive: true,
				maintainAspectRatio: false,
				legend: {position: 'top'},
			}
		});
	}
</script>
