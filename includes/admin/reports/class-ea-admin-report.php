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
	 * Retrieves the earning sql.
	 * @param  array $args
	 * @return mixed depending on query_type
	 */
	public function get_report_data( $args = array() ) {
		global $wpdb;

		$default_args = array(
			'select'     => array(),
			'from'       => '',
			'where'      => array(),
			'having'     => '',
			'group_by'   => '',
			'order_by'   => '',
			'limit'      => '',
			'query_type' => 'get_row',
		);
		$args         = wp_parse_args( $args, $default_args );

	}


	/**
	 * Retrieves report labels.
	 *
	 */
	public function get_labels( $range ) {

		$labels = array(
			'today'     => $this->get_hours_in_a_day(),
			'yesterday' => $this->get_hours_in_a_day(),
			'7_days'    => $this->get_days_in_period( 7 ),
			'30_days'   => $this->get_days_in_period( 30 ),
			'60_days'   => $this->get_days_in_period( 60 ),
			'90_days'   => $this->get_weeks_in_period( 90 ),
			'180_days'  => $this->get_weeks_in_period( 180 ),
			'360_days'  => $this->get_weeks_in_period( 360 ),
		);

		$label = isset( $labels[ $range ] ) ? $labels[ $range ] : $labels['7_days'];
		return apply_filters( 'getpaid_earning_graphs_get_labels', $label, $range );
	}

	public function output_report() {
		$current_range = ! empty( $_GET['date_range'] ) ? sanitize_text_field( wp_unslash( $_GET['date_range'] ) ) : '7day';
		if ( ! in_array( $current_range, array_keys( $this->get_periods() ), true ) ) {
			$current_range = '7day';
		}
		$data = $this->get_report_data(
			array(
				'select'       => array(
					'ID'        => array(
						'function' => 'COUNT',
						'alias'    => 'count',
						'distinct' => true,
						'from'     => '',
					),
					'post_date' => array(
						'function' => '',
						'alias'    => 'post_date',
					),
				),
				'joins'        => array(),
				'group_by'     => array(),
				'order_by'     => 'post_date ASC',
				'query_type'   => 'get_results',
				'filter_range' => true,
			)
		);
		global $wpdb;
		$clauses = $this->get_range_sql( $current_range, 'payment_date' );
		$sql     = "
		SELECT {$clauses[0]} payment_date, currency_code, currency_rate, amount
		FROM wp_ea_transactions
		WHERE category_id NOT IN ( SELECT id from wp_ea_categories WHERE type='other') AND `type` = 'income' AND  {$clauses[1]}
		";
		$results = $wpdb->get_results( $sql );
		foreach ( $results as $key => $result ) {
			$result->converted = eaccounting_price_convert_to_default( $result->amount, $result->currency_code, $result->currency_rate );
		}
		$labels   = $this->get_labels( $current_range );
		$datasets = wp_list_pluck( $results, 'converted' );
		//      $chart_data = array(
		//          'labels'   => array_values( $labels ),
		//          'datasets' => wp_list_pluck($results, 'converted'),
		//      );

		//      var_dump($chart_data);
		//      var_dump($results);
		?>

		<script>
			window.addEventListener( 'DOMContentLoaded', function() {

				var ctx = document.getElementById( 'getpaid-chartjs-earnings' ).getContext('2d');
				new Chart(
					ctx,
					{
						type: 'line',
						data: {
							'labels': <?php echo wp_json_encode( array_values( $labels ) ); ?>,
							'datasets': [
								{
									label: '<?php echo esc_attr( array_values( $labels ) ); ?>',
									data: <?php echo wp_json_encode( array_values( $datasets ) ); ?>,
									backgroundColor: 'rgba(54, 162, 235, 0.1)',
									borderColor: 'rgb(54, 162, 235)',
									borderWidth: 4,
									pointBackgroundColor: 'rgb(54, 162, 235)'
								}
							]
						},
						options: {
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
		<div class="ea-card ea-report-card">
			<div class="ea-card__header">
				<h3 class="ea-card__title">
					Sales Report
				</h3>
				<div class="ea-report-card__tools">
					<form method="get" class="getpaid-filter-earnings">
						<?php
						eaccounting_hidden_input(
							array(
								'name'  => 'page',
								'value' => 'ea-reports',
							)
						);
						eaccounting_select(
							array(
								'name'        => 'date_range',
								'placeholder' => __( 'Select a date range', 'wp-ever-accounting' ),
								'options'     => $this->get_periods(),
								'value'       => $current_range,
							)
						);
						?>
						<button class="button" type="submit">Submit</button>
					</form>

				</div>
			</div>

			<div class="ea-card__inside">
				<canvas id="getpaid-chartjs-earnings"></canvas>
			</div>
		</div>
		<?php
	}

	/**
	 * Returns an array of date ranges.
	 *
	 * @return array
	 */
	public function get_periods() {

		$periods = array(
			'today'        => __( 'Today', 'wp-ever-accounting' ),
			'yesterday'    => __( 'Yesterday', 'wp-ever-accounting' ),
			'7_days'       => __( 'Last 7 days', 'wp-ever-accounting' ),
			'30_days'      => __( 'Last 30 days', 'wp-ever-accounting' ),
			'60_days'      => __( 'Last 60 days', 'wp-ever-accounting' ),
			'90_days'      => __( 'Last 90 days', 'wp-ever-accounting' ),
			'180_days'     => __( 'Last 180 days', 'wp-ever-accounting' ),
			'360_days'     => __( 'Last 360 days', 'wp-ever-accounting' ),
			'this_quarter' => __( 'This Quarter', 'wp-ever-accounting' ),
			'last_quarter' => __( 'Last Quarter', 'wp-ever-accounting' ),
			'last_year'    => __( 'Last Year', 'wp-ever-accounting' ),
			'last_5_years' => __( 'Last 5 Years', 'wp-ever-accounting' ),
		);

		return apply_filters( 'eaccounting_report_periods', $periods );
	}

	/**
	 * Retrieves the current range's sql.
	 *
	 * @param $range
	 * @param $column
	 *
	 * @return string[]
	 */
	public function get_range_sql( $range, $column ) {

		$date = 'CAST(' . $column . ' AS DATE)';

		// Prepare durations.
		$today                = current_time( 'Y-m-d' );
		$yesterday            = date( 'Y-m-d', strtotime( '-1 day', current_time( 'timestamp' ) ) );
		$seven_days_ago       = date( 'Y-m-d', strtotime( '-7 days', current_time( 'timestamp' ) ) );
		$thirty_days_ago      = date( 'Y-m-d', strtotime( '-30 days', current_time( 'timestamp' ) ) );
		$ninety_days_ago      = date( 'Y-m-d', strtotime( '-90 days', current_time( 'timestamp' ) ) );
		$sixty_days_ago       = date( 'Y-m-d', strtotime( '-60 days', current_time( 'timestamp' ) ) );
		$one_eighty_days_ago  = date( 'Y-m-d', strtotime( '-180 days', current_time( 'timestamp' ) ) );
		$three_sixty_days_ago = date( 'Y-m-d', strtotime( '-360 days', current_time( 'timestamp' ) ) );

		$ranges = array(

			'today'     => array(
				"DATE_FORMAT($column, '%l%p')",
				"$date='$today'",
			),

			'yesterday' => array(
				"DATE_FORMAT($column, '%l%p')",
				"$date='$yesterday'",
			),

			'7_days'    => array(
				"DATE($column)",
				"$date BETWEEN '$seven_days_ago' AND '$today'",
			),

			'30_days'   => array(
				"DATE($column)",
				"$date BETWEEN '$thirty_days_ago' AND '$today'",
			),

			'60_days'   => array(
				"DATE($column)",
				"$date BETWEEN '$sixty_days_ago' AND '$today'",
			),

			'90_days'   => array(
				"WEEK($column)",
				"$date BETWEEN '$ninety_days_ago' AND '$today'",
			),

			'180_days'  => array(
				"WEEK($column)",
				"$date BETWEEN '$one_eighty_days_ago' AND '$today'",
			),

			'360_days'  => array(
				"WEEK($column)",
				"$date BETWEEN '$three_sixty_days_ago' AND '$today'",
			),

		);

		$sql = isset( $ranges[ $range ] ) ? $ranges[ $range ] : $ranges['7_days'];
		return $sql;
	}

	/**
	 * Retrieves the hours in a day
	 *
	 */
	public function get_hours_in_a_day() {

		return array(
			'12AM' => __( '12 AM', 'invoicing' ),
			'1AM'  => __( '1 AM', 'invoicing' ),
			'2AM'  => __( '2 AM', 'invoicing' ),
			'3AM'  => __( '3 AM', 'invoicing' ),
			'4AM'  => __( '4 AM', 'invoicing' ),
			'5AM'  => __( '5 AM', 'invoicing' ),
			'6AM'  => __( '6 AM', 'invoicing' ),
			'7AM'  => __( '7 AM', 'invoicing' ),
			'8AM'  => __( '8 AM', 'invoicing' ),
			'9AM'  => __( '9 AM', 'invoicing' ),
			'10AM' => __( '10 AM', 'invoicing' ),
			'11AM' => __( '11 AM', 'invoicing' ),
			'12pm' => __( '12 PM', 'invoicing' ),
			'1PM'  => __( '1 PM', 'invoicing' ),
			'2PM'  => __( '2 PM', 'invoicing' ),
			'3PM'  => __( '3 PM', 'invoicing' ),
			'4PM'  => __( '4 PM', 'invoicing' ),
			'5PM'  => __( '5 PM', 'invoicing' ),
			'6PM'  => __( '6 PM', 'invoicing' ),
			'7PM'  => __( '7 PM', 'invoicing' ),
			'8PM'  => __( '8 PM', 'invoicing' ),
			'9PM'  => __( '9 PM', 'invoicing' ),
			'10PM' => __( '10 PM', 'invoicing' ),
			'11PM' => __( '11 PM', 'invoicing' ),
		);

	}

	/**
	 * Retrieves the days in a period
	 *
	 */
	public function get_days_in_period( $days ) {

		$return = array();
		$format = 'Y-m-d';

		if ( $days < 8 ) {
			$format = 'D';
		}

		if ( $days < 32 ) {
			$format = 'M j';
		}

		while ( $days > 0 ) {

			$key            = date( 'Y-m-d', strtotime( "-$days days", current_time( 'timestamp' ) ) );
			$label          = date_i18n( $format, strtotime( "-$days days", current_time( 'timestamp' ) ) );
			$return[ $key ] = $label;
			$days--;

		}

		return $return;
	}

	/**
	 * Retrieves the weeks in a period
	 *
	 */
	public function get_weeks_in_period( $days ) {

		$return = array();

		while ( $days > 0 ) {

			$key            = date( 'W', strtotime( "-$days days", current_time( 'timestamp' ) ) );
			$label          = date_i18n( 'Y-m-d', strtotime( "-$days days", current_time( 'timestamp' ) ) );
			$return[ $key ] = $label;
			$days--;

		}

		return $return;
	}
}
