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
$accounts   = eac_get_accounts(
	array(
		'include'  => $expense->get_account_id(),
		'no_count' => true,
	)
);
$categories = eac_get_categories(
	array(
		'include'  => $expense->get_category_id(),
		'no_count' => true,
	)
);
?>
<form id="eac-expense-form" class="eac-ajax-form" method="post">
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Basic Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_form_field(
					array(
						'data_type'   => 'date',
						'name'        => 'date',
						'label'       => __( 'Date', 'wp-ever-accounting' ),
						'placeholder' => 'YYYY-MM-DD',
						'value'       => $expense->get_date(),
						'required'    => true,
						'class'       => 'eac-col-6',
					)
				);
				eac_form_field(
					array(
						'type'        => 'select',
						'name'        => 'account_id',
						'label'       => __( 'Account', 'wp-ever-accounting' ),
						'value'       => $expense->get_account_id(),
						'placeholder' => __( 'Select account', 'wp-ever-accounting' ),
						'options'     => wp_list_pluck( $accounts, 'formatted_name', 'id' ),
						'required'    => true,
						'class'       => 'eac-col-6',
						'input_class' => 'eac-select2',
						'attrs'       => 'data-action=eac_json_search&data-type=account',
						'suffix'      => sprintf(
							'<a class="button" href="%s" title="%s"><span class="dashicons dashicons-plus"></span></a>',
							esc_url( eac_action_url( 'action=get_html_response&html_type=edit_account' ) ),
							__( 'Add account', 'wp-ever-accounting' )
						),
					)
				);
				eac_form_field(
					array(
						'type'        => 'text',
						'name'        => 'amount',
						'label'       => __( 'Amount', 'wp-ever-accounting' ),
						'placeholder' => '0.00',
						'value'       => $expense->get_amount(),
						'required'    => true,
						'class'       => 'eac-col-6',
					)
				);
				// Conversion Rate.
				eac_form_field(
					array(
						'type'        => 'text',
						'name'        => 'exchange_rate',
						'label'       => __( 'Exchange Rate', 'wp-ever-accounting' ),
						'placeholder' => '1.00',
						'value'       => $expense->get_exchange_rate(),
						'required'    => true,
						'class'       => 'eac-col-6 display-none',
						'prefix'      => sprintf( '1 %s =', eac_get_base_currency() ),
						'suffix'      => sprintf( '%s', $expense->get_currency_code() ),
						'style'       => count( eac_get_currencies() ) > 1 ? '' : 'display:none',
					)
				);
				eac_form_field(
					array(
						'type'        => 'select',
						'name'        => 'category_id',
						'label'       => __( 'Category', 'wp-ever-accounting' ),
						'value'       => $expense->get_category_id(),
						'placeholder' => __( 'Select category', 'wp-ever-accounting' ),
						'options'     => wp_list_pluck( $categories, 'formatted_name', 'id' ),
						'required'    => true,
						'class'       => 'eac-col-6',
						'input_class' => 'eac-select2',
						'attrs'       => 'data-action=eac_json_search&data-type=expense_category',
						'suffix'      => sprintf(
							'<a class="button" href="%s" title="%s"><span class="dashicons dashicons-plus"></span></a>',
							esc_url( eac_action_url( 'action=get_html_response&html_type=edit_expense_category' ) ),
							__( 'Add category', 'wp-ever-accounting' )
						),
					)
				);
				eac_form_field(
					array(
						'type'        => 'select',
						'name'        => 'vendor_id',
						'label'       => __( 'Vendor', 'wp-ever-accounting' ),
						'value'       => $expense->get_vendor_id(),
						'placeholder' => __( 'Select vendor', 'wp-ever-accounting' ),
						'class'       => 'eac-col-6',
						'input_class' => 'eac-select2',
						'attrs'       => 'data-action=eac_json_search&data-type=vendor',
						'suffix'      => sprintf(
							'<a class="button" href="%s" title="%s"><span class="dashicons dashicons-plus"></span></a>',
							esc_url( eac_action_url( 'action=get_html_response&html_type=edit_customer' ) ),
							__( 'Add customer', 'wp-ever-accounting' )
						),
					)
				);
				eac_form_field(
					array(
						'type'        => 'select',
						'name'        => 'document_id',
						'label'       => __( 'Invoice', 'wp-ever-accounting' ),
						'value'       => $expense->get_document_id(),
						'placeholder' => __( 'Select invoice', 'wp-ever-accounting' ),
						'required'    => false,
						'class'       => 'eac-col-6',
						'input_class' => 'eac-select2',
						'attrs'       => 'data-action=eac_json_search&data-type=bill',
					)
				);
				eac_form_field(
					array(
						'type'        => 'select',
						'name'        => 'payment_method',
						'label'       => __( 'Payment Method', 'wp-ever-accounting' ),
						'value'       => $expense->get_payment_method(),
						'options'     => eac_get_payment_methods(),
						'placeholder' => __( 'Select payment method', 'wp-ever-accounting' ),
						'class'       => 'eac-col-6',
					)
				);
				?>
			</div>
		</div>
	</div>

	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Extra Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_form_field(
					array(
						'type'        => 'text',
						'name'        => 'reference',
						'label'       => __( 'Reference', 'wp-ever-accounting' ),
						'value'       => $expense->get_reference(),
						'placeholder' => __( 'Enter reference', 'wp-ever-accounting' ),
						'class'       => 'eac-col-12',
					)
				);
				eac_form_field(
					array(
						'type'        => 'textarea',
						'name'        => 'note',
						'label'       => __( 'Notes', 'wp-ever-accounting' ),
						'value'       => $expense->get_note(),
						'placeholder' => __( 'Enter description', 'wp-ever-accounting' ),
						'class'       => 'eac-col-12',
					)
				);
				?>

			</div>
		</div>
	</div>

	<?php wp_nonce_field( 'eac_edit_expense' ); ?>
	<input type="hidden" name="currency_code" value="<?php echo esc_attr( $expense->get_currency_code() ); ?>">
	<input type="hidden" name="action" value="eac_edit_expense">
	<input type="hidden" name="id" value="<?php echo esc_attr( $expense->get_id() ); ?>">
</form>


