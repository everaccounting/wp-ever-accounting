<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Dashboard
 *
 * @since 1.0.0
 * @package EverAccounting\Admin
 */
class Dashboard {

	/**
	 * Dashboard constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'eac_dashboard_page_content', array( __CLASS__, 'page_content' ) );
		add_action( 'eac_dashboard_overview_widgets', array( __CLASS__, 'overview_widget' ) );
		add_filter( 'eac_dashboard_overview_stats', array( __CLASS__, 'overview_stats' ) );
		add_action( 'eac_dashboard_widgets', array( __CLASS__, 'recent_payments' ) );
		add_action( 'eac_dashboard_widgets', array( __CLASS__, 'recent_expenses' ) );
		add_action( 'eac_dashboard_widgets', array( __CLASS__, 'recent_invoices' ) );
		add_action( 'eac_dashboard_widgets', array( __CLASS__, 'top_items' ) );
		add_action( 'eac_dashboard_widgets', array( __CLASS__, 'top_customers' ) );
		add_action( 'eac_dashboard_widgets', array( __CLASS__, 'top_vendors' ) );
	}

	/**
	 * Page content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function render_page() {
		include __DIR__ . '/views/dashboard.php';
	}

	/**
	 * Overview widget.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function overview_widget() {
		global $wpdb;
		$stats = apply_filters(
			'eac_dashboard_overview_stats',
			array(
				array(
					'label' => __( 'Receivable', 'wp-ever-accounting' ),
					'value' => eac_format_amount( 100 ),
				),

				array(
					'label' => __( 'Payable', 'wp-ever-accounting' ),
					'value' => eac_format_amount( 100 ),
				),

				array(
					'label' => __( 'Upcoming', 'wp-ever-accounting' ),
					'value' => eac_format_amount( 100 ),
				),
			)
		);

		// prepare the chart data. get all the transactions for the current year where the id is not exist in the transfers table in payment_id and expense_id columns.
		$transactions = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT
				MONTH(t.date) AS month,
				SUM(CASE WHEN t.type = 'payment' AND t.status = 'completed' THEN t.amount / t.exchange_rate ELSE 0 END) AS sales,
				SUM(CASE WHEN t.type = 'expense' AND t.status = 'completed' THEN t.amount / t.exchange_rate ELSE 0 END) AS expenses
		 		FROM {$wpdb->prefix}ea_transactions AS t
		 		LEFT JOIN {$wpdb->prefix}ea_transfers AS it ON t.id = it.payment_id OR t.id = it.expense_id
		 		WHERE YEAR(`date`) = %d
		 		AND it.id IS NULL
		 		GROUP BY MONTH(`date`)",
				date( 'Y' )
			)
		);
		// now prepare the data for the chart we have add the profit and loss data too by subtracting the expenses from the sales.
		$labels = array();
		$sales  = array();
		$expenses = array();
		$profits = array();
		foreach ( $transactions as $transaction ) {
			$labels[] = wp_date( 'F', mktime( 0, 0, 0, $transaction->month, 1 ) );
			$sales[] = $transaction->sales;
			$expenses[] = $transaction->expenses;
			$profits[] = $transaction->sales - $transaction->expenses;
		}

		$chart_data = array(
			'labels'   => $labels,
			'datasets' => array(
				array(
					'label'           => __( 'Sales', 'wp-ever-accounting' ),
					'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
					'borderColor'     => 'rgba(54, 162, 235, 1)',
					'borderWidth'     => 1,
					'data'            => $sales,
				),
				array(
					'label'           => __( 'Expenses', 'wp-ever-accounting' ),
					'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
					'borderColor'     => 'rgba(255, 99, 132, 1)',
					'borderWidth'     => 1,
					'data'            => $expenses,
				),
				array(
					'label'           => __( 'Profit/Loss', 'wp-ever-accounting' ),
					'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
					'borderColor'     => 'rgba(75, 192, 192, 1)',
					'borderWidth'     => 1,
					'data'            => $profits,
				),
			)
		);

		?>
		<div class="eac-card is--widget">
			<div class="eac-card__header">
				<?php esc_html_e( 'Overview', 'wp-ever-accounting' ); ?>
			</div>
			<div class="eac-card__body">
				<canvas id="eac-overview-chart" style="min-height: 300px;"></canvas>
			</div>
		</div>
		<div class="eac-stats stats--3">
			<?php foreach ( $stats as $stat ) : ?>
				<div class="eac-stat">
					<div class="eac-stat__label"><?php echo esc_html( $stat['label'] ); ?></div>
					<div class="eac-stat__value">
						<?php echo esc_html( $stat['value'] ); ?>
						<?php if ( isset( $stat['delta'] ) ) : ?>
							<?php $delta_class = $stat['delta'] > 0 ? 'is--positive' : 'is--negative'; ?>
							<div class="eac-stat__delta <?php echo esc_attr( $delta_class ); ?>">
								<?php echo esc_html( $stat['delta'] ); ?>%
							</div>
						<?php endif; ?>
					</div>
					<?php if ( isset( $stat['meta'] ) ) : ?>
						<div class="eac-stat__meta">
							<span><?php echo wp_kses_post( implode( ' </span><span> ', $stat['meta'] ) ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				var ctx = document.getElementById( 'eac-overview-chart' ).getContext( '2d' );
				var myChart = new Chart( ctx, {
					type: 'line',
					data: <?php echo wp_json_encode( $chart_data ); ?>,
					options: {
						responsive: true,
						maintainAspectRatio: false,
						legend: {
							display: true,
						},
						scales: {
							yAxes: [ {
								ticks: {
									beginAtZero: true,
								},
							} ],
						},
					},
				} );
			} );
		</script>
		<?php
	}

	/**
	 * Overview stats.
	 *
	 * @param array $stats Stats array.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function overview_stats( $stats ) {
		global $wpdb;
		// get total sales and expenses for the current month.
		$result = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT
            	SUM(CASE WHEN type = 'payment' AND status = 'completed' THEN amount / exchange_rate ELSE 0 END) AS sales,
            	SUM(CASE WHEN type = 'expense' AND status = 'completed' THEN amount / exchange_rate ELSE 0 END) AS expenses
         		FROM {$wpdb->prefix}ea_transactions
         		WHERE MONTH(`date`) = %d AND YEAR(`date`) = %d",
				date( 'm' ),
				date( 'Y' )
			)
		);


		$stats[] = array(
			'label' => __( 'Total Sales', 'wp-ever-accounting' ),
			'value' => eac_format_amount( $result->sales ),
			'meta'  => array(
				wp_date( 'F Y' ),
			)
		);

		$stats[] = array(
			'label' => __( 'Total Expenses', 'wp-ever-accounting' ),
			'value' => eac_format_amount( $result->expenses ),
			'meta'  => array(
				wp_date( 'F Y' ),
			)
		);

		$stats[] = array(
			'label' => __( 'Net Income', 'wp-ever-accounting' ),
			'value' => eac_format_amount( $result->sales - $result->expenses ),
			'meta'  => array(
				wp_date( 'F Y' ),
			)
		);

		return $stats;
	}

	/**
	 * Recent payments widget.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function recent_payments() {
		$payments = EAC()->payments->query(
			array(
				'limit'   => 5,
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		);

		?>
		<div class="eac-card is--widget">
			<div class="eac-card__header">
				<?php esc_html_e( 'Recent Payments', 'wp-ever-accounting' ); ?>
				<?php if ( ! empty( $payments ) ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=payments' ) ); ?>"><?php esc_html_e( 'View all', 'wp-ever-accounting' ); ?></a>
				<?php endif; ?>
			</div>

			<table class="eac-table is--fixed">
				<thead>
				<tr>
					<th><?php esc_html_e( 'Payment #', 'wp-ever-accounting' ); ?></th>
					<th><?php esc_html_e( 'Date', 'wp-ever-accounting' ); ?></th>
					<th><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php if ( ! empty( $payments ) ) : ?>
					<?php foreach ( $payments as $payment ) : ?>
						<tr>
							<td><a href="<?php echo esc_url( $payment->get_view_url() ); ?>"><?php echo esc_html( $payment->number ); ?></a></td>
							<td><?php echo esc_html( date_i18n( eac_date_format(), strtotime( $payment->date ) ) ); ?></td>
							<td><?php echo esc_html( $payment->formatted_amount ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="3">
							<p><?php esc_html_e( 'No payments found.', 'wp-ever-accounting' ); ?></p>
						</td>
					</tr>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Recent expenses widget.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function recent_expenses() {
		$expenses = EAC()->expenses->query(
			array(
				'limit'   => 5,
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		);

		?>
		<div class="eac-card is--widget">
			<div class="eac-card__header">
				<?php esc_html_e( 'Recent Expenses', 'wp-ever-accounting' ); ?>
				<?php if ( ! empty( $expenses ) ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-expenses&tab=expenses' ) ); ?>"><?php esc_html_e( 'View all', 'wp-ever-accounting' ); ?></a>
				<?php endif; ?>
			</div>

			<table class="eac-table is--fixed">
				<thead>
				<tr>
					<th><?php esc_html_e( 'Expense #', 'wp-ever-accounting' ); ?></th>
					<th><?php esc_html_e( 'Date', 'wp-ever-accounting' ); ?></th>
					<th><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php if ( ! empty( $expenses ) ) : ?>
					<?php foreach ( $expenses as $expense ) : ?>
						<tr>
							<td><a href="<?php echo esc_url( $expense->get_view_url() ); ?>"><?php echo esc_html( $expense->number ); ?></a></td>
							<td><?php echo esc_html( date_i18n( eac_date_format(), strtotime( $expense->date ) ) ); ?></td>
							<td><?php echo esc_html( $expense->formatted_amount ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="3">
							<p><?php esc_html_e( 'No expenses found.', 'wp-ever-accounting' ); ?></p>
						</td>
					</tr>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Recent invoices widget.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function recent_invoices() {
		$invoices = EAC()->invoices->query(
			array(
				'limit'   => 5,
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		);

		?>
		<div class="eac-card is--widget">
			<div class="eac-card__header">
				<?php esc_html_e( 'Recent Invoices', 'wp-ever-accounting' ); ?>
				<?php if ( ! empty( $invoices ) ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=invoices' ) ); ?>"><?php esc_html_e( 'View all', 'wp-ever-accounting' ); ?></a>
				<?php endif; ?>
			</div>

			<table class="eac-table is--fixed">
				<thead>
				<tr>
					<th><?php esc_html_e( 'Invoice #', 'wp-ever-accounting' ); ?></th>
					<th><?php esc_html_e( 'Date', 'wp-ever-accounting' ); ?></th>
					<th><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php if ( ! empty( $invoices ) ) : ?>
					<?php foreach ( $invoices as $invoice ) : ?>
						<tr>
							<td><a href="<?php echo esc_url( $invoice->get_view_url() ); ?>"><?php echo esc_html( $invoice->number ); ?></a></td>
							<td><?php echo esc_html( date_i18n( eac_date_format(), strtotime( $invoice->issue_date ) ) ); ?></td>
							<td><?php echo esc_html( $invoice->formatted_total ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="3">
							<p><?php esc_html_e( 'No invoices found.', 'wp-ever-accounting' ); ?></p>
						</td>
					</tr>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Top items widget.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function top_items() {
		global $wpdb;
		// we will query documents table where type is invoice and status is paid. then we will get the items from document items table.
		$items = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT di.item_id, SUM(di.subtotal / d.exchange_rate) AS total_sales
				 FROM {$wpdb->prefix}ea_document_items AS di
         	     JOIN {$wpdb->prefix}ea_documents AS d ON di.document_id = d.id
                 WHERE d.type = %s AND d.status = %s
                 GROUP BY di.item_id
                 ORDER BY total_sales DESC
         		LIMIT 5",
				'invoice',
				'paid'
			)
		);
		?>
		<div class="eac-card is--widget">
			<div class="eac-card__header">
				<?php esc_html_e( 'Top Items', 'wp-ever-accounting' ); ?>
			</div>
			<div class="eac-card__body">
				<p><?php esc_html_e( 'Coming soon!', 'wp-ever-accounting' ); ?></p>
			</div>
		</div>
		<?php
	}

	/**
	 * Top customers widget.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function top_customers() {
		global $wpdb;
		// we will query documents table where type is invoice and status is paid. then we will get the items from document items table.
//		$customers = $wpdb->get_results(
//			$wpdb->prepare(
//				"SELECT customer_id, SUM(amount / exchange_rate) AS amount
//				 FROM {$wpdb->prefix}ea_transactions
//				 WHERE type = %s AND status = %s
//				 WHERE d.type = %s AND d.status = %s
//				 GROUP BY d.contact_id
//				 ORDER BY amount DESC
//		 		LIMIT 5",
//				'invoice',
//				'paid'
//			)
//		);

		?>
		<div class="eac-card is--widget">
			<div class="eac-card__header">
				<?php esc_html_e( 'Top Customers', 'wp-ever-accounting' ); ?>
			</div>
			<div class="eac-card__body">
				<p><?php esc_html_e( 'Coming soon!', 'wp-ever-accounting' ); ?></p>
			</div>
		</div>
		<?php
	}

	/**
	 * Top vendors widget.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function top_vendors() {
		global $wpdb;
		// we will query documents table where type is invoice and status is paid. then we will get the items from document items table.
		$vendors = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT contact_id, SUM(amount / exchange_rate) AS amount
				 FROM {$wpdb->prefix}ea_transactions
				 WHERE type = 'expense' AND status = 'completed'
				 GROUP BY contact_id
				 ORDER BY amount DESC
		 		LIMIT %d",
				5
			)
		);

		?>
		<div class="eac-card is--widget">
			<div class="eac-card__header">
				<?php esc_html_e( 'Top Vendors', 'wp-ever-accounting' ); ?>
			</div>
			<div class="eac-card__body">
				<p><?php esc_html_e( 'Coming soon!', 'wp-ever-accounting' ); ?></p>
			</div>
		</div>
		<?php
	}
}
