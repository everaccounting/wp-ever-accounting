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
		$incomes = eaccounting()
			->query()
			->select( 't.paid_at, c.name, t.amount, t.currency_code' )
			->from( 'ea_transactions t' )
			->leftJoin( 'ea_categories as c', 'c.id', 't.category_id' )
			->where('t.type', 'income')
			->where('c.type', '!=', 'other')
			->where( 'c.company_id', eaccounting_get_active_company() )
			->order_by( 't.paid_at', 'DESC' )
			->limit( 5 )
			->get();
		if ( empty( $incomes ) ) {
			echo sprintf( '<p class="ea-overview-widget-notice">%s</p>',
				__( 'There is no income records.', 'wp-ever-accounting' ) );

			return;
		}

		?>
		<table class="ea-table">
			<thead>
			<tr>
				<th><?php _e( 'Date', 'wp-ever-accounting' ); ?></th>
				<th><?php _e( 'Category', 'wp-ever-accounting' ); ?></th>
				<th><?php _e( 'Amount', 'wp-ever-accounting' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $incomes as $income ): ?>
				<tr>
					<td><?php echo esc_html( $income->paid_at ); ?></td>
					<td><?php echo esc_html( $income->name ); ?></td>
					<td><?php echo eaccounting_format_price($income->amount, $income->currency_code); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}
}