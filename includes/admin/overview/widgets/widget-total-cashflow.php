<?php

namespace EverAccounting\Admin\Overview\Widgets;

use EverAccounting\Core\DateTime;
use EverAccounting\Abstracts\Widget;
use EverAccounting\Core\Chart;


class Cash_Flow extends Widget {
	/**
	 * Widget id.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $widget_id = 'ea-cashflow';

	/**
	 * Widget column size.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $widget_size = 'ea-col-12';

	/**
	 * Title of the widget.
	 *
	 * @since 1.0.2
	 */
	public function get_title() {
		echo sprintf( '<h2>%s</h2>', __( 'Cash Flow', 'wp-ever-accounting' ) );
	}

	/**
	 * Render the main content of widget.
	 *
	 * @since 1.0.2
	 */
	public function get_content() {
		$dates  = $this->get_dates();
		$period = empty( $_GET['period'] ) ? 'month' : eaccounting_clean( $_GET['period'] );
		$range  = empty( $_GET['range'] ) ? 'custom' : eaccounting_clean( $_GET['range'] );
		$start  = eaccounting_string_to_datetime( $dates['start'] );
		$end    = eaccounting_string_to_datetime( $dates['end'] );

		$start_month = (int) $start->format( 'm' );
		$end_month   = (int) $end->format( 'm' );
		// Monthly
		$labels = [];

		$s = clone $start;

		if ( $range == 'last_12_months' ) {
			$end_month   = 12;
			$start_month = 0;
		} elseif ( $range == 'custom' ) {
			$end_month   = $end->diff( $start )->format( '%m' );
			$start_month = 0;
		}

		for ( $j = $end_month; $j >= $start_month; $j -- ) {
			$labels[ $end_month - $j ] = $s->format( 'M Y' );

			if ( $period == 'month' ) {
				$s->add( new \DateInterval( 'P1M' ) );
			} else {
				$s->add( new \DateInterval( 'P3M' ) );
				$j -= 2;
			}
		}

		$incomes  = $this->calculate_total( 'income', $start, $end, $period );
		$expenses = $this->calculate_total( 'expense', $start, $end, $period );
		$profits  = $this->calculate_profit( $incomes, $expenses );

		$chart = new Chart();
		$chart->type( 'line' )
			  ->width( 0 )
			  ->height( 300 )
			  ->set_line_options()
			  ->labels( array_values( $labels ) )
			->dataset(
				array(
					'label'           => __( 'Incomes', 'wp-ever-accounting' ),
					'data'            => array_values( $incomes ),
					'borderColor'     => '#3644ff',
					'backgroundColor' => '#3644ff',
					'borderWidth'     => 4,
					'pointStyle'      => 'line',
					'fill'            => false,
				)
			)
			->dataset(
				array(
					'label'           => __( 'Expenses', 'wp-ever-accounting' ),
					'data'            => array_values( $expenses ),
					'borderColor'     => '#f2385a',
					'backgroundColor' => '#f2385a',
					'borderWidth'     => 4,
					'pointStyle'      => 'line',
					'fill'            => false,
				)
			)
			->dataset(
				array(
					'label'           => __( 'Profits', 'wp-ever-accounting' ),
					'data'            => array_values( $profits ),
					'borderColor'     => '#06d6a0',
					'backgroundColor' => '#06d6a0',
					'borderWidth'     => 4,
					'pointStyle'      => 'line',
					'fill'            => false,
				)
			)
			  ->render();
	}

	/**
	 * @since 1.0.2
	 *
	 * @param \EverAccounting\Core\DateTime $start
	 * @param \EverAccounting\Core\DateTime $end
	 * @param string                   $period
	 * @param                       $type
	 */
	public function calculate_total( $type, $start = null, $end = null, $period = null ) {
		global $wpdb;
		$totals = [];

		$date_format = 'Y-m';

		if ( $period == 'month' ) {
			$n          = 1;
			$start_date = $start->format( $date_format );
			$end_date   = $end->format( $date_format );
			$next_date  = $start_date;
		} else {
			$n          = 3;
			$start_date = $start->quarter();
			$end_date   = $end->quarter();
			$next_date  = $start_date;
		}

		$s = clone $start;

		// $totals[$start_date] = 0;
		while ( $next_date <= $end_date ) {
			$totals[ $next_date ] = 0;

			if ( $period == 'month' ) {
				$next_date = $s->add( new \DateInterval( "P{$n}M" ) )->format( $date_format );
			} else {
				if ( isset( $totals[4] ) ) {
					break;
				}

				$next_date = $s->add( new \DateInterval( "P{$n}M" ) )->quarter();
			}
		}

		$transactions = $wpdb->get_results($wpdb->prepare("
		SELECT amount, currency_code, currency_rate, paid_at
		FROM {$wpdb->prefix}ea_transactions
		WHERE (`paid_at` BETWEEN %s AND %s)
		AND `type`=%s
		AND category_id NOT IN(select id from {$wpdb->prefix}ea_categories where type='other')
		", $start->format( 'Y-m-d' ), $end->format( 'Y-m-d' ), $type));


		eaccounting_collect( $transactions )->each(
			function ( $item ) use ( $period, $date_format, &$totals ) {
				$paid_at = new DateTime( $item->paid_at );
				if ( $period == 'month' ) {
					  $i = $paid_at->format( $date_format );
				} else {
					$i = $paid_at->quarter();
				}
				$totals[ $i ] += eaccounting_price_convert_to_default( $item->amount, $item->currency_code, $item->currency_rate );
			}
		);

		return $totals;
	}

	/**
	 * @since 1.0.2
	 *
	 * @param $expenses
	 * @param $incomes
	 *
	 * @return array
	 */
	public function calculate_profit( $incomes, $expenses ) {
		$profit = [];

		foreach ( $incomes as $key => $income ) {
			if ( $income > 0 && $income > $expenses[ $key ] ) {
				$profit[ $key ] = $income - $expenses[ $key ];
			} else {
				$profit[ $key ] = 0;
			}
		}

		return $profit;
	}
}
