<?php
/**
 * Admin Vendors Form.
 * Page: Expenses
 * Tab: Vendors
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $vendor \EverAccounting\Models\Vendor Vendor object.
 */

defined( 'ABSPATH' ) || exit;
?>
	<form method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
		<span data-wp-text="name"></span>
		<div class="bkit-poststuff">
			<div class="column-1">
				<div class="bkit-card">
					<div class="bkit-card__header">
						<h2 class="bkit-card__title"><?php esc_html_e( 'Vendor Details', 'wp-ever-accounting' ); ?></h2>
					</div>

					<div class="bkit-card__body grid--fields">
						<?php
						eac_form_group(
							array(
								'id'          => 'name',
								'label'       => __( 'Name', 'wp-ever-accounting' ),
								'placeholder' => __( 'John Doe', 'wp-ever-accounting' ),
								'value'       => $vendor->name,
								'required'    => true,
							)
						);
						eac_form_group(
							array(
								'id'       => 'currency_code',
								'type'     => 'select',
								'label'    => __( 'Currency Code', 'wp-ever-accounting' ),
								'value'    => $vendor->currency_code,
								'default'  => eac_get_base_currency(),
								'required' => true,
								'class'    => 'eac-select2',
								'options'  => wp_list_pluck(
									eac_get_currencies(
										array(
											'status' => 'active',
											'limit'  => - 1,
										)
									),
									'formatted_name',
									'code'
								),
							)
						);
						eac_form_group(
							array(
								'id'          => 'email',
								'label'       => __( 'Email', 'wp-ever-accounting' ),
								'placeholder' => __( 'john@company.com', 'wp-ever-accounting' ),
								'value'       => $vendor->email,
							)
						);
						eac_form_group(
							array(
								'id'          => 'phone',
								'label'       => __( 'Phone', 'wp-ever-accounting' ),
								'placeholder' => __( '+1 123 456 7890', 'wp-ever-accounting' ),
								'value'       => $vendor->phone,
							)
						);
						eac_form_group(
							array(
								'id'          => 'company',
								'label'       => __( 'Company', 'wp-ever-accounting' ),
								'placeholder' => __( 'XYZ Inc.', 'wp-ever-accounting' ),
								'value'       => $vendor->company,
							)
						);
						eac_form_group(
							array(
								'id'          => 'website',
								'label'       => __( 'Website', 'wp-ever-accounting' ),
								'placeholder' => __( 'https://example.com', 'wp-ever-accounting' ),
								'value'       => $vendor->website,
							)
						);
						eac_form_group(
							array(
								'id'          => 'vat_number',
								'label'       => __( 'VAT Number', 'wp-ever-accounting' ),
								'placeholder' => __( '123456789', 'wp-ever-accounting' ),
								'value'       => $vendor->vat_number,
							)
						);
						eac_form_group(
							array(
								'id'          => 'vat_exempt',
								'label'       => __( 'VAT Exempt', 'wp-ever-accounting' ),
								'type'        => 'select',
								'options'     => array(
									'1' => __( 'Yes', 'wp-ever-accounting' ),
									'0' => __( 'No', 'wp-ever-accounting' ),
								),
								'value'       => $vendor->vat_exempt,
								'placeholder' => __( 'Select VAT exempt status', 'wp-ever-accounting' ),
							)
						);
						eac_form_group(
							array(
								'id'          => 'address_1',
								'label'       => __( 'Address Line 1', 'wp-ever-accounting' ),
								'placeholder' => __( '123 Main St', 'wp-ever-accounting' ),
								'value'       => $vendor->address_1,
							)
						);
						eac_form_group(
							array(
								'id'          => 'address_2',
								'label'       => __( 'Address Line 2', 'wp-ever-accounting' ),
								'placeholder' => __( 'Apartment, studio, or floor', 'wp-ever-accounting' ),
								'value'       => $vendor->address_2,
							)
						);
						eac_form_group(
							array(
								'id'          => 'city',
								'label'       => __( 'City', 'wp-ever-accounting' ),
								'placeholder' => __( 'New York', 'wp-ever-accounting' ),
								'value'       => $vendor->city,
							)
						);
						eac_form_group(
							array(
								'id'          => 'state',
								'label'       => __( 'State', 'wp-ever-accounting' ),
								'placeholder' => __( 'NY', 'wp-ever-accounting' ),
								'value'       => $vendor->state,
							)
						);
						eac_form_group(
							array(
								'id'          => 'postcode',
								'label'       => __( 'Postal Code', 'wp-ever-accounting' ),
								'placeholder' => __( '10001', 'wp-ever-accounting' ),
								'value'       => $vendor->postcode,
							)
						);
						eac_form_group(
							array(
								'type'    => 'select',
								'id'      => 'country',
								'label'   => __( 'Country', 'wp-ever-accounting' ),
								'options' => \EverAccounting\Utilities\I18n::get_countries(),
								'value'   => $vendor->country,
								'class'   => 'eac-select2',
							)
						);
						?>
					</div>
				</div>
			</div><!-- .column-1 -->

			<div class="column-2">
				<div class="bkit-card">
					<div class="bkit-card__header">
						<h2 class="bkit-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
					</div>
					<div class="bkit-card__body">
						<?php
						eac_form_group(
							array(
								'type'        => 'select',
								'id'          => 'status',
								'label'       => __( 'Status', 'wp-ever-accounting' ),
								'options'     => array(
									'active'   => __( 'Active', 'wp-ever-accounting' ),
									'inactive' => __( 'Inactive', 'wp-ever-accounting' ),
								),
								'value'       => $vendor->status,
								'placeholder' => __( 'Select status', 'wp-ever-accounting' ),
							)
						);
						?>
					</div>
					<div class="bkit-card__footer">
						<?php if ( $vendor->exists() ) : ?>
						<input type="hidden" name="id" value="<?php echo esc_attr( $vendor->id ); ?>"/>
						<?php endif; ?>
						<input type="hidden" name="action" value="eac_edit_vendor"/>
						<?php wp_nonce_field( 'eac_edit_vendor' ); ?>
						<?php if ( $vendor->exists() ) : ?>
						<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-expenses&tab=vendors&id=' . $vendor->id ) ), 'bulk-vendors' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
						<?php endif; ?>
						<?php if ( $vendor->exists() ) : ?>
						<button class="button button-primary"><?php esc_html_e( 'Update Vendor', 'wp-ever-accounting' ); ?></button>
						<?php else : ?>
						<button class="button button-primary bkit-w-100"><?php esc_html_e( 'Add Vendor', 'wp-ever-accounting' ); ?></button>
						<?php endif; ?>
					</div>
				</div>
			</div><!-- .column-2 -->

		</div><!-- .bkit-poststuff -->
	</form>
<?php