<?php

namespace EverAccounting\Admin\Overview\Widgets;

use EverAccounting\Abstracts\Widget;
use EverAccounting\Query_Transaction;

class Total_Profit extends Widget {
	/**
	 * Widget column.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $widget_id = 'ea-total-profit';

	/**
	 * Widget column size.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $widget_size = 'ea-col-4';


	/**
	 * Overwrite header markup
	 * so it does not show any header.
	 *
	 * @since 1.0.2
	 */
	public function render_header() {

	}

	/**
	 *
	 * @since 1.0.2
	 * @return string|void
	 */
	public function get_widget_class(){
		return 'ea-summery-widget profit';
	}

	/**
	 * Render the main content of widget.
	 *
	 * @since 1.0.2
	 */
	public function get_content() {
		global $wpdb;
		$dates = $this->get_dates();
		$total        = 0;
		$transactions = eaccounting()
				->query()
				->select( 'amount, currency_code, currency_rate, type' )
				->from( 'ea_transactions' )
				->whereDateBetween( 'paid_at',  $dates['start'], $dates['end'] )
				->whereRaw( "category_id NOT IN(select id from {$wpdb->prefix}ea_categories where type='other')" )
				->get();

		foreach ( $transactions as $transaction ) {
			if ( $transaction->type == 'income' ) {
				$total += eaccounting_price_convert_to_default( $transaction->amount, $transaction->currency_code, $transaction->currency_rate );
			} else if ( $transaction->type == 'expense' ) {
				$total -= eaccounting_price_convert_to_default( $transaction->amount, $transaction->currency_code, $transaction->currency_rate );
			}
		}
		?>
		<div class="ea-summery-widget-icon">
			<span class="dashicons dashicons-heart"></span>
		</div>
		<div class="ea-summery-widget-content">
			<?php
			echo sprintf( '<span class="ea-summery-widget-title">%s</span>', __( 'Total Profit', 'wp-ever-accounting' ) );
			echo sprintf( '<span class="ea-summery-widget-amount">%s</span>', eaccounting_format_price( $total ) );
			?>
		</div>
		<?php
	}
}