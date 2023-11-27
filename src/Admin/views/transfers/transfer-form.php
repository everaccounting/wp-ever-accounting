<?php
/**
 * View: Transfer Form
 *
 * @since    1.1.6
 * @package     EverAccounting
 * @subpackage  Admin/View/Vendors
 *
 * @var \EverAccounting\Models\Transfer $transfer Transfer object.
 */

defined( 'ABSPATH' ) || exit();
$accounts = eac_get_accounts(
	array(
		'include' => [ $transfer->get_from_account_id(), $transfer->get_to_account_id() ],
	)
);
?>
<form id="eac-transfer-form" class="eac-ajax-form" method="post">
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Basic Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_form_field(
					array(
						'type'        => 'select',
						'name'        => 'from_account_id',
						'label'       => __( 'From Account', 'wp-ever-accounting' ),
						'value'       => $transfer->get_from_account_id(),
						'options'     => wp_list_pluck( $accounts, 'formatted_name', 'id' ),
						'placeholder' => __( 'Select account', 'wp-ever-accounting' ),
						'required'    => true,
						'class'       => 'eac-col-6',
						'input_class' => 'eac-select2',
						'attrs'       => 'data-action=eac_json_search&data-type=account',
					)
				);
				eac_form_field(
					array(
						'type'        => 'select',
						'name'        => 'to_account_id',
						'label'       => __( 'To Account', 'wp-ever-accounting' ),
						'value'       => $transfer->get_to_account_id(),
						'options'     => wp_list_pluck( $accounts, 'formatted_name', 'id' ),
						'placeholder' => __( 'Select account', 'wp-ever-accounting' ),
						'required'    => true,
						'class'       => 'eac-col-6',
						'input_class' => 'eac-select2',
						'attrs'       => 'data-action=eac_json_search&data-type=account',
						'suffix'      => sprintf(
							'<a class="button" href="%s" title="%s"><span class="dashicons dashicons-plus"></span></a>',
							esc_url( eac_action_url( 'action=get_html_response&html_type=edit_account' ) ),
							__( 'Add Account', 'wp-ever-accounting' )
						),
					)
				);
				eac_form_field(
					array(
						'type'        => 'text',
						'name'        => 'amount',
						'label'       => __( 'Amount', 'wp-ever-accounting' ),
						'placeholder' => '0.00',
						'value'       => $transfer->get_amount(),
						'required'    => true,
						'class'       => 'eac-col-6',
					)
				);
				eac_form_field(
					array(
						'data_type'   => 'date',
						'name'        => 'date',
						'label'       => __( 'Date', 'wp-ever-accounting' ),
						'placeholder' => 'YYYY-MM-DD',
						'value'       => $transfer->get_date(),
						'required'    => true,
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
						'type'        => 'select',
						'name'        => 'payment_method',
						'label'       => __( 'Payment Method', 'wp-ever-accounting' ),
						'value'       => $transfer->get_payment_method(),
						'options'     => eac_get_payment_methods(),
						'placeholder' => __( 'Select payment method', 'wp-ever-accounting' ),
						'class'       => 'eac-col-6',
					)
				);
				eac_form_field(
					array(
						'type'        => 'text',
						'name'        => 'reference',
						'label'       => __( 'Reference', 'wp-ever-accounting' ),
						'value'       => $transfer->get_reference(),
						'placeholder' => __( 'Enter reference', 'wp-ever-accounting' ),
						'class'       => 'eac-col-6',
					)
				);
				eac_form_field(
					array(
						'type'        => 'textarea',
						'name'        => 'note',
						'label'       => __( 'Notes', 'wp-ever-accounting' ),
						'value'       => $transfer->get_note(),
						'placeholder' => __( 'Enter description', 'wp-ever-accounting' ),
						'class'       => 'eac-col-12',
					)
				);
				?>

			</div>
		</div>
	</div>

	<?php wp_nonce_field( 'eac_edit_transfer' ); ?>
	<input type="hidden" name="currency_code" value="<?php echo esc_attr( $transfer->get_currency_code() ); ?>">
	<input type="hidden" name="action" value="eac_edit_transfer">
	<input type="hidden" name="id" value="<?php echo esc_attr( $transfer->get_id() ); ?>">
</form>


