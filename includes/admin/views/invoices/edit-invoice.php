<?php
/**
 * Admin Invoice Edit Page.
 *
 * Page: Sales
 * Tab: Invoices
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Invoices
 * @package     EverAccounting
 *
 * @var int $invoice_id
 */

defined( 'ABSPATH' ) || exit();

try {
	$invoice = new \EverAccounting\Models\Invoice( $invoice_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
$title    = $invoice->exists() ? __( 'Update Invoice', 'wp-ever-accounting' ) : __( 'Add Invoice', 'wp-ever-accounting' );
?>
<div class="ea-invoice">
	<div class="ea-card">
		<div class="ea-card__header">
			<h3 class="ea-card__title"><?php echo esc_html( $title ); ?></h3>
			<button onclick="history.go(-1);" class="button-secondary"><?php _e( 'Go Back', 'wp-ever-accounting' ); ?></button>
		</div>

		<div class="ea-card__inside">
			<form id="ea-invoice-form" method="post" class="ea-documents">
				<div class="ea-row">
					<?php
					eaccounting_customer_dropdown(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Customer', 'wp-ever-accounting' ),
							'name'          => 'customer_id',
							'placeholder'   => __( 'Select Customer', 'wp-ever-accounting' ),
							'value'         => $invoice->get_customer_id(),
							'required'      => true,
							'type'          => 'customer',
							'creatable'     => true,
						)
					);
					eaccounting_currency_dropdown(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Currency', 'wp-ever-accounting' ),
							'name'          => 'currency_code',
							'value'         => $invoice->get_currency_code(),
							'required'      => true,
							'creatable'     => true,
						)
					);

					eaccounting_text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Invoice Date', 'wp-ever-accounting' ),
							'name'          => 'issue_date',
							'value'         => $invoice->get_issue_date() ? eaccounting_format_datetime( $invoice->get_issue_date(), 'Y-m-d' ) : null,
							'required'      => true,
							'data_type'     => 'date',
						)
					);

					eaccounting_text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Due Date', 'wp-ever-accounting' ),
							'name'          => 'due_date',
							'value'         => $invoice->get_due_date() ? eaccounting_format_datetime( $invoice->get_due_date(), 'Y-m-d' ) : null,
							'required'      => true,
							'data_type'     => 'date',
						)
					);

					eaccounting_text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Invoice Number', 'wp-ever-accounting' ),
							'name'          => 'invoice_number',
							'value'         => empty( $invoice->get_invoice_number() ) ? $invoice->get_next_invoice_number() : $invoice->get_next_invoice_number(),
							'required'      => true,
						)
					);

					eaccounting_text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Order Number', 'wp-ever-accounting' ),
							'name'          => 'order_number',
							'value'         => $invoice->get_order_number(),
							'required'      => false,
						)
					);

					eaccounting_category_dropdown(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Category', 'wp-ever-accounting' ),
							'name'          => 'category_id',
							'value'         => $invoice->get_category_id(),
							'required'      => true,
							'type'          => 'income',
							'creatable'     => true,
							'ajax_action'   => 'eaccounting_get_income_categories',
							'modal_id'      => 'ea-modal-add-income-category',
						)
					);

					eaccounting_get_admin_template( 'html-invoice-items', array( 'invoice' => $invoice ) );

					eaccounting_text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Terms', 'wp-ever-accounting' ),
							'name'          => 'terms',
							'value'         => $invoice->get_terms(),
							'required'      => false,
						)
					);

					eaccounting_file_input(
						array(
							'label'         => __( 'Attachments', 'wp-ever-accounting' ),
							'name'          => 'attachment_id',
							'value'         => $invoice->get_attachment_id(),
							'wrapper_class' => 'ea-col-6',
							'placeholder'   => __( 'Upload File', 'wp-ever-accounting' ),
						)
					);
					eaccounting_hidden_input(
						array(
							'name'  => 'currency_rate',
							'value' => $invoice->get_currency_rate(),
						)
					);
					eaccounting_hidden_input(
						array(
							'name'  => 'id',
							'value' => $invoice->get_id(),
						)
					);
					eaccounting_hidden_input(
						array(
							'name'  => 'action',
							'value' => 'eaccounting_edit_invoice',
						)
					);
					?>

				</div><!--.ea-row-->

				<?php
				wp_nonce_field( 'ea_edit_invoice' );
				submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
				?>

			</form>

		</div>

	</div>
</div>
