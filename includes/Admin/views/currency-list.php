<?php
/**
 * Admin List of Currencies.
 * Page: Misc
 * Tab: Currencies
 *
 * @since 1.0.0
 * @package EverAccounting
 * @var $currency \EverAccounting\Models\Currency Currency object.
 */

defined( 'ABSPATH' ) || exit;
?>
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Currencies', 'wp-ever-accounting' ); ?>
		<a href="#" class="button button-small add-currency">
			<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
		</a>
	</h1>

	<form id="eac-add-currency" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" class="tw-box-border tw-w-full tw-overflow-hidden tw-relative tw-top-2.5 tw-bg-[#fff] tw-border tw-mx-auto tw-border-solid tw-border-[#c3c4c7] tw-p-8 tw-m-8 tw-max-w-2xl" style="display: none;">
		<div class="eac-form-field">
			<label for="currency"><?php esc_html_e( 'Currency', 'wp-ever-accounting' ); ?>&nbsp;<abbr title="required"></abbr></label>
			<select name="currency" id="currency" class="eac_select2" required>
				<?php foreach ( eac_get_currencies() as $currency ) : ?>
					<option value="<?php echo esc_attr( $currency['code'] ); ?>"><?php echo esc_html( $currency['formatted_name'] ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="eac-form-field">
			<label for="rate"><?php esc_html_e( 'Exchange Rate', 'wp-ever-accounting' ); ?>&nbsp;<abbr title="required"></abbr></label>
			<div class="eac-input-group">
				<div class="addon">1 <?php echo esc_html( eac_base_currency() ); ?> =</div>
				<input type="text" name="rate" id="rate" placeholder="<?php esc_html_e( 'Exchange Rate', 'wp-ever-accounting' ); ?>" required/>
			</div>
			<p class="description"><?php esc_html_e( 'Enter the exchange rate of the currency with respect to the base currency.', 'wp-ever-accounting' ); ?></p>
		</div>

		<?php wp_nonce_field( 'eac_add_currency' ); ?>
		<input type="hidden" name="action" value="eac_add_currency"/>
		<button type="submit" class="button button-primary"><?php esc_html_e( 'Add Currency', 'wp-ever-accounting' ); ?></button>
	</form>

	<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>">
		<?php $list_table->views(); ?>
		<?php $list_table->display(); ?>
		<input type="hidden" name="page" value="eac-misc"/>
		<input type="hidden" name="tab" value="currencies"/>
	</form>

<?php
