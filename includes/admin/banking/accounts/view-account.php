<?php
/**
 * Render Single Account
 *
 * @since       1.0.2
 * @subpackage  Admin/Sales/Customers
 * @package     EverAccounting
 * @var int $account_id
 */

defined( 'ABSPATH' ) || exit();

$account = eaccounting_get_account( $account_id );

if ( empty( $account ) || ! $account->exists() ) {
	wp_die( __( 'Sorry, Account does not exist', 'wp-ever-accounting' ) );
}

$tabs   = array(
		'transactions' => __( 'Transactions', 'wp-ever-accounting' ),
		'transfers'    => __( 'Transfers', 'wp-ever-accounting' ),
);
$tabs   = apply_filters( 'eaccounting_account_subtabs', $tabs );
$active = isset( $_GET['subtab'] ) ? $_GET['subtab'] : current( array_keys( $tabs ) );
?>
<div class="ea-page-columns altered ea-single-account">
	<div class="ea-page-columns__content">
		<?php do_action( 'eaccounting_account_profile_top', $account ); ?>
		<div class="ea-card">
			<nav class="nav-tab-wrapper">
				<?php foreach ( $tabs as $tab_id => $title ) : ?>
					<?php
					$url = eaccounting_admin_url(
					array(
						'tab'        => 'accounts',
						'action'     => 'view',
						'account_id' => $account_id,
						'subtab'     => $tab_id,
						)
					);
					?>
					<a class="nav-tab <?php echo $tab_id === $active ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( $url ); ?>">
						<?php echo esc_html( $title ); ?>
					</a>
				<?php endforeach; ?>
			</nav>
			<div class="ea-card__inside">
				<?php do_action( 'eaccounting_account_profile_content_' . $active, $account ); ?>
			</div>
		</div>

	</div>

	<div class="ea-page-columns__aside">
		<?php do_action( 'eaccounting_account_profile_aside', $account ); ?>
	</div>

</div>


