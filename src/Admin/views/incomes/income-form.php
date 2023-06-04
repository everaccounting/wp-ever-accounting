<?php
/**
 * View: Income Form
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Items
 * @package     EverAccounting
 * @var $income \EverAccounting\Models\Income Payment object.
 */

defined( 'ABSPATH' ) || exit();

?>
<form id="eac-income-form" class="eac-form" method="post">
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Basic Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_input_field(
					array(
						'type'        => 'date',
						'name'        => 'payment_date',
						'label'       => __( 'Date', 'wp-ever-accounting' ),
						'placeholder' => 'YYYY-MM-DD',
						'value'       => $income->get_payment_date(),
						'required'    => true,
						'class'       => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'type'        => 'account',
						'name'        => 'account_id',
						'label'       => __( 'Account', 'wp-ever-accounting' ),
						'value'       => $income->get_account_id(),
						'placeholder' => __( 'Select account', 'wp-ever-accounting' ),
						'required'    => true,
						'class'       => 'eac-col-6',
						'suffix'      => sprintf(
							'<a class="button" href="%s" title="%s"><span class="dashicons dashicons-plus"></span></a>',
							esc_url( eac_action_url( 'action=get_html_response&html_type=edit_account' ) ),
							__( 'Add account', 'wp-ever-accounting' )
						),
					)
				);
				eac_input_field(
					array(
						'type'        => 'price',
						'name'        => 'amount',
						'label'       => __( 'Amount', 'wp-ever-accounting' ),
						'placeholder' => '0.00',
						'value'       => $income->get_amount(),
						'required'    => true,
						'class'       => 'eac-col-6',
						'attrs'       => [ 'data-currency' => $income->get_currency_code() ],
					)
				);
				// currency.
				eac_input_field(
					array(
						'type'        => 'currency',
						'name'        => 'currency_code',
						'label'       => __( 'Currency', 'wp-ever-accounting' ),
						'value'       => $income->get_currency_code(),
						'placeholder' => __( 'Select currency', 'wp-ever-accounting' ),
						'required'    => true,
						'class'       => 'eac-col-6',
					)
				);
				// currency rate.
				eac_input_field(
					array(
						'type'        => 'text',
						'name'        => 'currency_rate',
						'label'       => __( 'Currency Rate', 'wp-ever-accounting' ),
						'placeholder' => '1.00',
						'value'       => $income->get_currency_rate(),
						'required'    => true,
						'class'       => 'eac-col-6',
						'prefix'      => sprintf( '1 %s =', eac_get_base_currency() ),
					)
				);
				eac_input_field(
					array(
						'type'        => 'income_cat',
						'name'        => 'category_id',
						'label'       => __( 'Category', 'wp-ever-accounting' ),
						'value'       => $income->get_category_id(),
						'placeholder' => __( 'Select category', 'wp-ever-accounting' ),
						'required'    => true,
						'query_args'  => array( 'term_type' => 'income' ),
						'class'       => 'eac-col-6',
						'suffix'      => sprintf(
							'<a class="button" href="%s" title="%s"><span class="dashicons dashicons-plus"></span></a>',
							esc_url( eac_action_url( 'action=get_html_response&html_type=edit_category&type=income' ) ),
							__( 'Add category', 'wp-ever-accounting' )
						),
					)
				);
				eac_input_field(
					array(
						'type'        => 'customer',
						'name'        => 'customer_id',
						'label'       => __( 'Customer', 'wp-ever-accounting' ),
						'value'       => $income->get_customer_id(),
						'placeholder' => __( 'Select customer', 'wp-ever-accounting' ),
						'class'       => 'eac-col-6',
						'suffix'      => sprintf(
							'<a class="button" href="%s" title="%s"><span class="dashicons dashicons-plus"></span></a>',
							esc_url( eac_action_url( 'action=get_html_response&html_type=edit_customer' ) ),
							__( 'Add customer', 'wp-ever-accounting' )
						),
					)
				);
				eac_input_field(
					array(
						'type'        => 'invoice',
						'name'        => 'document_id',
						'label'       => __( 'Invoice', 'wp-ever-accounting' ),
						'value'       => $income->get_document_id(),
						'placeholder' => __( 'Select invoice', 'wp-ever-accounting' ),
						'required'    => false,
						'class'       => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'type'        => 'select',
						'name'        => 'payment_method',
						'label'       => __( 'Payment Method', 'wp-ever-accounting' ),
						'value'       => $income->get_payment_method(),
						'options'     => eac_get_payment_methods(),
						'placeholder' => __( 'Select payment method', 'wp-ever-accounting' ),
						'class'       => 'eac-col-6',
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
						'type'        => 'text',
						'name'        => 'reference',
						'label'       => __( 'Reference', 'wp-ever-accounting' ),
						'value'       => $income->get_reference(),
						'placeholder' => __( 'Enter reference', 'wp-ever-accounting' ),
						'class'       => 'eac-col-12',
					)
				);
				eac_input_field(
					array(
						'type'        => 'textarea',
						'name'        => 'payment_note',
						'label'       => __( 'Notes', 'wp-ever-accounting' ),
						'value'       => $income->get_payment_note(),
						'placeholder' => __( 'Enter description', 'wp-ever-accounting' ),
						'class'       => 'eac-col-12',
					)
				);
				?>

			</div>
		</div>
	</div>

	<?php wp_nonce_field( 'eac_edit_income' ); ?>
	<input type="hidden" name="action" value="eac_edit_income">
	<input type="hidden" name="id" value="<?php echo esc_attr( $income->get_id() ); ?>">
</form>


