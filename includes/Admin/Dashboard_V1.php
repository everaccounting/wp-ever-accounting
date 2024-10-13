<?php

namespace EverAccounting\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Class Dashboard
 *
 * @since 1.0.0
 * @package EverAccounting\Admin
 */
class Dashboard_V1 {

	/**
	 * Dashboard constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'eac_dashboard_page_tabs', array( __CLASS__, 'register_tabs' ) );
		add_action( 'eac_dashboard_page_overview_content', array( __CLASS__, 'page_content' ) );
	}

	/**
	 * Register tab.
	 *
	 * @param array $tabs Tabs.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function register_tabs( $tabs ) {
		$tabs['overview'] = __( 'Overview', 'wp-ever-accounting' );

		return $tabs;
	}

	/**
	 * Page content.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function page_content() {}


	/**
	 * Render dashboard.
	 *
	 * @since 1.0.0
	 */
	public static function render() {
		global $wpdb;
		// find the sales,expense and profit for the current month.
		$stats = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT
    			SUM(CASE WHEN type = 'payment' THEN amount/exchange_rate ELSE 0 END) AS payment,
    			SUM(CASE WHEN type = 'expense' THEN amount/exchange_rate ELSE 0 END) AS expense,
    			SUM(CASE WHEN type = 'payment' THEN amount/exchange_rate ELSE 0 END) -
    			SUM(CASE WHEN type = 'expense' THEN amount/exchange_rate ELSE 0 END) AS profit
				FROM {$wpdb->prefix}ea_transactions WHERE status = 'completed' AND MONTH(date) = %d AND YEAR(date) = %d",
				wp_date( 'm' ),
				wp_date( 'Y' )
			)
		);
		?>
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Dashboard', 'wp-ever-accounting' ); ?></h1>
		<div class="eac-card">
			<div class="eac-card__header">
				<?php esc_html_e( 'Overview', 'wp-ever-accounting' ); ?>
			</div>

			<div class="eac-card__body">
				<p><?php esc_html_e( 'Welcome to Ever Accounting!', 'wp-ever-accounting' ); ?></p>
			</div>
		</div>
		<div class="eac-stats stats--3">
			<div class="eac-stat">
				<div class="eac-stat__label"><?php esc_html_e( 'Sales', 'wp-ever-accounting' ); ?></div>
				<div class="eac-stat__value"><?php echo esc_html( eac_format_amount( $stats->payment ) ); ?></div>
				<div class="eac-stat__meta">
					<span><?php echo esc_html( wp_date( 'F Y' ) ); ?></span>
				</div>
			</div>
			<div class="eac-stat">
				<div class="eac-stat__label"><?php esc_html_e( 'Expenses', 'wp-ever-accounting' ); ?></div>
				<div class="eac-stat__value"><?php echo esc_html( eac_format_amount( $stats->expense ) ); ?></div>
				<div class="eac-stat__meta">
					<span><?php echo esc_html( wp_date( 'F Y' ) ); ?></span>
				</div>
			</div>
			<div class="eac-stat">
				<div class="eac-stat__label"><?php esc_html_e( 'Profits', 'wp-ever-accounting' ); ?></div>
				<div class="eac-stat__value"><?php echo esc_html( eac_format_amount( $stats->profit ) ); ?></div>
				<div class="eac-stat__meta">
					<span><?php echo esc_html( wp_date( 'F Y' ) ); ?></span>
				</div>
			</div>
		</div>
		<div class="eac-grid cols-2">
			<div class="eac-card eac-card--widget">
				<div class="eac-card__header">
					<?php esc_html_e( 'Payment Categories', 'wp-ever-accounting' ); ?>
				</div>

				<div class="eac-card__body">
					<p><?php esc_html_e( 'Coming soon!', 'wp-ever-accounting' ); ?></p>
				</div>
			</div>

			<div class="eac-card eac-card--widget">
				<div class="eac-card__header">
					<?php esc_html_e( 'Expense Categories', 'wp-ever-accounting' ); ?>
				</div>

				<div class="eac-card__body">
					<p><?php esc_html_e( 'Coming soon!', 'wp-ever-accounting' ); ?></p>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render dashboard.
	 *
	 * @since 1.0.0
	 */
	public static function render_v1() {
		$payments = EAC()->payments->query(
			array(
				'limit'   => 5,
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		);
		$expenses = EAC()->expenses->query(
			array(
				'limit'   => 5,
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		);
		$accounts = EAC()->accounts->query(
			array(
				'limit'   => 5,
				'orderby' => 'balance',
				'order'   => 'DESC',
			)
		);
		?>
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Dashboard', 'wp-ever-accounting' ); ?></h1>
		<div class="eac-card">
			<div class="eac-card__header">
				<?php esc_html_e( 'Overview', 'wp-ever-accounting' ); ?>
			</div>

			<div class="eac-card__body">
				<p><?php esc_html_e( 'Welcome to Ever Accounting!', 'wp-ever-accounting' ); ?></p>
			</div>
		</div>
		<div class="eac-stats stats--3">
			<div class="eac-stat">
				<div class="eac-stat__label"><?php esc_html_e( 'Sales', 'wp-ever-accounting' ); ?></div>
				<div class="eac-stat__value">24.00
				</div>
				<div class="eac-stat__meta">
					<span><?php echo esc_html( wp_date( 'F Y' ) ); ?></span>
				</div>
			</div>
			<div class="eac-stat">
				<div class="eac-stat__label"><?php esc_html_e( 'Expenses', 'wp-ever-accounting' ); ?></div>
				<div class="eac-stat__value">24.00</div>
				<div class="eac-stat__meta">
					<span><?php echo esc_html( wp_date( 'F Y' ) ); ?></span>
				</div>
			</div>
			<div class="eac-stat">
				<div class="eac-stat__label"><?php esc_html_e( 'Profits', 'wp-ever-accounting' ); ?></div>
				<div class="eac-stat__value">24.00</div>
				<div class="eac-stat__meta">
					<span><?php echo esc_html( wp_date( 'F Y' ) ); ?></span>
				</div>
			</div>
		</div>
		<div class="eac-grid cols-2">
			<div class="eac-card eac-card--widget">
				<div class="eac-card__header">
					<?php esc_html_e( 'Payment Categories', 'wp-ever-accounting' ); ?>
				</div>

				<div class="eac-card__body">
					<p><?php esc_html_e( 'Coming soon!', 'wp-ever-accounting' ); ?></p>
				</div>
			</div>

			<div class="eac-card eac-card--widget">
				<div class="eac-card__header">
					<?php esc_html_e( 'Expense Categories', 'wp-ever-accounting' ); ?>
				</div>

				<div class="eac-card__body">
					<p><?php esc_html_e( 'Coming soon!', 'wp-ever-accounting' ); ?></p>
				</div>
			</div>
		</div>
		<div class="eac-grid cols-3">

			<div class="eac-card eac-card--widget">
				<div class="eac-card__header">
					<?php esc_html_e( 'Recent Payments', 'wp-ever-accounting' ); ?>
					<?php if ( ! empty( $payments ) ) : ?>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=payments' ) ); ?>"><?php esc_html_e( 'View all', 'wp-ever-accounting' ); ?></a>
					<?php endif; ?>
				</div>

				<table class="eac-table">
					<thead>
					<tr>
						<th><?php esc_html_e( 'Date', 'wp-ever-accounting' ); ?></th>
						<th><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?></th>
						<th><?php esc_html_e( 'Category', 'wp-ever-accounting' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php if ( ! empty( $payments ) ) : ?>
						<?php foreach ( $payments as $payment ) : ?>
							<tr>
								<td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $payment->date ) ) ); ?></td>
								<td><?php echo esc_html( $payment->formatted_amount ); ?></td>
								<td>
									<?php
									echo esc_html( $payment->category && $payment->category->name ? $payment->category->name : '&mdash;' );
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="3"><?php esc_html_e( 'No payments found.', 'wp-ever-accounting' ); ?></td>
						</tr>
					<?php endif; ?>
					</tbody>
				</table>
			</div>

			<div class="eac-card eac-card--widget">
				<div class="eac-card__header">
					<?php esc_html_e( 'Recent Expenses', 'wp-ever-accounting' ); ?>
					<?php if ( ! empty( $expenses ) ) : ?>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-purchases&tab=expenses' ) ); ?>"><?php esc_html_e( 'View all', 'wp-ever-accounting' ); ?></a>
					<?php endif; ?>
				</div>

				<table class="eac-table">
					<thead>
					<tr>
						<th><?php esc_html_e( 'Date', 'wp-ever-accounting' ); ?></th>
						<th><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?></th>
						<th><?php esc_html_e( 'Category', 'wp-ever-accounting' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php if ( ! empty( $expenses ) ) : ?>
						<?php foreach ( $expenses as $expense ) : ?>
							<tr>
								<td><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $expense->date ) ) ); ?></td>
								<td><?php echo esc_html( $expense->formatted_amount ); ?></td>
								<td>
									<?php
									echo esc_html( $expense->category && $expense->category->name ? $expense->category->name : '&mdash;' );
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="3"><?php esc_html_e( 'No expenses found.', 'wp-ever-accounting' ); ?></td>
						</tr>
					<?php endif; ?>
					</tbody>
				</table>
			</div>

			<div class="eac-card eac-card--widget">
				<div class="eac-card__header">
					<?php esc_html_e( 'Account Balances', 'wp-ever-accounting' ); ?>
					<?php if ( ! empty( $accounts ) ) : ?>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-banking&tab=accounts' ) ); ?>"><?php esc_html_e( 'View all', 'wp-ever-accounting' ); ?></a>
					<?php endif; ?>
				</div>

				<table class="eac-table">
					<thead>
					<tr>
						<th><?php esc_html_e( 'Account', 'wp-ever-accounting' ); ?></th>
						<th><?php esc_html_e( 'Balance', 'wp-ever-accounting' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php if ( ! empty( $accounts ) ) : ?>
						<?php foreach ( $accounts as $account ) : ?>
							<tr>
								<td><?php echo esc_html( $account->name ); ?></td>
								<td><?php echo esc_html( $account->formatted_balance ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="2"><?php esc_html_e( 'No accounts found.', 'wp-ever-accounting' ); ?></td>
						</tr>
					<?php endif; ?>
				</table>
			</div>
			<!--top products-->
			<div class="eac-card eac-card--widget">
				<div class="eac-card__header">
					<?php esc_html_e( 'Top Items', 'wp-ever-accounting' ); ?>
				</div>

				<div class="eac-card__body">
					<p><?php esc_html_e( 'Coming soon!', 'wp-ever-accounting' ); ?></p>
				</div>
			</div>

			<!--top customers-->
			<div class="eac-card eac-card--widget">
				<div class="eac-card__header">
					<?php esc_html_e( 'Top Customers', 'wp-ever-accounting' ); ?>
				</div>

				<div class="eac-card__body">
					<p><?php esc_html_e( 'Coming soon!', 'wp-ever-accounting' ); ?></p>
				</div>
			</div>

			<!--top vendors-->
			<div class="eac-card eac-card--widget">
				<div class="eac-card__header">
					<?php esc_html_e( 'Top Vendors', 'wp-ever-accounting' ); ?>
				</div>

				<div class="eac-card__body">
					<p><?php esc_html_e( 'Coming soon!', 'wp-ever-accounting' ); ?></p>
				</div>
			</div>
		</div>
		<?php
	}
}
