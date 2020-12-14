<?php
/**
 * Admin Invoice View Page.
 *
 * @since       1.1.0
 * @subpackage  Admin/Sales/Invoices
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

$invoice_id = isset( $_REQUEST['invoice_id'] ) ? absint( $_REQUEST['invoice_id'] ) : null;
try {
	$invoice = new \EverAccounting\Models\Invoice( $invoice_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
?>
<div class="ea-invoice-page">
	<div class="ea-row">
		<div class="ea-col-9">
			<div class="ea-card">
				<div class="ea-card__header">
					<h3 class="ea-card__title">
						Invoice
					</h3>
					<div>
						<button class="button-secondary button">Edit</button>
						<button class="button-secondary button">Edit</button>
					</div>
				</div>
				<div class="ea-card__inside">

					<div class="ea-invoice">
						<div class="ea-invoice__watermark"><p>Paid</p></div>
						<div class="ea-invoice__header">
							<div class="ea-invoice__logo">
								<img src="https://wpeveraccounting.com/wp-content/uploads/2020/09/Logo-same-size-plugin-ever.svg" alt="WP Ever Accounting">
							</div>

							<div class="ea-invoice__title">Invoice</div>
						</div>

						<div class="ea-invoice__columns">
							<div>
								<table class="ea-invoice__party">
									<tr>
										<th>
											<div class="ea-invoice__subtitle">From</div>
										</th>
										<td>
											<div class="ea-invoice__company">BYTEEVER LIMITED</div>
											<div class="ea-invoice__address">
												<span class="ea-invoice__address-line">4485 Pennsylvania Avenue, Lyndhurst</span>
												<span class="ea-invoice__address-line">NJ, New Jersey</span>
												<span class="ea-invoice__address-line">United Stated of America</span>
											</div>
										</td>
									</tr>
								</table>

								<table class="ea-invoice__party">
									<tr>
										<th>
											<div class="ea-invoice__subtitle">To</div>
										</th>
										<td>
											<div class="ea-invoice__company">BYTEEVER LIMITED</div>
											<div class="ea-invoice__address">
												<span class="ea-invoice__address-line">4485 Pennsylvania Avenue, Lyndhurst</span>
												<span class="ea-invoice__address-line">NJ, New Jersey</span>
												<span class="ea-invoice__address-line">United Stated of America</span>
											</div>
										</td>
									</tr>
								</table>

							</div>


							<div class="ea-invoice__props">
								<table>
									<tr>
										<th class="ea-invoice__props-label">Invoice Number</th>
										<td class="ea-invoice__props-content">INV-00003</td>
									</tr>
									<tr>
										<th class="ea-invoice__props-label">Order Number</th>
										<td class="ea-invoice__props-content">ORD-00003</td>
									</tr>
									<tr>
										<th class="ea-invoice__props-label">Invoice Date</th>
										<td class="ea-invoice__props-content">Dec 17, 2020</td>
									</tr>
									<tr>
										<th class="ea-invoice__props-label">Payment Date</th>
										<td class="ea-invoice__props-content">Dec 17, 2020</td>
									</tr>
									<tr>
										<th class="ea-invoice__props-label">Invoice Due Date</th>
										<td class="ea-invoice__props-content">Dec 17, 2020</td>
									</tr>
									<tr>
										<th class="ea-invoice__props-label">Invoice Status</th>
										<td class="ea-invoice__props-content">Paid</td>
									</tr>
								</table>
							</div>
						</div>

						<table class="ea-invoice__items">
							<thead>
							<tr>
								<th class="text-left">Item</th>
								<th class="text-right">Quantity</th>
								<th class="text-right">Rate</th>
								<th class="text-right">Amount</th>
							</tr>
							</thead>

							<tbody>
							<tr>
								<td class="text-left">
									435 Ridenour Street
								</td>
								<td class="text-right">
									9
								</td>
								<td class="text-right">
									$100.00
								</td>
								<td class="text-right">
									9000.00
								</td>
							</tr>
							<tr>
								<td class="text-left">
									435 Ridenour Street
								</td>
								<td class="text-right">
									9
								</td>
								<td class="text-right">
									$100.00
								</td>
								<td class="text-right">
									9000.00
								</td>
							</tr>
							<tr>
								<td class="text-left">
									435 Ridenour Street
								</td>
								<td class="text-right">
									9
								</td>
								<td class="text-right">
									$100.00
								</td>
								<td class="text-right">
									9000.00
								</td>
							</tr>
							<tr>
								<td class="text-left">
									435 Ridenour Street
								</td>
								<td class="text-right">
									9
								</td>
								<td class="text-right">
									$100.00
								</td>
								<td class="text-right">
									9000.00
								</td>
							</tr>
							</tbody>

							<tfoot>
								<tr>
									<td colspan="2">
										<p class="ea-invoice__note">
											<strong>Note: </strong>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Minima, odio!
										</p>
									</td>

									<td colspan="2" class="padding-zero">
										<table class="ea-invoice__totals">
											<tbody>
											<tr>
												<th>Subtotal</th>
												<td>$9,900.00</td>
											</tr>
											<tr>
												<th>Tax</th>
												<td>$9,900.00</td>
											</tr>
											<tr>
												<th>Discount</th>
												<td>$9,900.00</td>
											</tr>
											<tr>
												<th>Total</th>
												<td>$9,900.00</td>
											</tr>
											<tr>
												<th>Paid</th>
												<td>$9,900.00</td>
											</tr>
											<tr>
												<th>Due</th>
												<td>$9,900.00</td>
											</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tfoot>

						</table>

						<p class="ea-invoice__terms">
							<strong>Terms: </strong>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium aliquid autem cum cumque doloribus iste nisi nulla numquam quisquam tempore!
						</p>

					</div>

				</div>
			</div>


		</div>

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
						Invoice Notes
					</h3>
				</div>
				<div class="ea-card__inside padding-zero">
					<ul class="ea-invoice-notes">
						<li class="ea-invoice-notes__item customer-note">
							<div class="ea-invoice-notes__item-content">
								Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi, maiores.
							</div>
							<div class="ea-invoice-notes__item-meta">
								<abbr class="exact-date" title="20-12-13 02:55:33">added on December 13, 2020 at 2:55 am</abbr>
								by admin
								<a href="#" class="delete_note" role="button">Delete note</a>
							</div>
						</li>
						<li class="ea-invoice-notes__item customer-note">
							<div class="ea-invoice-notes__item-content">
								Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi, maiores.
							</div>
							<div class="ea-invoice-notes__item-meta">
								<abbr class="exact-date" title="20-12-13 02:55:33">added on December 13, 2020 at 2:55 am</abbr>
								by admin
								<a href="#" class="delete_note" role="button">Delete note</a>
							</div>
						</li>
					</ul>

					<div class="ea-invoice-notes__add">
						<p>
							<label for="invoice_note">Add note</label>
							<textarea type="text" name="invoice_note" id="invoice_note" class="input-text" cols="20" rows="5" autocomplete="off" spellcheck="false"></textarea>
						</p>

						<p>
							<label for="invoice_note_type" class="screen-reader-text">Note type</label>
							<select name="invoice_note_type" id="invoice_note_type">
								<option value="">Private note</option>
								<option value="customer">Note to customer</option>
							</select>
							<button type="button" class="add_invoice_note button">Add</button>
						</p>

					</div>

				</div>
			</div>

		</div>
	</div>
</div>
