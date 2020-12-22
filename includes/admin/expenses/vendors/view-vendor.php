<?php
/**
 * Render Single Vendor
 *
 * @since       1.0.2
 * @subpackage  Admin/Expenses/Vendors
 * @package     EverAccounting
 * @var int $vendor_id
 */

defined( 'ABSPATH' ) || exit();

$vendor = eaccounting_get_vendor( $vendor_id );

if ( empty( $vendor ) || ! $vendor->exists() ) {
	wp_die( __( 'Sorry, Vendor does not exist', 'wp-ever-accounting' ) );
}

$tabs   = array(
	'transactions' => __( 'Transactions', 'wp-ever-accounting' ),
	'bills'        => __( 'Bills', 'wp-ever-accounting' ),
	'notes'        => __( 'Notes', 'wp-ever-accounting' ),
);
$tabs   = apply_filters( 'eaccounting_vendor_subtabs', $tabs );
$active = isset( $_GET['subtab'] ) ? $_GET['subtab'] : current( array_keys( $tabs ) );

?>
<div class="ea-page-columns altered ea-single-vendor">
	<div class="ea-page-columns__content">
		<?php do_action( 'eaccounting_vendor_profile_top', $vendor ); ?>
		<div class="ea-card">
			<nav class="nav-tab-wrapper">
				<?php foreach ( $tabs as $tab_id => $title ) : ?>
					<?php
					$url = eaccounting_admin_url(
						array(
							'tab'       => 'vendors',
							'action'    => 'view',
							'vendor_id' => $vendor_id,
							'subtab'    => $tab_id,
						)
					);
					?>
					<a class="nav-tab <?php echo $tab_id === $active ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( $url ); ?>">
						<?php echo esc_html( $title ); ?>
					</a>
				<?php endforeach; ?>
			</nav>
			<div class="ea-card__inside">
				<?php do_action( 'eaccounting_vendor_profile_content_' . $active, $vendor ); ?>
			</div>
		</div>

	</div>

	<div class="ea-page-columns__aside">
		<?php do_action( 'eaccounting_vendor_profile_aside', $vendor ); ?>
	</div>

</div>


