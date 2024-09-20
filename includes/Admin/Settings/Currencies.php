<?php

namespace EverAccounting\Admin\Settings;

/**
 * Class Currencies.
 *
 * @since 1.0.0
 * @package EverAccounting\Admin\Settings
 */
class Currencies extends Page {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id    = 'currencies';
		$this->label = __( 'Currencies', 'wp-ever-accounting' );

		parent::__construct();
	}

	/**
	 * Output the HTML for the settings.
	 */
	public function output() {
		wp_add_inline_script( 'eac-admin-currencies', 'var eac_admin_currencies_vars = ' . json_encode( [] ) . ';', 'after' );
		?>
		<table id="eac-admin-currencies" class="widefat fixed" style="margin-top: 20px;">
			<thead>
			<tr>
				<td width="10%"><?php esc_html_e( 'Currency', 'wp-ever-accounting' ); ?></td>
				<td width="10%"><?php esc_html_e( 'Symbol', 'wp-ever-accounting' ); ?></td>
				<td width="10%"><?php esc_html_e( 'Exchange Rate', 'wp-ever-accounting' ); ?></td>
				<td width="10%"><?php esc_html_e( 'Precision', 'wp-ever-accounting' ); ?></td>
				<td width="5%"><?php esc_html_e( 'Action', 'wp-ever-accounting' ); ?></td>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="5">
					<select name="" id="" class="add-currency">
						<?php foreach ( eac_get_currencies() as $currency ) : ?>
							<option value="<?php echo esc_attr( $currency['code'] ); ?>">
								<?php echo esc_html( $currency['name'] ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<button class="button button-secondary add-currency" type="button">
						<?php esc_html_e( 'Add Currency', 'wp-ever-accounting' ); ?>
					</button>
				</td>
			</tr>
			</tfoot>
			<tbody id="currencies">
			<tr>
				<th colspan="5" style="text-align: center;"><?php esc_html_e( 'Loading&hellip;', 'wp-ever-accounting' ); ?></th>
			</tr>
			</tbody>
		</table>

		<script type="text/html" id="tmpl-eac-currency-table-row">
			<tr>
				<td>
					<span class="currency-name">{{ data.code - data.name }}</span>
					<input type="hidden" value="{{ data.code }}" name="currencies[{{ data.code }}][code]" data-attribute="code"/>
				</td>
				<td>
					<input type="text" value="{{ data.symbol }}" name="currencies[{{ data.code }}][symbol]" data-attribute="symbol"/>
				</td>
				<td>
					<input type="text" value="{{ data.exchange_rate }}" name="currencies[{{ data.code }}][exchange_rate]" data-attribute="exchange_rate"/>
				</td>
				<td>
					<input type="number" value="{{ data.precision }}" name="currencies[{{ data.code }}][precision]" data-attribute="precision"/>
				</td>
				<td>
					<button class="button button-small remove-currency" data-code="{{ data.code }}">
						<?php esc_html_e( 'Remove', 'wp-ever-accounting' ); ?>
					</button>
				</td>
			</tr>
		</script>

		<script type="text/html" id="tmpl-eac-currency-table-empty">
			<tr>
				<th colspan="5" style="text-align: center;"><?php esc_html_e( 'No currencies found.', 'wp-ever-accounting' ); ?></th>
			</tr>
		</script>

		<script type="text/html" id="tmpl-eac-currency-table-actions">
			<tr>
				<td colspan="5">
					<select name="" id="" class="add-currency">
						<?php foreach ( eac_get_currencies() as $currency ) : ?>
							<option value="<?php echo esc_attr( $currency['code'] ); ?>">
								<?php echo esc_html( $currency['name'] ); ?>
							</option>
						<?php endforeach; ?>
					</select>
					<button class="button button-secondary add-currency" type="button">
						<?php esc_html_e( 'Add Currency', 'wp-ever-accounting' ); ?>
					</button>
				</td>
			</tr>
		</script>


		<?php
	}
}
