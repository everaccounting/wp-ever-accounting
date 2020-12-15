<?php
/**
 * Admin Revenue View Page.
 *
 * @since       1.1.0
 * @subpackage  Admin/Sales/Revenues
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();

wp_enqueue_script( 'ea-print' );
$revenue_id = isset( $_REQUEST['revenue_id'] ) ? absint( $_REQUEST['revenue_id'] ) : null;
try {
	$revenue = new \EverAccounting\Models\Income( $revenue_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
$back_url = remove_query_arg( array( 'action', 'revenue_id' ) );
$edit_url = add_query_arg( array( 'action' => 'edit', 'revenue_id' => $revenue->get_id() ), $back_url );
?>

<div class="ea-revenue-page">
	<div class="ea-row">
		<div class="ea-col-12">
			<div class="ea-card">
				<div class="ea-card__header">
					<h3 class="ea-card__title"><?php _e( 'Revenue Voucher', 'wp-ever-accounting' ); ?></h3>
					<div>
						<a href="<?php echo $edit_url; ?>" class="button-secondary button"><?php _e( 'Edit', 'wp-ever-accounting' ); ?></a>
						<a href="<?php echo $back_url; ?>" class="button button-secondary"><?php _e( 'Back', 'wp-ever-accounting' ); ?></a>
						<button class="button button-secondary print-button"><?php _e( 'Print', 'wp-ever-accounting' ) ?></button>
					</div>
				</div>
				<!-- /.ea-card__header -->
				<div id="ea-print-voucher" class="ea-card__inside">
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
											<div class="ea-revenue__subtitle"><?php _e( 'From', 'wp-ever-accounting' ); ?></div>
										</th>
										<td>
											<?php
											$account = $revenue->get_account();
											?>
											<div class="ea-revenue__company"><?php echo  $account['name'] ; ?></div>
											<div class="ea-revenue__address">
												<span class="ea-revenue__address-line"><?php echo isset( $account['bank_address'] ) && ! empty( $account['bank_address'] ) ? $account['bank_address'] : ''; ?></span>
											</div>
										</td>
									</tr>
								</table>

								<table class="ea-revenue__party">
									<tr>
										<th>
											<div class="ea-revenue__subtitle"><?php _e( 'To', 'wp-ever-accounting' ); ?></div>
										</th>
										<td>
											<?php
											$customers = $revenue->get_customer();
											?>
											<div class="ea-revenue__company"><?php echo $customers ?  $customers['name']  : __( 'Deleted Customer', 'wp-ever-accounting' ); ?></div>
											<div class="ea-revenue__address">
												<span class="ea-revenue__address-line"><?php echo isset( $customers['address'] ) && ! empty( $customers['address'] ) ? $customers['address'] : ''; ?></span>
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
											<div class="ea-revenue__subtitle"><?php _e( 'Voucher Number', 'wp-ever-accounting' ); ?></div>
										</th>
										<td>
											<div class="ea-revenue__value"><?php echo $revenue_id; ?></div>
										</td>
									</tr>
									<tr>

										<th>
											<div class="ea-revenue__subtitle"><?php _e( 'Payment Method', 'wp-ever-accounting' ); ?></div>
										</th>
										<?php
										$available_payment_methods = eaccounting_get_payment_methods();
										$revenue_payment_method    = $revenue->get_payment_method();
										?>
										<td>
											<div class="ea-revenue__value"><?php echo array_key_exists( $revenue_payment_method, $available_payment_methods ) ? $available_payment_methods[ $revenue_payment_method ] : ''; ?></div>
										</td>
									</tr>
									<tr>

										<th>
											<div class="ea-revenue__subtitle"><?php _e( 'Payment Date', 'wp-ever-accounting' ); ?></div>
										</th>
										<td>
											<?php
											$date_format = get_option( 'date_format' ) ? get_option( 'date_format' ) : 'F j, Y';
											?>
											<div class="ea-revenue__value"><?php echo date( $date_format, strtotime( $revenue->get_payment_date() ) ); ?></div>
										</td>
									</tr>
									<tr>

										<th>
											<div class="ea-revenue__subtitle"><?php _e( 'Bank Account', 'wp-ever-accounting' ); ?></div>
										</th>
										<td>
											<div class="ea-revenue__value"><?php echo $account ?  $account['name']  : __( 'Deleted Account', 'wp-ever-accounting' ); ?></div>
										</td>
									</tr>

									<tr>

										<th>
											<div class="ea-revenue__subtitle"><?php _e( 'Category', 'wp-ever-accounting' ); ?></div>
										</th>
										<?php
										$category = $revenue->get_category();
										?>
										<td>
											<div class="ea-revenue__value"><?php echo $category ? $category['name'] : __( 'Deleted Category', 'wp-ever-accounting' ); ?></div>
										</td>
									</tr>
									</tbody>
								</table>
							</div>
						</div>
						<!-- /.ea-revenue__columns -->


						<table class="ea-revenue__items">
							<thead>
							<tr>
								<th class="text-left"><?php _e( 'Sl', 'wp-ever-accounting' ); ?></th>
								<th class="text-center"><?php _e( 'Description', 'wp-ever-accounting' ); ?></th>
								<th class="text-right"><?php _e( 'Amount', 'wp-ever-accounting' ); ?></th>
							</tr>
							</thead>

							<tbody>
							<tr>
								<td class="text-left"><?php _e( '1', 'wp-ever-accounting' ) ?></td>
								<td class="text-center description"><?php echo ! empty( $revenue->get_description() ) ? $revenue->get_description() : ''; ?></td>
								<td class="text-right"><?php echo eaccounting_format_price( $revenue->get_amount(), $revenue->get_currency_code() ); ?></td>
							</tr>
							</tbody>

							<tfoot>
							<tr>
								<td colspan="2">
									<p class="ea-revenue__text">
										<strong><?php _e( 'In Word:', 'wp-ever-accounting' ); ?> </strong><?php echo eaccounting_numbers_to_words( $revenue->get_amount() ) . ' In ' . $revenue->get_currency_code(); ?>
									</p>
								</td>

								<td colspan="2" class="padding-zero">
									<table class="ea-revenue__totals">
										<tbody>
										<tr>
											<th><?php _e( 'Total', 'wp-ever-accounting' ) ?></th>
											<td><?php echo eaccounting_format_price( $revenue->get_amount(), $revenue->get_currency_code() ); ?></td>
										</tr>
										</tbody>
									</table>
								</td>
							</tr>

							</tfoot>
						</table>
						<!-- /.ea-revenue__items -->
						<p class="ea-revenue__reference">
							<strong><?php _e( 'Reference:', 'wp-ever-accounting' ) ?> </strong><?php echo ! empty( $revenue->get_reference() ) ? $revenue->get_reference() : ''; ?>
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
<script type="text/javascript">
	var $ =jQuery.noConflict();
	$('.print-button').on('click',function(e){
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

