<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Reports_Controller extends EAccounting_REST_Controller {
	/**
	 * @var string
	 */
	protected $namespace = 'ea/v1';

	/**
	 * @var string
	 */
	protected $rest_base = 'reports';

	/**
	 * @since 1.0.0
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/summery', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_summery' ],
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => $this->get_collection_params(),
			),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/cashflow', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_cashflow' ],
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => $this->get_collection_params(),
			),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/income_categories', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_income_categories' ],
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => $this->get_collection_params(),
			),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/expense_categories', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_expense_categories' ],
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => $this->get_collection_params(),
			),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/income_report', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_income_report' ],
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => $this->get_collection_params(),
			),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/expense_report', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_expense_report' ],
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => $this->get_collection_params(),
			),
		) );

		register_rest_route( $this->namespace, '/' . $this->rest_base . '/income_expense_report', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_income_expense_report' ],
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'                => $this->get_collection_params(),
			),
		) );
	}


	public function get_summery( $request ) {
		$response = array(
			'income'  => 0,
			'expense' => 0,
			'profit'  => 0,
		);

		$duration = $this->get_query_dates( $request['date'] );
		$incomes  = eaccounting_get_transactions( array_merge(
			[
				'include_transfer' => false,
				'nopaging'         => true,
				'type'             => 'income',
				'fields'           => [ 'amount', 'currency_code', 'currency_rate' ]
			],
			$duration
		) );

		foreach ( $incomes as $income ) {
			$response['income'] += eaccounting_price_convert_to_default( $income->amount, $income->currency_code, $income->currency_rate );
		}

		$expenses = eaccounting_get_transactions( array_merge(
			[
				'include_transfer' => false,
				'nopaging'         => true,
				'type'             => 'expense',
				'fields'           => [ 'amount', 'currency_code', 'currency_rate' ]
			],
			$duration
		) );

		foreach ( $expenses as $expense ) {
			$response['expense'] += eaccounting_price_convert_to_default( $expense->amount, $expense->currency_code, $expense->currency_rate );
		}


		$response['profit'] = $response['income'] - $response['expense'];

		$response = array(
			'income'  => eaccounting_format_price( $response['income'] ),
			'expense' => eaccounting_format_price( $response['expense'] ),
			'profit'  => eaccounting_format_price( $response['profit'] ),
		);

		return rest_ensure_response( $response );
	}


	public function get_cashflow( $request ) {
		$financial_start = eaccounting_get_financial_start();
		$duration        = $this->get_query_dates( $request['date'] );
		// check and assign year start
		if ( ( $year_start = date( 'Y-1-1' ) ) !== $financial_start ) {
			$year_start = $financial_start;
		}

		$start_date = eaccounting_sanitize_date( $duration['start'],  $year_start );
		$end_date   = eaccounting_sanitize_date( $duration['end'],  date( 'Y-m-d' ) );

		$start = new \DateTime( $start_date );
		$end   = new \DateTime( $end_date );

//		$start_month = $start->format( 'm' );
//		$end_month   = ceil( $end->diff( $start )->format( '%a' ) / 12 );
//		if ( $end_month == 0 ) {
//			$start_month = 0;
//			$start->sub( new \DateInterval( 'P1M' ) );
//		}
//
//		// Monthly
//		$labels = [];
//
//		$s = clone $start;
//
//		for ( $j = $end_month; $j >= $start_month; $j -- ) {
//			$labels[ $end_month - $j ] = $s->format( 'M Y' );
//			$s->add( new \DateInterval( 'P1M' ) );
//		}
//		global $wpdb;
//		$income             = $this->calculate_total( $wpdb->ea_revenues, $start, $end, 'income' );
//		$expense            = $this->calculate_total( $wpdb->ea_payments, $start, $end, 'expense' );
//		$profit             = $this->calculate_profit( $income, $expense );
//		$report             = new \StdClass();
//		$report->labels     = array_values( $labels );
//		$report->height     = 300;
//		$report->width      = 0;
//		$report->options    = array(
//			'tooltips'   => [
//				'backgroundColor' => '#000000',
//				'titleFontColor'  => '#ffffff',
//				'bodyFontColor'   => '#e5e5e5',
//				'bodySpacing'     => 4,
//				'YrPadding'       => 12,
//				'mode'            => 'nearest',
//				'intersect'       => 0,
//				'position'        => 'nearest',
//			],
//			'responsive' => true,
//			'scales'     => [
//				'yAxes' => [
//					[
//						'barPercentage' => 1.6,
//						'ticks'         => [
//							'padding'   => 10,
//							'fontColor' => '#9e9e9e',
//						],
//						'gridLines'     => [
//							'drawBorder'       => false,
//							'color'            => 'rgba(29,140,248,0.1)',
//							'zeroLineColor'    => 'transparent',
//							'borderDash'       => [ 2 ],
//							'borderDashOffset' => [ 2 ],
//						],
//					]
//				],
//				'xAxes' => [
//					[
//						'barPercentage' => 1.6,
//						'ticks'         => [
//							'suggestedMin' => 60,
//							'suggestedMax' => 125,
//							'padding'      => 20,
//							'fontColor'    => '#9e9e9e',
//						],
//						'gridLines'     => [
//							'drawBorder'    => false,
//							'color'         => 'rgba(29,140,248,0.0)',
//							'zeroLineColor' => 'transparent',
//						],
//					]
//				],
//			],
//		);
//		$report->datasets   = [];
//		$report->datasets[] = array(
//			'label'           => __( 'Income' ),
//			'fill'            => false,
//			'borderWidth'     => 4,
//			'lineTension'     => 0.1,
//			'backgroundColor' => 'rgba(75,192,192,0.4)',
//			'borderColor'     => '#00c0ef',
//			'data'            => array_values( $income ),
//		);
//		$report->datasets[] = array(
//			'label'           => __( 'Expense' ),
//			'fill'            => false,
//			'borderWidth'     => 4,
//			'lineTension'     => 0.1,
//			'backgroundColor' => 'rgba(75,192,192,0.4)',
//			'borderColor'     => '#dd4b39',
//			'data'            => array_values( $expense ),
//		);
//
//		$report->datasets[] = array(
//			'label'           => __( 'Expense' ),
//			'borderWidth'     => 4,
//			'fill'            => false,
//			'lineTension'     => 0.1,
//			'backgroundColor' => 'rgba(75,192,192,0.4)',
//			'borderColor'     => '#6da252',
//			'data'            => array_values( $profit ),
//		);

		return rest_ensure_response( [] );
	}


	public function get_income_categories( $request ) {
		$duration = $this->get_query_dates( $request['date'] );
//		$incomes = eaccounting_get_income_by_categories( $duration['start'], $duration['end'] );

		$income_response = [
			'labels'           => '',
			'background_color' => '',
			'data'             => ''
		];
//		$incomes_labels  = [];
//		$incomes_colors  = [];
//		$incomes_data    = [];
//
//		if ( ! empty( $incomes ) ) {
//			foreach ( $incomes as $income ) {
//				$incomes_labels[] = sprintf( "%s - %s", eaccounting_format_price( $income['total']), $income['name'] );
//				$incomes_colors[] = $income['color'];
//				$incomes_data[]   = $income['total'];
//			}
//			$income_response = [
//				'labels'           => $incomes_labels,
//				'background_color' => $incomes_colors,
//				'data'             => $incomes_data
//			];
//		}

		return rest_ensure_response( $income_response );
	}

	public function get_expense_categories( $request ) {
		$duration = $this->get_query_dates( $request['date'] );
//		$expenses = eaccounting_get_expense_by_categories( $duration['start'], $duration['end'] );
		$expense_response = [
			'labels'           => '',
			'background_color' => '',
			'data'             => ''
		];
//		$expenses_labels  = [];
//		$expenses_colors  = [];
//		$expenses_data    = [];
//
//		if ( ! empty( $expenses ) ) {
//			foreach ( $expenses as $expense ) {
//				$expenses_labels[] = sprintf( "%s - %s", eaccounting_format_price( $expense['total']), $expense['name'] );
//				$expenses_colors[] = $expense['color'];
//				$expenses_data[]   = $expense['total'];
//			}
//			$expense_response = [
//				'labels'           => $expenses_labels,
//				'background_color' => $expenses_colors,
//				'data'             => $expenses_data
//			];
//		}

		return rest_ensure_response( $expense_response );
	}

	public function get_report( $request ) {

	}

	/**
	 * @param string $table
	 * @param DateTime $start
	 * @param DateTime $end
	 * @param string $type
	 *
	 * @return array
	 * @since 1.0.2
	 */
	protected function calculate_total( $table, $start, $end, $type ) {
//		$totals      = [];
//		$date_format = 'Y-m';
//		$start_date  = $start->format( $date_format );
//		$end_date    = $end->format( $date_format );
//		$next_date   = $start_date;
//
//		$s = clone $start;
//
//		//$totals[$start_date] = 0;
//		while ( $next_date <= $end_date ) {
//			$totals[ $next_date ] = 0;
//			$next_date            = $s->add( new \DateInterval( 'P1M' ) )->format( $date_format );
//		}
//
//		global $wpdb;
//		$results = $wpdb->get_results( $wpdb->prepare( "SELECT paid_at, SUM(amount/currency_rate) total
//														from $table WHERE category_id IN (SELECT id FROM $wpdb->ea_categories WHERE type=%s)
//														AND paid_at >= DATE(%s) AND paid_at <= DATE(%s) group by paid_at",
//			$type, $start->format( 'Y-m-d' ), $end->format( 'Y-m-d' ) ) );
//
//
//		foreach ( $results as $result ) {
//			$date = date( $date_format, strtotime( $result->paid_at ) );
//			if ( ! isset( $totals[ $date ] ) ) {
//				continue;
//			}
//
//			$totals[ $date ] = $totals[ $date ] + $result->total;
//		}
//
//		return $totals;

	}

	protected function calculate_profit( $incomes, $expenses ) {
//		$profit = [];
//
//		foreach ( $incomes as $key => $income ) {
//			if ( $income > 0 && $income > $expenses[ $key ] ) {
//				$profit[ $key ] = $income - $expenses[ $key ];
//			} else {
//				$profit[ $key ] = 0;
//			}
//		}
//
//		return $profit;
	}

	public function get_income_report( $request ) {
		$duration        = $this->get_query_dates( $request['date'] );
		$dates           = $totals = $incomes = $incomes_graph = $categories = [];
		$year            = ! empty( $request->year ) ? absint( $request->year ) : date( 'Y' );
		$category_id     = ! empty( $request->category_id ) ? absint( $request->category_id ) : '';
		$financial_start = eaccounting_get_financial_start( $year );

		$transactions = EAccounting_Transactions::init();
		global $wpdb;
		$categories   = $wpdb->get_results( "SELECT id, name FROM $wpdb->ea_categories WHERE type='income' ORDER BY name ASC", ARRAY_A );
		$category_ids = wp_list_pluck( $categories, 'id' );
		if ( ! empty( $category_id ) && in_array( $category_id, $category_ids ) ) {
			$categories = wp_list_filter( $categories, [ 'id' => $category_id ] );
		}

		$categories = wp_list_pluck( $categories, 'name', 'id' );
		$date       = new DateTime( $financial_start );
//
//		for ( $j = 1; $j <= 12; $j ++ ) {
//			$dates[ $j ]                             = $date->format( 'F' );
//			$incomes_graph[ $date->format( 'F-Y' ) ] = 0;
//			// Totals
//			$totals[ $dates[ $j ] ] = array(
//				'amount' => 0,
//			);
//
//			foreach ( $categories as $category_id => $category_name ) {
//				$incomes[ $category_id ][ $dates[ $j ] ] = [
//					'category_id' => $category_id,
//					'name'        => $category_name,
//					'amount'      => 0,
//				];
//			}
//			$date->modify( '+1 month' )->format( 'Y-m' );
//		}
//
//		$revenues = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->ea_revenues WHERE YEAR(paid_at)=%d AND category_id NOT IN (SELECT id from $wpdb->ea_categories WHERE type='other')", $year ) );
//		foreach ( $revenues as $revenue ) {
//			$month      = date( 'F', strtotime( $revenue->paid_at ) );
//			$month_year = date( 'F-Y', strtotime( $revenue->paid_at ) );
//
//			if ( ! isset( $incomes[ $revenue->category_id ] ) || ! isset( $incomes[ $revenue->category_id ][ $month ] ) || ! isset( $incomes_graph[ $month_year ] ) ) {
//				continue;
//			}
//
//			$incomes[ $revenue->category_id ][ $month ]['amount'] += ($revenue->amount / $revenue->currency_rate);
//			$incomes_graph[ $month_year ]                         += ($revenue->amount / $revenue->currency_rate);
//			$totals[ $month ]['amount']                           += ($revenue->amount / $revenue->currency_rate);
//		}
//
//		$data = compact( 'dates', 'categories', 'statuses', 'accounts', 'customers', 'incomes', 'totals', 'incomes_graph' );
//
//		return rest_ensure_response( $data );
	}

	public function expense_report( $request ) {
	}

	public function income_expense_report( $request ) {
	}


}
