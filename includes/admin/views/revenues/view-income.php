<?php
/**
 * Admin Revenue View Page.
 *
 * @since       1.1.0
 * @subpackage  Admin/Sales/Income
 * @package     EverAccounting
 *
 * @var int $income_id
 */
defined( 'ABSPATH' ) || exit();

use EverAccounting\Models\Account;
use EverAccounting\Models\Category;
use EverAccounting\Models\Customer;

wp_enqueue_script( 'ea-print' );
try {
	$income = new \EverAccounting\Models\Income( $income_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
$back_url = remove_query_arg( array( 'action', 'income_id' ) );
$edit_url = add_query_arg( array( 'action' => 'edit' ), $back_url );
?>

<div class="ea-voucher-page">
	<div class="ea-row">
		<div class="ea-col-12">
			<div class="ea-card">
				<div class="ea-card__header">
					<h3 class="ea-card__title"><?php _e( 'Income Voucher', 'wp-ever-accounting' ); ?></h3>
					<div>
						<a href="<?php echo $edit_url; ?>" class="button-secondary button"><?php _e( 'Edit', 'wp-ever-accounting' ); ?></a>
						<a href="<?php echo $back_url; ?>" class="button button-secondary"><?php _e( 'Back', 'wp-ever-accounting' ); ?></a>
						<button class="button button-secondary print-button"><?php _e( 'Print', 'wp-ever-accounting' ); ?></button>
					</div>
				</div>
				<!-- /.ea-card__header -->
				<div id="ea-print-voucher" class="ea-card__inside">
					<div class="ea-voucher">
						<div class="ea-voucher__header">
							<div class="ea-voucher__logo">
								<img src="https://wpeveraccounting.com/wp-content/uploads/2020/09/Logo-same-size-plugin-ever.svg" alt="WP Ever Accounting">
							</div>

							<div class="ea-voucher__title"><?php _e( 'Income Voucher', 'wp-ever-accounting' ); ?></div>
						</div>

						<div class="ea-voucher__columns">
							<div>
								<table class="ea-voucher__party">
									<tr>
										<th>
											<div class="ea-voucher__subtitle"><?php _e( 'From', 'wp-ever-accounting' ); ?></div>
										</th>
										<td>
											<?php
											$account_id = $income->get_account_id();
											$account    = new Account( $account_id );
											?>
											<div class="ea-voucher__company"><?php echo $account->get_name(); ?></div>
											<div class="ea-voucher__address">
												<span class="ea-voucher__address-line"><?php echo ! empty( $account->get_bank_address() ) && ! empty( $account->get_bank_address() ) ? $account->get_bank_address() : ''; ?></span>
											</div>
										</td>
									</tr>
								</table>

								<table class="ea-voucher__party">
									<tr>
										<th>
											<div class="ea-voucher__subtitle"><?php _e( 'To', 'wp-ever-accounting' ); ?></div>
										</th>
										<td>
											<?php
											$customer_id = $income->get_customer_id();
											$customers   = new Customer( $customer_id );
											?>
											<div class="ea-voucher__company"><?php echo $customers ? $customers->get_name() : __( 'Deleted Customer', 'wp-ever-accounting' ); ?></div>
											<div class="ea-voucher__address">
												<span class="ea-voucher__address-line"><?php echo ! empty( $customers->get_address() ) && ! empty( $customers->get_address() ) ? $customers->get_address() : ''; ?></span>
											</div>
										</td>
									</tr>
								</table>

							</div>

							<div class="ea-voucher__props">
								<table class="ea-voucher__party">
									<tbody>
									<tr>

										<th>
											<div class="ea-voucher__subtitle"><?php _e( 'Voucher Number', 'wp-ever-accounting' ); ?></div>
										</th>
										<td>
											<div class="ea-voucher__value"><?php echo $income_id; ?></div>
										</td>
									</tr>
									<tr>

										<th>
											<div class="ea-voucher__subtitle"><?php _e( 'Payment Method', 'wp-ever-accounting' ); ?></div>
										</th>
										<?php
										$available_payment_methods = eaccounting_get_payment_methods();
										$income_payment_method     = $income->get_payment_method();
										?>
										<td>
											<div class="ea-voucher__value"><?php echo array_key_exists( $income_payment_method, $available_payment_methods ) ? $available_payment_methods[ $income_payment_method ] : ''; ?></div>
										</td>
									</tr>
									<tr>

										<th>
											<div class="ea-voucher__subtitle"><?php _e( 'Payment Date', 'wp-ever-accounting' ); ?></div>
										</th>
										<td>
											<?php
											$date_format = get_option( 'date_format' ) ? get_option( 'date_format' ) : 'F j, Y';
											?>
											<div class="ea-voucher__value"><?php echo eaccounting_format_datetime( $income->get_payment_date(), 'M j, Y' ); ?></div>
										</td>
									</tr>
									<tr>

										<th>
											<div class="ea-voucher__subtitle"><?php _e( 'Bank Account', 'wp-ever-accounting' ); ?></div>
										</th>
										<td>
											<div class="ea-voucher__value"><?php echo $account ? $account->get_name() : '&mdash'; ?></div>
										</td>
									</tr>

									<tr>

										<th>
											<div class="ea-voucher__subtitle"><?php _e( 'Category', 'wp-ever-accounting' ); ?></div>
										</th>
										<?php
										$category_id = $income->get_category_id();
										$category    = new Category( $category_id );
										?>
										<td>
											<div class="ea-voucher__value"><?php echo $category ? $category->get_name() : __( 'Deleted Category', 'wp-ever-accounting' ); ?></div>
										</td>
									</tr>
									</tbody>
								</table>
							</div>
						</div>
						<!-- /.ea-voucher__columns -->


						<table class="ea-voucher__items">
							<thead>
							<tr>
								<th class="text-left"><?php _e( 'Sl', 'wp-ever-accounting' ); ?></th>
								<th class="text-center"><?php _e( 'Description', 'wp-ever-accounting' ); ?></th>
								<th class="text-right"><?php _e( 'Amount', 'wp-ever-accounting' ); ?></th>
							</tr>
							</thead>

							<tbody>
							<tr>
								<td class="text-left"><?php _e( '1', 'wp-ever-accounting' ); ?></td>
								<td class="text-center description"><?php echo ! empty( $income->get_description() ) ? $income->get_description() : '&mdash;'; ?></td>
								<td class="text-right"><?php echo eaccounting_format_price( $income->get_amount(), $income->get_currency_code() ); ?></td>
							</tr>
							</tbody>

							<tfoot>
							<tr>
								<td colspan="2">
									<p class="ea-voucher__text">
										<strong><?php _e( 'In Word:', 'wp-ever-accounting' ); ?> </strong><?php echo eaccounting_numbers_to_words( $income->get_amount() ) . ' In ' . $income->get_currency_code(); ?>
									</p>
								</td>
								<td colspan="2" class="ea-voucher__totals"><span><?php _e( 'Total', 'wp-ever-accounting' ); ?></span><?php echo eaccounting_format_price( $income->get_amount(), $income->get_currency_code() ); ?></td>
							</tr>

							</tfoot>
						</table>
						<!-- /.ea-voucher__items -->
						<p class="ea-voucher__reference">
							<strong><?php _e( 'Reference:', 'wp-ever-accounting' ); ?> </strong><?php echo ! empty( $income->get_reference() ) ? $income->get_reference() : ''; ?>
						</p>
					</div>
					<!-- /.ea-voucher -->
				</div>
				<!-- /.ea-card__inside -->
			</div>
			<!-- /.ea-card -->
		</div>
		<!-- /.ea-col-9 -->
	</div>
	<!-- /.ea-row -->
</div>
<script type="text/javascript">
	var $ = jQuery.noConflict();
	$('.print-button').on('click', function (e) {
		$("#ea-print-voucher").printThis({
			debug: false,               // show the iframe for debugging
			importCSS: true,            // import parent page css
			importStyle: false,         // import style tags
			printContainer: true,       // print outer container/$.selector
			loadCSS: "",                // path to additional css file - use an array [] for multiple
			pageTitle: "",              // add title to print page
			removeInline: false,        // remove inline styles from print elements
			removeInlineSelector: "*",  // custom selectors to filter inline styles. removeInline must be true
			printDelay: 333,            // variable print delay
			header: null,               // prefix to html
			footer: null,               // postfix to html
			base: false,                // preserve the BASE tag or accept a string for the URL
			formValues: true,           // preserve input/form values
			canvas: false,              // copy canvas content
			doctypeString: '...',       // enter a different doctype for older markup
			removeScripts: false,       // remove script tags from print content
			copyTagClasses: false,      // copy classes from the html & body tag
			beforePrintEvent: null,     // function for printEvent in iframe
			beforePrint: null,          // function called before iframe is filled
			afterPrint: null            // function called before iframe is removed
		});
	});

</script>
