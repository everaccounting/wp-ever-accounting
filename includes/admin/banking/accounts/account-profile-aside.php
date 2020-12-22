<?php
/**
 * Account profile aside
 * @var \EverAccounting\Models\Account $account
 */

defined( 'ABSPATH' ) || exit();

$edit_url = eaccounting_admin_url(
		array(
				'page'       => 'ea-banking',
				'tab'        => 'accounts',
				'action'     => 'edit',
				'account_id' => $account->get_id()
		)
);

?>
	<div class="ea-card">
	<div class="ea-card__header">
		<h3 class="ea-card__title"><?php esc_html_e( 'Account Details', 'wp-ever-accounting' ); ?></h3>
		<a href="<?php echo esc_url( $edit_url ); ?>" class="button-secondary"><?php esc_html_e( 'Edit', 'wp-ever-accounting' ); ?></a>
	</div>

	<div class="ea-list-group">
	<div class="ea-list-group__item">
		<div class="ea-list-group__title"><?php esc_html_e( 'Name', 'wp-ever-accounting' ); ?></div>
		<div class="ea-list-group__text"><?php echo esc_html( $account->get_name() ); ?></div>
	</div>
	<div class="ea-list-group__item">
		<div class="ea-list-group__title"><?php esc_html_e( 'Account Number', 'wp-ever-accounting' ); ?></div>
		<div class="ea-list-group__text"><?php echo esc_html( $account->get_number() ); ?></div>
	</div>
	<div class="ea-list-group__item">
		<div class="ea-list-group__title"><?php esc_html_e( 'Currency', 'wp-ever-accounting' ); ?></div>
		<div class="ea-list-group__text"><?php echo ! empty( $account->get_currency_code() ) ? $account->get_currency_code() : '&mdash;'; ?></div>
	</div>

	<div class="ea-list-group__item">
		<div class="ea-list-group__title"><?php esc_html_e( 'Opening Balance', 'wp-ever-accounting' ); ?></div>
		<div class="ea-list-group__text"><?php echo ! empty( $account->get_opening_balance() ) ? eaccounting_price( $account->get_opening_balance(), $account->get_currency_code() ) : '&mdash;'; ?></div>
	</div>
	<div class="ea-list-group__item">
	<div class="ea-list-group__title"><?php esc_html_e( 'Balance', 'wp-ever-accounting' ); ?></div>
	<div class="ea-list-group__text"><?php echo ! empty( $account->get_balance() ) ? eaccounting_price( $account->get_balance(), $account->get_currency_code() ) : eaccounting_price( $account->get_opening_balance(), $account->get_currency_code() ); ?></div>
		</div>
		<div class="ea-list-group__item">
			<div class="ea-list-group__title"><?php esc_html_e( 'Bank Name', 'wp-ever-accounting' ); ?></div>
			<div class="ea-list-group__text"><?php echo ! empty( $account->get_bank_name() ) ? $account->get_bank_name() : ' &mdash;'; ?></div>
		</div>
		<div class="ea-list-group__item">
			<div class="ea-list-group__title"><?php esc_html_e( 'Bank Phone Number', 'wp-ever-accounting' ); ?></div>
			<div class="ea-list-group__text"><?php echo ! empty( $account->get_bank_phone() ) ? $account->get_bank_phone() : '&mdash;'; ?></div>
		</div>
		<div class="ea-list-group__item">
			<div class="ea-list-group__title"><?php esc_html_e( 'Bank Address', 'wp-ever-accounting' ); ?></div>
			<div class="ea-list-group__text"><?php echo ! empty( $account->get_bank_address() ) ? $account->get_bank_address() : '&mdash;'; ?></div>
		</div>
	</div>

	<div class="ea-card__footer">
		<p class="description">
			<?php
			echo sprintf(
			/* translators: %s date and %s name */
				esc_html__( 'The account was created at %1$s by %2$s', 'wp-ever-accounting' ),
				eaccounting_format_datetime( $account->get_date_created(), 'F m, Y H:i a' ),
				eaccounting_get_username( $account->get_creator_id() )
			);
			?>
		</p>
	</div>

</div>
