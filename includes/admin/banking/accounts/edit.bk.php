<?php
/**
 * Admin Account Edit Page
 *
 * @package     EverAccounting
 * @subpackage  Admin/Banking/Accounts
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();
$account_id = isset( $_REQUEST['account_id'] ) ? absint( $_REQUEST['account_id'] ) : null;
$account    = new EAccounting_Currency( $account_id );
$currencies =
?>
<div class="ea-form-card">
	<div class="ea-card ea-form-card__header is-compact">
		<h3 class="ea-form-card__header-title"><?php echo $account->exists()? __('Update Currency', 'wp-ever-accounting'): __('Add Currency', 'wp-ever-accounting'); ?></h3>
	</div>

	<div class="ea-card">
		<form id="ea-account-form" method="post">
			<div class="ea-row">
				<?php
				eaccounting_text_input( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Account Name', 'wp-ever-accounting' ),
						'name'          => 'name',
						'value'         => $account->get_name( 'edit' ),
						'required'      => true,
				) );
				eaccounting_text_input( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Account Number', 'wp-ever-accounting' ),
						'name'          => 'number',
						'value'         => $account->get_number( 'edit' ),
						'required'      => true,
				) );

				eaccounting_select( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Account Currency', 'wp-ever-accounting' ),
						'name'          => 'currency_code',
						'value'         => $account->get_currency_code( 'edit' ),
						'required'      => true,
				) );
				eaccounting_text_input( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Opening Balance', 'wp-ever-accounting' ),
						'name'          => 'opening_balance',
						'value'         => $account->get_opening_balance( 'edit' ),
						'required'      => true,
				) );
				eaccounting_text_input( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Bank Name', 'wp-ever-accounting' ),
						'name'          => 'bank_name',
						'value'         => $account->get_bank_name( 'edit' ),
				) );
				eaccounting_text_input( array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Bank Phone', 'wp-ever-accounting' ),
						'name'          => 'bank_phone',
						'value'         => $account->get_bank_phone( 'edit' ),
				) );
				eaccounting_textarea( array(
						'wrapper_class' => 'ea-col-12',
						'label'         => __( 'Bank Address', 'wp-ever-accounting' ),
						'name'          => 'bank_address',
						'value'         => $account->get_bank_address( 'edit' ),
				) );

				?>

				<button class="button-primary account-open">open Account</button>

			</div>
		</form>
	</div>

</div>
