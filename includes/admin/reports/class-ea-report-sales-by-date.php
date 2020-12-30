<?php
/**
 * EAccounting_Report_Sales_By_Date
 *
 * @author      EverAccounting
 * @category    Admin
 * @package     EverAccounting\Admin
 * @version     1.1.0
 */

defined( 'ABSPATH' ) || exit();

class EAccounting_Report_Sales_By_Date extends EAccounting_Admin_Report {
	protected $data;


	public function prepare_data() {
		global $wpdb;
		$clauses = $this->get_range_sql( $this->get_date_period(), 'payment_date' );
		$results = $wpdb->get_results(
			"SELECT {$clauses[0]} payment_date, currency_code, currency_rate, amount
		FROM wp_ea_transactions
		WHERE category_id NOT IN ( SELECT id from wp_ea_categories WHERE type='other') AND `type` = 'income' AND  {$clauses[1]}
		"
		);
		foreach ( $results as $key => $result ) {
			$result->converted = eaccounting_price_convert_to_default( $result->amount, $result->currency_code, $result->currency_rate );
		}

		$labels = $this->get_date_labels( $this->get_date_period() );

		$this->data = array(
			'results' => $results,
			'labels'  => $labels,
		);
	}

	public function get_title() {
		return esc_html__( 'Sales by date', 'wp-ever-accounting' );
	}

	/**
	 * Display chart
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function display_chart() {
		$datasets = wp_list_pluck( $this->data['results'], 'converted' );
		$this->render_chart(
			array(
				'data' => array(
					'labels'   => array_values( $this->data['labels'] ),
					'datasets' => array(
						array(
							'label'           => __( 'Income', 'wp-ever-accounting' ),
							'data'            => array_values( $datasets ),
							'borderColor'     => '#3644ff',
							'backgroundColor' => '#3644ff',
							'borderWidth'     => 4,
							'pointStyle'      => 'line',
							'fill'            => false,
						),
					),
				),
			)
		);
	}

	public function display_table() {
		var_dump( $this->data);
		?>
		<table class="ea-table">
			<thead>
			<tr>
				<th>Date</th>
				<th>Expense <small>In Default</small></th>
				<th>Expense</th>
			</tr>
			</thead>
			<tbody>
				<?php foreach ( $this->data['results'] as $result ) : ?>
				<tr>
					<td><?php echo esc_html( eaccounting_date( $result->payment_date ) ); ?></td>
					<td><?php echo esc_html( eaccounting_price( $result->converted ) ); ?></td>
					<td><?php echo esc_html( eaccounting_price( $result->amount, $result->currency_code ) ); ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}
}

new EAccounting_Report_Sales_By_Date();
