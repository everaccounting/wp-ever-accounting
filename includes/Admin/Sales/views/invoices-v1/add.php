<?php
/**
 * Add invoice view.
 *
 * @package EverAccounting
 * @var $document \EverAccounting\Models\Invoice
 */

defined( 'ABSPATH' ) || exit;
$columns            = eac_get_invoice_columns();
$columns['actions'] = '&nbsp;';
if ( ! $document->is_calculating_tax() && isset( $columns['tax'] ) ) {
	unset( $columns['tax'] );
}
wp_enqueue_script( 'eac-invoices' );
?>
<h1 class="wp-heading-inline">
	<?php esc_html_e( 'Add Invoice', 'wp-ever-accounting' ); ?>
	<a href="<?php echo esc_attr( remove_query_arg( 'add' ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
		<span class="dashicons dashicons-undo"></span>
	</a>
</h1>

<form id="eac-invoice-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" class="eac-document-form">
	<div class="eac-poststuff">
		<div class="column-1">
			<div class="eac-document-form__section document-lines">
				<table class="document-lines__table">
					<thead>
					<tr class="line-item">
						<?php foreach ( $columns as $key => $label ) : ?>
							<?php if ( 'item' === $key ) : ?>
								<th class="line-item__<?php echo esc_attr( $key ); ?>" colspan="2"><?php echo esc_html( $label ); ?></th>
							<?php else : ?>
								<th class="line-item__<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></th>
							<?php endif; ?>
						<?php endforeach; ?>
					</tr>
					<tbody>
					<tr>
						<td colspan="2">
							<div class="eac-input-group">
								<select class="add-line-item eac_select2" data-action="eac_json_search" data-type="item" name="items[<?php echo esc_attr( PHP_INT_MAX ); ?>][item_id]" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
								<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=eac-items&add=yes' ) ); ?>" title="<?php esc_attr_e( 'Add New Item', 'wp-ever-accounting' ); ?>">
									<span class="dashicons dashicons-plus"></span>
								</a>
							</div>
						</td>
					</tr>
					</tbody>
					<tfoot>
					</tfoot>
				</table>
			</div>
		</div>
		<div class="column-2">
			<button type="submit" class="button button-primary button-large tw-w-full">
				<?php esc_html_e( 'Save Invoice', 'wp-ever-accounting' ); ?>
			</button>

			<hr>

			<?php
			eac_form_field(
				array(
					'label'            => __( 'Customer', 'wp-ever-accounting' ),
					'type'             => 'select',
					'name'             => 'contact_id',
					'value'            => $document->contact_id,
					'options'          => array( $document->customer ),
					'option_value'     => 'id',
					'option_label'     => 'formatted_name',
					'default'          => filter_input( INPUT_GET, 'customer_id', FILTER_SANITIZE_NUMBER_INT ),
					'disabled'         => $document->exists() && $document->contact_id,
					'data-placeholder' => __( 'Select customer', 'wp-ever-accounting' ),
					'data-action'      => 'eac_json_search',
					'data-type'        => 'customer',
					'class'            => 'eac_select2',
					'suffix'           => sprintf(
						'<a class="button" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
						esc_url( admin_url( 'admin.php?page=eac-sales&tab=customers&add=yes' ) ),
						__( 'Add customer', 'wp-ever-accounting' )
					),
				)
			);
			eac_form_field(
				array(
					'label'       => esc_html__( 'Issue Date', 'wp-ever-accounting' ),
					'name'        => 'issue_date',
					'value'       => $document->issue_date,
					'type'        => 'text',
					'placeholder' => 'YYYY-MM-DD',
					'required'    => true,
					'class'       => 'eac_datepicker',
				)
			);

			eac_form_field(
				array(
					'label'       => esc_html__( 'Reference', 'wp-ever-accounting' ),
					'name'        => 'reference',
					'value'       => $document->reference,
					'type'        => 'text',
					'placeholder' => 'REF-0001',
				)
			);

			eac_form_field(
				array(
					'label'       => esc_html__( 'Due Date', 'wp-ever-accounting' ),
					'name'        => 'due_date',
					'value'       => $document->due_date,
					'type'        => 'text',
					'placeholder' => 'YYYY-MM-DD',
					'class'       => 'eac_datepicker',
				)
			);
			eac_form_field(
				array(
					'label'            => esc_html__( 'Currency', 'wp-ever-accounting' ),
					'name'             => 'currency_code',
					'value'            => $document->currency_code,
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

			eac_form_field(
				array(
					'label'   => esc_html__( 'VAT Exempt', 'wp-ever-accounting' ),
					'name'    => 'vat_exempt',
					'value'   => $document->vat_exempt,
					'type'    => 'select',
					'options' => array(
						''    => esc_html__( 'Select an option', 'wp-ever-accounting' ),
						'yes' => esc_html__( 'Yes', 'wp-ever-accounting' ),
						'no'  => esc_html__( 'No', 'wp-ever-accounting' ),
					),
				)
			);
			?>
		</div>
	</div>
</form>

<script type="text/template" id="tmpl-eac-invoice-line-item">
	<tr class="eac-invoice-line-item">
		<td class="eac-invoice-line-item__name">
			<input type="text" name="line_items[{{ data.index }}][name]" value="{{ data.name }}" class="eac-invoice-line-item__name-input">
		</td>
		<td class="eac-invoice-line-item__quantity">
			<input type="number" name="line_items[{{ data.index }}][quantity]" value="{{ data.quantity }}" class="eac-invoice-line-item__quantity-input">
		</td>
		<td class="eac-invoice-line-item__price">
			<input type="number" name="line_items[{{ data.index }}][price]" value="{{ data.price }}" class="eac-invoice-line-item__price-input">
		</td>
		<td class="eac-invoice-line-item__total">
			<input type="number" name="line_items[{{ data.index }}][total]" value="{{ data.total }}" class="eac-invoice-line-item__total-input">
		</td>
		<td class="eac-invoice-line-item__actions">
			<button type="button" class="button button-link eac-invoice-line-item__remove">
				<span class="dashicons dashicons-trash"></span>
			</button>
		</td>
	</tr>
</script>
