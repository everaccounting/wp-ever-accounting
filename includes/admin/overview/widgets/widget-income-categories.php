<?php

namespace EverAccounting\Admin\Overview\Widgets;

use EverAccounting\Abstracts\Widget;
use EverAccounting\Chart;
use EverAccounting\Query_Transaction;

class Income_Categories extends Widget {
	/**
	 * Widget column.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $widget_id = 'ea-income-by-categories';

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
		echo sprintf( '<h2>%s</h2>', __( 'Income By Categories', 'wp-ever-accounting' ) );
	}

	/**
	 * Render the main content of widget.
	 *
	 * @since 1.0.2
	 */
	public function get_content() {
		$dates  = $this->get_dates();
		$donuts = $data = [];
		eaccounting()
			->query()
			->select( 'c.name, c.color, t.amount, t.currency_rate, t.currency_code' )
			->from( 'ea_transactions t' )
			->leftJoin( 'ea_categories c', 'c.id', 't.category_id' )
			->where( 'c.type', 'income' )
			->whereDateBetween( 'paid_at',  $dates['start'], $dates['end'])
			->get( OBJECT, function ( $item ) use ( &$data ) {
				$amount = eaccounting_price_convert_to_default( $item->amount, $item->currency_code, $item->currency_rate );
				if ( isset( $data[ $item->name ] ) ) {
					$data[ $item->name ]['amount'] = (int) ( $data[ $item->name ]['amount'] + $amount );
				} else {
					$data[ $item->name ] = array(
						'label'  => $item->name,
						'color'  => $item->color,
						'amount' => (int) $amount,
					);
				}
			} );


		$donuts = eaccounting_collect( array_values( $data ) )
			->sort( function ( $item1, $item2 ) {
				return $item1['amount'] - $item2['amount'];
			} )
			->reverse()
			->take( 6 )
			->each( function ( $item ) {
				$item['label'] .= ' - ' . eaccounting_format_price( $item['amount'] );

				return $item;
			} )->all();

		$labels  = wp_list_pluck( $donuts, 'label' );
		$colors  = wp_list_pluck( $donuts, 'color' );
		$amounts = wp_list_pluck( $donuts, 'amount' );

		if ( array_sum( $amounts ) == 0 ) {
			echo sprintf( '<p class="ea-overview-widget-notice">%s</p>',
				__( 'There is not enough data to visualize income by category. Please add incomes.', 'wp-ever-accounting' ) );
			return ;
		}


		$chart = new Chart();
		$chart->type( 'doughnut' )
		      ->width( 0 )
		      ->height( 160 )
		      ->set_donut_options( $colors )
		      ->labels( $labels )
		      ->dataset( array(
			      'data'            => $amounts,
			      'backgroundColor' => $colors,
			      'borderWidth'     => 1
		      ) )
		      ->render();
	}
}
