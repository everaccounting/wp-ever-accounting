<?php

namespace EverAccounting\Admin\Overview\Widgets;

use EverAccounting\Abstracts\Widget;

class Total_Income extends Widget {
	/**
	 * Widget column.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $widget_id = 'ea-total-income';

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
	public function get_widget_class() {
		return 'ea-score-card success';
	}

	/**
	 * Render the main content of widget.
	 *
	 * @since 1.0.2
	 */
	public function get_content() {
		global $wpdb;
		$total        = 0;
		$transactions = $wpdb->get_results(
			$wpdb->prepare(
				"
		SELECT amount, currency_code, currency_rate
		FROM {$wpdb->prefix}ea_transactions
		WHERE type=%s
		AND category_id NOT IN(select id from {$wpdb->prefix}ea_categories where type='other')
		",
				'income'
			)
		);
		foreach ( $transactions as $transaction ) {
			$total += eaccounting_price_convert_to_default( $transaction->amount, $transaction->currency_code, $transaction->currency_rate );
		}
		?>
		<div class="ea-score-card__inside">
			<div class="ea-score-card__icon">
				<span class="dashicons dashicons-chart-pie"></span>
			</div>
			<div class="ea-score-card__content">

				<div class="ea-score-card__primary">
					<span class="ea-score-card__title"><?php esc_html_e('Total Income', 'wp-ever-accounting' );?></span>
					<span class="ea-score-card__amount"><?php echo esc_html(eaccounting_format_price( $total ));?></span>
				</div>

				<div class="ea-score-card__secondary">
					<span class="ea-score-card__title">Receivable</span>
					<span class="ea-score-card__amount">$50000</span>
				</div>
			</div>
		</div>
		<?php
	}
}
