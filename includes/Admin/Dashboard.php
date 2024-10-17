<?php

namespace EverAccounting\Admin;

use EverAccounting\Utilities\NumberUtil;use EverAccounting\Utilities\ReportsUtil;

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
		$report  = ReportsUtil::get_profits_report( date( 'Y' ) );
		$delta  =  (array_sum( $report['profits'] ) / array_sum( $report['payments'] )) * 100;
		$stats   = apply_filters( 'eac_dashboard_overview_stats', array(
			array(
				'label' => __( 'Income', 'wp-ever-accounting' ),
				'value' => eac_format_amount( array_sum( $report['payments'] ) ),
			),
			array(
				'label' => __( 'Expenses', 'wp-ever-accounting' ),
				'value' => eac_format_amount( array_sum( $report['expenses'] ) ),
			),
			array(
				'label' => __( 'Profit/Loss', 'wp-ever-accounting' ),
				'value' => eac_format_amount( array_sum( $report['profits'] ) ),
				'delta' => number_format( $delta, 2 ),
			),
		) );

		$datasets = array(
			'labels'   => array_keys( $report['payments'] ),
			'datasets' => array(
				array(
					'label'           => __( 'Sales', 'wp-ever-accounting' ),
					'backgroundColor' => 'transparent',
					'borderColor'     => 'rgba(54, 162, 235, 1)',
					'borderWidth'     => 2,
					'data'            => array_values( $report['payments'] ),
				),
				array(
					'label'           => __( 'Expenses', 'wp-ever-accounting' ),
					'backgroundColor' => 'transparent',
					'borderColor'     => 'rgba(255, 99, 132, 1)',
					'borderWidth'     => 2,
					'data'            => array_values( $report['expenses'] ),
				),
				array(
					'label'           => __( 'Profit/Loss', 'wp-ever-accounting' ),
					'backgroundColor' => 'transparent',
					'borderColor'     => 'rgba(75, 192, 192, 1)',
					'borderWidth'     => 2,
					'data'            => array_values( $report['profits'] ),
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
			jQuery(document).ready(function ($) {
				var symbol = "<?php echo esc_html( EAC()->currencies->get_symbol() ); ?>";
				var ctx = document.getElementById('eac-overview-chart').getContext('2d');
				var myChart = new Chart(ctx, {
					type: 'line',
					data: <?php echo wp_json_encode( $datasets ); ?>,
					options: {
						tooltips: {
							displayColors: true,
							YrPadding: 12,
							backgroundColor: "#000000",
							bodyFontColor: "#e5e5e5",
							bodySpacing: 4,
							intersect: 0,
							mode: "nearest",
							position: "nearest",
							titleFontColor: "#ffffff",
							callbacks: {
								label: function (tooltipItem, data) {
									let value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
									let datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
									return datasetLabel + ': ' + value.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + symbol;
								}
							}
						},
						scales: {
							xAxes: [{
								stacked: false,
								gridLines: {
									display: true,
								}
							}],
							yAxes: [{
								stacked: false,
								ticks: {
									beginAtZero: true,
									callback: function (value, index, ticks) {
										return Number(value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + symbol;
									}
								},
								type: 'linear',
								barPercentage: 0.4
							}]
						},
						responsive: true,
						maintainAspectRatio: false,
						legend: {display: false},
					}
				});
			});
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

		$document = $wpdb->get_row(
			"SELECT
				SUM(CASE WHEN type = 'invoice' AND status NOT IN ( 'draft', 'cancelled', 'paid' ) THEN total / exchange_rate ELSE 0 END) AS invoice,
				SUM(CASE WHEN type = 'bill' AND status NOT IN ( 'draft', 'cancelled', 'paid' ) THEN total / exchange_rate ELSE 0 END) AS bill
		 		FROM {$wpdb->prefix}ea_documents"
		);

		$trans = $wpdb->get_row(
			"SELECT
				SUM(CASE WHEN document_id IN ( SELECT document_id FROM {$wpdb->prefix}ea_documents WHERE status NOT IN ( 'draft', 'cancelled', 'paid' ) AND type='invoice' ) AND type='payment' AND status='completed' THEN amount / exchange_rate ELSE 0 END) AS invoice,
				SUM(CASE WHEN document_id IN ( SELECT document_id FROM {$wpdb->prefix}ea_documents WHERE status NOT IN ( 'draft', 'cancelled', 'paid' ) AND type='bill' ) AND type='expense' AND status='completed' THEN amount / exchange_rate ELSE 0 END) AS bill
		 		FROM {$wpdb->prefix}ea_transactions"
		);

		// now find the payable and receivable amount by subtracting the total amount from the total paid.
		$receivable = (float) $document->invoice - (float) $trans->invoice;
		$payable    = (float) $document->bill - (float) $trans->bill;
		$upcoming   = $receivable - $payable;

		$stats[] = array(
			'label' => __( 'Receivable', 'wp-ever-accounting' ),
			'value' => eac_format_amount( $receivable ),
		);

		$stats[] = array(
			'label' => __( 'Payable', 'wp-ever-accounting' ),
			'value' => eac_format_amount( $payable ),
		);

		$stats[] = array(
			'label' => __( 'Upcoming', 'wp-ever-accounting' ),
			'value' => eac_format_amount( $upcoming ),
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
				'orderby' => 'paid_at',
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
				'orderby' => 'paid_at',
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
		$payments = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT contact_id, SUM(amount / exchange_rate) AS amount
				 FROM {$wpdb->prefix}ea_transactions
				 WHERE type = 'payment' AND status = 'completed'
				 GROUP BY contact_id
				 ORDER BY amount DESC LIMIT %d",
				5
			)
		);

		$customers = array();
		foreach ( $payments as $payment ) {
			$customer = EAC()->customers->get( $payment->contact_id );
			if ( $customer ) {
				$customer->amount = $payment->amount;
				$customers[]      = $customer;
			}
		}
		?>
		<div class="eac-card is--widget">
			<div class="eac-card__header">
				<?php esc_html_e( 'Top Customers', 'wp-ever-accounting' ); ?>
			</div>
			<table class="eac-table is--fixed">
				<thead>
				<tr>
					<th><?php esc_html_e( 'Customer', 'wp-ever-accounting' ); ?></th>
					<th><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php if ( ! empty( $customers ) ) : ?>
					<?php foreach ( $customers as $customer ) : ?>
						<tr>
							<td>
								<a href="<?php echo esc_url( $customer->get_view_url() ); ?>">
									<?php echo esc_html( $customer->formatted_name ); ?>
								</a>
							</td>
							<td><?php echo esc_html( eac_format_amount( $customer->amount ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="2">
							<p><?php esc_html_e( 'No customers found.', 'wp-ever-accounting' ); ?></p>
						</td>
					</tr>
				<?php endif; ?>
				</tbody>
			</table>
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
		// we will query documents table where type is bill and status is paid. then we will get the items from document items table.
		$expenses = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT contact_id, SUM(amount / exchange_rate) AS amount
				 FROM {$wpdb->prefix}ea_transactions
				 WHERE type = 'expense' AND status = 'completed'
				 GROUP BY contact_id
				 ORDER BY amount DESC LIMIT %d",
				5
			)
		);

		$vendors = array();
		foreach ( $expenses as $expense ) {
			$vendor = EAC()->vendors->get( $expense->contact_id );
			if ( $vendor ) {
				$vendor->amount = $expense->amount;
				$vendors[]      = $vendor;
			}
		}
		?>
		<div class="eac-card is--widget">
			<div class="eac-card__header">
				<?php esc_html_e( 'Top Vendors', 'wp-ever-accounting' ); ?>
			</div>
			<table class="eac-table is--fixed">
				<thead>
				<tr>
					<th><?php esc_html_e( 'Vendor', 'wp-ever-accounting' ); ?></th>
					<th><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php if ( ! empty( $vendors ) ) : ?>
					<?php foreach ( $vendors as $vendor ) : ?>
						<tr>
							<td>
								<a href="<?php echo esc_url( $vendor->get_view_url() ); ?>">
									<?php echo esc_html( $vendor->formatted_name ); ?>
								</a>
							</td>
							<td><?php echo esc_html( eac_format_amount( $vendor->amount ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="2">
							<p><?php esc_html_e( 'No vendors found.', 'wp-ever-accounting' ); ?></p>
						</td>
					</tr>
				<?php endif; ?>
				</tbody>
			</table>

		</div>

		<?php
	}
}
