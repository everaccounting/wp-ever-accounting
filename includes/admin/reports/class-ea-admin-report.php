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

class EAccounting_Admin_Report {
	/**
	 * @since 1.1.0
	 * @var string
	 */
	protected $id;

	/**
	 * @since 1.1.0
	 * @var string
	 */
	protected $title;

	/**
	 * EAccounting_Admin_Report constructor.
	 *
	 * @param string $id
	 * @param string $title
	 */
	public function __construct( $id, $title ) {
		$this->id    = $id;
		$this->title = $title;
	}

	/**
	 * Retrieves the report year.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_report_year() {
		$years = eaccounting_get_report_years();

		if ( isset( $_GET['year'] ) && array_key_exists( $_GET['year'], $years ) ) {
			return sanitize_key( $_GET['year'] );
		}

		return current( array_keys( $years ) );
	}


	public function get_range_sql( $range, $column ) {
		$date   = 'CAST(`' . $column . '` AS DATE)';
		$start  = strtotime( $range[0] );
		$end    = strtotime( $range[1] );
		$period = 0;

		while ( ( $start = strtotime( '+1 MONTH', $start ) ) <= $end ) { //phpcs:ignore
			$period ++;
		}
		$sql = array();
		switch ( $period ) {
			case $period < 24;
				$sql = array(
					"DATE_FORMAT(`$column`, '%Y%m')",
					"$date BETWEEN '$range[0]' AND '$range[1]'",
				);
			break;
		}
		return $sql;
	}

	public function get_sql( $args = array() ) {
	}

	public function prepare_data() {
	}

	public function render_chart( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'type'    => 'line',
				'data'    => array(
					'labels'   => array(),
					'datasets' => array(),
				),
				'options' => array(
					'tooltips'   => array(
						'backgroundColor' => '#000000',
						'titleFontColor'  => '#ffffff',
						'bodyFontColor'   => '#e5e5e5',
						'bodySpacing'     => 4,
						'YrPadding'       => 12,
						'mode'            => 'nearest',
						'intersect'       => 0,
						'position'        => 'nearest',
					),
					'responsive' => true,
					'scales'     => array(
						'yAxes' => array(
							array(
								'barPercentage' => 1.6,
								'ticks'         => array(
									'padding'   => 10,
									'fontColor' => '#9e9e9e',
								),
								'gridLines'     => array(
									'drawBorder'       => false,
									'color'            => 'rgba(29,140,248,0.1)',
									'zeroLineColor'    => 'transparent',
									'borderDash'       => array( 2 ),
									'borderDashOffset' => array( 2 ),
								),
							),
						),
						'xAxes' => array(
							array(
								'barPercentage' => 1.6,
								'ticks'         => array(
									'suggestedMin' => 60,
									'suggestedMax' => 125,
									'padding'      => 20,
									'fontColor'    => '#9e9e9e',
								),
								'gridLines'     => array(
									'drawBorder'    => false,
									'color'         => 'rgba(29,140,248,0.0)',
									'zeroLineColor' => 'transparent',
								),
							),
						),
					),
				),
			)
		);
		$data = json_encode( $args );
		$id   = uniqid( 'ea-chart-' );
		echo '<canvas id="' . esc_attr__( $id ) . '" height="300" width="0"></canvas>';
		eaccounting_enqueue_js( "console.log($data)" );
	}


	public function render_table() {

	}

	/**
	 * Output report.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function output() {
		global $wpdb;
		$start        = eaccounting_get_financial_start( $this->get_report_year() );
		$end          = eaccounting_get_financial_end( $this->get_report_year() );
		$where        = "category_id NOT IN ( SELECT id from {$wpdb->prefix}ea_categories WHERE type='other')";
		$where       .= $wpdb->prepare( ' AND (payment_date BETWEEN %s AND %s)', $start, $end );
		$sql          =
				"SELECT payment_date, `name` category, currency_code, currency_rate, amount, ea_categories.id category_id
		      FROM {$wpdb->prefix}ea_transactions ea_transactions
		      LEFT JOIN {$wpdb->prefix}ea_categories ea_categories ON ea_categories.id=ea_transactions.category_id
		      WHERE $where AND ea_transactions.type = 'income'";
		$transactions = $wpdb->get_results( $sql );
		foreach ( $transactions as $key => $transaction ) {
			$transaction->converted = eaccounting_price_convert_to_default( $transaction->amount, $transaction->currency_code, $transaction->currency_rate );
		}
		$dates      = $totals = $expenses = $graph = $categories = array();
		$categories = wp_list_pluck( $transactions, 'category', 'category_id' );
		$date       = new \EverAccounting\Core\DateTime( $start );
		// Dates
		for ( $j = 1; $j <= 12; $j ++ ) {
			$dates[ $j ]                     = $date->format( 'F' );
			$graph[ $date->format( 'F-Y' ) ] = 0;
			// Totals
			$totals[ $dates[ $j ] ] = array(
				'amount' => 0,
			);

			foreach ( $categories as $cat_id => $category_name ) {
				$expenses[ $cat_id ][ $dates[ $j ] ] = array(
					'category_id' => $cat_id,
					'name'        => $category_name,
					'amount'      => 0,
				);
			}
			$date->modify( '+1 month' )->format( 'Y-m' );
		}

		foreach ( $transactions as $transaction ) {
			if ( isset( $expenses[ $transaction->category_id ] ) ) {
				$month      = date( 'F', strtotime( $transaction->payment_date ) );
				$month_year = date( 'F-Y', strtotime( $transaction->payment_date ) );
				$expenses[ $transaction->category_id ][ $month ]['amount'] += $transaction->amount;
				$graph[ $month_year ]                                      += $transaction->amount;
				$totals[ $month ]['amount']                                += $transaction->amount;
			}
		}
		?>
		<div class="ea-card">
			<div class="ea-card__header">
				<h3 class="ea-card__title">Sale Report</h3>
				<div class="ea-crd__toolbar">
					<form action="">
						<label for="">Financial Year</label>
						<?php
						eaccounting_select(
							array(
								'placeholder' => __( 'Year', 'wp-ever-accounting' ),
								'name'        => 'year',
								'options'     => eaccounting_get_report_years(),
								'value'       => $this->get_report_year(),
							)
						);
						?>
						<button type="submit" class="button-secondary button">Submit</button>
					</form>
				</div>
			</div>
			<div class="ea-card__inside">
				<div style="position: relative">
					<canvas id="getpaid-chartjs-earnings-fees_total" height="300"></canvas>
				</div>
				<script>
					window.addEventListener( 'DOMContentLoaded', function() {

						var ctx = document.getElementById( 'getpaid-chartjs-earnings-fees_total' ).getContext('2d');
						new Chart(
								ctx,
								{
									type: 'line',
									data: {
										'labels': ["Dec 23","Dec 24","Dec 25","Dec 26","Dec 27","Dec 28","Dec 29"],
										'datasets': [
											{
												label: 'Fees',
												data: [0,0,0,0,0,0,0],
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
										scales: {
											yAxes: [{
												ticks: {
													beginAtZero: true
												}
											}],
											xAxes: [{
												ticks: {
													maxTicksLimit: 15
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
				<div class="ea-table-report">
					<table class="ea-table">
						<thead>
						<tr>
							<th><?php _e( 'Categories', 'wp-ever-accounting' ); ?></th>
							<?php foreach ( $dates as $date ) : ?>
								<th class="align-right"><?php echo $date; ?></th>
							<?php endforeach; ?>
						</tr>
						</thead>
						<tbody>

						<?php if ( ! empty( $expenses ) ) : ?>
							<?php foreach ( $expenses as $category_id => $category ) : ?>
								<tr>
									<td><?php echo $categories[ $category_id ]; ?></td>
									<?php foreach ( $category as $item ) : ?>
										<td class="align-right"><?php echo eaccounting_format_price( $item['amount'] ); ?></td>
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
							<?php foreach ( $totals as $total ) : ?>
								<th class="align-right"><?php echo eaccounting_format_price( $total['amount'] ); ?></th>
							<?php endforeach; ?>
						</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
		<?php
	}
}
