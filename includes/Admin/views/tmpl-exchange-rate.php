<?php
/**
 * Exchange rate template.
 *
 * @since 1.0.0
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit;

?>
<label for="exchange_rate">
	<?php esc_html_e( 'Exchange Rate', 'ever-accounting' ); ?>&nbsp;<abbr title="required"></abbr>
</label>
<div class="eac-input-group">
	<span class="addon">1 {{data.code}} =</span>
	<input type="number" name="exchange_rate" id="exchange_rate" class="eac-input" value="{{data.rate}}" required>
	<span class="addon"><?php echo eac_get_base_currency(); ?></span>
</div>
