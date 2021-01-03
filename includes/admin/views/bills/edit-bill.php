<?php
/**
 * Admin Bill Edit Page.
 *
 * Page: Expenses
 * Tab: Bills
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Bills
 * @package     EverAccounting
 *
 * @var int $bill_id
 */

defined( 'ABSPATH' ) || exit();

try {
	$bill = new \EverAccounting\Models\Bill( $bill_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
$bill->maybe_set_document_number();
$title    = $bill->exists() ? __( 'Update Bill', 'wp-ever-accounting' ) : __( 'Add Bill', 'wp-ever-accounting' );
$view_url = admin_url( 'admin.php' ) . '?page=ea-sales&tab=bills&action=view&bill_id=' . $bill->get_id();
?>
<form id="ea-bill-form" method="post" class="ea-documents">
	<div class="ea-bill">
		<div class="ea-card">
			<div class="ea-card__header">
				<h3 class="ea-card__title"><?php echo esc_html( $title ); ?></h3>
				<div>
					<button onclick="history.go(-1);" class="button-secondary"><?php _e( 'Go Back', 'wp-ever-accounting' ); ?></button>
					<?php if ( $bill->exists() ) : ?>
						<?php do_action( 'eaccounting_bill_header_actions', $bill ); ?>
						<a class="button-secondary button" href="<?php echo esc_url( $view_url ); ?>"><?php _e( 'View Bill', 'wp-ever-accounting' ); ?></a>
					<?php endif; ?>
				</div>
			</div>

			<div class="ea-card__inside">
				<div class="ea-row">
					<?php
					eaccounting_vendor_dropdown(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Vendor', 'wp-ever-accounting' ),
							'name'          => 'vendor_id',
							'placeholder'   => __( 'Select Vendor', 'wp-ever-accounting' ),
							'value'         => $bill->get_vendor_id(),
							'required'      => true,
							'creatable'     => true,
						)
					);
					eaccounting_currency_dropdown(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Currency', 'wp-ever-accounting' ),
							'name'          => 'currency_code',
							'value'         => $bill->get_currency_code(),
							'required'      => true,
							'creatable'     => true,
						)
					);

					eaccounting_text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Bill Date', 'wp-ever-accounting' ),
							'name'          => 'issue_date',
							'value'         => $bill->get_issue_date() ? eaccounting_format_datetime( $bill->get_issue_date(), 'Y-m-d' ) : null,
							'required'      => true,
							'data_type'     => 'date',
						)
					);

					eaccounting_text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Due Date', 'wp-ever-accounting' ),
							'name'          => 'due_date',
							'value'         => $bill->get_due_date() ? eaccounting_format_datetime( $bill->get_due_date(), 'Y-m-d' ) : null,
							'required'      => true,
							'data_type'     => 'date',
						)
					);

					eaccounting_text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Bill Number', 'wp-ever-accounting' ),
							'name'          => 'bill_number',
							'value'         => empty( $bill->get_bill_number() ) ? $bill->get_bill_number() : $bill->get_bill_number(),
							'required'      => true,
						)
					);

					eaccounting_text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Order Number', 'wp-ever-accounting' ),
							'name'          => 'order_number',
							'value'         => $bill->get_order_number(),
							'required'      => false,
						)
					);

					eaccounting_category_dropdown(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Category', 'wp-ever-accounting' ),
							'name'          => 'category_id',
							'value'         => $bill->get_category_id(),
							'required'      => true,
							'type'          => 'expense',
							'creatable'     => true,
							'ajax_action'   => 'eaccounting_get_expense_categories',
							'modal_id'      => 'ea-modal-add-expense-category',
						)
					);

					eaccounting_text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Terms', 'wp-ever-accounting' ),
							'name'          => 'terms',
							'value'         => $bill->get_terms(),
							'required'      => false,
						)
					);

					eaccounting_get_admin_template(
						'bills/partials/items',
						array(
							'bill' => $bill,
							'mode' => 'edit',
						)
					);

					eaccounting_file_input(
						array(
							'label'         => __( 'Attachments', 'wp-ever-accounting' ),
							'name'          => 'attachment_id',
							'allowed-types' => 'jpg,jpeg,png,pdf',
							'value'         => $bill->get_attachment_id(),
							'wrapper_class' => 'ea-col-6',
							'placeholder'   => __( 'Upload File', 'wp-ever-accounting' ),
						)
					);
					eaccounting_hidden_input(
						array(
							'name'  => 'currency_rate',
							'value' => $bill->get_currency_rate(),
						)
					);
					eaccounting_hidden_input(
						array(
							'name'  => 'id',
							'value' => $bill->get_id(),
						)
					);
					eaccounting_hidden_input(
						array(
							'name'  => 'discount',
							'value' => $bill->get_discount(),
						)
					);
					eaccounting_hidden_input(
						array(
							'name'  => 'discount_type',
							'value' => $bill->get_discount_type(),
						)
					);
					eaccounting_hidden_input(
						array(
							'name'  => 'action',
							'value' => 'eaccounting_edit_bill',
						)
					);
					?>

				</div><!--.ea-row-->

			</div>
			<div class="ea-card__footer">
				<?php wp_nonce_field( 'ea_edit_bill' ); ?>
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
