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
	'columns' => $columns,
	'invoice' => $document->to_array(),
);
wp_localize_script( 'eac-invoices', 'eac_invoices_vars', $data );
wp_enqueue_script( 'eac-invoices' );
?>
<h1 class="wp-heading-inline">
	<?php esc_html_e( 'Add Invoice', 'wp-ever-accounting' ); ?>
	<a href="<?php echo esc_attr( remove_query_arg( 'add' ) ); ?>" title="<?php esc_attr_e( 'Go back', 'wp-ever-accounting' ); ?>">
		<span class="dashicons dashicons-undo"></span>
	</a>
</h1>

<form id="eac-invoice-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" class="eac-document">
	<div class="eac-poststuff">
		<div class="column-1">
			<table class="eac-invoice-table">
				<thead>
				<tr>
					<th class="eac-invoice-table__col eac-invoice-table__col-item" colspan="2"><?php esc_html_e( 'Item', 'wp-ever-accounting' ); ?></th>
					<?php foreach ( $columns as $key => $label ) : ?>
						<?php if ( 'item' !== $key ) : ?>
							<th class="eac-invoice-table__col eac-invoice-table__col-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></th>
						<?php endif; ?>
					<?php endforeach; ?>
				</tr>
				</thead>

				<tbody id="eac-invoice-items"></tbody>
				<tbody>
					<tr>
						<td colspan="2">
							<div class="eac-input-group">
								<select class="add-line-item eac_select2" data-action="eac_json_search" data-type="item" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
								<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=eac-items&add=yes' ) ); ?>" title="<?php esc_attr_e( 'Add New Item', 'wp-ever-accounting' ); ?>">
									<span class="dashicons dashicons-plus"></span>
								</a>
							</div>
						</td>
					</tr>
				</tbody>
				<tbody id="eac-invoice-totals"></tbody>
			</table>
		</div>
	</div>
	{{ data }}
</form>

<script type="text/template" id="tmpl-eac-invoice-item">
	<?php foreach ( $columns as $key => $label ) : ?>
		<?php if ( 'item' === $key ) : ?>
			<td class="eac-invoice-table__col eac-invoice-table__col-item" colspan="2">
				<input class="line-name" type="text" name="lines[{{ data.id }}][name]" value="{{ data.name }}" placeholder="<?php esc_attr_e( 'Name', 'wp-ever-accounting' ); ?>">
				<textarea class="line-description" name="lines[{{ data.id }}][description]" placeholder="<?php esc_attr_e( 'Description', 'wp-ever-accounting' ); ?>" maxlength="160">{{ data.description }}</textarea>


				<input type="hidden" name="lines[{{ data.id }}][id]" value="{{ data.id }}">
				<input type="hidden" name="lines[{{ data.id }}][item_id]" value="{{ data.item_id }}">
			</td>
		<?php else : ?>
			<td class="eac-invoice-table__col eac-invoice-table__col-<?php echo esc_attr( $key ); ?>">
				<?php
				switch ( $key ) {
					case 'price':
						?>
						<div class="eac-input-group">
							<span class="addon"><?php echo esc_html( eac_get_currency_symbol( $document->currency_code ) ); ?></span>
							<input class="line-price eac_decimal_input" type="text" name="lines[{{ data.id }}][price]" value="{{ data.price }}" placeholder="<?php esc_attr_e( 'Price', 'wp-ever-accounting' ); ?>">
						</div>
						<?php
						break;
					case 'quantity':
						printf( '<input class="line-quantity eac_decimal_input" type="number" name="lines[%s][quantity]" value="%s" placeholder="%s" />', '{{ data.id }}', '{{ data.quantity }}', esc_attr__( 'Quantity', 'wp-ever-accounting' ) );
						printf( '<input type="hidden" name="lines[%s][quantity]" value="%s" />', '{{ data.id }}', '{{ data.quantity }}' );
						printf( '<input type="hidden" name="lines[%s][unit]" value="%s" />', '{{ data.id }}', '{{ data.unit }}' );
						break;
					case 'tax':
						printf( '<span class="line-tax">%s</span>', '{{ data.tax || 0 }}' );
						printf( '<input type="hidden" name="lines[%s][tax]" value="%s" />', '{{ data.id }}', '{{ data.tax }}' );
						break;
					case 'subtotal':
						printf('<span class="line-subtotal">%s</span>', '{{ data.subtotal || 0 }}');
						printf( '<input type="hidden" name="lines[%s][subtotal]" value="%s" />', '{{ data.id }}', '{{ data.subtotal }}' );
						break;
					case 'actions':
						echo '<a href="#" class="remove-line"><span class="dashicons dashicons-trash"></span></a>';
						default:
						// code...
						break;
				}

				?>

			</td>
		<?php endif; ?>
	<?php endforeach; ?>
</script>
<script type="text/template" id="tmpl-eac-invoice-totals">
	<tr>
		<td colspan="2" class="eac-invoice-table__col eac-invoice-table__col-item"></td>
		<td> <?php esc_html_e( 'Subtotal', 'wp-ever-accounting' ); ?></td>
		<td class="eac-invoice-table__col eac-invoice-table__col-subtotal">
			<span class="subtotal">{{ data.subtotal }}</span>
			<input type="hidden" name="subtotal" value="{{ data.subtotal }}">
		</td>
	</tr>
	<tr>
		<td colspan="2" class="eac-invoice-table__col eac-invoice-table__col-item"></td>
		<td> <?php esc_html_e( 'Tax', 'wp-ever-accounting' ); ?></td>
		<td class="eac-invoice-table__col eac-invoice-table__col-tax">
			<span class="tax">{{ data.tax }}</span>
			<input type="hidden" name="tax" value="{{ data.tax }}">
		</td>
	</tr>

	<tr>
		<td colspan="2" class="eac-invoice-table__col eac-invoice-table__col-item"></td>
		<td> <?php esc_html_e( 'Total', 'wp-ever-accounting' ); ?></td>
		<td class="eac-invoice-table__col eac-invoice-table__col-total">
			<span class="total">{{ data.total }}</span>
			<input type="hidden" name="total" value="{{ data.total }}">
		</td>
	</tr>
</script>
