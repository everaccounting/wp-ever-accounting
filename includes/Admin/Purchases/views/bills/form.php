<?php
/**
 * Admin Bills Form.
 * Page: Bills
 * Tab: Bills
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $bill \EverAccounting\Models\Bill Bill object.
 */

defined( 'ABSPATH' ) || exit;
$columns = EAC()->bills->get_columns();
$bill    = new \EverAccounting\Models\Bill();
$data    = $bill->to_array();
wp_add_inline_script( 'eac-admin', 'var eac_bill_edit_vars = ' . json_encode( $data ) . ';', 'after' );
?>
<form id="eac-bill-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<div class="eac-poststuff">
		<div class="column-1">

			<div class="eac-card eac-document-overview">

				<div class="eac-card__child document-details eac-grid cols-2">
					<div>
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

					</div>
					<div class="eac-grid cols-2">
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
						eac_form_field(
							array(
								'label'           => esc_html__( 'Currency', 'wp-ever-accounting' ),
								'name'            => 'currency',
								'default'         => eac_base_currency(),
								'value'           => $bill->currency,
								'type'            => 'select',
								'options'         => eac_get_currencies(),
								'option_value'    => 'code',
								'option_label'    => 'formatted_name',
								'placeholder'     => esc_html__( 'Select a currency', 'wp-ever-accounting' ),
								'class'           => 'eac_select2',
								'data-action'     => 'eac_json_search',
								'data-type'       => 'currency',
								'data-allowClear' => 'false',
								'required'        => true,
							)
						);
						// exchange rate.
						eac_form_field(
							array(
								'label'       => esc_html__( 'Exchange Rate', 'wp-ever-accounting' ),
								'name'        => 'exchange_rate',
								'value'       => $bill->exchange_rate,
								'type'        => 'number',
								'placeholder' => '1.00',
								'step'        => '0.01',
								'min'         => '0',
								'prefix'      => '1 USD =',
								'suffix'      => 'BDT',
								'required'    => true,
							)
						);

						?>
					</div>
				</div><!-- .document-details -->

				<div class="document-items">
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

				<div class="document-footer eac-grid cols-2">
					<?php
					eac_form_field(
						array(
							'label'       => esc_html__( 'Notes', 'wp-ever-accounting' ),
							'name'        => 'note',
							'value'       => $bill->notes,
							'type'        => 'textarea',
							'placeholder' => esc_html__( 'Add notes here', 'wp-ever-accounting' ),
						)
					);
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
				</div><!-- .document-footer -->

			</div><!-- .eac-card -->

		</div><!-- .column-1 -->
		<div class="column-2">
			<div class="eac-card">
				<div class="eac-card__body">
					<?php
					eac_form_field(
						array(
							'label'       => __( 'Status', 'wp-ever-accounting' ),
							'type'        => 'select',
							'id'          => 'status',
							'options'     => EAC()->bills->get_statuses(),
							'value'       => $bill->status,
							'placeholder' => __( 'Select status', 'wp-ever-accounting' ),
							'required'    => true,
						)
					);
					?>
				</div><!-- .eac-card__body -->
				<div class="eac-card__footer">
					<?php if ( $bill->exists() ) : ?>
						<input type="hidden" name="account_id"
							   value="<?php echo esc_attr( $bill->account_id ); ?>"/>
						<input type="hidden" name="id" value="<?php echo esc_attr( $bill->id ); ?>"/>
					<?php endif; ?>
					<input type="hidden" name="action" value="eac_edit_expense"/>
					<?php wp_nonce_field( 'eac_edit_expense' ); ?>
					<?php if ( $bill->exists() ) : ?>
						<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-sales&tab=expenses&id=' . $bill->id ) ), 'bulk-expenses' ) ); ?>">
							<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $bill->exists() ) : ?>
						<button
							class="button button-primary eac-width-full"><?php esc_html_e( 'Update Bill', 'wp-ever-accounting' ); ?></button>
					<?php else : ?>
						<button
							class="button button-primary button-large eac-width-full"><?php esc_html_e( 'Add Bill', 'wp-ever-accounting' ); ?></button>
					<?php endif; ?>
				</div><!-- .eac-card__footer -->
			</div><!-- .eac-card -->
		</div><!-- .column-2 -->
	</div><!-- .eac-poststuff -->
</form>

<script type="text/html" id="tmpl-eac-billing-address">
	<table class="document-address">
		<tbody>
		<# if ( data.name ) { #>
		<tr>
			<td class="name">
				<span>{{ data.name }}</span>
				<input type="hidden" name="billing[name]" value="{{ data.name }}">
			</td>
		</tr>
		<# } #>

		<# if ( data.company ) { #>
		<tr>
			<td class="company">
				<span>{{ data.company }}</span>
				<input type="hidden" name="billing[company]" value="{{ data.company }}">
			</td>
		</tr>
		<# } #>

		<tr>
			<td class="address">
				{{ data.address }}<br>
				{{ data.city }} {{ data.state }} {{ data.zip }}
				<input type="hidden" name="billing[address]" value="{{ data.address }}">
				<input type="hidden" name="billing[city]" value="{{ data.city }}">
				<input type="hidden" name="billing[state]" value="{{ data.state }}">
				<input type="hidden" name="billing[zip]" value="{{ data.zip }}">
			</td>
		</tr>

		<# if ( data.country ) { #>
		<tr>
			<td class="country">
				<span>{{ data.country }}</span>
				<input type="hidden" name="billing[country]" value="{{ data.country }}">
			</td>
		</tr>
		<# } #>

		<# if ( data.phone || data.email ) { #>
		<tr>
			<td class="phone-email">
				<# if ( data.phone ) { #>
				<span class="phone">{{ data.phone }}</span>
				<input type="hidden" name="billing[phone]" value="{{ data.phone }}">
				<# } #>

				<# if ( data.phone && data.email ) { #>
				<span class="separator"> | </span>
				<# } #>

				<# if ( data.email ) { #>
				<span class="email">{{ data.email }}</span>
				<input type="hidden" name="billing[email]" value="{{ data.email }}">
				<# } #>
			</td>
		</tr>
		<# } #>

		</tbody>
	</table>
</script>
<script type="text/html" id="tmpl-eac-items-empty">
	<td colspan="<?php echo count( $columns ); ?>">
		<?php esc_html_e( 'No items added yet.', 'wp-ever-accounting' ); ?>
	</td>
</script>
<script type="text/html" id="tmpl-eac-items-item">
	<?php foreach ( $columns as $key => $label ) : ?>
		<td class="col-<?php echo esc_attr( $key ); ?>">
			<?php
			switch ( $key ) {
				case 'item':
					?>
					<input type="hidden" name="items[{{ data.id }}][id]" value="{{ data.id }}">
					<input type="hidden" name="items[{{ data.id }}][type]" value="{{ data.type }}">
					<input type="hidden" name="items[{{ data.id }}][price]" value="{{ data.price }}">
					<input type="hidden" name="items[{{ data.id }}][quantity]" value="{{ data.quantity }}">
					<input type="hidden" name="items[{{ data.id }}][unit]" value="{{ data.unit }}">
					<input type="hidden" name="items[{{ data.id }}][total]" value="{{ data.total }}">
					<input class="item-name" type="text" name="items[{{ data.id }}][name]" value="{{ data.name }}" readonly>
					<textarea class="item-description" name="items[{{ data.id }}][description]" placeholder="<?php esc_attr_e( 'Item Description', 'wp-ever-accounting' ); ?>">{{ data.description }}</textarea>
					<select class="item-taxes eac_select2" data-action="eac_json_search" data-type="tax" data-placeholder="<?php esc_attr_e( 'Select a tax rate', 'wp-ever-accounting' ); ?>" multiple>
						<# if ( data.taxes && data.taxes.length ) { #>
						<# _.each( data.taxes, function( taxes ) { #>
						<option value="{{ taxes.tax_id }}" selected>{{ taxes.formatted_name }}</option>
						<# } ); #>
						<# } #>
					</select>
					<# if ( data.taxes && data.taxes.length ) { #>
					<# _.each( data.taxes, function( taxes ) { #>
					<input type="hidden" name="items[{{ data.id }}][taxes]{{ taxes.id }}['id']" value="{{ taxes.id }}">
					<input type="hidden" name="items[{{ data.id }}][taxes]{{ taxes.id }}['tax_id']" value="{{ taxes.tax_id }}">
					<input type="hidden" name="items[{{ data.id }}][taxes]{{ taxes.id }}['name']" value="{{ taxes.name }}">
					<input type="hidden" name="items[{{ data.id }}][taxes]{{ taxes.id }}['rate']" value="{{ taxes.rate }}">
					<input type="hidden" name="items[{{ data.id }}][taxes]{{ taxes.id }}['compound']" value="{{ taxes.compound }}">
					<input type="hidden" name="items[{{ data.id }}][taxes]{{ taxes.id }}['amount']" value="{{ taxes.amount }}">
					<# } ); #>
					<# } #>
					<?php
					break;

				case 'price':
					echo '<input type="number" class="item-price" name="items[{{ data.id }}][price]" min="0" value="{{ data.price }}">';
					break;

				case 'quantity':
					echo '<input type="number" class="item-quantity" name="items[{{ data.id }}][quantity]" min="0" value="{{ data.quantity }}">';
					break;

				case 'tax_total':
					echo '<span class="item-tax">{{ data.formatted_tax_total}}</span>';
					break;

				case 'subtotal':
					echo '<span class="item-subtotal">{{ data.formatted_subtotal }}</span>';
					echo '<a href="#" class="remove-item"><span class="dashicons dashicons-trash"></span></a>';
					break;

				default:
					break;
			}
			?>
		</td>
	<?php endforeach; ?>
</script>
<script type="text/html" id="tmpl-eac-items-actions">
	<tr>
		<td colspan="<?php echo count( $columns ); ?>">
			<select class="add-item eac_select2" data-action="eac_json_search" data-type="item" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
		</td>
	</tr>
</script>
<script type="text/html" id="tmpl-eac-items-totals">
	<tr>
		<td class="col-label" colspan="<?php echo count( $columns ) - 1; ?>">
			<?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-amount">
			<div class="eac-input-group">
				<select name="discount_type" id="discount_type" class="addon">
					<option value="fixed"
					<# if ( 'fixed' === data.discount_type ) { #>selected="selected"<# } #>>($)</option>
					<option value="percent"
					<# if ( 'percent' === data.discount_type ) { #>selected="selected"<# } #>>(%)</option>
				</select>
				<input type="number" name="discount_value" id="discount_value" placeholder="10" style="text-align: right;width: auto;" value="{{data.discount_value}}"/>
			</div>
		</td>
	</tr>
	<tr>
		<td class="col-label" colspan="<?php echo count( $columns ) - 1; ?>">
			<?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-amount">
			{{ data.formatted_subtotal || 0 }}
		</td>
	</tr>
	<tr>
		<td class="col-label" colspan="<?php echo count( $columns ) - 1; ?>">
			<?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-amount">
			{{ data.formatted_discount || 0 }}
		</td>
	</tr>
	<tr>
		<td class="col-label" colspan="<?php echo count( $columns ) - 1; ?>">
			<?php esc_html_e( 'Tax', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-amount">
			{{ data.formatted_tax_total || 0 }}
		</td>
	</tr>
	<tr>
		<td class="col-label" colspan="<?php echo count( $columns ) - 1; ?>">
			<?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?>
		</td>
		<td class="col-amount">
			{{ data.formatted_total || 0 }}
		</td>
	</tr>
</script>
