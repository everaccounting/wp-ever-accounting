<?php
/**
 * Admin Bill Page.
 *
 * Page: Expenses
 * Tab: Bills
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Bills
 * @package     EverAccounting
 *
 * @var Bill $bill
 */

use EverAccounting\Models\Bill;

defined( 'ABSPATH' ) || exit();
if ( $bill->exists() ) {
	add_meta_box( 'bill_actions', __( 'Bill Actions', 'wp-ever-accounting' ), false, 'ea_bill', 'side' );
	add_meta_box( 'bill_notes', __( 'Bill Notes', 'wp-ever-accounting' ), array( 'EAccounting_Admin_Bills', 'bill_notes' ), 'ea_bill', 'side' );
	add_meta_box( 'bill_payments', __( 'Bill Payments', 'wp-ever-accounting' ), '__return_null', 'ea_bill', 'side' );
}

$bill->maybe_set_document_number();
$title = $bill->exists() ? __( 'Update Bill', 'wp-ever-accounting' ) : __( 'Add Bill', 'wp-ever-accounting' );

/**
 * Fires after all built-in meta boxes have been added, contextually for the given object.
 *
 * @since 1.1.0
 *
 * @param Bill $bill object.
 */
do_action( 'add_meta_boxes_ea_bill', $bill );
?>
<div class="ea-title-section">
	<div>
		<h1 class="wp-heading-inline"><?php echo esc_html( $title ); ?></h1>
		<?php if ( $bill->exists() ) : ?>
			<a href="<?php echo esc_url( 'admin.php?page=ea-expenses&tab=bills&action=add' ); ?>" class="page-title-action"><?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?></a>
		<?php else : ?>
			<a href="<?php echo remove_query_arg( array( 'action', 'id' ) ); ?>" class="page-title-action"><?php esc_html_e( 'View All', 'wp-ever-accounting' ); ?></a>
		<?php endif; ?>
	</div>
	<div>
		<button class="button-secondary"><span><?php esc_html_e( 'View Invoice', 'wp-ever-accounting' ); ?></span></button>
	</div>
</div>

<hr class="wp-header-end">

<?php if ( $bill->exists() && $bill->is_draft() ) : ?>
	<div class="notice error">
		<p><?php echo __( 'This is a <strong>DRAFT</strong> bill and will be reflected after it gets <strong>received</strong>.', 'wp-ever-accounting' ); ?></p>
	</div>
<?php endif; ?>

<form name="bill" method="post" id="ea-bill-form">
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="ea-card">
					<div class="ea-card__inside">

						<div class="ea-row">
							<?php
							eaccounting_vendor_dropdown(
								array(
									'wrapper_class' => 'ea-col-6',
									'label'         => __( 'Customer', 'wp-ever-accounting' ),
									'name'          => 'vendor_id',
									'placeholder'   => __( 'Select Customer', 'wp-ever-accounting' ),
									'value'         => $bill->get_vendor_id(),
									'required'      => true,
									'type'          => 'vendor',
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
									'label'         => __( 'Invoice Number', 'wp-ever-accounting' ),
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

							eaccounting_get_admin_template(
								'bills/bill-items',
								array(
									'bill' => $bill,
									'mode' => 'edit',
								)
							);

							eaccounting_text_input(
								array(
									'wrapper_class' => 'ea-col-12',
									'label'         => __( 'Terms', 'wp-ever-accounting' ),
									'name'          => 'terms',
									'value'         => $bill->get_terms(),
									'required'      => false,
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
							wp_nonce_field( 'ea_edit_bill' );
							?>

						</div><!--.ea-row-->

					</div>

				</div>
			</div>

			<div id="postbox-container-1" class="postbox-container">
				<div class="ea-card">
					<div class="ea-card__header">
						<h3 class="ea-card__title">
							<?php esc_html_e( 'Bill Actions', 'wp-ever-accounting' ); ?>
						</h3>
					</div>
					<?php if ( $bill->exists() && $bill->is_draft() ) : ?>
						<div class="ea-card__section alt">
							<div>
								<button class="button-secondary ea-button-alert"><span><?php esc_html_e( 'Mark Bill Received', 'wp-ever-accounting' ); ?></span></button>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( $bill->exists() && ! $bill->is_draft() && $bill->needs_payment() ) : ?>
						<div class="ea-card__section alt">
							<div>
								<button class="button-secondary ea-button-success"><span><?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?></span></button>
							</div>

							<div>
								<button class="button-secondary"><span><?php esc_html_e( 'Mark Paid', 'wp-ever-accounting' ); ?></span></button>
							</div>
						</div>
					<?php endif; ?>

					<div class="ea-card__inside">
						<?php
						eaccounting_text_input(
							array(
								'wrapper_class' => 'ea-col-6',
								'label'         => __( 'Issue Date', 'wp-ever-accounting' ),
								'name'          => 'issue_date',
								'value'         => $bill->get_issue_date() ? eaccounting_format_datetime( $bill->get_issue_date(), 'Y-m-d' ) : date_i18n( 'Y-m-d' ),
								'required'      => true,
								'data_type'     => 'date',
							)
						);
						eaccounting_text_input(
							array(
								'wrapper_class' => 'ea-col-6',
								'label'         => __( 'Due Date', 'wp-ever-accounting' ),
								'name'          => 'due_date',
								'value'         => $bill->get_due_date() ? eaccounting_format_datetime( $bill->get_due_date(), 'Y-m-d' ) : '',
								'required'      => true,
								'data_type'     => 'date',
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
						?>
						<?php if ( $bill->exists() && ! $bill->is_draft() ) : ?>
							<?php
							eaccounting_select(
								array(
									'wrapper_class' => 'ea-col-6',
									'label'         => __( 'Status', 'wp-ever-accounting' ),
									'name'          => 'status',
									'value'         => $bill->get_status(),
									'options'       => $bill::get_statuses(),
								)
							);
							?>
						<?php endif; ?>
					</div>

					<div class="ea-card__footer">
						<?php if ( $bill->exists() && $bill->is_draft() ) : ?>
						<a href="http://accounting.test/wp-admin/admin.php?page=ea-sales&amp;tab=invoices&amp;action=delete&amp;invoice_id=2&amp;_wpnonce=c444d72706">Remove</a>
						<?php endif; ?>
						<button class="button-primary"><span><?php esc_html_e( 'Save', 'wp-ever-accounting' ); ?></span></button>
					</div>
				</div>
				
				<?php eaccounting_do_meta_boxes( 'ea_bill', 'side', $bill ); ?>
			</div>

			<div id="postbox-container-2" class="postbox-container">
				<?php eaccounting_do_meta_boxes( 'ea_bill', 'normal', $bill ); ?>
				<?php eaccounting_do_meta_boxes( 'ea_bill', 'advanced', $bill ); ?>
			</div>

		</div>
	</div><!-- /poststuff -->
</form>

<?php
