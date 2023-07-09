<?php
/**
 * View: payment Form
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Items
 * @package     EverAccounting
 * @var $payment \EverAccounting\Models\Payment Payment object.
 */

defined( 'ABSPATH' ) || exit();
$accounts   = eac_get_accounts(
	array(
		'include'  => $payment->get_account_id(),
		'no_count' => true,
	)
);
$categories = eac_get_categories(
	array(
		'include'  => $payment->get_category_id(),
		'no_count' => true,
	)
);
$customers  = eac_get_customers(
	array(
		'include'  => $payment->get_customer_id(),
		'no_count' => true,
	)
);
$invoices   = eac_get_invoices(
	array(
		'include'  => $payment->get_document_id(),
		'no_count' => true,
	)
);
?>
<form id="eac-payment-form" class="eac-ajax-form" method="post">
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
						'value'       => $payment->get_date(),
						'required'    => true,
						'class'       => 'eac-col-6',
					)
				);
				eac_form_field(
					array(
						'type'        => 'select',
						'name'        => 'account_id',
						'label'       => __( 'Account', 'wp-ever-accounting' ),
						'value'       => $payment->get_account_id(),
						'default'     => filter_input( INPUT_GET, 'account_id', FILTER_SANITIZE_NUMBER_INT ),
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
						'name'        => 'amount',
						'label'       => __( 'Amount', 'wp-ever-accounting' ),
						'placeholder' => '0.00',
						'value'       => $payment->get_amount(),
						'required'    => true,
						'class'       => 'eac-col-6',
					)
				);
				// Conversion Rate.
				eac_form_field(
					array(
						'data_type'   => 'decimal',
						'name'        => 'exchange_rate',
						'label'       => __( 'Exchange Rate', 'wp-ever-accounting' ),
						'placeholder' => '1.00',
						'value'       => $payment->get_exchange_rate(),
						'required'    => true,
						'class'       => 'eac-col-6 display-none',
						'prefix'      => sprintf( '1 %s =', eac_get_base_currency() ),
						'suffix'      => sprintf( '%s', $payment->get_currency_code() ),
						'style'       => count( eac_get_currencies() ) > 1 ? '' : 'display:none',
					)
				);
				eac_form_field(
					array(
						'type'        => 'select',
						'name'        => 'category_id',
						'label'       => __( 'Category', 'wp-ever-accounting' ),
						'value'       => $payment->get_category_id(),
						'options'     => wp_list_pluck( $categories, 'formatted_name', 'id' ),
						'placeholder' => __( 'Select category', 'wp-ever-accounting' ),
						'required'    => true,
						'input_class' => 'eac-select2',
						'attrs'       => 'data-action=eac_json_search&data-type=payment_category',
						'class'       => 'eac-col-6',
						'suffix'      => sprintf(
							'<a class="button" href="%s" title="%s"><span class="dashicons dashicons-plus"></span></a>',
							esc_url( eac_action_url( 'action=get_html_response&html_type=edit_payment_category' ) ),
							__( 'Add category', 'wp-ever-accounting' )
						),
					)
				);
				eac_form_field(
					array(
						'type'        => 'select',
						'name'        => 'contact_id',
						'label'       => __( 'Customer', 'wp-ever-accounting' ),
						'value'       => $payment->get_customer_id(),
						'options'     => wp_list_pluck( $customers, 'formatted_name', 'id' ),
						'default'     => filter_input( INPUT_GET, 'customer_id', FILTER_SANITIZE_NUMBER_INT ),
						'placeholder' => __( 'Select customer', 'wp-ever-accounting' ),
						'input_class' => 'eac-select2',
						'attrs'       => 'data-action=eac_json_search&data-type=customer',
						'class'       => 'eac-col-6',
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
						'value'       => $payment->get_document_id(),
						'default'     => filter_input( INPUT_GET, 'document_id', FILTER_SANITIZE_NUMBER_INT ),
						'options'     => wp_list_pluck( $invoices, 'formatted_name', 'id' ),
						'placeholder' => __( 'Select invoice', 'wp-ever-accounting' ),
						'required'    => false,
						'class'       => 'eac-col-6',
						'input_class' => 'eac-select2',
						'attrs'       => 'data-action=eac_json_search&data-type=invoice',
					)
				);
				eac_form_field(
					array(
						'type'        => 'select',
						'name'        => 'payment_method',
						'label'       => __( 'Payment Method', 'wp-ever-accounting' ),
						'value'       => $payment->get_payment_method(),
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
						'value'       => $payment->get_reference(),
						'placeholder' => __( 'Enter reference', 'wp-ever-accounting' ),
						'class'       => 'eac-col-12',
					)
				);
				eac_form_field(
					array(
						'type'        => 'textarea',
						'name'        => 'note',
						'label'       => __( 'Notes', 'wp-ever-accounting' ),
						'value'       => $payment->get_note(),
						'placeholder' => __( 'Enter description', 'wp-ever-accounting' ),
						'class'       => 'eac-col-12',
					)
				);
				?>

			</div>
		</div>
	</div>

	<?php wp_nonce_field( 'eac_edit_payment' ); ?>
	<input type="hidden" name="currency_code" value="<?php echo esc_attr( $payment->get_currency_code() ); ?>">
	<input type="hidden" name="action" value="eac_edit_payment">
	<input type="hidden" name="id" value="<?php echo esc_attr( $payment->get_id() ); ?>">
</form>


