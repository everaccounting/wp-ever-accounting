<?php
/**
 * Edit bill view.
 *
 * @package EverAccounting
 * @var $item \EverAccounting\Models\Item
 */

use EverAccounting\Models\Bill;

defined( 'ABSPATH' ) || exit;

$id   = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
$bill = Bill::make( $id );

$columns  = EAC()->bills->get_columns();
$is_taxed = 'yes' === get_option( 'eac_tax_enabled', 'no' ) || $bill->tax > 0;
// if tax is not enabled and invoice has no tax, remove the tax column.
if ( ! $is_taxed ) {
	unset( $columns['tax'] );
}

defined( 'ABSPATH' ) || exit;
?>
<h1 class="wp-heading-inline">
	<?php esc_html_e( 'Edit Bill', 'wp-ever-accounting' ); ?>
	<a href="<?php echo esc_attr( remove_query_arg( array( 'action', 'id' ) ) ); ?>" class="button button-small" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
		<span class="dashicons dashicons-undo"></span>
	</a>
</h1>
<form id="eac-edit-bill" name="invoice" method="post">
	<div class="eac-poststuff">
		<div class="column-1">

			<div class="eac-card">
				<div class="tw-grid tw-grid-cols-2 tw-gap-x-[15px]">
					<div class="">
						<?php
						eac_form_field(
							array(
								'label'            => __( 'Vendor', 'wp-ever-accounting' ),
								'type'             => 'select',
								'name'             => 'contact_id',
								'options'          => array( $bill->vendor ),
								'value'            => $bill->vendor_id,
								'required'         => true,
								'readonly'         => true,
								'class'            => 'eac_select2',
								'option_value'     => 'id',
								'option_label'     => 'formatted_name',
								'data-placeholder' => __( 'Select a vendor', 'wp-ever-accounting' ),
								'data-action'      => 'eac_json_search',
								'data-type'        => 'vendor',
							)
						);
						?>

						<div class="document-address">
							<?php require __DIR__ . '/bill-editor-address.php'; ?>
						</div>

					</div>

					<div class="tw-grid xs:tw-grid-cols-1 tw-grid-cols-2 tw-gap-x-[15px]">
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
								'label'             => esc_html__( 'Invoice Number', 'wp-ever-accounting' ),
								'name'              => 'number',
								'value'             => $bill->number,
								'default'           => $bill->get_next_number(),
								'type'              => 'text',
								'placeholder'       => 'INV-0001',
								'required'          => true,
								'readonly'          => true,
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
								'value'             => $bill->reference,
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
								'data-allowClear' => 'false',
								'required'        => true,
							)
						);
						eac_form_field(
							array(
								'label'         => __( 'Exchange Rate', 'wp-ever-accounting' ),
								'name'          => 'exchange_rate',
								'value'         => $bill->exchange_rate,
								'default'       => 1,
								'placeholder'   => '1.00',
								'required'      => true,
								'prefix'        => '1 ' . eac_base_currency() . ' = ',
								'class'         => 'eac_exchange_rate',
								'attr-step'     => 'any',
								'readonly'      => eac_base_currency() === $bill->currency,
								'data-currency' => $bill->currency,
							)
						);
						?>
					</div>
				</div>

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
						<tbody class="eac-document-items__items">
							<?php require __DIR__ . '/bill-editor-items.php'; ?>
						</tbody>
						<tbody class="eac-document-items__toolbar">
						<tr>
							<td colspan="<?php echo esc_attr( count( $columns ) ); ?>">
								<select class="add-item eac_select2" data-action="eac_json_search" data-type="item" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
							</td>
						</tr>
						</tbody>
						<tfoot class="eac-document-items__totals">
							<?php require __DIR__ . '/bill-editor-totals.php'; ?>
						</tfoot>
					</table>
				</div><!-- .document-items -->

			</div>
		</div>

		<div class="column-2">
			Bill
		</div>
	</div>

	<input type="hidden" name="action" value="eac_edit_bill"/>
	<input type="hidden" name="id" value="<?php echo esc_attr( $bill->id ); ?>"/>
	<?php wp_nonce_field( 'eac_edit_bill' ); ?>
</form>
