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

<div class="bkit-form-group tw-mt-0">
	<label for="contact_id"><?php esc_html_e( 'Customer', 'wp-ever-accounting' ); ?></label>
	<div class="bkit-input-group">
		<select name="contact_id" id="contact_id" class="eac_select2" data-action="eac_json_search" data-type="customer" data-placeholder="<?php esc_attr_e( 'Select a customer', 'wp-ever-accounting' ); ?>">
			<?php if ( $document->contact_id ) : ?>
				<option value="<?php echo esc_attr( $document->contact_id ); ?>" selected="selected"><?php echo esc_html( $document->billing_name ); ?></option>
			<?php endif; ?>
		</select>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=customers&add=yes' ) ); ?>" target="_blank" class="addon" title="<?php esc_attr_e( 'Add New Customer', 'wp-ever-accounting' ); ?>">
			<span class="dashicons dashicons-plus"></span>
		</a>
	</div>
</div>


<div class="bkit-form-group">
	<label for="currency_code">
		<?php esc_html_e( 'Currency', 'wp-ever-accounting' ); ?>
	</label>
	<select name="currency_code" id="currency_code" class="eac_select2" data-action="eac_json_search" data-type="currency" data-placeholder="<?php esc_attr_e( 'Select a currency', 'wp-ever-accounting' ); ?>">
		<?php if ( $document->currency ) : ?>
			<option value="<?php echo esc_attr( $document->currency_code ); ?>" selected="selected"><?php echo esc_html( $document->currency->formatted_name ); ?></option>
		<?php endif; ?>
	</select>
</div>

<div class="bkit-form-group">
	<label for="vat_exempt">
		<?php esc_html_e( 'VAT Exempt', 'wp-ever-accounting' ); ?>
	</label>
	<select name="vat_exempt" id="vat_exempt">
		<option value=""><?php esc_html_e( 'Select an option', 'wp-ever-accounting' ); ?></option>
		<option value="yes" <?php selected( 'yes', $document->vat_exempt ); ?>><?php esc_html_e( 'Yes', 'wp-ever-accounting' ); ?></option>
		<option value="no" <?php selected( 'no', $document->vat_exempt ); ?>><?php esc_html_e( 'No', 'wp-ever-accounting' ); ?></option>
	</select>
</div>

<div class="bkit-form-group tw-mt-0">
	<label for="discount_amount"><?php esc_html_e( 'Discount', 'wp-ever-accounting' ); ?></label>
	<div class="bkit-input-group">
		<input type="number" name="discount_amount" id="discount_amount" placeholder=".05" value="<?php echo esc_attr( $document->discount_amount ); ?>"/>
		<select name="discount_type" id="discount_type" class="addon" style="width: 80px;">
			<option value="fixed" <?php selected( 'fixed', $document->discount_type ); ?>><?php echo esc_html( $document->currency->symbol ); ?></option>
			<option value="percentage" <?php selected( 'percentage', $document->discount_type ); ?>><?php echo esc_html( '(%)' ); ?></option>
		</select>
	</div>
</div>
