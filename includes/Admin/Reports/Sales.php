<?php

namespace EverAccounting\Admin\Reports;

use EverAccounting\Utilities\ReportsUtil;

defined( 'ABSPATH' ) || exit;

/**
 * Class Sales
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\Reports
 */
class Sales {

	/**
	 * Render the sales report.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function render() {
		global $wpdb;
		wp_verify_nonce( '_wpnonce' );
		$year        = ! empty( $_GET['year'] ) ? absint( $_GET['year'] ) : 0;
		$account_id  = ! empty( $_GET['account_id'] ) ? absint( $_GET['account_id'] ) : 0;
		$customer_id = ! empty( $_GET['customer_id'] ) ? absint( $_GET['customer_id'] ) : 0;
		$data        = ReportsUtil::get_payments_report( $year );
		$chart       = array(
			'type'     => 'line',
			'labels'   => array_keys( $data['months'] ),
			'datasets' => array(
				array(
					'label'           => __( 'Payments', 'wp-ever-accounting' ),
					'backgroundColor' => '#3644ff',
					'borderColor'     => '#3644ff',
					'fill'            => false,
					'data'            => array_values( $data['months'] ),
				),
			),
		);
		$customer    = EAC()->customers->get( $customer_id );
		$account     = EAC()->accounts->get( $account_id );
		$years       = $wpdb->get_col( "SELECT DISTINCT YEAR( payment_date ) FROM {$wpdb->prefix}ea_transactions WHERE payment_date IS NOT NULL ORDER BY payment_date DESC" );
		?>
		<div class="eac-section-header">
			<h3>
				<?php echo esc_html__( 'Sales Report', 'wp-ever-accounting' ); ?>
			</h3>
			<form class="ea-report-filters" method="get" action="">
				<label class="screen-reader-text" for="account_id">
					<?php echo esc_html__( 'Select Account', 'wp-ever-accounting' ); ?>
				</label>
				<select id="account_id" name="account_id" class="eac_select2" data-action="eac_json_search" data-type="account" data-placeholder="<?php echo esc_attr__( 'Select Account', 'wp-ever-accounting' ); ?>">
					<option value=""><?php echo esc_html__( 'All Accounts', 'wp-ever-accounting' ); ?></option>
					<?php if ( ! empty( $account ) ) : ?>
						<option value="<?php echo esc_attr( $account->id ); ?>" selected="selected">
							<?php echo esc_html( $account->name ); ?>
						</option>
					<?php endif; ?>
				</select>
				<label class="screen-reader-text" for="customer_id">
					<?php echo esc_html__( 'Select Customer', 'wp-ever-accounting' ); ?>
				</label>
				<select id="customer_id" name="customer_id" class="eac_select2" data-action="eac_json_search" data-type="customer" data-placeholder="<?php echo esc_attr__( 'Select Customer', 'wp-ever-accounting' ); ?>">
					<option value=""><?php echo esc_html__( 'All Customers', 'wp-ever-accounting' ); ?></option>
					<?php if ( ! empty( $customer ) ) : ?>
						<option value="<?php echo esc_attr( $customer->id ); ?>" selected="selected">
							<?php echo esc_html( $customer->name ); ?>
						</option>
					<?php endif; ?>
				</select>

				<label class="screen-reader-text" for="year">
					<?php echo esc_html__( 'Select Year', 'wp-ever-accounting' ); ?>
				</label>

				<select id="year" name="year" class="eac_select2" data-action="eac_json_search" data-type="year" data-placeholder="<?php echo esc_attr__( 'Select Year', 'wp-ever-accounting' ); ?>">
					<option value=""><?php echo esc_html__( 'All Years', 'wp-ever-accounting' ); ?></option>
					<?php foreach ( $years as $year ) : ?>
						<option value="<?php echo esc_attr( $year ); ?>" <?php selected( $year, $year ); ?>>
							<?php echo esc_html( $year ); ?>
						</option>
					<?php endforeach; ?>
				</select>

				<button type="submit" class="button">
					<?php echo esc_html__( 'Submit', 'wp-ever-accounting' ); ?>
				</button>
				<input hidden="hidden" name="page" value="eac-reports"/>
				<input hidden="hidden" name="tab" value="payments"/>
			</form>
		</div>

		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Chart', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="eac-card__body">
				<div class="eac-chart">
					<canvas class="eac-chart" id="eac-sales-chart" style="height: 300px;margin-bottom: 20px;" data-datasets="<?php echo esc_attr( wp_json_encode( $chart ) ); ?>" data-currency="<?php echo esc_attr( EAC()->currencies->get_symbol( eac_base_currency() ) ); ?>"></canvas>
				</div>
			</div>
		</div>

		<div class="eac-stats stats--3">
			<div class="eac-stat">
				<div class="eac-stat__label"><?php esc_html_e( 'Total Sale', 'wp-ever-accounting' ); ?></div>
				<div class="eac-stat__value"><?php echo esc_html( eac_format_amount( $data['total_amount'] ) ); ?></div>
			</div>
			<div class="eac-stat">
				<div class="eac-stat__label"><?php esc_html_e( 'Monthly Avg.', 'wp-ever-accounting' ); ?></div>
				<div class="eac-stat__value"><?php echo esc_html( eac_format_amount( $data['month_avg'] ) ); ?></div>
			</div>
			<div class="eac-stat">
				<div class="eac-stat__label"><?php esc_html_e( 'Daily Avg.', 'wp-ever-accounting' ); ?></div>
				<div class="eac-stat__value"><?php echo esc_html( eac_format_amount( $data['daily_avg'] ) ); ?></div>
			</div>
		</div>


		<div class="eac-card">
			<div class="eac-card__header">
				<h3 class="eac-card__title"><?php esc_html_e( 'Sales by Months', 'wp-ever-accounting' ); ?></h3>
			</div>
			<div class="tw-overflow-x-auto">
				<table class="eac-table has--border">
					<thead>
					<tr>
						<th><?php esc_html_e( 'Month', 'wp-ever-accounting' ); ?></th>
						<?php foreach ( array_keys( $data['months'] ) as $label ) : ?>
							<th><?php echo esc_html( $label ); ?></th>
						<?php endforeach; ?>
					</tr>
					</thead>
					<tbody>
					<?php if ( ! empty( $data['categories'] ) ) : ?>
						<?php foreach ( $data['categories'] as $category_id => $datum ) : ?>
							<tr>
								<td>
									<?php
									$term      = EAC()->categories->get( $category_id );
									$term_name = $term && $term->name ? $term->name : '&mdash;';
									echo esc_html( $term_name );
									?>
								</td>
								<?php foreach ( $datum as $value ) : ?>
									<td><?php echo esc_html( eac_format_amount( $value ) ); ?></td>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="<?php echo count( $data['months'] ) + 1; ?>">
								<p>
									<?php esc_html_e( 'No data found', 'wp-ever-accounting' ); ?>
								</p>
							</td>
						</tr>
					<?php endif; ?>
					</tbody>
					<tfoot>
					<tr>
						<th><?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?></th>
						<?php foreach ( $data['months'] as $value ) : ?>
							<th><?php echo esc_html( eac_format_amount( $value ) ); ?></th>
						<?php endforeach; ?>
					</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<?php
	}
}
