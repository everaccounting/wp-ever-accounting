<?php
/**
 * Render Single customer
 *
 * @since       1.0.2
 * @subpackage  Admin/Sales/Customers
 * @package     EverAccounting
 * @var int $customer_id
 */

defined( 'ABSPATH' ) || exit();

$customer = eaccounting_get_customer( $customer_id );

if ( empty( $customer ) || ! $customer->exists() ) {
	wp_die( __( 'Sorry, Customer does not exist', 'wp-ever-accounting' ) );
}

$tabs   = array(
	'transactions' => __( 'Transactions', 'wp-ever-accounting' ),
	'invoices'     => __( 'Invoices', 'wp-ever-accounting' ),
	'notes'        => __( 'Notes', 'wp-ever-accounting' ),
);
$tabs   = apply_filters( 'eaccounting_customer_subtabs', $tabs );
$active = isset( $_GET['subtab'] ) ? $_GET['subtab'] : current( array_keys( $tabs ) );

?>
<div class="ea-page-columns altered ea-single-customer">
	<div class="ea-page-columns__content">
		<?php do_action( 'eaccounting_customer_profile_top', $customer ); ?>
		<div class="ea-card">
			<nav class="nav-tab-wrapper">
				<?php foreach ( $tabs as $tab_id => $title ) : ?>
					<?php
					$url = eaccounting_admin_url(
						array(
							'tab'         => 'customers',
							'action'      => 'view',
							'customer_id' => $customer_id,
							'subtab'      => $tab_id,
						)
					);
					?>
					<a class="nav-tab <?php echo $tab_id === $active ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( $url ); ?>">
						<?php echo esc_html( $title ); ?>
					</a>
				<?php endforeach; ?>
			</nav>
			<div class="ea-card__inside">
				<?php do_action( 'eaccounting_customer_profile_content_' . $active, $customer ); ?>
			</div>
		</div>

	</div>

	<div class="ea-page-columns__aside">
		<?php do_action( 'eaccounting_customer_profile_aside', $customer ); ?>
	</div>

</div>
