<?php

namespace EverAccounting\Admin\Overview\Widgets;

use EverAccounting\Abstracts\Widget;
use EverAccounting\Chart;
use EverAccounting\Graph;
use EverAccounting\Query_Transaction;

class Latest_Incomes extends Widget {
	/**
	 * Widget column.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $widget_id = 'ea-latest-income';

	/**
	 * Widget column size.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $widget_size = 'ea-col-4';

	/**
	 * Title of the widget.
	 *
	 * @since 1.0.2
	 */
	public function get_title() {
		echo sprintf( '<h2>%s</h2>', __( 'Latest Incomes', 'wp-ever-accounting' ) );
	}

	/**
	 * Render the main content of widget.
	 *
	 * @since 1.0.2
	 */
	public function get_content() {
		$chart = new Chart();
		$chart->type( 'doughnut' )
			  ->width( 0 )
			  ->height( 160 )
			  ->labels( [ 'OK', 'WARNING', 'CRITICAL', 'UNKNOWN' ] )
			  ->dataset( '# of Tomatoes', [ 12, 19, 3, 5 ], array(
					  'backgroundColor' => [
							  'rgba(255, 99, 132, 0.5)',
							  'rgba(54, 162, 235, 0.2)',
							  'rgba(255, 206, 86, 0.2)',
							  'rgba(75, 192, 192, 0.2)'
					  ],
					  'borderColor'     => [
							  'rgba(255,99,132,1)',
							  'rgba(54, 162, 235, 1)',
							  'rgba(255, 206, 86, 1)',
							  'rgba(75, 192, 192, 1)'
					  ],
					  'borderWidth'     => 1
			  ) );
		$chart->render();
	}
}
