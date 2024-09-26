<?php
/**
 * Currency list view.
 *
 * @since   1.0.0
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit;

?>

<form id="eac-currency-list" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<?php wp_nonce_field( 'eac_currency_list' ); ?>
	<input type="hidden" name="action" value="eac_currency_list"/>
	<input type="hidden" name="eac_currency_list" value="1"/>

	<table class="wp-list-table widefat fixed striped">
		<thead>
		<tr>
			<th>
				<?php esc_html_e( 'Currency', 'wp-ever-accounting' ); ?>
				<?php eac_tooltip( __( 'The name of the currency.', 'wp-ever-accounting' ) ); ?>
			</th>
			<th>
				<?php esc_html_e( 'Symbol', 'wp-ever-accounting' ); ?>
				<?php eac_tooltip( __( 'The symbol of the currency.', 'wp-ever-accounting' ) ); ?>
			</th>
			<th>
				<?php esc_html_e( 'Exchange rate', 'wp-ever-accounting' ); ?>
				<?php eac_tooltip( __( 'The exchange rate of the currency to the base currency.', 'wp-ever-accounting' ) ); ?>
			</th>
			<th>
				<?php esc_html_e( 'Precision', 'wp-ever-accounting' ); ?>
				<?php eac_tooltip( __( 'The number of decimal places to display.', 'wp-ever-accounting' ) ); ?>
			</th>

			<th>
				<?php esc_html_e( 'Decimal separator', 'wp-ever-accounting' ); ?>
				<?php eac_tooltip( __( 'The character used to separate the integer part from the fractional part.', 'wp-ever-accounting' ) ); ?>
			</th>

			<th>
				<?php esc_html_e( 'Thousand separator', 'wp-ever-accounting' ); ?>
				<?php eac_tooltip( __( 'The character used to separate the thousands.', 'wp-ever-accounting' ) ); ?>
			</th>

			<th>
				<?php esc_html_e( 'Position', 'wp-ever-accounting' ); ?>
				<?php eac_tooltip( __( 'The position of the currency symbol.', 'wp-ever-accounting' ) ); ?>
			</th>

			<th style="width: 1%;">&nbsp;</th>
		</tr>
		</thead>
		<tbody>

		</tbody>
		<tfoot>
		<tr>
			<td colspan="2">
				<select id="add-currency" class="eac_select2" data-placeholder="<?php esc_attr_e( 'Select Currency', 'wp-ever-accounting' ); ?>">
					<option value=""><?php esc_html_e( 'Select Currency', 'wp-ever-accounting' ); ?></option>
					<?php foreach ( \EverAccounting\Utilities\I18n::get_currencies() as $code => $currency ) : ?>
						<option value="<?php echo esc_attr( $code ); ?>"
								data-name="<?php echo esc_attr( $currency['name'] ); ?>"
								data-symbol="<?php echo esc_attr( $currency['symbol'] ); ?>"
								data-rate="<?php echo esc_attr( $currency['rate'] ); ?>"
								data-precision="<?php echo esc_attr( $currency['precision'] ); ?>"
								data-decimal-separator="<?php echo esc_attr( $currency['decimal_separator'] ); ?>"
								data-thousand-separator="<?php echo esc_attr( $currency['thousand_separator'] ); ?>"
								data-position="<?php echo esc_attr( $currency['position'] ); ?>">
							<?php echo esc_html( $currency['code'] . '-' . $currency['name'] ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</td>
			<td colspan="6">
				<button type="button" class="button" id="eac-add-currency">
					<?php esc_html_e( 'Add Currency', 'wp-ever-accounting' ); ?>
				</button>
			</td>
		</tfoot>
	</table>

	<?php submit_button( __( 'Save Changes', 'wp-ever-accounting' ), 'primary', 'submit', true ); ?>
</form>
