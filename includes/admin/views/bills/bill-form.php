<?php
/**
 * Admin Bill Form.
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
$bill->maybe_set_document_number();
$title = $bill->exists() ? __( 'Update Bill', 'wp-ever-accounting' ) : __( 'Add Bill', 'wp-ever-accounting' );
defined( 'ABSPATH' ) || exit();
?>
<div class="ea-row">
	<div class="ea-col-7">
		<h1 class="wp-heading-inline"><?php echo esc_html( $title ); ?></h1>
		<?php if ( $bill->exists() ) : ?>
			<a href="<?php echo esc_url( 'admin.php?page=ea-expenses&tab=bills&action=add' ); ?>" class="page-title-action"><?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?></a>
		<?php else : ?>
			<a href="<?php echo remove_query_arg( array( 'action', 'id' ) ); ?>" class="page-title-action"><?php esc_html_e( 'View All', 'wp-ever-accounting' ); ?></a>
		<?php endif; ?>
	</div>

	<div class="ea-col-5">

	</div>
</div>
<hr class="wp-header-end">

<form id="ea-bill-form" name="bill" method="post" class="ea-form">
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">

				<div class="ea-card">
					<div class="ea-card__inside">
						<div class="ea-row">
							<?php
							do_action( 'eaccounting_bill_form_top', $bill );
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
							do_action( 'eaccounting_bill_form_bottom', $bill );
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
					</div>

					<div class="ea-card__footer">
						<a href="<?php echo add_query_arg( array( 'action' => 'view' ) ); ?>" class="button-secondary"><span><?php esc_html_e( 'View Bill', 'wp-ever-accounting' ); ?></span></a>
						<button class="button-primary"><span><?php esc_html_e( 'Save', 'wp-ever-accounting' ); ?></span></button>
					</div>

				</div><!-- /ea-card -->
			</div><!-- /postbox-container -->

		</div>
	</div>

	<?php eaccounting_hidden_input( 'currency_rate', $bill->get_currency_rate() ); ?>
	<?php eaccounting_hidden_input( 'id', $bill->get_id() ); ?>
	<?php eaccounting_hidden_input( 'discount', $bill->get_discount() ); ?>
	<?php eaccounting_hidden_input( 'discount_type', $bill->get_discount_type() ); ?>
	<?php eaccounting_hidden_input( 'action', 'eaccounting_edit_bill' ); ?>
	<?php wp_nonce_field( 'ea_edit_bill' ); ?>
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
