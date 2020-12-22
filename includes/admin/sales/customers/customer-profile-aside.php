<?php
/**
 * Customer profile aside
 * @var \EverAccounting\Models\Customer $customer
 */

defined( 'ABSPATH' ) || exit();

$edit_url = eaccounting_admin_url(
	array(
		'page'        => 'ea-sales',
		'tab'         => 'customers',
		'action'      => 'edit',
		'customer_id' => $customer->get_id()
	)
);
?>
<div class="ea-card">
	<div class="ea-card__header">
		<h3 class="ea-card__title"><?php esc_html_e( 'Customer Details', 'wp-ever-accounting' ); ?></h3>
		<a href="<?php echo esc_url( $edit_url ); ?>" class="button-secondary"><?php esc_html_e( 'Edit', 'wp-ever-accounting' ); ?></a>
	</div>

	<div class="ea-card__inside">
		<div class="ea-avatar ea-center-block">
			<img src="<?php echo esc_url( $customer->get_avatar_url() ); ?>" alt="<?php echo esc_html( $customer->get_name() ); ?>">
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
