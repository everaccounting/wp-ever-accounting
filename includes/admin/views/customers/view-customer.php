<?php
/**
 * Render Single customer
 * Page: Sales
 * Tab: Customers
 *
 * @since       1.0.2
 * @subpackage  Admin/Views/Customers
 * @package     EverAccounting
 * @var int $customer_id
 */

defined( 'ABSPATH' ) || exit();

$customer = eaccounting_get_customer( $customer_id );

if ( empty( $customer ) || ! $customer->exists() ) {
	wp_die( __( 'Sorry, Customer does not exist', 'wp-ever-accounting' ) );
}

$sections = array(
	'transactions' => __( 'Transactions', 'wp-ever-accounting' ),
	'invoices'     => __( 'Invoices', 'wp-ever-accounting' ),
);

$sections        = apply_filters( 'eaccounting_customer_sections', $sections );
$first_section   = current( array_keys( $sections ) );
$current_section = ! empty( $_GET['section'] ) && array_key_exists( $_GET['section'], $sections ) ? sanitize_title( $_GET['section'] ) : $first_section;
$edit_url        = eaccounting_admin_url(
	array(
		'page'        => 'ea-sales',
		'tab'         => 'customers',
		'action'      => 'edit',
		'customer_id' => $customer->get_id(),
	)
);
?>
<div class="ea-page-columns altered ea-single-customer">
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
							'tab'         => 'customers',
							'action'      => 'view',
							'customer_id' => $customer_id,
							'section'     => $section_id,
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
					case 'incomes':
						include dirname( __FILE__ ) . '/customer-sections/' . sanitize_file_name( $current_section ) . '.php';
						break;
					default:
						do_action( 'eaccounting_customer_section_' . $current_section, $customer );
						break;
				}
				?>
			</div>
		</div>

	</div>

	<div class="ea-page-columns__aside">
		<div class="ea-card">
			<div class="ea-card__header">
				<h3 class="ea-card__title"><?php esc_html_e( 'Customer Details', 'wp-ever-accounting' ); ?></h3>
				<a href="<?php echo esc_url( $edit_url ); ?>" class="button-secondary"><?php esc_html_e( 'Edit', 'wp-ever-accounting' ); ?></a>
			</div>

			<div class="ea-card__inside">
				<div class="ea-avatar ea-center-block">
					<?php echo $customer->get_attachment_image(); ?>
				</div>
			</div>

			<div class="ea-list-group">
				<div class="ea-list-group__item">
					<div class="ea-list-group__title"><?php esc_html_e( 'Name', 'wp-ever-accounting' ); ?></div>
					<div class="ea-list-group__text"><?php echo esc_html( $customer->get_name() ); ?></div>
				</div>
				<div class="ea-list-group__item">
					<div class="ea-list-group__title"><?php esc_html_e( 'Currency', 'wp-ever-accounting' ); ?></div>
					<div class="ea-list-group__text"><?php echo ! empty( $customer->get_currency_code() ) ? $customer->get_currency_code() : '&mdash;'; ?></div>
				</div>
				<div class="ea-list-group__item">
					<div class="ea-list-group__title"><?php esc_html_e( 'Birthdate', 'wp-ever-accounting' ); ?></div>
					<div class="ea-list-group__text"><?php echo ! empty( $customer->get_birth_date() ) ? eaccounting_date( $customer->get_birth_date() ) : '&mdash;'; ?></div>
				</div>
				<div class="ea-list-group__item">
					<div class="ea-list-group__title"><?php esc_html_e( 'Phone', 'wp-ever-accounting' ); ?></div>
					<div class="ea-list-group__text"><?php echo ! empty( $customer->get_phone() ) ? $customer->get_phone() : '&mdash;'; ?></div>
				</div>
				<div class="ea-list-group__item">
					<div class="ea-list-group__title"><?php esc_html_e( 'Email', 'wp-ever-accounting' ); ?></div>
					<div class="ea-list-group__text"><?php echo ! empty( $customer->get_email() ) ? $customer->get_email() : '&mdash;'; ?></div>
				</div>
				<div class="ea-list-group__item">
					<div class="ea-list-group__title"><?php esc_html_e( 'Fax', 'wp-ever-accounting' ); ?></div>
					<div class="ea-list-group__text"><?php echo ! empty( $customer->get_fax() ) ? $customer->get_fax() : '&mdash;'; ?></div>
				</div>
				<div class="ea-list-group__item">
					<div class="ea-list-group__title"><?php esc_html_e( 'Tax Number', 'wp-ever-accounting' ); ?></div>
					<div class="ea-list-group__text"><?php echo ! empty( $customer->get_tax_number() ) ? $customer->get_tax_number() : '&mdash;'; ?></div>
				</div>
				<div class="ea-list-group__item">
					<div class="ea-list-group__title"><?php esc_html_e( 'Website', 'wp-ever-accounting' ); ?></div>
					<div class="ea-list-group__text"><?php echo ! empty( $customer->get_website() ) ? $customer->get_website() : '&mdash;'; ?></div>
				</div>
				<div class="ea-list-group__item">
					<div class="ea-list-group__title"><?php esc_html_e( 'Address', 'wp-ever-accounting' ); ?></div>
					<div class="ea-list-group__text"><?php echo ! empty( $customer->get_address() ) ? $customer->get_address() : '&mdash;'; ?></div>
				</div>
			</div>

			<div class="ea-card__footer">
				<p class="description">
					<?php
					echo sprintf(
					/* translators: %s date and %s name */
						esc_html__( 'The customer was created at %1$s by %2$s', 'wp-ever-accounting' ),
						eaccounting_format_datetime( $customer->get_date_created(), 'F m, Y H:i a' ),
						eaccounting_get_username( $customer->get_creator_id() )
					);
					?>
				</p>
			</div>

		</div>
	</div>

</div>