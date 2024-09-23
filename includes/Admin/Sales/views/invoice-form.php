<?php
/**
 * Invoice form.
 *
 * @since 1.0.0
 * @package EverAccounting
 * @var \EverAccounting\Models\Invoice $invoice Invoice.
 */

defined( 'ABSPATH' ) || exit;

$columns = EAC()->invoices->get_columns();
wp_add_inline_script(
	'eac-admin-invoices',
	'var eac_invoice_vars = ' . wp_json_encode( $invoice->to_array() ) . ';',
	'before'
);
?>
<form id="eac-invoice-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<div class="eac-poststuff">
		<div class="column-1">
			<div class="eac-card eac-document-overview">

				<div class="eac-card__child document-details eac-grid cols-2">
					<div>
						<?php
						eac_form_field(
							array(
								'label'            => __( 'Customer', 'wp-ever-accounting' ),
								'type'             => 'select',
								'name'             => 'contact_id',
								'options'          => array( $invoice->customer ),
								'value'            => $invoice->customer_id,
								'required'         => true,
								'class'            => 'eac_select2',
								'option_value'     => 'id',
								'option_label'     => 'formatted_name',
								'data-placeholder' => __( 'Select a customer', 'wp-ever-accounting' ),
								'data-action'      => 'eac_json_search',
								'data-type'        => 'customer',
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
								'value'             => $invoice->issue_date,
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
								'value'             => $invoice->number,
								'default'           => $invoice->get_next_number(),
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
								'value'             => $invoice->due_date,
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
								'value'             => $invoice->order_number,
								'type'              => 'text',
								'placeholder'       => 'REF-0001',
								'attr-autocomplete' => 'off',
							)
						);
						eac_form_field(
							array(
								'label'           => esc_html__( 'Currency', 'wp-ever-accounting' ),
								'name'            => 'currency_code',
								'default'         => eac_base_currency(),
								'value'           => $invoice->currency_code,
								'type'            => 'select',
								'options'         => eac_get_currencies(),
								'option_value'    => 'code',
								'option_label'    => 'formatted_name',
								'placeholder'     => esc_html__( 'Select a currency', 'wp-ever-accounting' ),
								'class'           => 'eac_select2',
								'data-allowClear' => 'false',
								'required'        => true,
							)
						);
						// exchange rate.
						eac_form_field(
							array(
								'label'       => esc_html__( 'Exchange Rate', 'wp-ever-accounting' ),
								'name'        => 'exchange_rate',
								'value'       => $invoice->exchange_rate,
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
							'value'       => $invoice->notes,
							'type'        => 'textarea',
							'placeholder' => esc_html__( 'Add notes here', 'wp-ever-accounting' ),
						)
					);
					eac_form_field(
						array(
							'label'       => esc_html__( 'Terms', 'wp-ever-accounting' ),
							'name'        => 'terms',
							'value'       => $invoice->terms,
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
				<div class="eac-card__header">
					<h3 class="eac-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h3>
				</div>
				<div class="eac-card__body">
					<?php
					eac_form_field(
						array(
							'label'       => __( 'Status', 'wp-ever-accounting' ),
							'type'        => 'select',
							'id'          => 'status',
							'options'     => EAC()->invoices->get_statuses(),
							'value'       => $invoice->status,
							'placeholder' => __( 'Select status', 'wp-ever-accounting' ),
							'required'    => true,
						)
					);
					?>
				</div>
				<div class="eac-card__footer">
					<?php if ( $invoice->exists() ) : ?>
						<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-sales&tab=invoices&id=' . $invoice->id ) ), 'bulk-invoices' ) ); ?>">
							<?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?>
						</a>
						<button class="button button-primary eac-width-full"><?php esc_html_e( 'Update Invoice', 'wp-ever-accounting' ); ?></button>
					<?php else : ?>
						<button class="button button-primary button-large eac-width-full"><?php esc_html_e( 'Add Invoice', 'wp-ever-accounting' ); ?></button>
					<?php endif; ?>
				</div><!-- .eac-card__footer -->
			</div><!-- .eac-card -->

			<div class="eac-card">
				<div class="eac-card__header">
					<h3 class="eac-card__title"><?php esc_html_e( 'Attachment', 'wp-ever-accounting' ); ?></h3>
				</div>
				<div class="eac-card__body">
					<?php eac_file_uploader( array( 'value' => $invoice->attachment_id ) ); ?>
				</div>
			</div><!-- .eac-card -->

		</div><!-- .column-2 -->

	</div><!-- .eac-poststuff -->

	<input type="hidden" name="action" value="eac_edit_invoice"/>
	<input type="hidden" name="id" value="<?php echo esc_attr( $invoice->id ); ?>"/>
	<?php wp_nonce_field( 'eac_edit_invoice' ); ?>
</form>

<script type="text/html" id="tmpl-eac-invoice-billing-address">
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
<script type="text/html" id="tmpl-eac-invoice-toolbar">
	<tr>
		<td colspan="<?php echo count( $columns ); ?>">
			<select class="add-item eac_select2" data-action="eac_json_search" data-type="item" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
		</td>
	</tr>
</script>
<script type="text/html" id="tmpl-eac-invoice-totals">
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
