<?php
/**
 * Admin Invoices Form sidebar.
 * Page: Sales
 * Tab: Invoices
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $document \EverAccounting\Models\Invoice Invoice object.
 */

defined( 'ABSPATH' ) || exit;
?>
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
			''    => esc_html__( 'Select an option', 'wp-ever-accounting' ),
			'yes' => esc_html__( 'Yes', 'wp-ever-accounting' ),
			'no'  => esc_html__( 'No', 'wp-ever-accounting' ),
		),
	)
);
?>
<div class="eac-form-group tw-mt-0">
	<label for="discount_amount"><?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?></label>
	<div class="eac-input-group">
		<input type="number" name="discount_amount" id="discount_amount" placeholder="10" value="<?php echo esc_attr( $document->discount_amount ); ?>"/>
		<select name="discount_type" id="discount_type" class="addon" style="width: 80px;">
			<option value="fixed" <?php selected( 'fixed', $document->discount_type ); ?>><?php echo $document->currency ? esc_html( $document->currency->symbol ) : esc_html( '($)' ); ?></option>
			<option value="percentage" <?php selected( 'percentage', $document->discount_type ); ?>><?php echo esc_html( '(%)' ); ?></option>
		</select>
	</div>
</div>
