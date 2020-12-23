<?php
/**
 * Render Single Vendor
 * Page: Expenses
 * Tab: Vendors
 *
 * @since       1.0.2
 * @subpackage  Admin/Views/Vendors
 * @package     EverAccounting
 * @var int $vendor_id
 */

defined( 'ABSPATH' ) || exit();

$vendor = eaccounting_get_vendor( $vendor_id );

if ( empty( $vendor ) || ! $vendor->exists() ) {
	wp_die( __( 'Sorry, Vendor does not exist', 'wp-ever-accounting' ) );
}

$sections   = array(
	'transactions' => __( 'Transactions', 'wp-ever-accounting' ),
	'bills'        => __( 'Bills', 'wp-ever-accounting' ),
	'notes'        => __( 'Notes', 'wp-ever-accounting' ),
);

$sections        = apply_filters( 'eaccounting_vendor_sections', $sections );
$first_section   = current( array_keys( $sections ) );
$current_section = ! empty( $_GET['section'] ) && array_key_exists( $_GET['section'], $sections ) ? sanitize_title( $_GET['section'] ) : $first_section;
$edit_url        = eaccounting_admin_url(
	array(
		'page'        => 'ea-expenses',
		'tab'         => 'vendors',
		'action'      => 'edit',
		'vendor_id' => $vendor->get_id(),
	)
);

?>
<div class="ea-page-columns altered ea-single-vendor">
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
							'tab'       => 'vendors',
							'action'    => 'view',
							'vendor_id' => $vendor_id,
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
					case 'expenses':
						include dirname( __FILE__ ) . '/vendor-sections/' . sanitize_file_name( $current_section ) . '.php';
						break;
					default:
						do_action( 'eaccounting_vendor_section_' . $current_section, $vendor );
						break;
				}
				?>
			</div>
		</div>

	</div>

	<div class="ea-page-columns__aside">
		<div class="ea-card">
			<div class="ea-card__header">
				<h3 class="ea-card__title"><?php esc_html_e( 'Vendor Details', 'wp-ever-accounting' ); ?></h3>
				<a href="<?php echo esc_url( $edit_url ); ?>" class="button-secondary"><?php esc_html_e( 'Edit', 'wp-ever-accounting' ); ?></a>
			</div>

			<div class="ea-card__inside">
				<div class="ea-avatar ea-center-block">
					<img src="<?php echo esc_url( $vendor->get_avatar_url() ); ?>" alt="<?php echo esc_html( $vendor->get_name() ); ?>">
				</div>
			</div>

			<div class="ea-list-group">
				<div class="ea-list-group__item">
					<div class="ea-list-group__title"><?php esc_html_e( 'Name', 'wp-ever-accounting' ); ?></div>
					<div class="ea-list-group__text"><?php echo esc_html( $vendor->get_name() ); ?></div>
				</div>
				<div class="ea-list-group__item">
					<div class="ea-list-group__title"><?php esc_html_e( 'Currency', 'wp-ever-accounting' ); ?></div>
					<div class="ea-list-group__text"><?php echo ! empty( $vendor->get_currency_code() ) ? $vendor->get_currency_code() : '&mdash;'; ?></div>
				</div>
				<div class="ea-list-group__item">
					<div class="ea-list-group__title"><?php esc_html_e( 'Birthdate', 'wp-ever-accounting' ); ?></div>
					<div class="ea-list-group__text"><?php echo ! empty( $vendor->get_birth_date() ) ? eaccounting_date( $vendor->get_birth_date() ) : '&mdash;'; ?></div>
				</div>
				<div class="ea-list-group__item">
					<div class="ea-list-group__title"><?php esc_html_e( 'Phone', 'wp-ever-accounting' ); ?></div>
					<div class="ea-list-group__text"><?php echo ! empty( $vendor->get_phone() ) ? $vendor->get_phone() : '&mdash;'; ?></div>
				</div>
				<div class="ea-list-group__item">
					<div class="ea-list-group__title"><?php esc_html_e( 'Email', 'wp-ever-accounting' ); ?></div>
					<div class="ea-list-group__text"><?php echo ! empty( $vendor->get_email() ) ? $vendor->get_email() : '&mdash;'; ?></div>
				</div>
				<div class="ea-list-group__item">
					<div class="ea-list-group__title"><?php esc_html_e( 'Fax', 'wp-ever-accounting' ); ?></div>
					<div class="ea-list-group__text"><?php echo ! empty( $vendor->get_fax() ) ? $vendor->get_fax() : '&mdash;'; ?></div>
				</div>
				<div class="ea-list-group__item">
					<div class="ea-list-group__title"><?php esc_html_e( 'Tax Number', 'wp-ever-accounting' ); ?></div>
					<div class="ea-list-group__text"><?php echo ! empty( $vendor->get_tax_number() ) ? $vendor->get_tax_number() : '&mdash;'; ?></div>
				</div>
				<div class="ea-list-group__item">
					<div class="ea-list-group__title"><?php esc_html_e( 'Website', 'wp-ever-accounting' ); ?></div>
					<div class="ea-list-group__text"><?php echo ! empty( $vendor->get_website() ) ? $vendor->get_website() : '&mdash;'; ?></div>
				</div>
				<div class="ea-list-group__item">
					<div class="ea-list-group__title"><?php esc_html_e( 'Address', 'wp-ever-accounting' ); ?></div>
					<div class="ea-list-group__text"><?php echo ! empty( $vendor->get_address() ) ? $vendor->get_address() : '&mdash;'; ?></div>
				</div>
			</div>

			<div class="ea-card__footer">
				<p class="description">
					<?php
					echo sprintf(
					/* translators: %s date and %s name */
							esc_html__( 'The vendor was created at %1$s by %2$s', 'wp-ever-accounting' ),
							eaccounting_format_datetime( $vendor->get_date_created(), 'F m, Y H:i a' ),
							eaccounting_get_username( $vendor->get_creator_id() )
					);
					?>
				</p>
			</div>

		</div>
	</div>

</div>


