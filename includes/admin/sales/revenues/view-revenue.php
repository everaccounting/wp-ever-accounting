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
		<div class="ea-col-9">
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
								<table>
									<tr>
										<th colspan="ea-revenue__props-label">Voucher Number</th>
										<td colspan="ea-revenue__props-content">1</td>
									</tr>
									<tr>
										<th colspan="ea-revenue__props-label">Payment Method</th>
										<td colspan="ea-revenue__props-content">Cash</td>
									</tr>
									<tr>
										<th colspan="ea-revenue__props-label">Payment Date</th>
										<td colspan="ea-revenue__props-content">Dec 17, 2020</td>
									</tr>
									<tr>
										<th colspan="ea-revenue__props-label">Bank Account</th>
										<td colspan="ea-revenue__props-content">Test</td>
									</tr>
									<tr>
										<th colspan="ea-revenue__props-label">Category</th>
										<td colspan="ea-revenue__props-content">Test</td>
									</tr>
								</table>
							</div>
						</div>
						<!-- /.ea-revenue__columns -->

						<table class="ea-revenue__items">
							<thead>
							<tr>
								<th class="text-left">Sl</th>
								<th class="text-left">Description</th>
								<th class="text-right">Amount</th>
							</tr>
							</thead>

							<tbody>
							<tr>
								<td class="text-left">1</td>
								<td class="text-left">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Minima, odio!</td>
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
		<div class="ea-col-3">
			<div class="ea-card">
				<div class="ea-card__header">
					<h3 class="ea-card__title">
						Action
					</h3>
				</div>
				<div class="ea-card__inside">
					<select name="" id="">
						<option value="">Select</option>
					</select>
					<button class="button-primary">Submit</button>
				</div>
			</div>

			<div class="ea-card">
				<div class="ea-card__header">
					<h3 class="ea-card__title">
						Revenue Notes
					</h3>
				</div>
				<div class="ea-card__inside padding-zero">
					<ul class="ea-revenue-notes">
						<li class="ea-revenue-notes__item customer-note">
							<div class="ea-revenue-notes__item-content">
								Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi, maiores.
							</div>
							<div class="ea-revenue-notes__item-meta">
								<abbr class="exact-date" title="20-12-13 02:55:33">added on December 13, 2020 at 2:55 am</abbr>
								by admin
								<a href="#" class="delete_note" role="button">Delete note</a>
							</div>
						</li>
						<li class="ea-revenue-notes__item customer-note">
							<div class="ea-revenue-notes__item-content">
								Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi, maiores.
							</div>
							<div class="ea-revenue-notes__item-meta">
								<abbr class="exact-date" title="20-12-13 02:55:33">added on December 13, 2020 at 2:55 am</abbr>
								by admin
								<a href="#" class="delete_note" role="button">Delete note</a>
							</div>
						</li>
					</ul>

					<div class="ea-revenue-notes__add">
						<p>
							<label for="revenue_note">Add note</label>
							<textarea type="text" name="revenue_note" id="revenue_note" class="input-text" cols="20" rows="5" autocomplete="off" spellcheck="false"></textarea>
						</p>

						<p>
							<label for="revenue_note_type" class="screen-reader-text">Note type</label>
							<select name="revenue_note_type" id="revenue_note_type">
								<option value="">Private note</option>
								<option value="customer">Note to customer</option>
							</select>
							<button type="button" class="add_revenue_note button">Add</button>
						</p>

					</div>

				</div>
			</div>
		</div>
		<!-- /.ea-col-3 -->
	</div>
	<!-- /.ea-row -->
</div>

