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

<form id="eac-account-form" class="eac-form" method="post">
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Account Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_input_field(
					array(
						'id'            => 'name',
						'label'         => __( 'Name', 'wp-ever-accounting' ),
						'type'          => 'text',
						'placeholder'   => __( 'Enter a name for this account.', 'wp-ever-accounting' ),
						'value'         => $account->get_name(),
						'wrapper_class' => 'eac-col-12',
						'required'      => true,
					)
				);
				eac_input_field(
					array(
						'id'            => 'number',
						'label'         => __( 'Number', 'wp-ever-accounting' ),
						'type'          => 'text',
						'placeholder'   => __( 'Enter an unique number for this account.', 'wp-ever-accounting' ),
						'value'         => $account->get_number(),
						'wrapper_class' => 'eac-col-6',
						'required'      => true,
					)
				);
				eac_input_field(
					array(
						'id'            => 'type',
						'label'         => __( 'Type', 'wp-ever-accounting' ),
						'type'          => 'select',
						'options'       => eac_get_account_types(),
						'value'         => $account->get_type(),
						'wrapper_class' => 'eac-col-6',
						'required'      => true,
					)
				);
				eac_input_field(
					array(
						'id'            => 'currency_code',
						'label'         => __( 'Currency', 'wp-ever-accounting' ),
						'type'          => 'currency',
						'placeholder'   => __( 'Select Currency', 'wp-ever-accounting' ),
						'value'         => $account->get_currency_code(),
						'wrapper_class' => 'eac-col-6',
						'required'      => true,
					)
				);
				eac_input_field(
					array(
						'id'            => 'opening_balance',
						'label'         => __( 'Opening Balance', 'wp-ever-accounting' ),
						'type'          => 'price',
						'placeholder'   => __( 'Enter the opening balance of this account.', 'wp-ever-accounting' ),
						'desc'          => __( 'Initial balance of this account. Use . (dot) as decimal separator and no thousand separator.', 'wp-ever-accounting' ),
						'value'         => $account->get_opening_balance(),
						'wrapper_class' => 'eac-col-6',
						'required'      => true,
					)
				);
				?>
			</div>
		</div>
		<div class="eac-card__separator"></div>
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Bank Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<p><?php esc_html_e( 'You may have multiple bank accounts in more than one banks. Recording information about your bank will make it easier to match the transactions within your bank.', 'wp-ever-accounting' ); ?></p>
			<div class="eac-columns">
				<?php
				eac_input_field(
					array(
						'id'            => 'bank_name',
						'label'         => __( 'Bank Name', 'wp-ever-accounting' ),
						'type'          => 'text',
						'placeholder'   => __( 'Enter the name of the bank.', 'wp-ever-accounting' ),
						'value'         => $account->get_bank_name(),
						'wrapper_class' => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'id'            => 'bank_phone',
						'label'         => __( 'Bank Phone', 'wp-ever-accounting' ),
						'type'          => 'text',
						'placeholder'   => __( 'Enter the phone number of the bank.', 'wp-ever-accounting' ),
						'value'         => $account->get_bank_phone(),
						'wrapper_class' => 'eac-col-6',
					)
				);

				eac_input_field(
					array(
						'id'            => 'bank_address',
						'label'         => __( 'Bank Address', 'wp-ever-accounting' ),
						'type'          => 'textarea',
						'placeholder'   => __( 'Enter the address of the bank.', 'wp-ever-accounting' ),
						'value'         => $account->get_bank_address(),
						'wrapper_class' => 'eac-col-12',
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

