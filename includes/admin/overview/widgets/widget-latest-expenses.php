<?php

namespace EverAccounting\Admin\Overview\Widgets;

use EverAccounting\Abstracts\Widget;
use EverAccounting\Query_Transaction;

class Latest_Expenses extends Widget {
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
		echo sprintf( '<h2>%s</h2>', __( 'Latest Expenses', 'wp-ever-accounting' ) );
	}

	/**
	 * Render the main content of widget.
	 *
	 * @since 1.0.2
	 */
	public function get_content() {
		global $wpdb;
		$expenses = $wpdb->get_results($wpdb->prepare("
		SELECT t.paid_at, c.name, t.amount, t.currency_code
		FROM {$wpdb->prefix}ea_transactions t
		LEFT JOIN {$wpdb->prefix}ea_categories as c on c.id=t.category_id
		WHERE t.type= 'expense'
		AND c.type != 'other'
		ORDER BY t.paid_at DESC
		LIMIT %d
		", 5));
		if ( empty( $expenses ) ) {
			echo sprintf(
				'<p class="ea-overview-widget-notice">%s</p>',
				__( 'There is no expense records.', 'wp-ever-accounting' )
			);

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
			<?php foreach ( $expenses as $expense ) : ?>
				<tr>
					<td><?php echo esc_html( $expense->paid_at ); ?></td>
					<td><?php echo esc_html( $expense->name ); ?></td>
					<td><?php echo eaccounting_format_price( $expense->amount, $expense->currency_code ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}
}
