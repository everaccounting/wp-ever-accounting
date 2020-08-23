<?php
/**
 * This class handles building pretty report graphs
 *
 * @since       1.0.2
 * @package     EverAccounting
 */

namespace EverAccounting;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit();

/**
 * EAccounting_Graph Class
 *
 * @since 1.0.2
 */
class Graph {
	/*
	Simple example:

	data format for each point: array( location on x, location on y )

	$data = array(

		'Label' => array(
			array( 1, 5 ),
			array( 3, 8 ),
			array( 10, 2 )
		),

		'Second Label' => array(
			array( 1, 7 ),
			array( 4, 5 ),
			array( 12, 8 )
		)
	);

	$graph = new EAccounting_Graph( $data );
	$graph->display();

	*/

	/**
	 * Data to graph
	 *
	 * @since 1.0.2
	 * @var array
	 */
	public $data;

	/**
	 * Unique ID for the graph
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $id = '';

	/**
	 * Graph options
	 *
	 * @since 1.0.2
	 * @var array
	 */
	public $options = array();

	/**
	 * Get things started
	 *
	 * @since 1.0.2
	 */
	public function __construct( $_data = array() ) {

		$this->data = $_data;

		// Generate unique ID
		$this->id = md5( rand() );

		// Setup default options;
		$this->options = array(
				'y_mode'          => null,
				'x_mode'          => null,
				'y_decimals'      => 0,
				'x_decimals'      => 0,
				'y_position'      => 'right',
				'time_format'     => '%d/%b',
				'ticksize_unit'   => 'day',
				'ticksize_num'    => 1,
				'multiple_y_axes' => false,
				'bgcolor'         => '#f9f9f9',
				'bordercolor'     => '#ccc',
				'borderwidth'     => 2,
				'bars'            => false,
				'lines'           => true,
				'points'          => true,
				'currency'        => true,
				'show_controls'   => true,
				'form_wrapper'    => true,
		);

	}

	/**
	 * Set an option
	 *
	 * @since 1.0.2
	 *
	 * @param string|array $value The value to assign to the key
	 *
	 * @param string       $key   Graph option key to set
	 */
	public function set( $key, $value ) {
		if ( 'data' == $key ) {
			$this->data = $value;
		} else {
			$this->options[ $key ] = $value;

		}
	}

	/**
	 * Get an option
	 *
	 * @since 1.0.2
	 *
	 * @param string $key The option key to get
	 *
	 */
	public function get( $key ) {
		return isset( $this->options[ $key ] ) ? $this->options[ $key ] : false;
	}

	/**
	 * Get graph data
	 *
	 * @since 1.0.2
	 */
	public function get_data() {
		/**
		 * Filters the data for the current reporting graph.
		 *
		 * @since 1.0.2
		 *
		 * @param       $this $instance Current Graph instance.
		 *
		 * @param array $data Graph data.
		 */
		return apply_filters( 'eaccounting_get_graph_data', $this->data, $this );
	}

	/**
	 * Load the graphing library script
	 *
	 * @since 1.0.2
	 */
	public function load_scripts() {
		wp_enqueue_script( 'ea-chartjs' );
	}


	/**
	 * Build the graph and return it as a string
	 *
	 * @since 1.0.2
	 * @return string
	 * @var array
	 */
	public function build_graph() {
		$this->load_scripts();

		ob_start();

		wp_add_inline_script( 'ea-chartjs', $this->graph_js() );
		if ( false !== $this->get( 'show_controls' ) ) {
			$this->graph_controls();
		}
		?>
	<div id="ea-graph-<?php echo $this->id; ?>" class="ea-graph" style="height: 300px; width:97%;"></div><?php
		return ob_get_clean();
	}

	/**
	 * Retrieves the Graph initialization JS for output inline.
	 *
	 * @access public
	 * @since  1.0.2
	 *
	 * @return string Graph JS output.
	 */
	public function graph_js() {
		$yaxis_count = 1;

		ob_start();
		?>
		var ea_vars;
		jQuery( document ).ready( function($) {
		new Chart(
		$("#ea-graph-<?php echo $this->id; ?>"),
		[
		<?php foreach ( $this->get_data() as $label => $data ) : ?>
			{
			label: "<?php echo esc_attr( $label ); ?>",
			id: "<?php echo sanitize_key( $label ); ?>",
			// data format is: [ point on x, value on y ]
			data: [<?php foreach ( $data as $point ) {
				echo '[' . implode( ',', $point ) . '],';
			} ?>],
			points: {
			show: <?php echo $this->options['points'] ? 'true' : 'false'; ?>,
			},
			bars: {
			show: <?php echo $this->options['bars'] ? 'true' : 'false'; ?>,
			barWidth: 2,
			align: 'center'
			},
			lines: {
			show: <?php echo $this->options['lines'] ? 'true' : 'false'; ?>
			},
			<?php if ( $this->options['multiple_y_axes'] ) : ?>
				yaxis: <?php echo $yaxis_count; ?>
			<?php endif; ?>
			},
			<?php $yaxis_count ++; endforeach; ?>
		],
		{
		// Options
		grid: {
		show: true,
		aboveData: false,
		backgroundColor: "<?php echo $this->options['bgcolor']; ?>",
		borderColor: "<?php echo $this->options['bordercolor']; ?>",
		borderWidth: <?php echo absint( $this->options['borderwidth'] ); ?>,
		clickable: false,
		hoverable: true
		},
		xaxis: {
		mode: "<?php echo $this->options['x_mode']; ?>",
		timeformat: "<?php echo $this->options['x_mode'] == 'time' ? $this->options['time_format'] : ''; ?>",
		tickSize: "<?php echo $this->options['x_mode'] == 'time' ? '' : 1; ?>",
		<?php if ( $this->options['x_mode'] != 'time' ) : ?>
			tickDecimals: <?php echo $this->options['x_decimals']; ?>
		<?php endif; ?>
		},
		yaxis: {
		position: 'right',
		min: 0,
		mode: "<?php echo $this->options['y_mode']; ?>",
		timeformat: "<?php echo $this->options['y_mode'] == 'time' ? $this->options['time_format'] : ''; ?>",
		<?php if ( $this->options['y_mode'] != 'time' ) : ?>
			tickDecimals: <?php echo $this->options['y_decimals']; ?>
		<?php endif; ?>
		}
		}

		);

		var previousPoint = null;
		$("#ea-graph-<?php echo $this->id; ?>").bind("plothover", function (event, pos, item) {
		$("#x").text(pos.x.toFixed(2));
		$("#y").text(pos.y.toFixed(2));
		if (item) {
		if (previousPoint != item.dataIndex) {
		previousPoint = item.dataIndex;
		$("#ea-flot-tooltip").remove();
		var x = item.datapoint[0].toFixed(2),
		y = item.datapoint[1].toFixed(2);

		<?php if ( $this->get( 'currency' ) ) : ?>
			if( ea_vars.currency_pos == 'before' ) {
			ea_flot_tooltip( item.pageX, item.pageY, item.series.label + ' ' + ea_vars.currency_sign + y );
			} else {
			ea_flot_tooltip( item.pageX, item.pageY, item.series.label + ' ' + y + ea_vars.currency_sign );
			}
		<?php else : ?>
			ea_flot_tooltip( item.pageX, item.pageY, item.series.label + ' ' + y );
		<?php endif; ?>
		}
		} else {
		$("#ea-flot-tooltip").remove();
		previousPoint = null;
		}
		});

		$( '#ea-graphs-date-options' ).change( function() {
		var $this = $(this);
		if( $this.val() == 'other' ) {
		$( '#ea-date-range-options' ).css('display', 'inline-block');
		} else {
		$( '#ea-date-range-options' ).hide();
		}
		});

		});
		<?php
		return ob_get_clean();
	}

	/**
	 * Output the final graph
	 *
	 * @since 1.0.2
	 */
	public function display() {
		/**
		 * Fires just prior to the graph output.
		 *
		 * @since 1.0
		 *
		 * @param stdClass $graph The graph object.
		 *
		 */
		do_action( 'eaccounting_before_graph', $this );

		echo $this->build_graph();

		/**
		 * Fires immediately after the graph output.
		 *
		 * @since 1.0.2
		 *
		 * @param stdClass $graph The graph object.
		 *
		 */
		do_action( 'eaccounting_after_graph', $this );
	}

	/**
	 * Displays the report graph date filters.
	 *
	 * @since    1.0.2
	 * @internal Note that this method is also used on the front-end. Any changes here
	 *           should be equally tested in the Affiliate Area..
	 *
	 * @access   public
	 */
	public function graph_controls() {
		/**
		 * Filters the date filter options to use in the controls for the current reports graph.
		 *
		 * @since 1.0.2
		 *
		 * @param array $date_options List of date options and their user-facing, translatable labels.
		 *
		 */
		$date_options = apply_filters( 'ea_report_date_options', array(
				'today'        => __( 'Today', 'wp-ever-accounting' ),
				'yesterday'    => __( 'Yesterday', 'wp-ever-accounting' ),
				'this_week'    => __( 'This Week', 'wp-ever-accounting' ),
				'last_week'    => __( 'Last Week', 'wp-ever-accounting' ),
				'this_month'   => __( 'This Month', 'wp-ever-accounting' ),
				'last_month'   => __( 'Last Month', 'wp-ever-accounting' ),
				'this_quarter' => __( 'This Quarter', 'wp-ever-accounting' ),
				'last_quarter' => __( 'Last Quarter', 'wp-ever-accounting' ),
				'this_year'    => __( 'This Year', 'wp-ever-accounting' ),
				'last_year'    => __( 'Last Year', 'wp-ever-accounting' ),
				'other'        => __( 'Custom', 'wp-ever-accounting' )
		) );

		$dates = ea_get_report_dates();

		$display = $dates['range'] == 'other' ? 'style="display:inline-block;"' : 'style="display:none;"';

		$current_time = current_time( 'timestamp' );

		if ( $this->get( 'form_wrapper' ) ) {
			?>
			<form id="ea-graphs-filter" method="get">
			<div class="tablenav top">
			<?php
		}

		if ( is_admin() ) : ?>
			<?php $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'referral'; ?>
			<?php $page = isset( $_GET['page'] ) ? $_GET['page'] : 'wp-ever-accounting'; ?>
			<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>"/>
		<?php else: ?>
			<?php $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'graphs'; ?>
			<input type="hidden" name="page_id" value="<?php echo esc_attr( get_the_ID() ); ?>"/>
		<?php endif; ?>

		<input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>"/>

		<?php if ( isset( $_GET['affiliate_id'] ) ) : ?>
			<input type="hidden" name="affiliate_id" value="<?php echo absint( $_GET['affiliate_id'] ); ?>"/>
			<input type="hidden" name="action" value="view_affiliate"/>
		<?php endif; ?>

		<select id="ea-graphs-date-options" class="ea-graphs-date-options" name="range">
			<?php
			foreach ( $date_options as $key => $option ) {
				echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $dates['range'] ) . '>' . esc_html( $option ) . '</option>';
			}
			?>
		</select>

		<div id="ea-date-range-options" <?php echo $display; ?>>

			<?php
			$from = empty( $_REQUEST['filter_from'] ) ? '' : $_REQUEST['filter_from'];
			$to   = empty( $_REQUEST['filter_to'] ) ? '' : $_REQUEST['filter_to'];
			?>
			<span class="ea-search-date">
				<span><?php _ex( 'From', 'date filter', 'wp-ever-accounting' ); ?></span>
				<input type="text" class="ea-datepicker" autocomplete="off" name="filter_from" placeholder="<?php esc_attr_e( 'From - mm/dd/yyyy', 'wp-ever-accounting' ); ?>" aria-label="<?php esc_attr_e( 'From - mm/dd/yyyy', 'wp-ever-accounting' ); ?>" value="<?php echo esc_attr( $from ); ?>"/>
				<span><?php _ex( 'To', 'date filter', 'wp-ever-accounting' ); ?></span>
				<input type="text" class="ea-datepicker" autocomplete="off" name="filter_to" placeholder="<?php esc_attr_e( 'To - mm/dd/yyyy', 'wp-ever-accounting' ); ?>" aria-label="<?php esc_attr_e( 'To - mm/dd/yyyy', 'wp-ever-accounting' ); ?>" value="<?php echo esc_attr( $to ); ?>"/>
			</span>

		</div>
		<?php
		if ( $this->get( 'form_wrapper' ) ) {
			?>
			<input name="submit" id="submit" class="button" value="<?php esc_attr_e( 'Filter', 'wp-ever-accounting' ); ?>" type="submit">
			</div><!-- .tablenav .top -->
			</form><!-- .ea-graphs-filter -->
			<?php
		}
	}

}

/**
 * Sets up the dates used to filter graph data
 *
 * Date sent via $_GET is read first and then modified (if needed) to match the
 * selected date-range (if any)
 *
 * @since 1.0.2
 *
 * @return array {
 *     Date values used by the reports API.
 *
 * @type int $day      Day of the month (1-31) to start filtering results by.
 * @type int $day_end  Day of the month (1-31) to end filtering results by.
 * @type int $m_start  Month of the year (1-12) to start filtering results by.
 * @type int $m_end    Month of the year (1-12) to end filtering results by.
 * @type int $year     Year to start filtering results by.
 * @type int $year_end Year to end filtering results by.
 * }
 */
function ea_get_report_dates() {
	$dates = array();

	$current_time = current_time( 'timestamp' );

	$dates['date_from'] = ! empty( $_REQUEST['filter_from'] ) ? $_REQUEST['filter_from'] : date( 'j/n/Y', $current_time );
	$dates['date_to']   = ! empty( $_REQUEST['filter_to'] ) ? $_REQUEST['filter_to'] : date( 'j/n/Y', $current_time );

	$variable_from_time = ! empty( $_REQUEST['filter_from'] ) ? strtotime( $dates['date_from'] ) : $current_time;
	$variable_to_time   = ! empty( $_REQUEST['filter_to'] ) ? strtotime( $dates['date_to'] ) : $current_time;

	$dates['range']    = isset( $_GET['range'] ) ? $_GET['range'] : 'this_month';
	$dates['year']     = isset( $_GET['year_start'] ) ? $_GET['year_start'] : date( 'Y', $variable_from_time );
	$dates['year_end'] = isset( $_GET['year_end'] ) ? $_GET['year_end'] : date( 'Y', $variable_to_time );
	$dates['m_start']  = isset( $_GET['m_start'] ) ? $_GET['m_start'] : date( 'n', $variable_from_time );
	$dates['m_end']    = isset( $_GET['m_end'] ) ? $_GET['m_end'] : date( 'n', $variable_to_time );
	$dates['day']      = isset( $_GET['day'] ) ? $_GET['day'] : date( 'd', $variable_from_time );
	$dates['day_end']  = isset( $_GET['day_end'] ) ? $_GET['day_end'] : date( 'd', $variable_to_time );

	// Modify dates based on predefined ranges
	switch ( $dates['range'] ) :

		case 'this_month' :
			$dates['day']     = 1;
			$dates['m_start'] = date( 'n', $current_time );
			$dates['m_end']   = date( 'n', $current_time );
			$dates['day_end'] = cal_days_in_month( CAL_GREGORIAN, $dates['m_start'], date( 'Y', $current_time ) );
			$dates['year']    = date( 'Y', $current_time );
			break;

		case 'last_month' :
			if ( date( 'n' ) == 1 ) {
				$dates['day']      = 1;
				$dates['day_end']  = cal_days_in_month( CAL_GREGORIAN, 12, date( 'Y', $current_time ) );
				$dates['m_start']  = 12;
				$dates['m_end']    = 12;
				$dates['year']     = date( 'Y', $current_time ) - 1;
				$dates['year_end'] = date( 'Y', $current_time ) - 1;
			} else {
				$dates['day']      = 1;
				$dates['day_end']  = cal_days_in_month( CAL_GREGORIAN, date( 'n' ) - 1, date( 'Y', $current_time ) );
				$dates['m_start']  = date( 'n' ) - 1;
				$dates['m_end']    = date( 'n' ) - 1;
				$dates['year_end'] = $dates['year'];
			}
			break;

		case 'today' :
			$dates['day']     = date( 'd', $current_time );
			$dates['day_end'] = date( 'd', $current_time );
			$dates['m_start'] = date( 'n', $current_time );
			$dates['m_end']   = date( 'n', $current_time );
			$dates['year']    = date( 'Y', $current_time );
			break;

		case 'yesterday' :
			$month            = date( 'n', $current_time ) == 1 && date( 'd', $current_time ) == 1 ? 12 : date( 'n', $current_time );
			$days_in_month    = cal_days_in_month( CAL_GREGORIAN, $month, date( 'Y', $current_time ) );
			$yesterday        = date( 'd', $current_time ) == 1 ? $days_in_month : date( 'd', $current_time ) - 1;
			$dates['day']     = $yesterday;
			$dates['day_end'] = $yesterday;
			$dates['m_start'] = $month;
			$dates['m_end']   = $month;
			$dates['year']    = $month == 1 && date( 'd', $current_time ) == 1 ? date( 'Y', $current_time ) - 1 : date( 'Y', $current_time );
			break;

		case 'this_week' :
			$dates['day']     = date( 'd', $current_time - ( date( 'w', $current_time ) - 1 ) * 60 * 60 * 24 ) - 1;
			$dates['day']     += get_option( 'start_of_week' );
			$dates['day_end'] = $dates['day'] + 6;
			$dates['m_start'] = date( 'n', $current_time );
			$dates['m_end']   = date( 'n', $current_time );
			$dates['year']    = date( 'Y', $current_time );
			break;

		case 'last_week' :
			$dates['day']     = date( 'd', $current_time - ( date( 'w' ) - 1 ) * 60 * 60 * 24 ) - 8;
			$dates['day']     += get_option( 'start_of_week' );
			$dates['day_end'] = $dates['day'] + 6;
			$dates['year']    = date( 'Y', $current_time );

			if ( date( 'j', $current_time ) <= 7 ) {
				$dates['m_start'] = date( 'n', $current_time ) - 1;
				$dates['m_end']   = date( 'n', $current_time ) - 1;
				if ( $dates['m_start'] <= 1 ) {
					$dates['year']     = date( 'Y', $current_time ) - 1;
					$dates['year_end'] = date( 'Y', $current_time ) - 1;
				}
			} else {
				$dates['m_start'] = date( 'n', $current_time );
				$dates['m_end']   = date( 'n', $current_time );
			}
			break;

		case 'this_quarter' :
			$month_now    = date( 'n', $current_time );
			$dates['day'] = 1;

			if ( $month_now <= 3 ) {

				$dates['day_end'] = cal_days_in_month( CAL_GREGORIAN, 4, date( 'Y', $current_time ) );
				$dates['m_start'] = 1;
				$dates['m_end']   = 4;
				$dates['year']    = date( 'Y', $current_time );

			} else if ( $month_now <= 6 ) {

				$dates['day_end'] = cal_days_in_month( CAL_GREGORIAN, 7, date( 'Y', $current_time ) );
				$dates['m_start'] = 4;
				$dates['m_end']   = 7;
				$dates['year']    = date( 'Y', $current_time );

			} else if ( $month_now <= 9 ) {

				$dates['day_end'] = cal_days_in_month( CAL_GREGORIAN, 10, date( 'Y', $current_time ) );
				$dates['m_start'] = 7;
				$dates['m_end']   = 10;
				$dates['year']    = date( 'Y', $current_time );

			} else {

				$dates['day_end']  = cal_days_in_month( CAL_GREGORIAN, 1, date( 'Y', $current_time ) + 1 );
				$dates['m_start']  = 10;
				$dates['m_end']    = 1;
				$dates['year']     = date( 'Y', $current_time );
				$dates['year_end'] = date( 'Y', $current_time ) + 1;

			}
			break;

		case 'last_quarter' :
			$month_now    = date( 'n' );
			$dates['day'] = 1;

			if ( $month_now <= 3 ) {

				$dates['day_end'] = cal_days_in_month( CAL_GREGORIAN, 9, date( 'Y', $current_time ) - 1 );
				$dates['m_start'] = 10;
				$dates['m_end']   = 12;
				$dates['year']    = date( 'Y', $current_time ) - 1; // Previous year

			} else if ( $month_now <= 6 ) {

				$dates['day_end'] = cal_days_in_month( CAL_GREGORIAN, 3, date( 'Y', $current_time ) );
				$dates['m_start'] = 1;
				$dates['m_end']   = 3;
				$dates['year']    = date( 'Y', $current_time );

			} else if ( $month_now <= 9 ) {

				$dates['day_end'] = cal_days_in_month( CAL_GREGORIAN, 6, date( 'Y', $current_time ) );
				$dates['m_start'] = 4;
				$dates['m_end']   = 6;
				$dates['year']    = date( 'Y', $current_time );

			} else {

				$dates['day_end'] = cal_days_in_month( CAL_GREGORIAN, 9, date( 'Y', $current_time ) );
				$dates['m_start'] = 7;
				$dates['m_end']   = 9;
				$dates['year']    = date( 'Y', $current_time );

			}
			break;

		case 'this_year' :
			$dates['day']      = 1;
			$dates['day_end']  = cal_days_in_month( CAL_GREGORIAN, 12, date( 'Y', $current_time ) );
			$dates['m_start']  = 1;
			$dates['m_end']    = 12;
			$dates['year']     = date( 'Y', $current_time );
			$dates['year_end'] = date( 'Y', $current_time );
			break;

		case 'last_year' :
			$dates['day']      = 1;
			$dates['day_end']  = cal_days_in_month( CAL_GREGORIAN, 12, date( 'Y', $current_time ) - 1 );
			$dates['m_start']  = 1;
			$dates['m_end']    = 12;
			$dates['year']     = date( 'Y', $current_time ) - 1;
			$dates['year_end'] = date( 'Y', $current_time ) - 1;
			break;

	endswitch;

	/**
	 * Filters the dates array for the current reports graph.
	 *
	 * @since 1.0.2
	 *
	 * @param array $dates    {
	 *                        Date values used by the reports API.
	 *
	 * @type int    $day      Day of the month (1-31) to start filtering results by.
	 * @type int    $day_end  Day of the month (1-31) to end filtering results by.
	 * @type int    $m_start  Month of the year (1-12) to start filtering results by.
	 * @type int    $m_end    Month of the year (1-12) to end filtering results by.
	 * @type int    $year     Year to start filtering results by.
	 * @type int    $year_end Year to end filtering results by.
	 * }
	 */
	return apply_filters( 'eaccounting_report_dates', $dates );
}
