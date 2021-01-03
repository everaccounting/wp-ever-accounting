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
$due      = eaccounting()->settings->get( 'invoice_due', 15 );
$due_date = date( 'Y-m-d', strtotime( "+ $due days", current_time( 'timestamp' ) ) );
$invoice->maybe_set_document_number();
$title    = $invoice->exists() ? __( 'Update Invoice', 'wp-ever-accounting' ) : __( 'Add Invoice', 'wp-ever-accounting' );
$view_url = admin_url( 'admin.php' ) . '?page=ea-sales&tab=invoices&action=view&invoice_id=' . $invoice->get_id();
?>
<form id="ea-invoice-form" method="post" class="ea-documents">
	<div class="ea-invoice">
		<div class="ea-card">
			<div class="ea-card__header">
				<h3 class="ea-card__title"><?php echo esc_html( $title ); ?></h3>
				<div>
					<button onclick="history.go(-1);" class="button-secondary button"><?php _e( 'Go Back', 'wp-ever-accounting' ); ?></button>
					<?php if ( $invoice->exists() ) : ?>
						<?php do_action( 'eaccounting_invoice_header_actions', $invoice ); ?>
						<a class="button-secondary button" href="<?php echo esc_url( $view_url ); ?>"><?php _e( 'View Invoice', 'wp-ever-accounting' ); ?></a>
					<?php endif; ?>
				</div>
			</div>

			<div class="ea-card__inside">
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
							'value'         => $invoice->get_issue_date() ? eaccounting_format_datetime( $invoice->get_issue_date(), 'Y-m-d' ) : date_i18n( 'Y-m-d' ),
							'required'      => true,
							'data_type'     => 'date',
						)
					);

					eaccounting_text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Due Date', 'wp-ever-accounting' ),
							'name'          => 'due_date',
							'value'         => $invoice->get_due_date() ? eaccounting_format_datetime( $invoice->get_due_date(), 'Y-m-d' ) : $due_date,
							'required'      => true,
							'data_type'     => 'date',
						)
					);

					eaccounting_text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Invoice Number', 'wp-ever-accounting' ),
							'name'          => 'invoice_number',
							'value'         => empty( $invoice->get_invoice_number() ) ? $invoice->get_invoice_number() : $invoice->get_invoice_number(),
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

					eaccounting_text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Terms', 'wp-ever-accounting' ),
							'name'          => 'terms',
							'value'         => $invoice->get_terms(),
							'required'      => false,
						)
					);

					eaccounting_get_admin_template(
						'invoices/partials/items',
						array(
							'invoice' => $invoice,
							'mode'    => 'edit',
						)
					);

					eaccounting_file_input(
						array(
							'label'         => __( 'Attachments', 'wp-ever-accounting' ),
							'name'          => 'attachment_id',
							'allowed-types' => 'jpg,jpeg,png,pdf',
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
							'name'  => 'discount',
							'value' => $invoice->get_discount(),
						)
					);
					eaccounting_hidden_input(
						array(
							'name'  => 'discount_type',
							'value' => $invoice->get_discount_type(),
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

			</div>
			<div class="ea-card__footer">
				<?php wp_nonce_field( 'ea_edit_invoice' ); ?>
				<?php submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' ); ?>
			</div>
		</div>
	</div>
</form>

<script type="text/template" id="ea-modal-add-discount" data-title="<?php esc_html_e( 'Add Discount', 'wp-ever-accounting' ); ?>">
	<form action="" method="post">
		<?php
		eaccounting_text_input(
			array(
				'label'    => __( 'Discount Amount', 'wp-ever-accounting' ),
				'name'     => 'discount',
				'type'     => 'number',
				'value'    => 0.0000,
				'required' => true,
				'attr'     => array(
					'step' => 0.1,
					'min'  => 0,
				),
			)
		);
		eaccounting_select(
			array(
				'label'    => __( 'Discount Type', 'wp-ever-accounting' ),
				'name'     => 'discount_type',
				'required' => true,
				'options'  => array(
					'percentage' => __( 'Percentage', 'wp-ever-accounting' ),
					'fixed'      => __( 'Fixed', 'wp-ever-accounting' ),
				),
			)
		);
		?>
	</form>
</script>
