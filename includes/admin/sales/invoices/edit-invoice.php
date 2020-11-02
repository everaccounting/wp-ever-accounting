<?php
/**
 * Admin Invoice Edit Page.
 *
 * @since       1.1.0
 * @subpackage  Admin/Sales/Invoices
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();

$invoice_id = isset( $_REQUEST['invoice_id'] ) ? absint( $_REQUEST['invoice_id'] ) : null;
try {
	$invoice = new \EverAccounting\Invoice( $invoice_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}

$back_url = remove_query_arg( array( 'action', 'id' ) );
?>

	<div class="ea-form-card">
		<div class="ea-card ea-form-card__header is-compact">
			<h3 class="ea-form-card__header-title"><?php echo $invoice->exists() ? __( 'Update Invoice', 'wp-ever-accounting' ) : __( 'Add Invoice', 'wp-ever-accounting' ); ?></h3>
			<a href="<?php echo $back_url; ?>" class="button button-secondary"><span class="dashicons dashicons-arrow-left-alt"></span><?php _e( 'Back', 'wp-ever-accounting' ); ?></a>
		</div>

		<div class="ea-card">
			<form id="ea-invoice-form" class="ea-ajax-form" method="post" enctype="multipart/form-data">
				<div class="ea-row">
					<?php
					eaccounting_text_input( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Invoice Number', 'wp-ever-accounting' ),
							'name'          => 'invoice_number',
							'placeholder'   => __( 'Enter invoice number', 'wp-ever-accounting' ),
							'value'         => $invoice->get_invoice_number(),
							'required'      => true,
					) );
					eaccounting_text_input( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Order Number', 'wp-ever-accounting' ),
							'name'          => 'order_number',
							'placeholder'   => __( 'Enter order number', 'wp-ever-accounting' ),
							'value'         => $invoice->get_order_number(),
							'required'      => true,
					) );
					eaccounting_text_input( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Status', 'wp-ever-accounting' ),
							'name'          => 'status',
							'placeholder'   => __( 'Enter status', 'wp-ever-accounting' ),
							'value'         => $invoice->get_status(),
					) );
					eaccounting_text_input( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Invoiced At', 'wp-ever-accounting' ),
							'name'          => 'invoiced_at',
							'placeholder'   => __( 'Enter invoiced at', 'wp-ever-accounting' ),
							'value'         => $invoice->get_invoiced_at() ? $invoice->get_invoiced_at()->format( 'Y-m-d' ) : null,
							'type'          => 'date',
							'required'      => true
					) );
					eaccounting_text_input( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Due At', 'wp-ever-accounting' ),
							'name'          => 'due_at',
							'placeholder'   => __( 'Enter due at', 'wp-ever-accounting' ),
							'value'         => $invoice->get_due_at() ? $invoice->get_due_at()->format( 'Y-m-d' ) : null,
							'type'          => 'date',
					) );
					eaccounting_text_input( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Subtotal', 'wp-ever-accounting' ),
							'name'          => 'subtotal',
							'placeholder'   => __( 'Enter subtotal', 'wp-ever-accounting' ),
							'value'         => $invoice->get_subtotal(),
					) );
					eaccounting_text_input( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Discount', 'wp-ever-accounting' ),
							'name'          => 'discount',
							'placeholder'   => __( 'Enter discount', 'wp-ever-accounting' ),
							'value'         => $invoice->get_discount(),
					) );
					eaccounting_text_input( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Tax', 'wp-ever-accounting' ),
							'name'          => 'tax',
							'placeholder'   => __( 'Enter tax', 'wp-ever-accounting' ),
							'value'         => $invoice->get_tax(),
					) );
					eaccounting_text_input( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Shipping', 'wp-ever-accounting' ),
							'name'          => 'shipping',
							'placeholder'   => __( 'Enter shipping', 'wp-ever-accounting' ),
							'value'         => $invoice->get_shipping(),
					) );
					eaccounting_text_input( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Total', 'wp-ever-accounting' ),
							'name'          => 'total',
							'placeholder'   => __( 'Enter total', 'wp-ever-accounting' ),
							'value'         => $invoice->get_total(),
							'required'      => true
					) );
					eaccounting_currency_dropdown( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Currency Code', 'wp-ever-accounting' ),
							'name'          => 'currency_code',
							'placeholder'   => __( 'Enter Currency Code', 'wp-ever-accounting' ),
							'value'         => $invoice->get_currency_code(),
							'required'      => true
					) );
					eaccounting_text_input( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Currency Rate', 'wp-ever-accounting' ),
							'name'          => 'currency_rate',
							'placeholder'   => __( 'Enter currency_rate', 'wp-ever-accounting' ),
							'value'         => $invoice->get_currency_rate(),
							'required'      => true
					) );
					eaccounting_category_dropdown( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Category ID', 'wp-ever-accounting' ),
							'name'          => 'category_id',
							'placeholder'   => __( 'Enter category_id', 'wp-ever-accounting' ),
							'value'         => $invoice->get_category_id(),
							'type'          => 'expense',
							'required'      => true,
							'creatable'     => true,
					) );
					eaccounting_text_input( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Contact Name', 'wp-ever-accounting' ),
							'name'          => 'contact_name',
							'placeholder'   => __( 'Enter contact_name', 'wp-ever-accounting' ),
							'value'         => $invoice->get_contact_name(),
							'required'      => true
					) );
					eaccounting_text_input( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Contact email', 'wp-ever-accounting' ),
							'name'          => 'contact_email',
							'placeholder'   => __( 'Enter contact email', 'wp-ever-accounting' ),
							'value'         => $invoice->get_contact_email(),
							'type'          => 'email'
					) );
					eaccounting_text_input( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Contact Tax Number', 'wp-ever-accounting' ),
							'name'          => 'contact_tax_number',
							'placeholder'   => __( 'Enter contact tax number', 'wp-ever-accounting' ),
							'value'         => $invoice->get_contact_tax_number(),
					) );
					eaccounting_text_input( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Contact Phone', 'wp-ever-accounting' ),
							'name'          => 'contact_phone',
							'placeholder'   => __( 'Enter contact phone', 'wp-ever-accounting' ),
							'value'         => $invoice->get_contact_phone(),
					) );
					eaccounting_textarea( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Contact address', 'wp-ever-accounting' ),
							'name'          => 'contact_address',
							'placeholder'   => __( 'Enter contact address', 'wp-ever-accounting' ),
							'value'         => $invoice->get_contact_address(),
					) );
					eaccounting_textarea( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Note', 'wp-ever-accounting' ),
							'name'          => 'note',
							'placeholder'   => __( 'Enter note', 'wp-ever-accounting' ),
							'value'         => $invoice->get_note(),
					) );
					eaccounting_textarea( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Footer', 'wp-ever-accounting' ),
							'name'          => 'footer',
							'placeholder'   => __( 'Enter footer', 'wp-ever-accounting' ),
							'value'         => $invoice->get_footer(),
					) );

					eaccounting_hidden_input( array(
							'name'  => 'id',
							'value' => $invoice->get_id()
					) );

					eaccounting_hidden_input( array(
							'name'  => 'action',
							'value' => 'eaccounting_edit_invoice'
					) );

					?>
				</div>
				<?php
				wp_nonce_field( 'ea_edit_invoice' );
				submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
				?>
			</form>
		</div>
	</div>
<?php
