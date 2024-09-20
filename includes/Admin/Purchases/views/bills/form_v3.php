<?php
/**
 * Admin Bills Form.
 * Page: Expenses
 * Tab: Bills
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $bill \EverAccounting\Models\Bill Bill object.
 */

defined( 'ABSPATH' ) || exit;
$columns           = EAC()->bills->get_columns();
$columns['action'] = '&nbsp;';
$bill              = new \EverAccounting\Models\Bill();
$data              = $bill->to_array();
wp_add_inline_script( 'eac-admin', 'var eac_bill_edit_vars = ' . json_encode( $data ) . ';', 'after' );
?>
<form id="eac-bill-form" class="eac-document-overview" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<div class="eac-poststuff">
		<div class="column-1">

			<div class="eac-card">
				<div class="eac-card__child has-2-cols tw-pt-[2em] tw-pl-[2em] tw-pr-[2em]">
					<div class="col-1">
						<?php
						eac_form_field(
							array(
								'label'            => __( 'Vendor', 'wp-ever-accounting' ),
								'type'             => 'select',
								'name'             => 'contact_id',
								'options'          => array( $bill->vendor ),
								'value'            => $bill->vendor_id,
								'required'         => true,
								'class'            => 'eac_select2',
								'option_value'     => 'id',
								'option_label'     => 'formatted_name',
								'data-placeholder' => __( 'Select a vendor', 'wp-ever-accounting' ),
								'data-action'      => 'eac_json_search',
								'data-type'        => 'vendor',
							)
						);
						?>
						<div class="billing-address"></div>
					</div><!-- .col-1 -->
					<div class="col-2 has-2-cols">
						<?php
						eac_form_field(
							array(
								'label'             => esc_html__( 'Issue Date', 'wp-ever-accounting' ),
								'name'              => 'issue_date',
								'value'             => $bill->issue_date,
								'type'              => 'text',
								'placeholder'       => 'YYYY-MM-DD',
								'required'          => true,
								'class'             => 'eac_datepicker',
								'attr-autocomplete' => 'off',
							)
						);
						eac_form_field(
							array(
								'label'             => esc_html__( 'Bill Number', 'wp-ever-accounting' ),
								'name'              => 'number',
								'value'             => $bill->number,
								'default'           => $bill->get_next_number(),
								'type'              => 'text',
								'placeholder'       => 'INV-0001',
								'required'          => true,
								'attr-autocomplete' => 'off',
							)
						);
						eac_form_field(
							array(
								'label'             => esc_html__( 'Due Date', 'wp-ever-accounting' ),
								'name'              => 'due_date',
								'value'             => $bill->due_date,
								'type'              => 'text',
								'placeholder'       => 'YYYY-MM-DD',
								'class'             => 'eac_datepicker',
								'attr-autocomplete' => 'off',
							)
						);
						eac_form_field(
							array(
								'label'             => esc_html__( 'Order Number', 'wp-ever-accounting' ),
								'name'              => 'order_number',
								'value'             => $bill->order_number,
								'type'              => 'text',
								'placeholder'       => 'REF-0001',
								'attr-autocomplete' => 'off',
							)
						);
						?>
					</div><!-- .col-2 -->
				</div>

				<div class="tw-pt-[2em] tw-overflow-x-auto">
					<table class="eac-document-items">
						<thead class="eac-document-items__head">
						<tr>
							<?php foreach ( $columns as $key => $label ) : ?>
								<th class="col-<?php echo esc_attr( $key ); ?>">
									<?php echo esc_html( $label ); ?>
								</th>
							<?php endforeach; ?>
						</tr>
						</thead>
					</table>
				</div><!-- .document-items -->

				<div class="tw-pt-[2em] tw-pl-[2em] tw-pr-[2em]">
					<?php
					// notes.
					eac_form_field(
						array(
							'label'       => esc_html__( 'Notes', 'wp-ever-accounting' ),
							'name'        => 'notes',
							'value'       => $bill->notes,
							'type'        => 'textarea',
							'placeholder' => esc_html__( 'Add notes here', 'wp-ever-accounting' ),
						)
					);

					//terms.
					eac_form_field(
						array(
							'label'       => esc_html__( 'Terms', 'wp-ever-accounting' ),
							'name'        => 'terms',
							'value'       => $bill->terms,
							'type'        => 'textarea',
							'placeholder' => esc_html__( 'Add terms here', 'wp-ever-accounting' ),
						)
					);
					?>
				</div>

			</div><!-- .eac-card -->


			</div><!-- .column-1 -->
			<div class="column-2">

				<button type="submit" class="button button-primary button-large tw-w-full">
					<?php esc_html_e( 'Save Bill', 'wp-ever-accounting' ); ?>
				</button>
				<hr>

				<?php
				eac_form_field(
					array(
						'label'            => esc_html__( 'Currency', 'wp-ever-accounting' ),
						'name'             => 'currency_code',
						'default'          => eac_base_currency(),
						'value'            => $bill->currency_code,
						'type'             => 'select',
						'options'          => eac_get_currencies(),
						'option_value'     => 'code',
						'option_label'     => 'formatted_name',
						'placeholder'      => esc_html__( 'Select a currency', 'wp-ever-accounting' ),
						'class'            => 'eac_select2',
						'data-action'      => 'eac_json_search',
						'data-type'        => 'currency',
						'data-allow-clear' => 'false',
					)
				);
				?>

				<div class="eac-form-field">
					<label for="discount_amount"><?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?></label>
					<div class="eac-input-group">
						<input type="number" name="discount_amount" id="discount_amount" placeholder="10" value="<?php echo esc_attr( $bill->discount_amount ); ?>"/>
						<select name="discount_type" id="discount_type" class="addon" style="max-width: 80px;">
							<option value="fixed" <?php selected( 'fixed', $bill->discount_type ); ?>><?php echo $bill->currency ? esc_html( $bill->currency->symbol ) : esc_html( '($)' ); ?></option>
							<option value="percent" <?php selected( 'percent', $bill->discount_type ); ?>><?php echo esc_html( '(%)' ); ?></option>
						</select>
					</div>
				</div><!-- .eac-form-field -->

			</div><!-- .column-2 -->
		</div><!-- .eac-poststuff -->
</form>


<script type="text/html" id="tmpl-eac-billing-address">
	<table class="eac-document-address">
		<tbody>
		<# if ( data.name ) { #>
		<tr>
			<td class="name">
				<span>{{ data.name }}</span>
				<input type="hidden" name="address[name]" value="{{ data.name }}">
			</td>
		</tr>
		<# } #>

		<# if ( data.company ) { #>
		<tr>
			<td class="company">
				<span>{{ data.company }}</span>
				<input type="hidden" name="address[company]" value="{{ data.company }}">
			</td>
		</tr>
		<# } #>

		<tr>
			<td class="address">
				{{ data.address }}<br>
				{{ data.city }} {{ data.state }} {{ data.zip }}
				<input type="hidden" name="address[address]" value="{{ data.address }}">
				<input type="hidden" name="address[city]" value="{{ data.city }}">
				<input type="hidden" name="address[state]" value="{{ data.state }}">
				<input type="hidden" name="address[zip]" value="{{ data.zip }}">
			</td>
		</tr>

		<# if ( data.country ) { #>
		<tr>
			<td class="country">
				<span>{{ data.country }}</span>
				<input type="hidden" name="address[country]" value="{{ data.country }}">
			</td>
		</tr>
		<# } #>

		<# if ( data.phone || data.email ) { #>
		<tr>
			<td class="phone-email">
				<# if ( data.phone ) { #>
				<span class="phone">{{ data.phone }}</span>
				<input type="hidden" name="address[phone]" value="{{ data.phone }}">
				<# } #>

				<# if ( data.phone && data.email ) { #>
				<span class="separator"> | </span>
				<# } #>

				<# if ( data.email ) { #>
				<span class="email">{{ data.email }}</span>
				<input type="hidden" name="address[email]" value="{{ data.email }}">
				<# } #>
			</td>
		</tr>
		<# } #>

		</tbody>
	</table>
</script>
