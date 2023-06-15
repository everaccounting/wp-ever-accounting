<?php
/**
 * View: Account Form
 *
 * @subpackage  Admin/View/Accounts
 * @package     EverAccounting
 * @var $account \EverAccounting\Models\Account Account object.
 * @since    1.1.6
 */

defined( 'ABSPATH' ) || exit();

?>

<form id="eac-account-form" class="eac-ajax-form" method="post">
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Account Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_form_field(
					array(
						'id'          => 'name',
						'label'       => __( 'Name', 'wp-ever-accounting' ),
						'type'        => 'text',
						'placeholder' => __( 'XYZ Bank', 'wp-ever-accounting' ),
						'value'       => $account->get_name(),
						'class'       => 'eac-col-6',
						'required'    => true,
					)
				);
				eac_form_field(
					array(
						'id'          => 'number',
						'label'       => __( 'Number', 'wp-ever-accounting' ),
						'type'        => 'text',
						'placeholder' => __( '1234567890', 'wp-ever-accounting' ),
						'value'       => $account->get_number(),
						'class'       => 'eac-col-6',
						'required'    => true,
					)
				);
				eac_form_field(
					array(
						'id'          => 'type',
						'label'       => __( 'Type', 'wp-ever-accounting' ),
						'type'        => 'select',
						'placeholder' => __( 'Select Type', 'wp-ever-accounting' ),
						'options'     => eac_get_account_types(),
						'value'       => $account->get_type(),
						'class'       => 'eac-col-6',
						'required'    => true,
					)
				);
				eac_form_field(
					array(
						'id'          => 'currency_code',
						'label'       => __( 'Currency', 'wp-ever-accounting' ),
						'type'        => 'currency',
						'placeholder' => __( 'Select Account Currency', 'wp-ever-accounting' ),
						'value'       => $account->get_currency_code(),
						'class'       => 'eac-col-6',
						'required'    => true,
						'input_class' => 'eac-select2',
					)
				);
				eac_form_field(
					array(
						'id'          => 'opening_balance',
						'label'       => __( 'Opening Balance', 'wp-ever-accounting' ),
						'type'        => 'decimal',
						'placeholder' => __( '0.00', 'wp-ever-accounting' ),
						'tooltip'     => __( 'Initial balance of this account.', 'wp-ever-accounting' ),
						'value'       => $account->get_opening_balance(),
						'class'       => 'eac-col-6',
					)
				);
				?>
			</div>
		</div>
	</div>
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Bank Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_form_field(
					array(
						'id'          => 'bank_name',
						'label'       => __( 'Bank Name', 'wp-ever-accounting' ),
						'type'        => 'text',
						'placeholder' => __( 'Enter the name of the bank.', 'wp-ever-accounting' ),
						'value'       => $account->get_bank_name(),
						'class'       => 'eac-col-6',
					)
				);
				eac_form_field(
					array(
						'id'          => 'bank_phone',
						'label'       => __( 'Bank Phone', 'wp-ever-accounting' ),
						'type'        => 'text',
						'placeholder' => __( 'Enter the phone number of the bank.', 'wp-ever-accounting' ),
						'value'       => $account->get_bank_phone(),
						'class'       => 'eac-col-6',
					)
				);

				eac_form_field(
					array(
						'id'          => 'bank_address',
						'label'       => __( 'Bank Address', 'wp-ever-accounting' ),
						'type'        => 'textarea',
						'placeholder' => __( 'Enter the address of the bank.', 'wp-ever-accounting' ),
						'value'       => $account->get_bank_address(),
						'class'       => 'eac-col-12',
					)
				);
				?>
			</div>
		</div>
	</div>
	<?php wp_nonce_field( 'eac_edit_account' ); ?>
	<input type="hidden" name="action" value="eac_edit_account">
	<input type="hidden" name="id" value="<?php echo esc_attr( $account->get_id() ); ?>">
</form>

