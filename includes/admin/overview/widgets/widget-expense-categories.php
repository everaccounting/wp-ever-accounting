<?php

namespace EverAccounting\Admin\Overview\Widgets;

use EverAccounting\Abstracts\Widget;
use EverAccounting\Chart;
use EverAccounting\Query;
use EverAccounting\Query_Transaction;

class Expense_Categories extends Widget {
	/**
	 * Widget column.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $widget_id = 'ea-expense-by-categories';

	/**
	 * Widget column size.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $widget_size = 'ea-col-6';

	/**
	 * Title of the widget.
	 *
	 * @since 1.0.2
	 */
	public function get_title() {
		echo sprintf( '<h2>%s</h2>', __( 'Expense By Categories', 'wp-ever-accounting' ) );
	}

	/**
	 * Render the main content of widget.
	 *
	 * @since 1.0.2
	 */
	public function get_content() {
		global $wpdb;
		$chart = new Chart();
		$donuts = [];
		$expenses = Query::init()
		                     ->select( 'c.name, c.color, t.amount, t.currency_rate, t.currency_code' )
		                     ->from( 'ea_transactions t' )
		                     ->leftJoin( 'ea_categories c', 'c.id', 't.category_id' )
		                     ->where( 'c.type', 'income' )
		                     ->get(OBJECT, function ($expense){

		                     });

	}
}
