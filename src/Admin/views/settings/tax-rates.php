<?php
/**
 * View: List Tax Rates
 *
 * @since    1.1.6
 * @package     EverAccounting
 * @subpackage  Admin/View/Setttings
 */

defined( 'ABSPATH' ) || exit();

?>

<div class="eac-page__header eac-mt-20">
	<div class="eac-page__header-col">
		<h2 class="eac-page__title"><?php esc_html_e( 'Tax Rates', 'ever-accounting' ); ?></h2>
	</div>
	<div class="eac-page__header-col">
		<?php submit_button( __( 'Save Rates', 'wp-ever-accounting' ), 'primary', 'submit', false, array( 'form' => 'eac-item-form' ) ); ?>
	</div>
</div>
<form id="eac-tax-rates-form" method="post">
	<table class="fixed striped widefat eac-mt-20">
		<thead>
		<tr>
			<?php foreach ( $columns as $column => $label ) : ?>
				<th><?php echo esc_html( $label ); ?></th>
			<?php endforeach; ?>
		</tr>
		</thead>
		<tbody>
		<?php foreach ( $tax_rates as $tax_rate ) : ?>
			<tr>
				<?php foreach ( $columns as $column => $label ) : ?>
					<!--rate field is editable-->
					<!--compound field is a checkbox-->
					<!--name field is editable-->
					<?php if ( 'name' === $column ) : ?>
						<td>
							<input type="text" name="tax_rates[<?php echo esc_attr( $column ); ?>][]" value="<?php echo esc_attr( $tax_rate[ $column ] ); ?>" placeholder="<?php esc_attr_e( 'Name', 'wp-ever-accounting' ); ?>" />
						</td>
					<?php elseif ( 'compound' === $column ) : ?>
						<td>
							<input type="checkbox" name="tax_rates[<?php echo esc_attr( $column ); ?>][]" value="yes" <?php checked( 'yes', $tax_rate[ $column ] ); ?> placeholder="<?php esc_attr_e( 'Compound', 'wp-ever-accounting' ); ?>" />
						</td>
					<?php else : ?>
						<td>
							<input type="text" name="tax_rates[<?php echo esc_attr( $column ); ?>][]" value="<?php echo esc_attr( $tax_rate[ $column ] ); ?>"  placeholder="<?php esc_attr_e( 'Rate', 'wp-ever-accounting' ); ?>" />
						</td>
					<?php endif; ?>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
			<!--add row-->
		<tr class="eac-p-20">
			<td colspan="<?php echo esc_attr( count( $columns ) ); ?>">
				<button type="button" class="button" id="eac-add-tax-rate"><?php esc_html_e( 'Add Tax Rate', 'wp-ever-accounting' ); ?></button>
			</td>
		</tr>
		</tbody>
	</table>
</form>

<script>
	jQuery( document ).ready( function( $ ) {
		$( '#eac-add-tax-rate' ).on( 'click', function() {
			var $table = $( '#eac-tax-rates-form table' );
			var $selfrow = $( this ).closest( 'tr' );
			var $row = $( '<tr></tr>' );
			var columns = <?php echo wp_json_encode( $columns ); ?>;

			$.each( columns, function( column, label ) {
				var $cell = $( '<td></td>' );
				var $input = $( '<input />' );

				$input.attr( 'type', 'text' );
				$input.attr( 'name', 'tax_rates[' + column + '][]' );
				$input.attr( 'placeholder', label );

				$cell.append( $input );
				$row.append( $cell );
			} );

			// append the row before self row
			$row.insertBefore( $selfrow );
		} );
	} );
</script>
