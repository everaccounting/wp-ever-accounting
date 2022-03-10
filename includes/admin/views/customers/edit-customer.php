<?php
/**
 * Render Customer edit
 * Page: Sales
 * Tab: Customers
 *
 * @since       1.0.2
 * @subpackage  Admin/Views/Customers
 * @package     Ever_Accounting
 *
 * @var int $customer_id
 */

use \Ever_Accounting\Helpers\Form;

defined( 'ABSPATH' ) || exit();

try {
	$customer = new \Ever_Accounting\Customer( $customer_id );
} catch ( Exception $e ) {
	wp_redirect( admin_url( 'admin.php?page=ea-sales&tab=customers' ) );
}
$title = $customer->exists() ? __( 'Update Customer', 'wp-ever-accounting' ) : __( 'Add Customer', 'wp-ever-accounting' );
?>
<div class="ea-title-section">
	<div>
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Customers', 'wp-ever-accounting' ); ?></h1>
		<?php if ( $customer->exists() ) : ?>
			<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'customers', 'page' => 'ea-sales', 'action' => 'add' ), admin_url( 'admin.php' ) ) );//phpcs:ignore ?>" class="page-title-action">
				<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
			</a>
		<?php else : ?>
			<a href="<?php echo remove_query_arg( array( 'action', 'id' ) ); ?>" class="page-title-action"><?php esc_html_e( 'View All', 'wp-ever-accounting' ); ?></a>
		<?php endif; ?>
	</div>
</div>
<hr class="wp-header-end">

<form id="ea-customer-form" method="post" enctype="multipart/form-data">
	<div class="ea-card">
		<div class="ea-card__header">
			<h3 class="ea-card__title"><?php echo esc_html( $title ); ?></h3>
			<?php if ( $customer->exists() ) : ?>
			<div>
				<a href="<?php echo esc_url( add_query_arg( 'action', 'view' ) ); ?>" class="button-secondary">
					<?php esc_html_e( 'View Customer', 'wp-ever-accounting' ); ?>
				</a>
			</div>
			<?php endif;?>
		</div>
		<div class="ea-card__body">
			<div class="ea-card__inside">
				<div class="ea-row">
					<?php
					Form::text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Name', 'wp-ever-accounting' ),
							'name'          => 'name',
							'placeholder'   => __( 'Enter name', 'wp-ever-accounting' ),
							'value'         => $customer->get_name(),
							'required'      => true,
						)
					);
					Form::currency_dropdown(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Currency', 'wp-ever-accounting' ),
							'name'          => 'currency_code',
							'value'         => $customer->get_currency_code(),
							'required'      => true,
							'creatable'     => true,
						)
					);
					Form::text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Company', 'wp-ever-accounting' ),
							'name'          => 'company',
							'value'         => $customer->get_company(),
							'required'      => false,
						)
					);

					Form::text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Email', 'wp-ever-accounting' ),
							'name'          => 'email',
							'placeholder'   => __( 'Enter email', 'wp-ever-accounting' ),
							'data_type'     => 'email',
							'value'         => $customer->get_email(),
						)
					);
					Form::text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Phone', 'wp-ever-accounting' ),
							'name'          => 'phone',
							'placeholder'   => __( 'Enter phone', 'wp-ever-accounting' ),
							'value'         => $customer->get_phone(),
						)
					);
					Form::text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'VAT Number', 'wp-ever-accounting' ),
							'name'          => 'vat_number',
							'placeholder'   => __( 'Enter vat number', 'wp-ever-accounting' ),
							'value'         => $customer->get_vat_number(),
						)
					);
					Form::text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Website', 'wp-ever-accounting' ),
							'name'          => 'website',
							'placeholder'   => __( 'Enter website', 'wp-ever-accounting' ),
							'data_type'     => 'url',
							'value'         => $customer->get_website(),
						)
					);
					Form::text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Birth Date', 'wp-ever-accounting' ),
							'name'          => 'birth_date',
							'placeholder'   => __( 'Enter birth date', 'wp-ever-accounting' ),
							'data_type'     => 'date',
							'value'         => $customer->get_birth_date() ? $customer->get_birth_date() : null,
						)
					);
					Form::text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Street', 'wp-ever-accounting' ),
							'name'          => 'street',
							'placeholder'   => __( 'Enter street', 'wp-ever-accounting' ),
							'value'         => $customer->get_street(),
						)
					);
					Form::text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'City', 'wp-ever-accounting' ),
							'name'          => 'city',
							'placeholder'   => __( 'Enter city', 'wp-ever-accounting' ),
							'value'         => $customer->get_city(),
						)
					);
					Form::text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'State', 'wp-ever-accounting' ),
							'name'          => 'state',
							'placeholder'   => __( 'Enter state', 'wp-ever-accounting' ),
							'value'         => $customer->get_state(),
						)
					);
					Form::text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Postcode', 'wp-ever-accounting' ),
							'name'          => 'postcode',
							'placeholder'   => __( 'Enter postcode', 'wp-ever-accounting' ),
							'value'         => $customer->get_postcode(),
						)
					);
					Form::country_dropdown(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Country', 'wp-ever-accounting' ),
							'name'          => 'country',
							'value'         => $customer->get_country(),
						)
					);
					Form::file_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Photo', 'wp-ever-accounting' ),
							'name'          => 'thumbnail_id',
							'allowed-types' => 'jpg,jpeg,png',
							'value'         => $customer->get_thumbnail_id(),
						)
					);
					Form::hidden_input(
						array(
							'name'  => 'id',
							'value' => $customer->get_id(),
						)
					);
					Form::hidden_input(
						array(
							'name'  => 'action',
							'value' => 'ever_accounting_edit_customer',
						)
					);
					?>
				</div>
			</div>
		</div>
		<div class="ea-card__footer">
			<?php wp_nonce_field( 'ea_edit_customer' ); ?>
			<?php submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' ); ?>
		</div>

	</div>
</form>
