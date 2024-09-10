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
	<tr>
		<td colspan="2">
			<div class="eac-input-group">
				<select class="add-line-item eac_select2" data-action="eac_json_search" data-type="item" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=eac-items&add=yes' ) ); ?>" title="<?php esc_attr_e( 'Add New Item', 'wp-ever-accounting' ); ?>">
					<span class="dashicons dashicons-plus"></span>
				</a>
			</div>
			<button class="button add-taxes" title="<?php esc_attr_e( 'Add Tax', 'wp-ever-accounting' ); ?>">
				<?php esc_html_e( 'Add Tax', 'wp-ever-accounting' ); ?>
			</button>
		</td>
		<td colspan="<?php echo count( $columns ) - (2 + 1); ?>">
			<input type="number" name="quantity" value="1" min="1"/>
		</td>
	</tr>
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
		<header class="eac_modal__header">
			<h2 class="eac_modal__title" id="modal-1-title">
				Micromodal
			</h2>
			<button class="eac_modal__close" aria-label="Close modal" data-micromodal-close></button>
		</header>
		<main class="eac_modal__content" id="modal-1-content">
			<p>
				Try hitting the <code>tab</code> key and notice how the focus stays within the modal itself. Also, <code>esc</code> to close modal.
				{{ JSON.stringify(data) }}
			</p>
		</main>
		<footer class="eac_modal__footer">
			<button class="button">Continue</button>
			<button class="button" data-micromodal-close aria-label="Close this dialog window">Close</button>
		</footer>
	</form>
</script>


<div
	id="edd-admin-order-add-item-dialog"
	title="<?php esc_attr_e( 'Add Download', 'easy-digital-downloads' ); ?>"
	style="display: none;"
>
	<div id="edd-admin-order-add-item-dialog-content"></div>
</div>

<script type="text/template" id="tmpl-edd-admin-order-form-add-order-item">
	<div class="edd-order-overview-modal">
		<form class="edd-order-overview-add-item">
			<div class="eac-input-group">
				<select class="add-line-item eac_select2" data-action="eac_json_search" data-type="item" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
				<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=eac-items&add=yes' ) ); ?>" title="<?php esc_attr_e( 'Add New Item', 'wp-ever-accounting' ); ?>">
					<span class="dashicons dashicons-plus"></span>
				</a>
			</div>
			<input type="number" name="quantity" value="{{ data.quantity }}" min="1"/>
			<input type="number" name="price" value="{{ data.price }}" min="0"/>
			Subtotal: <span class="edd-order-overview-subtotal">{{ data.subtotal }}</span>
		</form>
	</div>
</script>


<script type="text/template" id="tmpl-wc-modal-tracking-setup">
	<div class="wc-backbone-modal woocommerce-tracker">
		<div class="wc-backbone-modal-content">
			<section class="wc-backbone-modal-main" role="main">
				<header class="wc-backbone-modal-header">
					<h1><?php esc_html_e( 'Help improve WooCommerce with usage tracking', 'woocommerce' ); ?></h1>
				</header>
				<article>
					<form class="edd-order-overview-add-item">
						<div class="eac-input-group">
							<select class="add-line-item eac_select2" data-action="eac_json_search" data-type="item" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
							<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=eac-items&add=yes' ) ); ?>" title="<?php esc_attr_e( 'Add New Item', 'wp-ever-accounting' ); ?>">
								<span class="dashicons dashicons-plus"></span>
							</a>
						</div>
						<input type="number" name="quantity" value="{{ data.quantity }}" min="1"/>
						<input type="number" name="price" value="{{ data.price }}" min="0"/>
						Subtotal: <span class="edd-order-overview-subtotal">{{ data.subtotal }}</span>
					</form>
				</article>
				<footer>
					<div class="inner">
						<button class="button button-primary button-large" id="wc_tracker_submit" aria-label="<?php esc_attr_e( 'Continue', 'woocommerce' ); ?>"><?php esc_html_e( 'Continue', 'woocommerce' ); ?></button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>
