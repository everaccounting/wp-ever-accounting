<?php
/**
 * Admin Report Expenses By Date.
 *
 * Extended by reports to show charts and stats in admin.
 *
 * @author      EverAccounting
 * @category    Admin
 * @package     EverAccounting\Admin
 * @version     1.1.0
 */

namespace EverAccounting\Admin\Report;

defined( 'ABSPATH' ) || exit();

/**
 * Profits Class
 * @package EverAccounting\Admin\Report
 */
class Profits extends Report {
	/**
	 * @param array $args
	 *
	 * @return array|mixed|void
	 * @since 1.1.0
	 *
	 */
	public function get_report( $args = array() ) {
		global $wpdb;
		$this->maybe_clear_cache( $args );
		if ( empty( $args['year'] ) ) {
			echo '<p>';
			esc_html_e( 'Please select a year to generate the report.', 'wp-ever-accounting' );
			echo '</p>';

			return false;
		}

		$report = false;// $this->get_cache( $args );
		if ( empty( $report ) ) {
			$report          = array();
			$start_date      = $this->get_start_date( $args['year'] );
			$end_date        = $this->get_end_date( $args['year'] );
			$where           = empty( $args['account_id'] ) ? '' : $wpdb->prepare( ' AND t.account_id = %d', intval( $args['account_id'] ) );
			$where           .= empty( $args['payment_method'] ) ? '' : $wpdb->prepare( ' AND t.payment_method = %s', sanitize_key( $args['payment_method'] ) );
			$dates           = $this->get_dates_in_period( $start_date, $end_date );
			$sql             = $wpdb->prepare(
				"SELECT DATE_FORMAT(t.payment_date, '%Y-%m') `date`, SUM(t.amount) amount, t.type, t.currency_code, t.currency_rate,t.category_id,t.payment_method, c.name category
					   FROM {$wpdb->prefix}ea_transactions t
					   LEFT JOIN {$wpdb->prefix}ea_categories c on c.id=t.category_id
					   WHERE c.type != %s AND t.payment_date BETWEEN %s AND %s $where
					   GROUP BY t.currency_code,t.currency_rate, t.payment_date, t.category_id, t.type, t.payment_method ",
				'other',
				$start_date,
				$end_date
			);
			$results         = $wpdb->get_results( $sql );
			$report['dates'] = $dates;
			$report['data']  = array();
			foreach ( array_keys( $dates ) as $date ) {
				$report['data']['totals'][ $date ] = 0;
			}

			if ( ! empty( $results ) ) {
				$categories = wp_list_pluck( $results, 'category', 'category_id' );
				foreach ( array_keys( $dates ) as $date ) {
					foreach ( $categories as $cat_id => $category_name ) {
						$report['data']['category'][ $cat_id ][ $date ] = 0;
					}
				}

				foreach ( $results as $result ) {
					$amount      = eaccounting_price_to_default( $result->amount, $result->currency_code, $result->currency_rate );
					$category_id = $result->category_id;
					$amount      = (float) eaccounting_format_decimal( $amount );
					$date        = $result->date;
					if ( 'income' === $result->type ) {
						$report['data']['totals'][ $date ]                   += $amount;
						$report['data']['category'][ $category_id ][ $date ] += $amount;
					} else {
						$report['data']['totals'][ $date ]                   -= $amount;
						$report['data']['category'][ $category_id ][ $date ] -= $amount;
					}
				}

				$report['categories'] = $categories;
			}

			//$this->set_cache( $args, $report );
		}

		return $report;
	}

	/**
	 * Output report.
	 *
	 * @return void
	 * @since 1.1.0
	 */
	public function output() {
		$year           = empty( $_GET['year'] ) ? date_i18n( 'Y' ) : intval( $_GET['year'] );
		$account_id     = empty( $_GET['account_id'] ) ? '' : intval( $_GET['account_id'] );
		$payment_method = empty( $_GET['payment_method'] ) ? '' : $_GET['payment_method'];
		$report         = $this->get_report(
			array(
				'year'           => $year,
				'account_id'     => $account_id,
				'payment_method' => $payment_method,
			)
		);
		?>
		<div class="ea-card">
			<div class="ea-card__header">
				<h3 class="ea-card__title"><?php esc_html_e( 'Profit report', 'wp-ever-accounting' ); ?></h3>
				<div class="ea-card__toolbar">
					<form action="<?php echo admin_url( 'admin.php?page=ea-reports' ); ?>>" method="get">
						<?php esc_html_e( 'Filter', 'wp-ever-accounting' ); ?>
						<?php
						eaccounting_select2(
							array(
								'placeholder' => __( 'Year', 'wp-ever-accounting' ),
								'name'        => 'year',
								'options'     => eaccounting_get_report_years(),
								'value'       => $year,
							)
						);
						eaccounting_account_dropdown(
							array(
								'name'        => 'account_id',
								'placeholder' => __( 'Select Account', 'wp-ever-accounting' ),
								'creatable'   => false,
								'value'       => $account_id,
							)
						);
						eaccounting_payment_method_dropdown(
							array(
								'name'    => 'payment_method',
								'value'   => $payment_method,
								'default' => '',
							)
						);
						?>
						<input type="hidden" name="page" value="ea-reports">
						<input type="hidden" name="tab" value="profits">
						<input type="hidden" name="filter" value="true">
						<button type="submit" class="button-primary button"><?php esc_html_e( 'Submit', 'wp-ever-accounting' ); ?></button>
						<?php if ( isset( $_GET['filter'] ) ) : ?>
							<a class="button-secondary button" href="<?php echo esc_url( admin_url( 'admin.php?page=ea-reports&tab=profits' ) ); ?>"><?php esc_html_e( 'Reset', 'wp-ever-accounting' ); ?></a>
						<?php endif; ?>
					</form>
				</div>
			</div>
			<?php if ( ! empty( $year ) ) : ?>
				<div class="ea-card__inside">
					<div class="chart-container" style="position: relative; height:300px; width:100%">
						<canvas id="ea-expenses-chart" height="300" width="0"></canvas>
					</div>
					<script>
						window.addEventListener('DOMContentLoaded', function () {
							var ctx = document.getElementById('ea-expenses-chart').getContext('2d');
							new Chart(
								ctx,
								{
									type: 'line',
									data: {
										'labels': <?php echo json_encode( array_values( $report['dates'] ) ); ?>,
										'datasets': [
											{
												label: '<?php echo __( 'Sales', 'wp-ever-accounting' ); ?>',
												data: <?php echo json_encode( array_values( $report['data']['totals'] ) ); ?>,
												backgroundColor: 'rgba(0, 198, 137, 0.1)',
												borderColor: 'rgb(0, 198, 137)',
												borderWidth: 4,
												pointBackgroundColor: 'rgb(0, 198, 137)'
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
												label: function (tooltipItem, data) {
													let label = data.labels[tooltipItem.index];
													let value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
													return ' ' + label + ': ' + eaccountingi10n.currency['symbol'] + Number((value).toFixed(1)).toLocaleString();
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
				</div>

				<div class="ea-card__body">
					<div class="ea-table-report">
						<table class="wp-list-table widefat fixed striped">
							<thead>
							<tr>
								<th><?php _e( 'Categories', 'wp-ever-accounting' ); ?></th>
								<?php foreach ( $report['dates'] as $date ) : ?>
									<th class="align-right"><?php echo $date; ?></th>
								<?php endforeach; ?>
							</tr>
							</thead>

							<tbody>
							<?php if ( ! empty( $report['data']['category'] ) ) : ?>
								<?php foreach ( $report['data']['category'] as $category_id => $sales ) : ?>
									<tr>
										<td><?php echo $report['categories'][ $category_id ]; ?></td>
										<?php foreach ( $sales as $amount ) : ?>
											<td><?php echo eaccounting_format_price( $amount ); ?></td>
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

							<?php if ( ! empty( $report['data']['totals'] ) ) : ?>
								<tfoot>
								<tr>
									<th><?php _e( 'Total', 'wp-ever-accounting' ); ?></th>
									<?php foreach ( $report['data']['totals'] as $total ) : ?>
										<th class="align-right"><?php echo eaccounting_format_price( $total ); ?></th>
									<?php endforeach; ?>
								</tr>
								</tfoot>
							<?php endif; ?>
						</table>
					</div>
				</div>

				<div class="ea-card__footer">
					<a class="button button-secondary" href="<?php echo wp_nonce_url( add_query_arg( 'refresh_report', 'yes' ), 'refresh_report' ); ?>">
						<?php esc_html_e( 'Reset Cache', 'wp-ever-accounting' ); ?>
					</a>
				</div>
			<?php else : ?>
				<div class="ea-card__inside">
					<p><?php _e( "Please select financial year.", "wp-ever-accounting" ); ?></p>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}