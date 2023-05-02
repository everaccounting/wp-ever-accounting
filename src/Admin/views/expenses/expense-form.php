<?php
/**
 * View: Expense Form
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Items
 * @package     EverAccounting
 * @var $expense \EverAccounting\Models\Expense Expense object.
 */

defined( 'ABSPATH' ) || exit();

?>
<form id="eac-expense-form" class="eac-form" method="post">
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Basic Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_input_field(
					array(
						'type'          => 'account',
						'name'          => 'account_id',
						'label'         => __( 'Account', 'wp-ever-accounting' ),
						'value'         => $expense->get_account_id(),
						'placeholder'   => __( 'Select account', 'wp-ever-accounting' ),
						'required'      => true,
						'wrapper_class' => 'eac-col-6',
						'suffix'        => '<button class="eac-add-account" type="button"><span class="dashicons dashicons-plus"></span></button>',
					)
				);
				eac_input_field(
					array(
						'type'          => 'price',
						'name'          => 'amount',
						'label'         => __( 'Amount', 'wp-ever-accounting' ),
						'placeholder'   => '0.00',
						'value'         => $expense->get_amount(),
						'required'      => true,
						'wrapper_class' => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'type'          => 'date',
						'name'          => 'payment_date',
						'label'         => __( 'Date', 'wp-ever-accounting' ),
						'placeholder'   => 'YYYY-MM-DD',
						'value'         => $expense->get_payment_date(),
						'required'      => true,
						'wrapper_class' => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'type'          => 'category',
						'name'          => 'category_id',
						'label'         => __( 'Category', 'wp-ever-accounting' ),
						'value'         => $expense->get_category_id(),
						'placeholder'   => __( 'Select category', 'wp-ever-accounting' ),
						'required'      => true,
						'subtype'       => 'expense',
						'wrapper_class' => 'eac-col-6',
						'suffix'        => '<button class="eac-add-category" type="button" data-type="expense"><span class="dashicons dashicons-plus"></span></button>',
					)
				);
				eac_input_field(
					array(
						'type'          => 'bill',
						'name'          => 'document_id',
						'label'         => __( 'Bill', 'wp-ever-accounting' ),
						'value'         => $expense->get_document_id(),
						'placeholder'   => __( 'Select Bill', 'wp-ever-accounting' ),
						'required'      => false,
						'wrapper_class' => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'type'          => 'vendor',
						'name'          => 'vendor_id',
						'label'         => __( 'Vendor', 'wp-ever-accounting' ),
						'value'         => $expense->get_vendor_id(),
						'placeholder'   => __( 'Select vendor', 'wp-ever-accounting' ),
						'wrapper_class' => 'eac-col-6',
						'suffix'        => '<button class="eac-add-vendor" type="button"><span class="dashicons dashicons-plus"></span></button>',
					)
				);
				eac_input_field(
					array(
						'type'          => 'select',
						'name'          => 'payment_method',
						'label'         => __( 'Payment Method', 'wp-ever-accounting' ),
						'value'         => $expense->get_payment_method(),
						'options'       => eac_get_payment_methods(),
						'placeholder'   => __( 'Select payment method', 'wp-ever-accounting' ),
						'wrapper_class' => 'eac-col-6',
					)
				);
				?>
			</div>
		</div>
		<div class="eac-card__separator"></div>
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Extra Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_input_field(
					array(
						'type'          => 'text',
						'name'          => 'reference',
						'label'         => __( 'Reference', 'wp-ever-accounting' ),
						'value'         => $expense->get_reference(),
						'placeholder'   => __( 'Enter reference', 'wp-ever-accounting' ),
						'wrapper_class' => 'eac-col-12',
					)
				);
				eac_input_field(
					array(
						'type'          => 'textarea',
						'name'          => 'note',
						'label'         => __( 'Notes', 'wp-ever-accounting' ),
						'value'         => $expense->get_note(),
						'placeholder'   => __( 'Enter description', 'wp-ever-accounting' ),
						'wrapper_class' => 'eac-col-12',
					)
				);
				?>
			</div>
		</div>
	</div>

	<?php wp_nonce_field( 'eac_edit_expense' ); ?>
	<input type="hidden" name="action" value="eac_edit_expense">
	<input type="hidden" name="id" value="<?php echo esc_attr( $expense->get_id() ); ?>">
</form>


