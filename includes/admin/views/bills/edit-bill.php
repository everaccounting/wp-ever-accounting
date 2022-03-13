<?php
/**
 * Admin Bill Form.
 *
 * Page: Expenses
 * Tab: Bills
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Bills
 * @package     Ever_Accounting
 *
 * @var Bill $bill
 */

use Ever_Accounting\Bill;
use Ever_Accounting\Helpers\Form;
use Ever_Accounting\Helpers\Formatting;
use Ever_Accounting\Helpers\Template;

defined( 'ABSPATH' ) || exit();

$bill->maybe_set_bill_number();
$title    = $bill->exists() ? __( 'Update Bill', 'wp-ever-accounting' ) : __( 'Add Bill', 'wp-ever-accounting' );
$note     = ever_accounting_get_option( 'bill_note' );
$terms    = ever_accounting_get_option( 'bill_terms' );
$due      = ever_accounting_get_option( 'bill_due', 15 );
$due_date = date_i18n( 'Y-m-d', strtotime( "+ $due days", current_time( 'timestamp' ) ) );//phpcs:ignore
?>
<div class="ea-row">
	<div class="ea-col-7">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Bills', 'wp-ever-accounting' ); ?></h1>
		<?php if ( $bill->exists() ) : ?>
			<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'bills', 'page' => 'ea-expenses', 'action' => 'add' ), admin_url( 'admin.php' ) ) );//phpcs:ignore ?>" class="page-title-action">
				<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
			</a>
		<?php else : ?>
			<a href="<?php echo remove_query_arg( array( 'action', 'id' ) ); ?>" class="page-title-action"><?php esc_html_e( 'View All', 'wp-ever-accounting' ); ?></a>
		<?php endif; ?>
	</div>

	<div class="ea-col-5">

	</div>
</div>
<hr class="wp-header-end">

<form id="ea-bill-form" name="bill" method="post" class="ea-form">
	<div class="ea-card">
		<div class="ea-card__header">
			<h3 class="ea-card__title"><?php echo esc_html( $title ); ?></h3>
			<div>
				<?php if ( $bill->exists() ) : ?>
					<a href="<?php echo esc_url( add_query_arg( 'action', 'view' ) ); ?>" class="button-secondary">
						<?php esc_html_e( 'View Bill', 'wp-ever-accounting' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
		<div class="ea-card__inside">

			<div class="ea-row">
				<?php
				Form::vendor_dropdown(
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
				Form::currency_dropdown(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Currency', 'wp-ever-accounting' ),
						'name'          => 'currency_code',
						'value'         => $bill->get_currency_code(),
						'required'      => true,
						'creatable'     => true,
					)
				);

				Form::text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Bill Date', 'wp-ever-accounting' ),
						'name'          => 'issue_date',
						'value'         => $bill->get_issue_date() ? Formatting::date( $bill->get_issue_date(), 'Y-m-d' ) : date_i18n( 'Y-m-d' ),
						'required'      => true,
						'data_type'     => 'date',
					)
				);

				Form::text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Due Date', 'wp-ever-accounting' ),
						'name'          => 'due_date',
						'value'         => $bill->get_due_date() ? Formatting::date( $bill->get_due_date(), 'Y-m-d' ) : $due_date,
						'required'      => true,
						'data_type'     => 'date',
					)
				);

				Form::text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Bill Number', 'wp-ever-accounting' ),
						'name'          => 'bill_number',
						'value'         => empty( $bill->get_bill_number() ) ? $bill->get_bill_number() : $bill->get_bill_number(),
						'required'      => true,
					)
				);

				Form::text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Order Number', 'wp-ever-accounting' ),
						'name'          => 'order_number',
						'value'         => $bill->get_order_number(),
						'required'      => false,
					)
				);

				Form::category_dropdown(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Category', 'wp-ever-accounting' ),
						'name'          => 'category_id',
						'value'         => $bill->get_category_id(),
						'required'      => true,
						'type'          => 'expense',
						'creatable'     => true,
						'ajax_action'   => 'ever_accounting_get_expense_categories',
						'modal_id'      => 'ea-modal-add-expense-category',
					)
				);
				?>

			</div>

			<?php
			Template::get_admin_template(
				'bills/bill-items',
				array(
					'bill' => $bill,
				)
			);
			?>

			<div class="ea-row ea-mt-20">
				<?php
				Form::textarea(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Note', 'wp-ever-accounting' ),
						'name'          => 'note',
						'value'         => $bill->exists() ? $bill->get_note() : $note,
						'required'      => false,
					)
				);
				FOrm::textarea(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Terms & Conditions', 'wp-ever-accounting' ),
						'name'          => 'terms',
						'value'         => $bill->exists() ? $bill->get_terms() : $terms,
						'required'      => false,
					)
				);
				?>
			</div>


		</div>

		<div class="ea-card__footer">
			<?php submit_button( __( 'Submit', 'wp-ever-accounting' ) ); ?>
		</div>
	</div>

	<?php Form::hidden_input( 'id', $bill->get_id() ); ?>
	<?php Form::hidden_input( 'discount', $bill->get_discount() ); ?>
	<?php Form::hidden_input( 'discount_type', $bill->get_discount_type() ); ?>
	<?php Form::hidden_input( 'action', 'ever_accounting_edit_bill' ); ?>
	<?php wp_nonce_field( 'ea_edit_bill' ); ?>
</form>


<script type="text/template" id="ea-modal-add-discount" data-title="<?php esc_html_e( 'Add Discount', 'wp-ever-accounting' ); ?>">
	<form action="" method="post">
		<?php
		Form::text_input(
			array(
				'label'    => __( 'Discount Amount', 'wp-ever-accounting' ),
				'name'     => 'discount',
				'type'     => 'number',
				'value'    => 0.0000,
				'required' => true,
				'attr'     => array(
					'step' => 0.0001,
					'min'  => 0,
				),
			)
		);
		Form::select(
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
