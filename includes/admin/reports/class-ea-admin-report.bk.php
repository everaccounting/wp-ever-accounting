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
		$yesterday            = date( 'Y-m-d', strtotime( '-1 day', current_time( 'timestamp' ) ) ); //phpcs:ignore
		$seven_days_ago       = date( 'Y-m-d', strtotime( '-7 days', current_time( 'timestamp' ) ) ); //phpcs:ignore
		$thirty_days_ago      = date( 'Y-m-d', strtotime( '-30 days', current_time( 'timestamp' ) ) ); //phpcs:ignore
		$ninety_days_ago      = date( 'Y-m-d', strtotime( '-90 days', current_time( 'timestamp' ) ) ); //phpcs:ignore
		$sixty_days_ago       = date( 'Y-m-d', strtotime( '-60 days', current_time( 'timestamp' ) ) ); //phpcs:ignore
		$one_eighty_days_ago  = date( 'Y-m-d', strtotime( '-180 days', current_time( 'timestamp' ) ) ); //phpcs:ignore
		$three_sixty_days_ago = date( 'Y-m-d', strtotime( '-360 days', current_time( 'timestamp' ) ) ); //phpcs:ignore

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
						"MONTH($column)",
						"$date BETWEEN '$three_sixty_days_ago' AND '$today'",
				),

		);

		$sql = isset( $ranges[ $range ] ) ? $ranges[ $range ] : $ranges['30_days'];

		return $sql;
	}

	/**
	 * Retrieves report labels.
	 *
	 */
	public function get_date_labels( $range ) {

		$labels = array(
				'today'     => $this->get_hours_in_a_day(),
				'yesterday' => $this->get_hours_in_a_day(),
				'7_days'    => $this->get_dates_in_period( 7 ),
				'30_days'   => $this->get_weeks_in_period( 30 ),
				'60_days'   => $this->get_weeks_in_period( 60 ),
				'90_days'   => $this->get_weeks_in_period( 90 ),
				'180_days'  => $this->get_weeks_in_period( 180 ),
				'360_days'  => $this->get_weeks_in_period( 360 ),
		);

		$label = isset( $labels[ $range ] ) ? $labels[ $range ] : $labels['30_days'];
		return apply_filters( 'eaccounting_report_date_labels', $label, $range );
	}

	/**
	 * Returns an array of date ranges.
	 *
	 * @since 1.1.0
	 * @return mixed|void
	 */
	public function get_date_periods() {
		$periods = array(
				'today'     => __( 'Today', 'wp-ever-accounting' ),
				'yesterday' => __( 'Yesterday', 'wp-ever-accounting' ),
				'7_days'    => __( 'Last 7 days', 'wp-ever-accounting' ),
				'30_days'   => __( 'Last 30 days', 'wp-ever-accounting' ),
				'60_days'   => __( 'Last 60 days', 'wp-ever-accounting' ),
				'90_days'   => __( 'Last 90 days', 'wp-ever-accounting' ),
				'180_days'  => __( 'Last 180 days', 'wp-ever-accounting' ),
				'360_days'  => __( 'Last 360 days', 'wp-ever-accounting' ),
		);

		return apply_filters( 'eaccounting_report_date_periods', $periods );
	}

	/**
	 * Retrieves the current range.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_date_period() {
		$valid_ranges = $this->get_date_periods();

		if ( isset( $_GET['date_range'] ) && array_key_exists( $_GET['date_range'], $valid_ranges ) ) {
			return sanitize_key( $_GET['date_range'] );
		}

		return '30_days';
	}

	/**
	 * Retrieves the hours in a day
	 *
	 */
	public function get_hours_in_a_day() {

		return array(
				'12AM' => __( '12 AM', 'wp-ever-accounting' ),
				'1AM'  => __( '1 AM', 'wp-ever-accounting' ),
				'2AM'  => __( '2 AM', 'wp-ever-accounting' ),
				'3AM'  => __( '3 AM', 'wp-ever-accounting' ),
				'4AM'  => __( '4 AM', 'wp-ever-accounting' ),
				'5AM'  => __( '5 AM', 'wp-ever-accounting' ),
				'6AM'  => __( '6 AM', 'wp-ever-accounting' ),
				'7AM'  => __( '7 AM', 'wp-ever-accounting' ),
				'8AM'  => __( '8 AM', 'wp-ever-accounting' ),
				'9AM'  => __( '9 AM', 'wp-ever-accounting' ),
				'10AM' => __( '10 AM', 'wp-ever-accounting' ),
				'11AM' => __( '11 AM', 'wp-ever-accounting' ),
				'12pm' => __( '12 PM', 'wp-ever-accounting' ),
				'1PM'  => __( '1 PM', 'wp-ever-accounting' ),
				'2PM'  => __( '2 PM', 'wp-ever-accounting' ),
				'3PM'  => __( '3 PM', 'wp-ever-accounting' ),
				'4PM'  => __( '4 PM', 'wp-ever-accounting' ),
				'5PM'  => __( '5 PM', 'wp-ever-accounting' ),
				'6PM'  => __( '6 PM', 'wp-ever-accounting' ),
				'7PM'  => __( '7 PM', 'wp-ever-accounting' ),
				'8PM'  => __( '8 PM', 'wp-ever-accounting' ),
				'9PM'  => __( '9 PM', 'wp-ever-accounting' ),
				'10PM' => __( '10 PM', 'wp-ever-accounting' ),
				'11PM' => __( '11 PM', 'wp-ever-accounting' ),
		);

	}

	/**
	 * Retrieves the days in a period
	 *
	 * @param $days
	 *
	 * @return array
	 */
	public function get_dates_in_period( $days ) {

		$return = array();
		$format = 'Y-m-d';

		if ( $days < 8 ) {
			$format = 'D';
		}

		if ( $days < 32 ) {
			$format = 'M j';
		}

		while ( $days > 0 ) {

			$key            = date( 'Y-m-d', strtotime( "-$days days", current_time( 'timestamp' ) ) ); //phpcs:ignore
			$label          = date_i18n( $format, strtotime( "-$days days", current_time( 'timestamp' ) ) ); //phpcs:ignore
			$return[ $key ] = $label;
			$days --;

		}

		return $return;
	}

	/**
	 * Retrieves the weeks in a period
	 *
	 * @param $days
	 *
	 * @return array
	 */
	public function get_weeks_in_period( $days ) {
		$return = array();
		while ( $days > 0 ) {

			$key            = date( 'W', strtotime( "-$days days", current_time( 'timestamp' ) ) ); //phpcs:ignore
			$label          = date_i18n( 'Y-m-d', strtotime( "-$days days", current_time( 'timestamp' ) ) ); //phpcs:ignore
			$return[ $key ] = $label;
			$days --;

		}

		return $return;
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
		echo '<canvas id="'.esc_attr__($id).'" height="300" width="0"></canvas>';
		eaccounting_enqueue_js( "new Chart(document.getElementById('$id'),$data);" );
	}

	/**
	 * @since 1.1.0
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Report title', 'wp-ever-accounting' );
	}

	/**
	 * @since 1.1.0
	 */
	public function display_filter() {
		?>
		<form method="get" class="ea-report-filter">
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
							'options'     => $this->get_date_periods(),
							'value'       => $this->get_date_period(),
					)
			);
			?>
			<button class="button" type="submit">Submit</button>
		</form>
		<?php
	}

	public function prepare_data(){}

	/**
	 * Display chart
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function display_chart() {
	}

	/**
	 * Display table
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function display_table() {
	}


	/**
	 * Display
	 *
	 * @since 1.1.0
	 */
	public function display() {
		$this->prepare_data();
		?>
		<div class="ea-card">
			<div class="ea-card__header">
				<h3 class="ea-card__title"><?php echo esc_html( $this->get_title() ); ?></h3>
				<div class="ea-report-card__tools">
					<?php $this->display_filter(); ?>
				</div>
			</div>
			<div class="ea-card__inside">
				<?php $this->display_chart(); ?>
			</div>
			<div class="ea-card__footer">
				<?php $this->display_table(); ?>
			</div>

		</div>
		<?php
	}

}
