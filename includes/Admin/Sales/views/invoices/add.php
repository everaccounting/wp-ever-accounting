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
	'columns'    => $columns,
	'rest_url'   => rest_url( 'eac/v1' ),
	'rest_nonce' => wp_create_nonce( 'wp_rest' ),
	'invoice'    => $document->to_array(),
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
				<tbody id="eac-invoice-totals"></tbody>

				<tfoot>
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
				</tfoot>
			</table>

		</div><!-- .column-1 -->
	</div><!-- .eac-poststuff -->
</form>

<script type="text/template" id="tmpl-invoice-item-template">
	<tr class="eac-invoice-table__item" data-id="{{ data.id }}">
		<td class="eac-invoice-table__col eac-invoice-table__col-item">
			<input type="hidden" name="items[{{ data.id }}][item_id]" value="{{ data.id }}">
			{{ data.name }}
		</td>

		<?php foreach ( $columns as $key => $label ) : ?>
			<?php if ( ! in_array( $key, array( 'item', 'actions' ) ) ) : ?>
				<td class="eac-invoice-table__col eac-invoice-table__col-<?php echo esc_attr( $key ); ?>">
					<input type="text" name="items[{{ data.id }}][{{ key }}]" value="{{ data[ key ] }}">
				</td>
			<?php endif; ?>
		<?php endforeach; ?>

		<td class="eac-invoice-table__col eac-invoice-table__col-actions">
			<button class="button button-link eac-remove-line-item" title="<?php esc_attr_e( 'Remove', 'wp-ever-accounting' ); ?>">
				<span class="dashicons dashicons-trash"></span>
			</button>
		</td>
	</tr>
</script>
