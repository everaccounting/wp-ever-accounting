<?php
/**
 * View: Vendor Form
 *
 * @since 1.1.6
 * @package EverAccounting
 * @var \EverAccounting\Models\Vendor $vendor Vendor object.
 */

defined( 'ABSPATH' ) || exit;

?>
<form id="eac-vendor-form" class="eac-ajax-form" method="post">
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Basic Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_form_field(
					array(
						'id'          => 'name',
						'label'       => __( 'Name', 'wp-ever-accounting' ),
						'placeholder' => __( 'John Doe', 'wp-ever-accounting' ),
						'value'       => $vendor->get_name(),
						'class'       => 'eac-col-6',
						'required'    => true,
					)
				);
				eac_form_field(
					array(
						'type'        => 'select',
						'id'          => 'currency_code',
						'label'       => __( 'Currency Code', 'wp-ever-accounting' ),
						'value'       => $vendor->get_currency_code(),
						'class'       => 'eac-col-6',
						'required'    => true,
						'input_class' => 'eac-select2',
						'options'     => wp_list_pluck(
							eac_get_currencies(
								[
									'status' => 'active',
									'limit'  => - 1,
								]
							),
							'formatted_name',
							'code'
						),
					)
				);
				eac_form_field(
					array(
						'id'          => 'email',
						'label'       => __( 'Email', 'wp-ever-accounting' ),
						'placeholder' => __( 'john@company.com', 'wp-ever-accounting' ),
						'value'       => $vendor->get_email(),
						'class'       => 'eac-col-6',
					)
				);
				eac_form_field(
					array(
						'id'          => 'phone',
						'label'       => __( 'Phone', 'wp-ever-accounting' ),
						'placeholder' => __( '+1 123 456 7890', 'wp-ever-accounting' ),
						'value'       => $vendor->get_phone(),
						'class'       => 'eac-col-6',
					)
				);
				?>
			</div>
		</div>
	</div>
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Business Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_form_field(
					array(
						'id'          => 'company',
						'label'       => __( 'Company', 'wp-ever-accounting' ),
						'placeholder' => __( 'XYZ Inc.', 'wp-ever-accounting' ),
						'value'       => $vendor->get_company(),
						'class'       => 'eac-col-6',
					)
				);
				eac_form_field(
					array(
						'id'          => 'website',
						'label'       => __( 'Website', 'wp-ever-accounting' ),
						'placeholder' => __( 'https://example.com', 'wp-ever-accounting' ),
						'value'       => $vendor->get_website(),
						'class'       => 'eac-col-6',
					)
				);
				eac_form_field(
					array(
						'id'          => 'vat_number',
						'label'       => __( 'VAT Number', 'wp-ever-accounting' ),
						'placeholder' => __( '123456789', 'wp-ever-accounting' ),
						'value'       => $vendor->get_vat_number(),
						'class'       => 'eac-col-6',
					)
				);
				?>
			</div>
		</div>
	</div>
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Address', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_form_field(
					array(
						'id'          => 'address_1',
						'label'       => __( 'Address Line 1', 'wp-ever-accounting' ),
						'placeholder' => __( '123 Main St', 'wp-ever-accounting' ),
						'value'       => $vendor->get_address_1(),
						'class'       => 'eac-col-6',
					)
				);
				eac_form_field(
					array(
						'id'          => 'address_2',
						'label'       => __( 'Address Line 2', 'wp-ever-accounting' ),
						'placeholder' => __( 'Apartment, studio, or floor', 'wp-ever-accounting' ),
						'class'       => 'eac-col-6',
						'value'       => $vendor->get_address_2(),
					)
				);
				eac_form_field(
					array(
						'id'          => 'city',
						'label'       => __( 'City', 'wp-ever-accounting' ),
						'placeholder' => __( 'New York', 'wp-ever-accounting' ),
						'value'       => $vendor->get_city(),
						'class'       => 'eac-col-6',
					)
				);
				eac_form_field(
					array(
						'id'          => 'state',
						'label'       => __( 'State', 'wp-ever-accounting' ),
						'placeholder' => __( 'NY', 'wp-ever-accounting' ),
						'value'       => $vendor->get_state(),
						'class'       => 'eac-col-6',
					)
				);
				eac_form_field(
					array(
						'id'          => 'postcode',
						'label'       => __( 'Postal Code', 'wp-ever-accounting' ),
						'placeholder' => __( '10001', 'wp-ever-accounting' ),
						'value'       => $vendor->get_postcode(),
						'class'       => 'eac-col-6',
					)
				);
				eac_form_field(
					array(
						'type'        => 'country',
						'id'          => 'country',
						'label'       => __( 'Country', 'wp-ever-accounting' ),
						'value'       => $vendor->get_country(),
						'class'       => 'eac-col-6',
						'input_class' => 'eac-select2',
					)
				);
				?>
			</div>
		</div>
	</div>
	<?php wp_nonce_field( 'eac_edit_vendor' ); ?>
	<input type="hidden" name="id" value="<?php echo esc_attr( $vendor->get_id() ); ?>">
	<input type="hidden" name="action" value="eac_edit_vendor">
</form>


