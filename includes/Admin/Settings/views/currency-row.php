<?php
/**
 * Currency row view
 *
 * @package EverAccounting
 * @since 1.0.0
 * @var array $currency Currency data.
 */

defined( 'ABSPATH' ) || exit;
?>

<tr>
	<td>
		<?php echo esc_html( $currency['name'] ); ?>
	</td>
	<td>
		<input type="number" step="any" name="rate" value="<?php echo esc_attr( $currency['rate'] ); ?>" required/>
	</td>
	<td>
		<input type="number" name="precision" value="<?php echo esc_attr( $currency['precision'] ); ?>" required/>
	</td>
	<td>
		<input type="text" name="decimal_separator" value="<?php echo esc_attr( $currency['decimal_separator'] ); ?>" required/>
	</td>
	<td>
		<input type="text" name="thousand_separator" value="<?php echo esc_attr( $currency['thousand_separator'] ); ?>" required/>
	</td>
	<td>
		<select name="position" required>
			<option value="left" <?php selected( 'left', $currency['position'] ); ?>><?php esc_html_e( 'Left', 'wp-ever-accounting' ); ?></option>
			<option value="right" <?php selected( 'right', $currency['position'] ); ?>><?php esc_html_e( 'Right', 'wp-ever-accounting' ); ?></option>
		</select>
	</td>
</tr>
