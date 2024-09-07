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
$data = array(
	'columns'            => $columns,
	'invoice'            => $document->to_array(),
	'is_calculating_tax' => $document->is_calculating_tax(),
);

wp_localize_script( 'eac-invoices', 'eac_invoices_vars', $data );
wp_enqueue_script( 'eac-invoices' );
?>

<form id="eac-invoice-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" class="eac-document">
	<div class="eac-poststuff">
		<div class="column-1">
			<table class="eac-invoice-table widefat">
				<thead>
				<tr>
					<?php foreach ( $columns as $key => $label ) : ?>
						<th class="col-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></th>
					<?php endforeach; ?>
				</tr>
				</thead>
			</table>
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
					'label'       => esc_html__( 'Reference', 'wp-ever-accounting' ),
					'name'        => 'reference',
					'value'       => $document->reference,
					'type'        => 'text',
					'placeholder' => 'REF-0001',
				)
			);

			eac_form_field(
				array(
					'label'        => esc_html__( 'Currency', 'wp-ever-accounting' ),
					'name'         => 'currency_code',
					'value'        => $document->currency_code,
					'type'         => 'select',
					'options'      => eac_get_currencies(),
					'option_value' => 'code',
					'option_label' => 'formatted_name',
					'placeholder'  => esc_html__( 'Select a currency', 'wp-ever-accounting' ),
					'class'        => 'eac_select2',
					'data-action'  => 'eac_json_search',
					'data-type'    => 'currency',
				)
			);

			eac_form_field(
				array(
					'label'   => esc_html__( 'VAT Exempt', 'wp-ever-accounting' ),
					'name'    => 'vat_exempt',
					'value'   => $document->vat_exempt,
					'type'    => 'select',
					'options' => array(
						'no'  => esc_html__( 'No', 'wp-ever-accounting' ),
						'yes' => esc_html__( 'Yes', 'wp-ever-accounting' ),
					),
				)
			);
			?>
		</div>

	</div>
</form>

<script type="text/template" id="tmpl-eac-invoice-totals">
	<tr>
		<td class="col-summary" colspan="4">
			Subtotal
		</td>
		<td class="col-amount">
			0.00
		</td>
		<td class="col-actions"></td>
	</tr>
	<tr>
		<td class="col-summary" colspan="4">
			Shipping
		</td>
		<td class="col-amount">
			0.00
		</td>
		<td class="col-actions"></td>
	</tr>
	<tr>
		<td class="col-summary" colspan="4">
			Tax
		</td>
		<td class="col-amount">
			0.00
		</td>
		<td class="col-actions"></td>
	</tr>
	<tr>
		<td class="col-summary" colspan="4">
			Fee
		</td>
		<td class="col-amount">
			0.00
		</td>
		<td class="col-actions"></td>
	</tr>
	<tr>
		<td class="col-summary" colspan="4">
			Total
		</td>
		<td class="col-amount">
			0.00
		</td>
		<td class="col-actions"></td>
	</tr>
</script>

<script type="text/template" id="tmpl-eac-invoice-actions">
	<td colspan="<?php echo count( $columns ); ?>">
		<button class="button add-item" title="<?php esc_attr_e( 'Add Line Item', 'wp-ever-accounting' ); ?>">
			<?php esc_html_e( 'Add Line Item', 'wp-ever-accounting' ); ?>
		</button>
		<button class="button add-taxes" title="<?php esc_attr_e( 'Add Tax', 'wp-ever-accounting' ); ?>">
			<?php esc_html_e( 'Add Tax', 'wp-ever-accounting' ); ?>
		</button>
	</td>
</script>

<script type="text/template" id="tmpl-eac-invoice-no-items">
	<tr>
		<td colspan="<?php echo count( $columns ); ?>">
			<?php esc_html_e( 'No line items.', 'wp-ever-accounting' ); ?>
		</td>
	</tr>
</script>

<script type="text/template" id="tmpl-eac-invoice-add-item">
	<form action="">
		<header class="modal__header">
			<h2 class="modal__title" id="modal-1-title">
				Micromodal
			</h2>
			<button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
		</header>
		<main class="modal__content" id="modal-1-content">
			<p>
				Try hitting the <code>tab</code> key and notice how the focus stays within the modal itself. Also, <code>esc</code> to close modal.
			</p>
		</main>
		<footer class="modal__footer">
			<button class="modal__btn modal__btn-primary">Continue</button>
			<button class="modal__btn" data-micromodal-close aria-label="Close this dialog window">Close</button>
		</footer>
	</form>
</script>
