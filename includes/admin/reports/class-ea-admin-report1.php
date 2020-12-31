<?php
/**
 * Admin Report.
 *
 * Extended by reports to show charts and stats in admin.
 *
 * @author      EverAccounting
 * @category    Admin
 * @package     EverAccounting\Admin
 * @version     1.1.0
 */
defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Report1 {
	/**
	 * Delete nonce request.
	 *
	 * @since 1.2.0
	 */
	protected function delete_cache( $key ) {
		delete_transient( $key );
	}

	/**
	 * Get start date.
	 *
	 * @since 1.1.0
	 *
	 * @param $year
	 *
	 * @return string
	 */
	public function get_start_date( $year = null ) {
		if ( null === $year ) {
			$year = date_i18n( 'Y' );
		}

		return eaccounting_get_financial_start( intval( $year ) );
	}

	/**
	 * Get end date.
	 *
	 * @since 1.1.0
	 *
	 * @param $year
	 *
	 * @throws Exception
	 * @return string
	 */
	public function get_end_date( $year = null ) {
		if ( null === $year ) {
			$year = date_i18n( 'Y' );
		}

		return eaccounting_get_financial_end( intval( $year ) );
	}

//	/**
//	 * Get range sql.
//	 *
//	 * @since 1.1.0
//	 *
//	 * @param null $start_date
//	 * @param null $end_date
//	 * @param      $column
//	 *
//	 * @throws Exception
//	 * @return array
//	 */
//	public function get_range_sql( $column, $start_date = null, $end_date = null ) {
//		global $wpdb;
//		$start_date = empty( $start_date ) ? $this->get_start_date() : $start_date;
//		$end_date   = empty( $end_date ) ? $this->get_end_date() : $end_date;
//		$start      = strtotime( $start_date );
//		$end        = strtotime( $end_date );
//		$date       = 'CAST(`' . $column . '` AS DATE)';
//
//		$period = 0;
//		while ( ( $start = strtotime( '+1 MONTH', $start ) ) <= $end ) { //phpcs:ignore
//			$period ++;
//		}
//
//		$sql = array();
//		switch ( $period ) {
//			case $period < 24:
//				$sql = array(
//					"DATE_FORMAT(`$column`, '%Y-%m')",
//					$wpdb->prepare( "$date BETWEEN %s AND %s", $start_date, $end_date ),
//				);
//				break;
//		}
//
//		return $sql;
//	}
//
//	/**
//	 * Get months in the financial period.
//	 *
//	 * @since 1.1.0
//	 *
//	 * @param        $start_date
//	 *
//	 * @param        $end_date
//	 * @param string $period
//	 * @param string $date_key
//	 * @param string $date_value
//	 *
//	 * @return array
//	 */
//	public function get_dates_in_period( $start_date, $end_date, $interval = 'M', $date_key = 'Y-m', $date_value = 'F Y' ) {
//		$dates  = array();
//		$period = new DatePeriod(
//			new DateTime( $start_date ),
//			new DateInterval( "P1{$interval}" ),
//			new DateTime( $end_date )
//		);
//		foreach ( $period as $key => $value ) {
//			$dates[ $value->format( $date_key ) ] = $value->format( $date_value );
//		}
//
//		return $dates;
//	}

	public function get_report( $args = array() ) {
		global $wpdb;
		$args      = wp_parse_args(
			$args,
			array(
				'year' => date_i18n( 'Y' ),
			)
		);
		$cache_key = 'eaccounting_report_' . md5( serialize( $args ) );
		if ( ! empty( $_GET['refresh_report'] )
			 && ! empty( $_GET['_wpnonce'] )
			 && wp_verify_nonce( $_GET['_wpnonce'], 'refresh_report' ) ) {
			$this->delete_cache( $cache_key );
			wp_redirect( remove_query_arg( array( 'refresh_report', '_wpnonce' ) ) );
			exit();
		}
		$report    = get_transient( $cache_key );
		var_dump($report);
		if ( empty( $report ) ) {
			$report     = array();
			$start_date = $this->get_start_date( $args['year'] );
			$end_date   = $this->get_start_date( $args['year'] );
			$dates      = $this->get_dates_in_period( $start_date, $end_date );
			$sql        = $wpdb->prepare(
				"SELECT DATE_FORMAT(t.payment_date, '%Y-%m') `date`, SUM(t.amount) amount, t.currency_code, t.currency_rate,t.category_id, c.name category
					   FROM {$wpdb->prefix}ea_transactions t
					   LEFT JOIN {$wpdb->prefix}ea_categories c on c.id=t.category_id
					   WHERE c.type = %s AND t.payment_date BETWEEN %s AND %s
					   GROUP BY t.currency_code,t.currency_rate, t.payment_date, t.category_id ",
				'income',
				$start_date,
				$end_date
			);

			$results              = $wpdb->get_results( $sql );
			$categories           = wp_list_pluck( $results, 'category', 'category_id' );
			$report['dates']      = $dates;
			$report['categories'] = $categories;
			foreach ( $dates as $date => $label ) {
				$report['total'][ $date ] = 0;
				foreach ( $categories as $cat_id => $category_name ) {
					$report['category'][ $cat_id ][ $date ] = 0;
				}
			}

			foreach ( $results as $result ) {
				$amount                    = eaccounting_price_convert_to_default( $result->amount, $result->currency_code, $result->currency_rate );
				$amount                    = eaccounting_format_decimal( $amount );
				$date                      = $result->date;
				$category_id               = $result->category_id;
				$report['total'][ $date ] += eaccounting_format_decimal( $amount );
				$report['category'][ $category_id ][ $date ] += $amount;
			}
			set_transient( $cache_key, $report, MINUTE_IN_SECONDS * 5 );
		}
		return $report;
	}

	/**
	 * Output report.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function output() {
		$year   = empty( $_GET['year'] ) ? date_i18n( 'Y' ) : intval( $_GET['year'] );
		$report = $this->get_report( array( 'year' => $year ) );
		?>
		<div class="ea-card">
			<div class="ea-card__header">
				<h3 class="ea-card__title">Sale Report</h3>
				<div class="ea-card__toolbar">
					<form action="<?php echo admin_url( 'admin.php?page=ea-reports' ); ?>>" method="get">
						<label for="">Financial Year</label>

						<?php
						eaccounting_select(
							array(
								'placeholder' => __( 'Year', 'wp-ever-accounting' ),
								'name'        => 'year',
								'options'     => eaccounting_get_report_years(),
								'value'       => $year,
							)
						);

						?>
						<input type="hidden" name="page" value="ea-reports">
						<input type="hidden" name="tab" value="sales">
						<input type="hidden" name="section" value="by_date">
						<button type="submit" class="button-secondary button">Submit</button>
					</form>
				</div>
			</div>
			<div class="ea-card__inside">
				<div class="chart-container" style="position: relative; height:300px; width:100%">
					<canvas id="ea-sales-chart" height="300" width="0"></canvas>
				</div>
			</div>
			<div class="ea-card__section">
				<div class="ea-table-report">
					<table class="ea-table">
						<thead>
						<tr>
							<th><?php _e( 'Categories', 'wp-ever-accounting' ); ?></th>
							<?php foreach ( $report['dates'] as $date ) : ?>
								<th class="align-right"><?php echo $date; ?></th>
							<?php endforeach; ?>
						</tr>
						</thead>
						<tbody>

						<?php if ( ! empty( $report['category'] ) ) : ?>
							<?php foreach ( $report['category'] as $category_id => $category ) : ?>
								<tr>
									<td><?php echo $report['categories'][ $category_id ]; ?></td>
									<?php foreach ( $category as $item ) : ?>
										<td class="align-right"><?php echo eaccounting_format_price( $item ); ?></td>
									<?php endforeach; ?>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr class="no-results">
								<td colspan="13">
									<p><?php _e( 'No records found', 'wp-ever-accounting' ); ?></p>
								</td>
							</tr>
						<?php endif; ?>
						</tbody>

						<tfoot>
						<tr>
							<th><?php _e( 'Total', 'wp-ever-accounting' ); ?></th>
							<?php foreach ( $report['total'] as $total ) : ?>
								<th class="align-right"><?php echo eaccounting_format_price( $total ); ?></th>
							<?php endforeach; ?>
						</tr>
						</tfoot>
					</table>
				</div>
			</div>
			<div class="ea-card__footer">
				<a class="button button-secondary" href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=ea-reports&tab=sales&section=by_date&refresh_report=yes' ), 'refresh_report' ); ?>">
					<?php esc_html_e( 'Reset Cache', 'wp-ever-accounting' ); ?>
				</a>
			</div>
		</div>
		<script>
			window.addEventListener('DOMContentLoaded', function () {
				var ctx = document.getElementById('ea-sales-chart').getContext('2d');
				new Chart(
						ctx,
						{
							type: 'line',
							data: {
								'labels': <?php echo json_encode( array_values( $report['dates'] ) ); ?>,
								'datasets': [
									{
										label: '<?php echo __( 'Income', 'wp-ever-accounting' ); ?>',
										data: <?php echo json_encode( array_values( $report['total'] ) ); ?>,
										backgroundColor: 'rgba(54, 162, 235, 0.1)',
										borderColor: 'rgb(54, 162, 235)',
										borderWidth: 4,
										pointBackgroundColor: 'rgb(54, 162, 235)'
									}
								]
							},
							options: {
								responsive: true,
								maintainAspectRatio: false,
								tooltips: {
									YrPadding: 12,
									backgroundColor: "#000000",
									bodyFontColor: "#e5e5e5",
									bodySpacing: 4,
									intersect: 0,
									mode: "nearest",
									position: "nearest",
									titleFontColor: "#ffffff",
									callbacks: {
										label: function (t, d) {
											var xLabel = d.datasets[t.datasetIndex].label;
											var yLabel = t.yLabel;
											return xLabel + ': ' + yLabel;
										}
									}
								},
								scales: {
									yAxes: [{
										barPercentage: 1.6,
										gridLines: {
											// borderDash: [1],
											// borderDashOffset: [2],
											color: "rgba(29,140,248,0.1)",
											drawBorder: false,
											zeroLineColor: "transparent",
										},
										ticks: {
											padding: 10,
											fontColor: '#9e9e9e',
											beginAtZero: true,
											callback: function (value, index, values) {
												return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
											}
										}
									}],
									xAxes: [{
										gridLines: {
											color: "rgba(29,140,248,0.0)",
											drawBorder: false,
											zeroLineColor: "transparent",
										},
										ticks: {
											fontColor: "#9e9e9e",
											suggestedMax: 125,
											suggestedMin: 60,
										}
									}]
								},
								legend: {
									display: false
								}
							}
						}
				);

			})
		</script>
		<?php
	}
}
