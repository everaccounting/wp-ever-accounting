<?php
/**
 * Add Payment View.
 *
 * @package EverAccounting
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<script type="text/html" id="tmpl-eac-add-payment">
<form>
	<div class="eac-modal-header">
		<h3><?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?></h3>
	</div>
	<div class="eac-modal-body">
		<div class="eac-form-field">
			<label for="payment_date"><?php esc_html_e( 'Payment Date', 'wp-ever-accounting' ); ?><abbr title="required"></abbr></label>
			<input type="text" name="payment_date" id="payment_date" value="<?php echo esc_attr( date_i18n( get_option( 'date_format' ) ) ); ?>" class="eac_datepicker" required>
		</div>
		<div class="eac-form-field">
			<label for="account_id"><?php esc_html_e( 'Account', 'wp-ever-accounting' ); ?><abbr title="required"></abbr></label>
			<select name="account_id" id="account_id" class="eac_select2 account_id" data-action="eac_json_search" data-type="account" data-placeholder="<?php esc_html_e( 'Select an account', 'wp-ever-accounting' ); ?>" required>
				<option value=""><?php esc_html_e( 'Select an account', 'wp-ever-accounting' ); ?></option>
			</select>
		</div>
		<div class="eac-form-field">
			<label for="exchange_rate"><?php esc_html_e( 'Exchange Rate', 'wp-ever-accounting' ); ?><abbr title="required"></abbr></label>
			<input type="text" name="exchange_rate" id="exchange_rate" value="1.00" class="eac_exchange_rate" data-currency="<?php echo esc_attr( eac_base_currency() ); ?>" readonly required>
		</div>

		<div class="eac-form-field">
			<label for="amount"><?php esc_html_e( 'Amount', 'wp-ever-accounting' ); ?><abbr title="required"></abbr></label>
			<input type="text" name="amount" id="amount" class="eac_currency" required>
		</div>

		<div class="eac-form-field">
			<label for="payment_method"><?php esc_html_e( 'Payment Method', 'wp-ever-accounting' ); ?><abbr title="required"></abbr></label>
			<select name="payment_method" id="payment_method" required>
				<option value=""><?php esc_html_e( 'Select Payment Method', 'wp-ever-accounting' ); ?></option>
				<?php foreach ( eac_get_payment_methods() as $key => $value ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="eac-form-field">
			<label for="reference"><?php esc_html_e( 'Reference', 'wp-ever-accounting' ); ?></label>
			<input type="text" name="reference" id="reference">
		</div>

		<div class="eac-form-field">
			<label for="note"><?php esc_html_e( 'Description', 'wp-ever-accounting' ); ?></label>
			<textarea name="note" id="note" rows="3"></textarea>
		</div>
	</div>

	<div class="eac-modal-footer">
		<button type="submit" class="button button-primary"><?php esc_html_e( 'Add Payment', 'wp-ever-accounting' ); ?></button>
		<button type="button" class="button eac-modal-close"><?php esc_html_e( 'Cancel', 'wp-ever-accounting' ); ?></button>
	</div>
</form>
</script>
