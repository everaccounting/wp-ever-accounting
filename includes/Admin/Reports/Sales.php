<?php

namespace EverAccounting\Admin\Reports;

use EverAccounting\Utilities\ReportsUtil;

defined( 'ABSPATH' ) || exit;

/**
 * Class Sales
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\Reports
 */
class Sales {

	/**
	 * Render the sales report.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function render() {
		wp_verify_nonce( '_wpnonce' );
		$year     = ! empty( $_GET['year'] ) ? absint( $_GET['year'] ) : wp_date( 'Y' );
		$datasets = array();
		$data     = ReportsUtil::get_payments_report( $year );
		$labels   = array_keys( $data['months'] );
		foreach ( $data['categories'] as $category_id => $datum ) {
			if ( ! isset( $datasets[ $category_id ] ) ) {
				$term                     = EAC()->categories->get( $category_id );
				$term_name                = $term && $term->name ? esc_html( $term->name ) : '&mdash;';
				$datasets[ $category_id ] = array(
					'label'           => $term_name,
					'backgroundColor' => ReportsUtil::get_random_color( $term_name ),
				);
			}
			$datasets[ $category_id ]['data'] = array_values( $datum );
		}
		$datasets['total'] = array(
			'type'            => 'line',
			'fill'            => false,
			'label'           => esc_html__( 'Total', 'wp-ever-accounting' ),
			'backgroundColor' => '#3644ff',
			'borderColor'     => '#3644ff',
			'data'            => array_values( $data['months'] ),
		);
		?>
		<div class="eac-section-header">
			<h3>
				<?php echo esc_html__( 'Sales Report', 'wp-ever-accounting' ); ?>
			</h3>
			<form class="ea-report-filters" method="get" action="">
				<input type="number" name="year" value="<?php echo esc_attr( $year ); ?>" placeholder="<?php echo esc_attr__( 'Year', 'wp-ever-accounting' ); ?>"/>
				<button type="submit" class="button">
					<?php echo esc_html__( 'Submit', 'wp-ever-accounting' ); ?>
				</button>
				<input hidden="hidden" name="page" value="eac-reports"/>
				<input hidden="hidden" name="tab" value="payments"/>
			</form>
		</div>

		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Chart', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__body">
				<div class="eac-chart">
					<canvas id="eac-sales-chart" style="min-height: 300px;"></canvas>
				</div>
			</div>
		</div>

		<div class="eac-stats stats--3">
			<div class="eac-stat">
				<div class="eac-stat__label"><?php esc_html_e( 'Total Sale', 'wp-ever-accounting' ); ?></div>
				<div class="eac-stat__value"><?php echo esc_html( eac_format_amount( $data['total_amount'] ) ); ?></div>
			</div>
			<div class="eac-stat">
				<div class="eac-stat__label"><?php esc_html_e( 'Monthly Avg.', 'wp-ever-accounting' ); ?></div>
				<div class="eac-stat__value"><?php echo esc_html( eac_format_amount( $data['month_avg'] ) ); ?></div>
			</div>
			<div class="eac-stat">
				<div class="eac-stat__label"><?php esc_html_e( 'Daily Avg.', 'wp-ever-accounting' ); ?></div>
				<div class="eac-stat__value"><?php echo esc_html( eac_format_amount( $data['daily_avg'] ) ); ?></div>
			</div>
		</div>


		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Sales by Months', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__body !tw-p-0">
				<div class="tw-overflow-x-auto">
					<table class="eac-table has--border">
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
										$term      = EAC()->categories->get( $category_id );
										$term_name = $term && $term->name ? $term->name : '&mdash;';
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
									<p>
										<?php esc_html_e( 'No data found', 'wp-ever-accounting' ); ?>
									</p>
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
				var symbol = "<?php echo esc_html( EAC()->currencies->get_symbol() ); ?>";
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

		<?php
	}
}
