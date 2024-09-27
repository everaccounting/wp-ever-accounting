<?php
/**
 * Admin Customers Form.
 * Page: Sales
 * Tab: Customers
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $customer \EverAccounting\Models\Customer Customer object.
 */

defined( 'ABSPATH' ) || exit;
?>
	<form id="eac-customer-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
		<div class="eac-poststuff">
			<div class="column-1">
				<div class="eac-card">
					<div class="eac-card__header">
						<h2 class="eac-card__title"><?php esc_html_e( 'Customer Details', 'wp-ever-accounting' ); ?></h2>
					</div>
					<div class="eac-card__body grid--fields">
						<?php
						eac_form_field(
							array(
								'id'          => 'name',
								'label'       => __( 'Name', 'wp-ever-accounting' ),
								'placeholder' => __( 'John Doe', 'wp-ever-accounting' ),
								'value'       => $customer->name,
								'required'    => true,
							)
						);
						eac_form_field(
							array(
								'id'           => 'currency',
								'type'         => 'select',
								'label'        => __( 'Currency Code', 'wp-ever-accounting' ),
								'value'        => $customer->currency_code,
								'default'      => eac_base_currency(),
								'required'     => true,
								'class'        => 'eac_select2',
								'options'      => eac_get_currencies(
									array(
										'status' => 'active',
										'limit'  => - 1,
									)
								),
								'option_value' => 'code',
								'option_label' => 'formatted_name',
							)
						);
						eac_form_field(
							array(
								'id'          => 'email',
								'label'       => __( 'Email', 'wp-ever-accounting' ),
								'placeholder' => __( 'john@company.com', 'wp-ever-accounting' ),
								'value'       => $customer->email,
							)
						);
						eac_form_field(
							array(
								'id'          => 'phone',
								'label'       => __( 'Phone', 'wp-ever-accounting' ),
								'placeholder' => __( '+1 123 456 7890', 'wp-ever-accounting' ),
								'value'       => $customer->phone,
							)
						);
						eac_form_field(
							array(
								'id'          => 'company',
								'label'       => __( 'Company', 'wp-ever-accounting' ),
								'placeholder' => __( 'XYZ Inc.', 'wp-ever-accounting' ),
								'value'       => $customer->company,
							)
						);
						eac_form_field(
							array(
								'id'          => 'website',
								'label'       => __( 'Website', 'wp-ever-accounting' ),
								'placeholder' => __( 'https://example.com', 'wp-ever-accounting' ),
								'value'       => $customer->website,
							)
						);
						eac_form_field(
							array(
								'id'          => 'tax_number',
								'label'       => __( 'TAX Number', 'wp-ever-accounting' ),
								'placeholder' => __( '123456789', 'wp-ever-accounting' ),
								'value'       => $customer->vat_number,
							)
						);
						eac_form_field(
							array(
								'id'            => 'address',
								'label'         => __( 'Address', 'wp-ever-accounting' ),
								'placeholder'   => __( '123 Main St', 'wp-ever-accounting' ),
								'value'         => $customer->address,
							)
						);
						eac_form_field(
							array(
								'id'          => 'city',
								'label'       => __( 'City', 'wp-ever-accounting' ),
								'placeholder' => __( 'New York', 'wp-ever-accounting' ),
								'value'       => $customer->city,
							)
						);
						eac_form_field(
							array(
								'id'          => 'state',
								'label'       => __( 'State', 'wp-ever-accounting' ),
								'placeholder' => __( 'NY', 'wp-ever-accounting' ),
								'value'       => $customer->state,
							)
						);
						eac_form_field(
							array(
								'id'          => 'postcode',
								'label'       => __( 'Postal Code', 'wp-ever-accounting' ),
								'placeholder' => __( '10001', 'wp-ever-accounting' ),
								'value'       => $customer->postcode,
							)
						);
						eac_form_field(
							array(
								'type'        => 'select',
								'id'          => 'country',
								'label'       => __( 'Country', 'wp-ever-accounting' ),
								'options'     => \EverAccounting\Utilities\I18n::get_countries(),
								'value'       => $customer->country,
								'class'   	  => 'eac-select2',
								'placeholder' => __( 'Select Country', 'wp-ever-accounting' ),
							)
						);
						?>
					</div>
				</div>

			</div><!-- .column-1 -->

			<div class="column-2">
				<div class="eac-card">
					<div class="eac-card__header">
						<h2 class="eac-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
					</div>
					<div class="eac-card__body">
						<?php
						eac_form_field(
							array(
								'type'        => 'select',
								'id'          => 'status',
								'label'       => __( 'Status', 'wp-ever-accounting' ),
								'options'     => array(
									'active'   => __( 'Active', 'wp-ever-accounting' ),
									'inactive' => __( 'Inactive', 'wp-ever-accounting' ),
								),
								'value'       => $customer->status,
								'required'    => true,
							)
						);
						?>
					</div>
					<div class="eac-card__footer">
						<?php if ( $customer->exists() && EAC()->customers->get($customer->id) ) : ?>
							<input type="hidden" name="id" value="<?php echo esc_attr( $customer->id ); ?>"/>
						<?php endif; ?>
						<input type="hidden" name="action" value="eac_edit_customer"/>
						<?php wp_nonce_field( 'eac_edit_customer' ); ?>
						<?php if ( $customer->exists() ) : ?>
							<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-sales&tab=customers&id=' . $customer->id ) ), 'bulk-customers' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
						<?php endif; ?>
						<?php if ( $customer->exists() ) : ?>
							<button class="button button-primary"><?php esc_html_e( 'Update Customer', 'wp-ever-accounting' ); ?></button>
						<?php else : ?>
							<button class="button button-primary eac-w-100"><?php esc_html_e( 'Add Customer', 'wp-ever-accounting' ); ?></button>
						<?php endif; ?>
					</div>
				</div>
			</div><!-- .column-2 -->

		</div><!-- .eac-poststuff -->
	</form>
<?php
