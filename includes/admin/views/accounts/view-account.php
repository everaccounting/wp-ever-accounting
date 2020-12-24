<?php
/**
 * Render Single Account
 * Page: Banking
 * Tab: Accounts
 *
 * @since       1.0.2
 * @subpackage  Admin/Views/Accounts
 * @package     EverAccounting
 * @var int $account_id
 */

defined( 'ABSPATH' ) || exit();

$account = eaccounting_get_account( $account_id );

if ( empty( $account ) || ! $account->exists() ) {
	wp_die( __( 'Sorry, Account does not exist', 'wp-ever-accounting' ) );
}

$sections        = array(
	'transactions' => __( 'Transactions', 'wp-ever-accounting' ),
	'transfers'    => __( 'Transfers', 'wp-ever-accounting' ),
);
$sections        = apply_filters( 'eaccounting_account_sections', $sections );
$first_section   = current( array_keys( $sections ) );
$current_section = ! empty( $_GET['section'] ) && array_key_exists( $_GET['section'], $sections ) ? sanitize_title( $_GET['section'] ) : $first_section;
$edit_url        = eaccounting_admin_url(
	array(
		'page'       => 'ea-banking',
		'tab'        => 'accounts',
		'action'     => 'edit',
		'account_id' => $account->get_id(),
	)
);
?>
<div class="ea-page-columns altered ea-single-account">
	<div class="ea-page-columns__content">
		<div class="ea-row">
			<div class="ea-col">
				<div class="ea-card">
					<div class="ea-card__inside">
						Lorem ipsum dolor sit amet, consectetur adipisicing elit. Fugit, numquam.
					</div>
				</div>
			</div>
			<div class="ea-col">
				<div class="ea-card">
					<div class="ea-card__inside">
						Lorem ipsum dolor sit amet, consectetur adipisicing elit. Fugit, numquam.
					</div>
				</div>
			</div>
			<div class="ea-col">
				<div class="ea-card">
					<div class="ea-card__inside">
						Lorem ipsum dolor sit amet, consectetur adipisicing elit. Fugit, numquam.
					</div>
				</div>
			</div>
		</div>
		<div class="ea-card">
			<nav class="nav-tab-wrapper">
				<?php foreach ( $sections as $section_id => $section_title ) : ?>
					<?php
					$url = eaccounting_admin_url(
						array(
							'tab'        => 'accounts',
							'action'     => 'view',
							'account_id' => $account_id,
							'section'    => $section_id,
						)
					);
					?>
					<a class="nav-tab <?php echo $section_id === $current_section ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( $url ); ?>">
						<?php echo esc_html( $section_title ); ?>
					</a>
				<?php endforeach; ?>
			</nav>
			<div class="ea-card__inside">
				<?php
				switch ( $current_section ) {
					case 'transactions':
					case 'transfers':
						include dirname( __FILE__ ) . '/account-sections/' . sanitize_file_name( $current_section ) . '.php';
						break;
					default:
						do_action( 'eaccounting_account_section_' . $current_section, $account );
						break;
				}
				?>
			</div>
		</div>

	</div>

	<div class="ea-page-columns__aside">
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
	</div>

</div>


