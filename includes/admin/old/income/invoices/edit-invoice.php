<?php
/**
 * Render invoices tab contents
 */
defined( 'ABSPATH' ) || exit();
$invoices_page_url = admin_url( 'admin.php?page=eaccounting-income' );
$title             = __( 'Add Invoice', 'wp-ever-accounting' );
?>

<?php echo sprintf( '<h1 class="wp-heading-inline">%s</h1>', $title ); ?>
<?php echo sprintf( '<a href="%s" class="page-title-action">%s</a>', $invoices_page_url, __( 'All Invoices', 'wp-ever-accounting' ) ); ?>

<div class="ea-card">
	<form action="">
		<div class="ea-row">
			<?php
			echo eaccounting_input_field( array(
				'label'         => __( 'Customer', 'wp-ever-accounting' ),
				'name'          => 'customer_id',
				'value'         => isset( $account['customer_id'] ) ? $account['customer_id'] : '',
				'placeholder'   => __( 'Select Customer', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-user',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Invoice Number', 'wp-ever-accounting' ),
				'name'          => 'invoice_number',
				'value'         => isset( $account['invoice_number'] ) ? $account['invoice_number'] : '',
				'placeholder'   => __( 'INV-0001', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-file-text-o',
				'required'      => true,
				'readonly'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Invoice Date', 'wp-ever-accounting' ),
				'name'          => 'issued_at',
				'value'         => isset( $account['issued_at'] ) ? $account['issued_at'] : '',
				'placeholder'   => date( 'Y-m-d', current_time( 'timestamp' ) ),
				'icon'          => 'fa fa-calendar',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Due Date', 'wp-ever-accounting' ),
				'name'          => 'issued_at',
				'value'         => isset( $account['due_at'] ) ? $account['due_at'] : '',
				'placeholder'   => date( 'Y-m-d', strtotime( '+15 days' ) ),
				'icon'          => 'fa fa-calendar',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Category', 'wp-ever-accounting' ),
				'name'          => 'category',
				'value'         => isset( $account['category'] ) ? $account['category'] : '',
				'placeholder'   => '',
				'icon'          => 'fa fa-folder',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Order Number', 'wp-ever-accounting' ),
				'name'          => 'order_number',
				'value'         => isset( $account['order_number'] ) ? $account['order_number'] : '',
				'placeholder'   => '',
				'icon'          => 'fa fa-shopping-cart',
				'wrapper_class' => 'ea-col-6',
			) );

			?>
			<div class="ea-col-12">
				<span class="ea-control-label"><?php _e( 'Items', 'wp-ever-accounting' ); ?></span>
				<div class="ea-transaction-table-wrap">
					<table class="ea-transaction-table" id="transaction-items">
						<thead>
						<tr>
							<th><?php _e( 'Actions', 'wp-ever-accounting' ); ?></th>
							<th><?php _e( 'Name', 'wp-ever-accounting' ); ?></th>
							<th><?php _e( 'Quantity', 'wp-ever-accounting' ); ?></th>
							<th><?php _e( 'Price', 'wp-ever-accounting' ); ?></th>
							<th><?php _e( 'Tax', 'wp-ever-accounting' ); ?></th>
							<th><?php _e( 'Total', 'wp-ever-accounting' ); ?></th>
						</tr>
						</thead>

						<tbody>

						<tr id="tr-add-item">
							<td>
								<button id="ea-button-add-item" class="button-primary button button-small">
									<i class="fa fa-plus"></i>
								</button>
							</td>
							<td colspan="5"></td>
						</tr>

						<tr id="tr-subtotal">
							<td colspan="5"><strong>Subtotal</strong></td>
							<td ><span id="sub-total">$10,000.00</span></td>
						</tr>

						<tr id="tr-discount">
							<td colspan="5">Add Discount</td>
							<td>
								<span id="discount-total"></span>
								<input id="discount" class="ea-form-control" name="discount" type="text" value="">
							</td>
						</tr>

						<tr id="tr-tax">
							<td colspan="5">
								<strong>Tax</strong>
							</td>
							<td><span id="tax-total">$100.00</span></td>
						</tr>

						<tr id="tr-total">
							<td colspan="5"><strong>Total</strong></td>
							<td><span id="grand-total">$10,100.00</span></td>
						</tr>

						</tbody>


					</table>
				</div>


			</div>


		</div>

	</form>
</div>
