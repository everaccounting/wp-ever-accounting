<?php
/**
 * View: Vendor Form
 *
 * @package EverAccounting
 * @since 1.1.6
 * @var \EverAccounting\Models\Vendor $vendor Vendor object.
 */

defined( 'ABSPATH' ) || exit;

?>
<form id="eac-vendor-form" class="eac-form" method="post">
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Basic Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_input_field(
					array(
						'id'            => 'name',
						'label'         => __( 'Name', 'wp-ever-accounting' ),
						'placeholder'   => __( 'John Doe', 'wp-ever-accounting' ),
						'value'         => $vendor->get_name(),
						'wrapper_class' => 'eac-col-6',
						'required'      => true,
					)
				);
				eac_input_field(
					array(
						'type'          => 'currency',
						'id'            => 'currency_code',
						'label'         => __( 'Currency Code', 'wp-ever-accounting' ),
						'value'         => $vendor->get_currency_code(),
						'wrapper_class' => 'eac-col-6',
						'required'      => true,
					)
				);
				eac_input_field(
					array(
						'id'            => 'email',
						'label'         => __( 'Email', 'wp-ever-accounting' ),
						'placeholder'   => __( 'john@company.com', 'wp-ever-accounting' ),
						'value'         => $vendor->get_email(),
						'wrapper_class' => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'id'            => 'phone',
						'label'         => __( 'Phone', 'wp-ever-accounting' ),
						'placeholder'   => __( '+1 123 456 7890', 'wp-ever-accounting' ),
						'value'         => $vendor->get_phone(),
						'wrapper_class' => 'eac-col-6',
					)
				);
				?>
			</div>
		</div>
		<div class="eac-card__separator"></div>
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Business Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_input_field(
					array(
						'id'            => 'company',
						'label'         => __( 'Business Name', 'wp-ever-accounting' ),
						'placeholder'   => __( 'XYZ Inc.', 'wp-ever-accounting' ),
						'value'         => $vendor->get_company(),
						'wrapper_class' => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'id'            => 'website',
						'label'         => __( 'Website', 'wp-ever-accounting' ),
						'placeholder'   => __( 'https://example.com', 'wp-ever-accounting' ),
						'value'         => $vendor->get_website(),
						'wrapper_class' => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'id'            => 'vat_number',
						'label'         => __( 'VAT Number', 'wp-ever-accounting' ),
						'placeholder'   => __( '123456789', 'wp-ever-accounting' ),
						'value'         => $vendor->get_vat_number(),
						'wrapper_class' => 'eac-col-6',
					)
				);
				?>
			</div>
		</div>
		<div class="eac-card__separator"></div>
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Address', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_input_field(
					array(
						'id'            => 'street',
						'label'         => __( 'Street', 'wp-ever-accounting' ),
						'placeholder'   => __( '123 Main St', 'wp-ever-accounting' ),
						'value'         => $vendor->get_street(),
						'wrapper_class' => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'id'            => 'city',
						'label'         => __( 'City', 'wp-ever-accounting' ),
						'placeholder'   => __( 'New York', 'wp-ever-accounting' ),
						'value'         => $vendor->get_city(),
						'wrapper_class' => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'id'            => 'state',
						'label'         => __( 'State', 'wp-ever-accounting' ),
						'placeholder'   => __( 'NY', 'wp-ever-accounting' ),
						'value'         => $vendor->get_state(),
						'wrapper_class' => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'id'            => 'postal_code',
						'label'         => __( 'Postal Code', 'wp-ever-accounting' ),
						'placeholder'   => __( '10001', 'wp-ever-accounting' ),
						'value'         => $vendor->get_postcode(),
						'wrapper_class' => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'type'          => 'country',
						'id'            => 'country',
						'label'         => __( 'Country', 'wp-ever-accounting' ),
						'value'         => $vendor->get_country(),
						'wrapper_class' => 'eac-col-6',
					)
				);
				?>
			</div>
	</div>
	<?php wp_nonce_field( 'eac_edit_vendor' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $vendor->get_id() ); ?>">
	<input type="hidden" name="action" value="eac_edit_vendor">
</form>


