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

try {
	$account = new \EverAccounting\Account( $account_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
$default_currency = eaccounting()->settings->get( 'default_currency', 'USD' );
?>
	<div class="ea-form-card">
		<div class="ea-card ea-form-card__header is-compact">
			<h3 class="ea-form-card__header-title"><?php echo $account->exists() ? __( 'Update Account', 'wp-ever-accounting' ) : __( 'Add Account', 'wp-ever-accounting' ); ?></h3>
			<?php echo sprintf( '<a href="%s" class="button button-secondary"><span class="dashicons dashicons-arrow-left-alt"></span> %s</a>',
					eaccounting_admin_url( array( 'tab' => 'accounts', 'page' => 'ea-banking' ) ),
					__( 'Back', 'wp-ever-accounting' )
			); ?>
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
					$currency = eaccounting_get_currency_by_code( $account->get_currency_code( 'edit' ) );
					eaccounting_select( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Account Currency', 'wp-ever-accounting' ),
							'name'          => 'currency_code',
							'class'         => 'currency_code_picker ea-ajax-select2',
							'value'         => $account->get_currency_code( 'edit' ),
							'options'       => empty( $currency ) ? [] : [ $currency->get_code() => sprintf( '%s(%s)', $currency->get_name(), $currency->get_symbol() ) ],
							'default'       => $default_currency,
							'required'      => true,
							'attr'          => array(
									'data-search'      => eaccounting_esc_json( json_encode( array(
											'nonce'  => wp_create_nonce( 'dropdown-search' ),
											'type'   => 'currency',
											'action' => 'eaccounting_dropdown_search',
									) ), true ),
									'data-modal'       => eaccounting_esc_json( json_encode( array(
											'event' => 'ea-init-currency-modal',
											'nonce' => 'edit_currency',
									) ), true ),
									'data-placeholder' => __( 'Select Currency', 'wp-ever-accounting' ),
							)
					) );

					eaccounting_text_input( array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Opening Balance', 'wp-ever-accounting' ),
							'name'          => 'opening_balance',
							'value'         => eaccounting_format_price( $account->get_opening_balance(), $account->get_currency_code() ),
							'default'       => '0.00',
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
							'label'         => __( 'Account Phone', 'wp-ever-accounting' ),
							'name'          => 'bank_phone',
							'value'         => $account->set_bank_phone( 'edit' ),
					) );

					eaccounting_textarea( array(
							'wrapper_class' => 'ea-col-12',
							'label'         => __( 'Bank Address', 'wp-ever-accounting' ),
							'name'          => 'bank_address',
							'value'         => $account->get_bank_address( 'edit' )
					) );

					eaccounting_hidden_input( array(
							'name'  => 'account_id',
							'value' => $account->get_id()
					) );
					?>
				</div>

				<div class="ea-form-submit">
					<?php wp_nonce_field( 'edit_account' ); ?>
					<?php submit_button( __( 'Submit', 'wp-ever-accounting' ) ); ?>
				</div>
			</form>
		</div>

	</div>
<?php
