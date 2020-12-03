<?php
/**
 * Admin Account Edit Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Banking/Accounts
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();


$account_id = isset( $_REQUEST['account_id'] ) ? absint( $_REQUEST['account_id'] ) : null;
try {
	$account = new \EverAccounting\Models\Account( $account_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}

$back_url = remove_query_arg( array( 'action', 'id' ) );
?>

<div class="ea-form-card">
	<div class="ea-card ea-form-card__header is-compact">
		<h3 class="ea-form-card__header-title"><?php echo $account->exists() ? __( 'Update Account', 'wp-ever-accounting' ) : __( 'Add Account', 'wp-ever-accounting' ); ?></h3>
		<a href="<?php echo $back_url; ?>" class="button button-secondary"><span class="dashicons dashicons-arrow-left-alt"></span><?php _e( 'Back', 'wp-ever-accounting' ); ?></a>
	</div>

	<div class="ea-card">
		<form id="ea-account-form" method="post">
			<div class="ea-row">
				<?php
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Account Name', 'wp-ever-accounting' ),
						'name'          => 'name',
						'value'         => $account->get_name( 'edit' ),
						'required'      => true,
						'placeholder'   => __( 'Enter account name', 'wp-ever-accounting' ),
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Account Number', 'wp-ever-accounting' ),
						'name'          => 'number',
						'value'         => $account->get_number( 'edit' ),
						'required'      => true,
						'placeholder'   => __( 'Enter account number', 'wp-ever-accounting' ),
					)
				);
				eaccounting_currency_dropdown(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Account Currency', 'wp-ever-accounting' ),
						'name'          => 'currency_code',
						'value'         => $account->get_currency_code(),
						'required'      => true,
						'creatable'     => true,
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Opening Balance', 'wp-ever-accounting' ),
						'name'          => 'opening_balance',
						'value'         => $account->get_opening_balance(),
						'default'       => '0.00',
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Bank Name', 'wp-ever-accounting' ),
						'name'          => 'bank_name',
						'value'         => $account->get_bank_name( 'edit' ),
						'placeholder'   => __( 'Enter bank name', 'wp-ever-accounting' ),
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Bank Phone', 'wp-ever-accounting' ),
						'name'          => 'bank_phone',
						'value'         => $account->get_bank_phone( 'edit' ),
						'placeholder'   => __( 'Enter bank phone', 'wp-ever-accounting' ),
					)
				);
				eaccounting_textarea(
					array(
						'wrapper_class' => 'ea-col-12',
						'label'         => __( 'Bank Address', 'wp-ever-accounting' ),
						'name'          => 'bank_address',
						'value'         => $account->get_bank_address( 'edit' ),
						'placeholder'   => __( 'Enter bank address', 'wp-ever-accounting' ),
					)
				);
				eaccounting_hidden_input(
					array(
						'name'  => 'id',
						'value' => $account->get_id(),
					)
				);
				eaccounting_hidden_input(
					array(
						'name'  => 'action',
						'value' => 'eaccounting_edit_account',
					)
				);
				?>
			</div>
			<?php

			wp_nonce_field( 'ea_edit_account' );

			submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
			?>
		</form>
	</div>

</div>
