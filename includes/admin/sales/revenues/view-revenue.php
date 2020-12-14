<?php
/**
 * Admin Revenue View Page.
 *
 * @since       1.1.0
 * @subpackage  Admin/Sales/Revenues
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();

$revenue_id = isset( $_REQUEST['revenue_id'] ) ? absint( $_REQUEST['revenue_id'] ) : null;
try {
	$revenue = new \EverAccounting\Models\Income( $revenue_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
$back_url = remove_query_arg( array( 'action', 'revenue_id' ) );
?>

<div class="ea-revenue-page">
	<div class="ea-row">
		<div class="ea-col-12">
			<div class="ea-card">
				<div class="ea-card__header">
					<h3 class="ea-card__title"><?php _e( 'Revenue Voucher', 'wp-ever-accounting' ); ?></h3>
					<div>
						<button class="button-secondary button">Edit</button>
						<a href="<?php echo $back_url; ?>" class="button button-secondary"><?php _e( 'Back', 'wp-ever-accounting' ); ?></a>
					</div>
				</div>
				<!-- /.ea-card__header -->
				<div class="ea-card__inside">
					<div class="ea-revenue">
						<div class="ea-revenue__header">
							<div class="ea-revenue__logo">
								<img src="https://wpeveraccounting.com/wp-content/uploads/2020/09/Logo-same-size-plugin-ever.svg" alt="WP Ever Accounting">
							</div>

							<div class="ea-revenue__title"><?php _e( 'Revenue Voucher', 'wp-ever-accounting' ); ?></div>
						</div>

						<div class="ea-revenue__columns">
							<div>
								<table class="ea-revenue__party">
									<tr>
										<th>
											<div class="ea-revenue__subtitle">From</div>
										</th>
										<td>
											<div class="ea-revenue__company">BYTEEVER LIMITED</div>
											<div class="ea-revenue__address">
												<span class="ea-revenue__address-line">4485 Pennsylvania Avenue, Lyndhurst</span>
												<span class="ea-revenue__address-line">NJ, New Jersey</span>
												<span class="ea-revenue__address-line">United Stated of America</span>
											</div>
										</td>
									</tr>
								</table>

								<table class="ea-revenue__party">
									<tr>
										<th>
											<div class="ea-revenue__subtitle">To</div>
										</th>
										<td>
											<div class="ea-revenue__company">BYTEEVER LIMITED</div>
											<div class="ea-revenue__address">
												<span class="ea-revenue__address-line">4485 Pennsylvania Avenue, Lyndhurst</span>
												<span class="ea-revenue__address-line">NJ, New Jersey</span>
												<span class="ea-revenue__address-line">United Stated of America</span>
											</div>
										</td>
									</tr>
								</table>

							</div>

							<div class="ea-revenue__props">
								<table class="ea-revenue__party">
									<tbody>
									<tr>

										<th>
											<div class="ea-revenue__subtitle">Voucher Number</div>
										</th>
										<td><div class="ea-revenue__value">1</td>
									</tr>
									<tr>

										<th>
											<div class="ea-revenue__subtitle">Payment Method</div>
										</th>
										<td><div class="ea-revenue__value">Cash</td>
									</tr>
									<tr>

										<th>
											<div class="ea-revenue__subtitle">Payment Date</div>
										</th>
										<td><div class="ea-revenue__value">Dec 17, 2020</td>
									</tr>
									<tr>

										<th>
											<div class="ea-revenue__subtitle">Bank Account</div>
										</th>
										<td><div class="ea-revenue__value">Test</td>
									</tr>

									<tr>

										<th>
											<div class="ea-revenue__subtitle">Category</div>
										</th>
										<td><div class="ea-revenue__value">Test</td>
									</tr>
									</tbody>
								</table>
							</div>
						</div>
						<!-- /.ea-revenue__columns -->



						<table class="ea-revenue__items">
							<thead>
							<tr>
								<th class="text-left">Sl</th>
								<th class="text-center">Description</th>
								<th class="text-right">Amount</th>
							</tr>
							</thead>

							<tbody>
							<tr>
								<td class="text-left">1</td>
								<td class="text-center description">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Minima, odio!</td>
								<td class="text-right">$100</td>
							</tr>
							</tbody>

							<tfoot>
							<tr>
								<td colspan="2">
									<p class="ea-revenue__text">
										<strong>In Word: </strong>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium aliquid autem cum cumque doloribus iste nisi nulla numquam quisquam tempore!
									</p>
								</td>

								<td colspan="2" class="padding-zero">
									<table class="ea-revenue__totals">
										<tbody>
										<tr>
											<th>Total</th>
											<td>$100.00</td>
										</tr>
										</tbody>
									</table>
								</td>
							</tr>

							</tfoot>
						</table>
						<!-- /.ea-revenue__items -->
						<p class="ea-revenue__reference">
							<strong>Reference: </strong>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Minima, odio!
						</p>
					</div>
					<!-- /.ea-revenue -->
				</div>
				<!-- /.ea-card__inside -->
			</div>
			<!-- /.ea-card -->
		</div>
		<!-- /.ea-col-9 -->
	</div>
	<!-- /.ea-row -->
</div>

