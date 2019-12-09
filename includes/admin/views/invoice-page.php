<?php
defined( 'ABSPATH' ) || exit();
$invoices_page_url = admin_url( 'admin.php?page=eaccounting-income' );
$title             = __( 'Add Invoice', 'wp-ever-accounting' );

?>

<?php echo sprintf( '<h1 class="wp-heading-inline">%s</h1>', $title ); ?>
<?php echo sprintf( '<a href="%s" class="page-title-action">%s</a>', $invoices_page_url, __( 'All Invoices', 'wp-ever-accounting' ) ); ?>

<div class="ea-wrapper">
	<div class="ea-card">
		<div class="ea-card-body">
			<form action="">
				<div class="ea-row">
					<?php
					//					echo EAccounting_Form::customer_dropdown( array(
					//						'label'         => __( 'Customer', 'wp-ever-accounting' ),
					//						'name'          => 'customer_id',
					////				'value'         => isset( $account['customer_id'] ) ? $account['customer_id'] : '',
					//						'placeholder'   => __( 'Select Customer', 'wp-ever-accounting' ),
					//						'icon'          => 'fa fa-user',
					//						'required'      => true,
					//						'wrapper_class' => 'ea-col-6',
					//					) );
					//
					//					echo EAccounting_Form::input_control( array(
					//						'label'         => __( 'Invoice Number', 'wp-ever-accounting' ),
					//						'name'          => 'invoice_number',
					////				'value'         => isset( $account['invoice_number'] ) ? $account['invoice_number'] : '',
					//						'placeholder'   => __( 'INV-0001', 'wp-ever-accounting' ),
					//						'icon'          => 'fa fa-file-text-o',
					//						'required'      => true,
					//						'readonly'      => true,
					//						'wrapper_class' => 'ea-col-6',
					//					) );
					//
					//					echo EAccounting_Form::date_control( array(
					//						'label'         => __( 'Invoice Date', 'wp-ever-accounting' ),
					//						'name'          => 'issued_at',
					////				'value'         => isset( $account['issued_at'] ) ? $account['issued_at'] : '',
					//						'placeholder'   => date( 'Y-m-d', current_time( 'timestamp' ) ),
					//						'icon'          => 'fa fa-calendar',
					//						'required'      => true,
					//						'wrapper_class' => 'ea-col-6',
					//					) );
					//
					//					echo EAccounting_Form::date_control( array(
					//						'label'         => __( 'Due Date', 'wp-ever-accounting' ),
					//						'name'          => 'issued_at',
					////				'value'         => isset( $account['due_at'] ) ? $account['due_at'] : '',
					//						'placeholder'   => date( 'Y-m-d', strtotime( '+15 days' ) ),
					//						'icon'          => 'fa fa-calendar',
					//						'required'      => true,
					//						'wrapper_class' => 'ea-col-6',
					//					) );
					//
					//					echo EAccounting_Form::categories_dropdown( array(
					//						'label'         => __( 'Category', 'wp-ever-accounting' ),
					//						'name'          => 'category',
					//						'type'          => 'income',
					////				'value'         => isset( $account['category'] ) ? $account['category'] : '',
					//						'placeholder'   => '',
					//						'icon'          => 'fa fa-folder',
					//						'required'      => true,
					//						'wrapper_class' => 'ea-col-6',
					//					) );
					//
					//					echo EAccounting_Form::input_control( array(
					//						'label'         => __( 'Order Number', 'wp-ever-accounting' ),
					//						'name'          => 'order_number',
					////				'value'         => isset( $account['order_number'] ) ? $account['order_number'] : '',
					//						'placeholder'   => '',
					//						'icon'          => 'fa fa-shopping-cart',
					//						'wrapper_class' => 'ea-col-6',
					//					) );

					?>
					<div class="ea-col-12">
						<span class="ea-control-label"><?php _e( 'Items', 'wp-ever-accounting' ); ?></span>
						<div class="ea-invoice-table-wrap">
							<table class="ea-invoice-table" id="ea-invoice-table">
								<thead>
								<tr>
									<th class="ea-invoice-table-actions"><?php _e( 'Actions', 'wp-ever-accounting' ); ?></th>
									<th class="ea-invoice-table-names"><?php _e( 'Name', 'wp-ever-accounting' ); ?></th>
									<th class="ea-invoice-table-quantities"><?php _e( 'Quantity', 'wp-ever-accounting' ); ?></th>
									<th class="ea-invoice-table-prices"><?php _e( 'Price', 'wp-ever-accounting' ); ?></th>
									<th class="ea-invoice-table-totals"><?php _e( 'Total', 'wp-ever-accounting' ); ?></th>
								</tr>
								</thead>
								<tbody>

								<?php
								$item_row = 0;
								for ( $i = 0; $i <= 4; $i ++ ) {
									eaccounting_get_views( 'invoice/line-item.php', [
										'item_row' => $i,
										'line_id'  => null,
										'item_id'  => null,
										'name'     => '',
										'quantity' => $i * 2,
										'price'    => $i * 5,
									] );
								}


								?>

								<tr id="add-item-row">
									<td class="text-center" style="vertical-align: middle;">
										<button type="button" id="add-line-item" class="btn btn-xs btn-danger"><i
												class="fa fa-plus"></i></button>
									</td>
								</tr>

								</tbody>

								<tfoot>
								<tr id="tr-subtotal">
									<td colspan="4"><strong>Subtotal</strong></td>
									<td><span id="sub-total"><?php echo eaccounting_price( '0' ); ?></span></td>
								</tr>

								<tr id="tr-discount">
									<td colspan="4">Add Discount</td>
									<td>
										<input id="discount" class="ea-form-control" name="discount" type="text" value="0">
									</td>
								</tr>

								<tr id="tr-shipping-input">
									<td colspan="4">Add Shipping</td>
									<td>
										<input id="shipping" class="ea-form-control ea-price-control" name="shipping" type="text" value="0">
									</td>
								</tr>

								<tr id="tr-discount">
									<td colspan="4"><strong>Discount</strong></td>
									<td><span id="discount-total"><?php echo eaccounting_price( '0' ); ?></span></td>
								</tr>

								<tr id="tr-shipping">
									<td colspan="4"><strong>Shipping</strong></td>
									<td><span id="shipping-total"><?php echo eaccounting_price( '0' ); ?></span></td>
								</tr>

								<tr id="tr-tax">
									<td colspan="4"><strong>Tax</strong></td>
									<td><span id="tax-total"><?php echo eaccounting_price( '0' ); ?></span></td>
								</tr>

								<tr id="tr-total">
									<td colspan="4"><strong>Total</strong></td>
									<td><span id="grand-total"><?php echo eaccounting_price( '0' ); ?></span></td>
								</tr>
								</tfoot>
							</table>
						</div>


					</div>


				</div>

			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function ($) {
		var requesting = false;
		var focus = false;
		var item_row = '<?php echo $item_row;?>';
		var ajax_url = '<?php echo admin_url( 'admin-ajax.php' );?>';

		itemTableResize();
		totalItem();

		$(document).on('click', '#ea-invoice-table #add-line-item', function (e) {
			e.preventDefault();

			var nonce = "<?php echo wp_create_nonce( 'invoice_add_item' );?>";
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'JSON',
				data: {
					action: 'eaccounting_invoice_add_item',
					item_row: item_row,
					nonce: nonce
				},
				success: function (res) {
					$('#ea-invoice-table tbody #add-item-row').before(res.data.html);
					$(document).trigger('eAccountingInvoiceUpdated');
					item_row++;
				}
			});
		});


		$(document).on('change', '#ea-invoice-table #discount', function (e) {
			e.preventDefault();
			totalItem();
		});

		$(document).on('change', '#ea-invoice-table tbody select', function(){
			totalItem();
		});

		$(document).on('focusin', '#items .input-price', function(){
			focus = true;
		});

		// $(document).on('keyup', '#ea-invoice-table .item-line-remove', function (e) {
		// 	e.preventDefault();
		// 	$(this).closest('tr').remove();
		// 	totalItem();
		// });
		// //
		// $(document).on('change', '#ea-invoice-table .ea-price-control, #ea-invoice-table .ea-price-control', function () {
		// 	totalItem();
		// });
		//

		//
		function itemTableResize() {
			colspan = $('#ea-invoice-table thead tr th').length - 1;

			$('#ea-invoice-table tbody #add-line-item').attr('colspan', colspan);
			$('#ea-invoice-table tbody #tr-subtotal td:first').attr('colspan', colspan);
			$('#ea-invoice-table tbody #tr-discount td:first').attr('colspan', colspan);
			$('#ea-invoice-table tbody #tr-tax td:first').attr('colspan', colspan);
			$('#ea-invoice-table tbody #tr-total td:first').attr('colspan', colspan);
		}

		function totalItem() {
			// if (requesting) {
			// 	return false;
			// }
			//
			// requesting = true;
			var nonce = "<?php echo wp_create_nonce( 'invoice_total_item' );?>";
			var data = $.extend({},
				{action: 'eaccounting_get_invoice_total_item'},
				$('#ea-invoice-table .line-item input, #ea-invoice-table .line-item select, #ea-invoice-table .line-item textarea').serializeObject(),
				{discount: $('#ea-invoice-table #discount').val()},
				{shipping: $('#ea-invoice-table #shipping').val()},
				{nonce: nonce}
			);

			$.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'JSON',
				data: data,
				success: function (res) {
					$.each(res.data.items, function (key, value) {
						$('#item-total-' + key).html(value);
					});
					$('#sub-total').html(res.data.subtotal);
					$('#discount-total').html(res.data.discount_total);
					$('#tax-total').html(res.data.tax_total);
					$('#grand-total').html(res.data.grand_total);
					$('#shipping-total').html(res.data.shipping);

					$(document).trigger('eAccountingInvoiceUpdated');

					requesting = false;
				},
				error: function (errorThrown) {
					requesting = false;
					console.log(errorThrown);
				}
			});
		}

		//
		//eAccounting.initializePlugins();

	});


</script>
