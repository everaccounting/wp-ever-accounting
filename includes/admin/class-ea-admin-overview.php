<?php
/**
 * EverAccounting Admin Overview Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin
 * @version     1.1.0
 */

defined( 'ABSPATH' ) || exit();

class EAccounting_Admin_Overview {
	/**
	 * EAccounting_Admin_Overview constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_page' ), 1 );
	}

	/**
	 * Registers the overview page.
	 *
	 * @since 1.1.0
	 */
	public function register_page() {
		global $menu;

		if ( current_user_can( 'manage_eaccounting' ) ) {
			$menu[] = array( '', 'read', 'ea-separator', '', 'wp-menu-separator accounting' );
		}
		$icons = 'data:image/svg+xml;base64,' . base64_encode( file_get_contents( eaccounting()->plugin_path( 'assets/images/icon.svg' ) ) );

		add_menu_page(
				__( 'Accounting', 'wp-ever-accounting' ),
				__( 'Accounting', 'wp-ever-accounting' ),
				'manage_eaccounting',
				'eaccounting',
				null,
				$icons,
				'54.5'
		);
		$overview = add_submenu_page(
				'eaccounting',
				__( 'Overview', 'wp-ever-accounting' ),
				__( 'Overview', 'wp-ever-accounting' ),
				'manage_eaccounting',
				'eaccounting',
				array( $this, 'render_page' )
		);
		//      error_log($overview);
		add_action( 'load-' . $overview, array( __CLASS__, 'eaccounting_dashboard_setup' ) );
	}

	/**
	 * Render page.
	 *
	 * @since 1.1.0
	 */
	public function render_page() {
		include dirname( __FILE__ ) . '/views/admin-page-overview.php';
	}

	public static function eaccounting_dashboard_setup() {
		add_meta_box( 'total-income', false, array( __CLASS__, 'render_total_income_widget' ), 'ea-overview', 'top' );
		add_meta_box( 'total-expense', false, array( __CLASS__, 'render_total_expense_widget' ), 'ea-overview', 'top' );
		add_meta_box( 'total-profit', false, array( __CLASS__, 'render_total_profit_widget' ), 'ea-overview', 'top' );
		add_meta_box( 'cash-flow', __( 'Cash Flow', 'wp-ever-accounting' ), array( __CLASS__, 'render_cashflow' ), 'ea-overview', 'middle', 'high', array( 'col' => '12' ) );
		add_meta_box( 'income-category-chart', __( 'Income By Categories', 'wp-ever-accounting' ), array( __CLASS__, 'render_incomes_categories' ), 'ea-overview', 'advanced', 'high', array( 'col' => '6' ) );
		add_meta_box( 'expense-category-chart', __( 'Expense By Categories', 'wp-ever-accounting' ), array( __CLASS__, 'render_expenses_categories' ), 'ea-overview', 'advanced', 'high', array( 'col' => '6' ) );
		add_meta_box( 'latest-income', __( 'Latest Incomes', 'wp-ever-accounting' ), array( __CLASS__, 'render_latest_incomes' ), 'ea-overview' );
		add_meta_box( 'latest-expense', __( 'Latest Expenses', 'wp-ever-accounting' ), array( __CLASS__, 'render_latest_expenses' ), 'ea-overview' );
		add_meta_box( 'account-balance', __( 'Account Balances', 'wp-ever-accounting' ), array( __CLASS__, 'render_account_balances' ), 'ea-overview' );
		do_action( 'eaccounting_dashboard_setup' );
	}

	public static function render_total_income_widget() {
		global $wpdb;
		$total_income = get_transient( 'eaccounting_widget_total_income' );
		if ( empty( $total_income ) ) {
			$sql          = $wpdb->prepare(
					"
										SELECT Sum(amount) amount,
								   currency_code,
								   currency_rate
							FROM   {$wpdb->prefix}ea_transactions
							WHERE  type = %s
								   AND category_id NOT IN (SELECT id
														   FROM   {$wpdb->prefix}ea_categories
														   WHERE  type = 'other')
							GROUP  BY currency_code,
									  currency_rate
			",
					'income'
			);
			$results      = $wpdb->get_results( $sql );
			$total_income = 0;
			foreach ( $results as $result ) {
				$total_income += eaccounting_price_convert_to_default( $result->amount, $result->currency_code, $result->currency_rate );
			}
			set_transient( 'eaccounting_widget_total_income', $total_income, MINUTE_IN_SECONDS * 1 );
		}

		$total_receivable = get_transient( 'eaccounting_widget_total_receivable' );
		if ( empty( $total_receivable ) ) {
			$sql = $wpdb->prepare(
					"
			SELECT Sum(amount) amount,
				   currency_code,
				   currency_rate
			FROM   {$wpdb->prefix}ea_transactions
			WHERE  type = %s
				   AND document_id IN (SELECT id
									   FROM   {$wpdb->prefix}ea_documents
									   WHERE  status NOT IN ( 'draft', 'cancelled' )
											  AND `status` <> 'paid'
											  AND type = 'invoice')
			GROUP  BY currency_code,
					  currency_rate
			",
					'income'
			);

			$results          = $wpdb->get_results( $sql );
			$total_receivable = 0;
			foreach ( $results as $result ) {
				$total_receivable += eaccounting_price_convert_to_default( $result->amount, $result->currency_code, $result->currency_rate );
			}
			set_transient( 'eaccounting_widget_total_receivable', $total_receivable, MINUTE_IN_SECONDS * 1 );
		}

		?>
		<div class="ea-score-card__inside">
			<div class="ea-score-card__icon">
				<span class="dashicons dashicons-money-alt"></span>
			</div>
			<div class="ea-score-card__content">
				<div class="ea-score-card__primary">
					<span class="ea-score-card__title"><?php esc_html_e( 'Total Sales', 'wp-ever-accounting' ); ?></span>
					<span class="ea-score-card__amount"><?php echo eaccounting_format_price( $total_income ); ?></span>
				</div>

				<div class="ea-score-card__secondary">
					<span class="ea-score-card__title"><?php esc_html_e( 'Receivable', 'wp-ever-accounting' ); ?></span>
					<span class="ea-score-card__amount"><?php echo eaccounting_format_price( $total_receivable ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	public static function render_total_expense_widget() {
		global $wpdb;
		$total_expense = get_transient( 'eaccounting_widget_total_expense' );
		if ( empty( $total_expense ) ) {
			$sql           = $wpdb->prepare(
					"
										SELECT Sum(amount) amount,
								   currency_code,
								   currency_rate
							FROM   {$wpdb->prefix}ea_transactions
							WHERE  type = %s
								   AND category_id NOT IN (SELECT id
														   FROM   {$wpdb->prefix}ea_categories
														   WHERE  type = 'other')
							GROUP  BY currency_code,
									  currency_rate
			",
					'expense'
			);
			$results       = $wpdb->get_results( $sql );
			$total_expense = 0;
			foreach ( $results as $result ) {
				$total_expense += eaccounting_price_convert_to_default( $result->amount, $result->currency_code, $result->currency_rate );
			}
			set_transient( 'eaccounting_widget_total_expense', $total_expense, MINUTE_IN_SECONDS * 1 );
		}
		$total_payable = get_transient( 'eaccounting_widget_total_payable' );
		if ( empty( $total_payable ) ) {
			$sql = $wpdb->prepare(
					"
			SELECT Sum(amount) amount,
				   currency_code,
				   currency_rate
			FROM   {$wpdb->prefix}ea_transactions
			WHERE  type = %s
				   AND document_id IN (SELECT id
									   FROM   {$wpdb->prefix}ea_documents
									   WHERE  status NOT IN ( 'draft', 'cancelled' )
											  AND `status` <> 'paid'
											  AND type = 'bill')
			GROUP  BY currency_code,
					  currency_rate
			",
					'expense'
			);

			$results       = $wpdb->get_results( $sql );
			$total_payable = 0;
			foreach ( $results as $result ) {
				$total_payable += eaccounting_price_convert_to_default( $result->amount, $result->currency_code, $result->currency_rate );
			}
			set_transient( 'eaccounting_widget_total_payable', $total_payable, MINUTE_IN_SECONDS * 1 );
		}
		?>
		<div class="ea-widget-card alert">
			<div class="ea-widget-card__icon">
				<span class="dashicons dashicons-money-alt"></span>
			</div>
			<div class="ea-widget-card__content">
				<div class="ea-score-card__primary">
					<span class="ea-score-card__title"><?php esc_html_e( 'Total Expenses', 'wp-ever-accounting' ); ?></span>
					<span class="ea-score-card__amount"><?php echo eaccounting_format_price( $total_expense ); ?></span>
				</div>

				<div class="ea-score-card__secondary">
					<span class="ea-score-card__title"><?php esc_html_e( 'Payable', 'wp-ever-accounting' ); ?></span>
					<span class="ea-score-card__amount"><?php echo eaccounting_format_price( $total_payable ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	public static function render_total_profit_widget() {
		$total_income     = (float) get_transient( 'eaccounting_widget_total_income' );
		$total_expense    = (float) get_transient( 'eaccounting_widget_total_expense' );
		$total_receivable = (float) get_transient( 'eaccounting_widget_total_receivable' );
		$total_payable    = (float) get_transient( 'eaccounting_widget_total_payable' );
		$total_profit     = $total_income - $total_expense;
		$total_upcoming   = $total_receivable - $total_payable;
		?>
		<div class="ea-widget-card success">
			<div class="ea-widget-card__icon">
				<span class="dashicons dashicons-money-alt"></span>
			</div>
			<div class="ea-widget-card__content">
				<div class="ea-score-card__primary">
					<span class="ea-score-card__title"><?php esc_html_e( 'Total Profit', 'wp-ever-accounting' ); ?></span>
					<span class="ea-score-card__amount"><?php echo eaccounting_format_price( $total_profit ); ?></span>
				</div>

				<div class="ea-score-card__secondary">
					<span class="ea-score-card__title"><?php esc_html_e( 'Upcoming', 'wp-ever-accounting' ); ?></span>
					<span class="ea-score-card__amount"><?php echo eaccounting_format_price( $total_upcoming ); ?></span>
				</div>
			</div>
		</div>
		<?php
	}

	public static function render_cashflow() {
		require_once dirname( __FILE__ ) . '/reports/class-ea-admin-report.php';
		require_once dirname( __FILE__ ) . '/reports/class-ea-report-cashflow.php';
		$year   = date_i18n( 'Y' );
		$init   = new EAccounting_Report_CashFlow();
		$report = $init->get_report( array( 'year' => $year ) );
		?>
		<div class="ea-card__inside" style="position: relative; height:300px;">
			<canvas id="ea-cashflow-chart" height="300" width="0"></canvas>
			<script>
				window.addEventListener('DOMContentLoaded', function () {
					var ctx = document.getElementById('ea-cashflow-chart').getContext('2d');
					new Chart(
							ctx,
							{
								type: 'line',
								data: {
									'labels': <?php echo json_encode( array_values( $report['dates'] ) ); ?>,
									'datasets': [
										{
											label: '<?php echo __( 'Income', 'wp-ever-accounting' ); ?>',
											data: <?php echo json_encode( array_values( $report['incomes'] ) ); ?>,
											backgroundColor: 'rgba(54, 68, 255, 0.1)',
											borderColor: 'rgb(54, 68, 255)',
											borderWidth: 4,
											fill: false,
											pointBackgroundColor: 'rgb(54, 68, 255)'
										},
										{
											label: '<?php echo __( 'Expense', 'wp-ever-accounting' ); ?>',
											data: <?php echo json_encode( array_values( $report['expenses'] ) ); ?>,
											backgroundColor: 'rgba(242, 56, 90, 0.1)',
											borderColor: 'rgb(242, 56, 90)',
											borderWidth: 4,
											fill: false,
											pointBackgroundColor: 'rgb(242, 56, 90)'
										},
										{
											label: '<?php echo __( 'Profit', 'wp-ever-accounting' ); ?>',
											data: <?php echo json_encode( array_values( $report['profits'] ) ); ?>,
											backgroundColor: 'rgba(0, 198, 137, 0.1)',
											borderColor: 'rgb(0, 198, 137)',
											borderWidth: 4,
											fill: false,
											pointBackgroundColor: 'rgb(0, 198, 137)'
										}
									]
								},
								options: {
									responsive: true,
									maintainAspectRatio: false,
									tooltips: {
										YrPadding: 12,
										backgroundColor: "#000000",
										bodyFontColor: "#e5e5e5",
										bodySpacing: 4,
										intersect: 0,
										mode: "nearest",
										position: "nearest",
										titleFontColor: "#ffffff",
										callbacks: {
											label: function (t, d) {
												var xLabel = d.datasets[t.datasetIndex].label;
												var yLabel = t.yLabel;
												return xLabel + ': ' + yLabel;
											}
										}
									},
									scales: {
										yAxes: [{
											barPercentage: 1.6,
											gridLines: {
												// borderDash: [1],
												// borderDashOffset: [2],
												color: "rgba(29,140,248,0.1)",
												drawBorder: false,
												zeroLineColor: "transparent",
											},
											ticks: {
												padding: 10,
												fontColor: '#9e9e9e',
												beginAtZero: true,
												callback: function (value, index, values) {
													return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
												}
											}
										}],
										xAxes: [{
											gridLines: {
												color: "rgba(29,140,248,0.0)",
												drawBorder: false,
												zeroLineColor: "transparent",
											},
											ticks: {
												fontColor: "#9e9e9e",
												suggestedMax: 125,
												suggestedMin: 60,
											}
										}]
									},
									legend: {
										display: false
									}
								}
							}
					);

				})
			</script>
		</div>
		<?php
	}

	public static function render_incomes_categories() {
		global $wpdb;
		require_once dirname( __FILE__ ) . '/reports/class-ea-admin-report.php';
		$report     = new EAccounting_Admin_Report();
		$start_date = $report->get_start_date();
		$end_date   = $report->get_end_date();
		$sql        = $wpdb->prepare(
				"SELECT SUM(t.amount) amount, t.currency_code, t.currency_rate, t.category_id, c.name category, c.color
		                     FROM {$wpdb->prefix}ea_transactions t
		                     LEFT JOIN {$wpdb->prefix}ea_categories c on c.id=t.category_id
		                     WHERE c.type = %s AND t.payment_date BETWEEN %s AND %s
		                     GROUP BY t.currency_code,t.currency_rate, t.category_id ",
				'income',
				$start_date,
				$end_date
		);
		$results    = $wpdb->get_results( $sql );
		$data       = array();
		foreach ( $results as $result ) {
			$amount = eaccounting_price_convert_to_default( $result->amount, $result->currency_code, $result->currency_rate );
			if ( isset( $data[ $result->category ] ) ) {
				$data[ $result->category ]['amount'] = (int) ( $data[ $result->category ]['amount'] + $amount );
			} else {
				$data[ $result->category ] = array(
						'name'   => $result->category,
						'color'  => $result->color,
						'amount' => (int) $amount,
				);
			}
		}
		$labels  = wp_list_pluck( $data, 'name' );
		$colors  = wp_list_pluck( $data, 'color' );
		$amounts = wp_list_pluck( $data, 'amount' );
		?>
		<div class="chart-container" style="position: relative; height:300px; width:100%">
			<canvas id="ea-incomes-category-chart" height="300" width="0"></canvas>
			<script>
				window.addEventListener('DOMContentLoaded', function () {
					var ctx = document.getElementById('ea-incomes-category-chart').getContext('2d');

					var data = [{
						data: [50, 55, 60, 33],
						labels: ["India", "China", "US", "Canada"],
						backgroundColor: [
							"#4b77a9",
							"#5f255f",
							"#d21243",
							"#B27200"
						],
						borderColor: "#fff"
					}];

					var options = {
						tooltips: {
							enabled: false
						},
						plugins: {
							labels: [
								{
									render: 'label',
									position: 'outside'
								},
								{
									render: 'percentage'
								}
							]
						}
					};

					var myChart = new Chart(ctx, {
						type: 'pie',
						data: {
							datasets: data
						},
						options: options
					});


					// new Chart(ctx, {
					// 	type: 'pie',
					// 	data: {
					// 		labels: ["Africa", "Asia", "Europe", "Latin America", "North America"],
					// 		datasets: [{
					// 			label: "Population (millions)",
					// 			backgroundColor: ["#3e95cd", "#8e5ea2", "#3cba9f", "#e8c3b9", "#c45850"],
					// 			data: [2478, 5267, 734, 784, 433]
					// 		}]
					// 	},
					// 	options: {
					// 		plugins: {
					// 			labels: {
					// 				render: 'percentage',
					// 				fontColor: ['green', 'white', 'red'],
					// 				precision: 2
					// 			}
					// 		}
					// 	}
					// });

					//new Chart(document.getElementById('ea-incomes-category-chart'), {
					//	"type": "pie",
					//	"data": {"labels": <?php //echo json_encode( array_values( $labels ) ); ?>//, "datasets": [{"label": "", "data": <?php //echo json_encode( array_values( $amounts ) ); ?>//, "color": "", "backgroundColor": <?php //echo json_encode( array_values( $colors ) ); ?>//, "options": [], "fill": false, "borderWidth": 1}]},
					//	"options": {"color": <?php //echo json_encode( array_values( $colors ) ); ?>//, "cutoutPercentage": 50, "legend": {"position": "right"}, "tooltips": {"backgroundColor": "#000000", "titleFontColor": "#ffffff", "bodyFontColor": "#e5e5e5", "bodySpacing": 4, "xPadding": 12, "mode": "nearest", "intersect": 0, "position": "nearest"}, "scales": {"yAxes": {"display": false}, "xAxes": {"display": false}}},
					//	plugins: {
					//		datalabels: {
					//			formatter: (value, ctx) => {
					//
					//				let sum = 0;
					//				let dataArr = ctx.chart.data.datasets[0].data;
					//				dataArr.map(data => {
					//					sum += data;
					//				});
					//				let percentage = (value*100 / sum).toFixed(2)+"%";
					//				return percentage;
					//
					//
					//			},
					//			color: '#fff',
					//		}
					//	}
					//})

					//new Chart(
					//		ctx,
					//		{
					//			type: 'pie',
					//			data: {
					//				datasets: [{
					//					data: <?php //echo json_encode( array_values( $amounts ) ); ?>
					//				}],
					//				labels: <?php //echo json_encode( array_values( $labels ) ); ?>
					//			},
					//			options: {
					//				tooltips: {
					//					enabled: false
					//				}
					//			}
					//);

				})
			</script>
		</div>
		<?php
	}

	public static function render_expenses_categories() {
		require_once dirname( __FILE__ ) . '/reports/class-ea-admin-report.php';
		require_once dirname( __FILE__ ) . '/reports/class-ea-report-expenses.php';
		$report          = new EAccounting_Report_Expenses();
		$data            = $report->get_report( array( 'year' => date_i18n( 'Y' ) ) );
		$category_colors = wp_list_pluck( $data['results'], 'color', 'category_id' );
	}

	public static function render_latest_incomes() {
		global $wpdb;
		$incomes = $wpdb->get_results(
				$wpdb->prepare(
						"
		SELECT t.payment_date, c.name, t.amount, t.currency_code
		FROM {$wpdb->prefix}ea_transactions t
		LEFT JOIN {$wpdb->prefix}ea_categories as c on c.id=t.category_id
		WHERE t.type= 'income'
		AND t.currency_code != ''
		AND c.type != 'other'
		ORDER BY t.payment_date DESC
		LIMIT %d
		",
						5
				)
		);

		if ( empty( $incomes ) ) {
			echo sprintf(
					'<p class="ea-card__inside">%s</p>',
					__( 'There is no income records.', 'wp-ever-accounting' )
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
			<?php foreach ( $incomes as $income ) : ?>
				<tr>
					<td><?php echo esc_html( $income->payment_date ); ?></td>
					<td><?php echo esc_html( $income->name ); ?></td>
					<td><?php echo eaccounting_format_price( $income->amount, $income->currency_code ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	public static function render_latest_expenses() {
		global $wpdb;
		$expenses = $wpdb->get_results(
				$wpdb->prepare(
						"
		SELECT t.payment_date, c.name, t.amount, t.currency_code
		FROM {$wpdb->prefix}ea_transactions t
		LEFT JOIN {$wpdb->prefix}ea_categories as c on c.id=t.category_id
		WHERE t.type= 'expense'
		AND t.currency_code != ''
		AND c.type != 'other'
		ORDER BY t.payment_date DESC
		LIMIT %d
		",
						5
				)
		);
		if ( empty( $expenses ) ) {
			echo sprintf(
					'<p class="ea-card__inside">%s</p>',
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
					<td><?php echo esc_html( $expense->payment_date ); ?></td>
					<td><?php echo esc_html( $expense->name ); ?></td>
					<td><?php echo eaccounting_format_price( $expense->amount, $expense->currency_code ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	public static function render_account_balances() {
		global $wpdb;
		$accounts = $wpdb->get_results(
				$wpdb->prepare(
						"
				SELECT a.name, a.opening_balance, a.currency_code,
				SUM(CASE WHEN t.type='income' then amount WHEN t.type='expense' then - amount END ) balance
				FROM {$wpdb->prefix}ea_accounts a
				LEFT JOIN {$wpdb->prefix}ea_transactions as t ON t.account_id=a.id
				GROUP BY a.id
				ORDER BY balance DESC
				LIMIT %d",
						5
				)
		);

		foreach ( $accounts as $key => $account ) {
			$total            = $account->balance + $account->opening_balance;
			$account->balance = eaccounting_format_price( $total, $account->currency_code );
			$accounts[ $key ] = $account;
		}

		if ( empty( $accounts ) ) {
			echo sprintf(
					'<p class="ea-card__inside">%s</p>',
					__( 'There is not accounts.', 'wp-ever-accounting' )
			);

			return;
		}

		?>
		<table class="ea-table">
			<thead>
			<tr>
				<th><?php _e( 'Account', 'wp-ever-accounting' ); ?></th>
				<th><?php _e( 'Balance', 'wp-ever-accounting' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $accounts as $account ) : ?>
				<tr>
					<td><?php echo esc_html( $account->name ); ?></td>
					<td><?php echo esc_html( $account->balance ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}
}

return new EAccounting_Admin_Overview();
