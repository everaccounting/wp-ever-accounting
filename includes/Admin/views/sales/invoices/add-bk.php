<?php
/**
 * Add invoice view.
 *
 * @package EverAccounting
 * @var &payment \EverAccounting\Models\Invoice
 */

defined( 'ABSPATH' ) || exit;
?>
<h1 class="wp-heading-inline">
	<?php esc_html_e( 'Add Invoice', 'wp-ever-accounting' ); ?>
	<a href="<?php echo esc_attr( remove_query_arg( 'add' ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
		<span class="dashicons dashicons-undo"></span>
	</a>
</h1>

<button data-micromodal-trigger="edit-billing-details" role="button"> Demo Modal</button>

<div class="eac-modal" id="edit-billing-details" aria-hidden="false">
	<div class="eac-modal__overlay" tabindex="-1" data-custom-close="">
		<div class="eac-modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-2-title">
			<header class="eac-modal__header">
				<h3 class="eac-modal__title">
					<?php esc_html_e( 'Edit Billing Details', 'wp-ever-accounting' ); ?>
				</h3>
				<button class="eac-modal__close" data-micromodal-close="edit-billing-details"></button>
			</header>
			<div class="eac-modal__body tw-box-sizing:border-box tw-flex-wrap:wrap tw-gap-x-[20px] tw-grid tw-grid-cols-2 tw-justify-between tw-items-self-start tw-content-flex-start">
				<?php
				$fields = array(
					array(
						'type'          => 'text',
						'id'            => 'billing_name',
						'label'         => __( 'Name', 'wp-ever-accounting' ),
						'value'         => $document->billing_name,
						'placeholder'   => __( 'John Doe', 'wp-ever-accounting' ),
						'wrapper_class' => 'tw-mt-0',
					),
					array(
						'type'          => 'text',
						'id'            => 'billing_company',
						'label'         => __( 'Company', 'wp-ever-accounting' ),
						'value'         => $document->billing_company,
						'placeholder'   => __( 'XYZ Corp', 'wp-ever-accounting' ),
						'wrapper_class' => 'tw-mt-0',
					),
					array(
						'type'          => 'text',
						'id'            => 'billing_address_1',
						'label'         => __( 'Address 1', 'wp-ever-accounting' ),
						'value'         => $document->billing_address_1,
						'placeholder'   => __( '123 Main St', 'wp-ever-accounting' ),
						'wrapper_class' => 'tw-mt-0',
					),
					array(
						'type'          => 'text',
						'id'            => 'billing_address_2',
						'label'         => __( 'Address 2', 'wp-ever-accounting' ),
						'value'         => $document->billing_address_2,
						'placeholder'   => __( 'Suite 100', 'wp-ever-accounting' ),
						'wrapper_class' => 'tw-mt-0',
					),
					array(
						'type'          => 'text',
						'id'            => 'billing_city',
						'label'         => __( 'City', 'wp-ever-accounting' ),
						'value'         => $document->billing_city,
						'placeholder'   => __( 'New York', 'wp-ever-accounting' ),
						'wrapper_class' => 'tw-mt-0',
					),
					array(
						'type'          => 'text',
						'id'            => 'billing_state',
						'label'         => __( 'State', 'wp-ever-accounting' ),
						'value'         => $document->billing_state,
						'placeholder'   => __( 'NY', 'wp-ever-accounting' ),
						'wrapper_class' => 'tw-mt-0',
					),
					array(
						'type'          => 'text',
						'id'            => 'billing_postcode',
						'label'         => __( 'Postcode', 'wp-ever-accounting' ),
						'value'         => $document->billing_postcode,
						'placeholder'   => __( '10001', 'wp-ever-accounting' ),
						'wrapper_class' => 'tw-mt-0',
					),
					array(
						'type'          => 'select',
						'id'            => 'billing_country',
						'label'         => __( 'Country', 'wp-ever-accounting' ),
						'options'       => \EverAccounting\Utilities\I18n::get_countries(),
						'value'         => $document->billing_country,
						'placeholder'   => __( 'Select a country', 'wp-ever-accounting' ),
						'wrapper_class' => 'tw-mt-0',
					),
					array(
						'type'          => 'text',
						'id'            => 'billing_phone',
						'label'         => __( 'Phone', 'wp-ever-accounting' ),
						'value'         => $document->billing_phone,
						'placeholder'   => __( '555-555-5555', 'wp-ever-accounting' ),
						'wrapper_class' => 'tw-mt-0',
					),
					array(
						'type'          => 'email',
						'id'            => 'billing_email',
						'label'         => __( 'Email', 'wp-ever-accounting' ),
						'value'         => $document->billing_email,
						'placeholder'   => 'john@doe.com',
						'wrapper_class' => 'tw-mt-0',
					),
					// vat number.
					array(
						'type'          => 'text',
						'id'            => 'billing_vat_number',
						'label'         => __( 'VAT Number', 'wp-ever-accounting' ),
						'value'         => $document->billing_vat_number,
						'placeholder'   => __( 'VAT Number', 'wp-ever-accounting' ),
						'wrapper_class' => 'tw-mt-0',
					),
					// vat exempt.
					array(
						'type'          => 'select',
						'id'            => 'billing_vat_exempt',
						'label'         => __( 'VAT Exempt', 'wp-ever-accounting' ),
						'value'         => filter_var( $document->billing_vat_exempt, FILTER_VALIDATE_BOOLEAN ) ? 'yes' : '',
						'options'       => array(
							'yes' => __( 'Yes', 'wp-ever-accounting' ),
							''    => __( 'No', 'wp-ever-accounting' ),
						),
						'wrapper_class' => 'tw-mt-0',
						'input_class'   => 'trigger-update',
					),
				);
				foreach ( $fields as $field ) {
					eac_form_group( $field );
				}
				?>
			</div>
			<footer class="eac-modal__footer">
				<button type="button" class="button button-primary"><?php esc_html_e( 'Save', 'wp-ever-accounting' ); ?></button>
				<button type="button" class="button" data-micromodal-close="edit-billing-details"><?php esc_html_e( 'Cancel', 'wp-ever-accounting' ); ?></button>
			</footer>
		</div>
	</div>
</div>
