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
			<div class="eac-document__section document-items">
				<table>
					<thead>
					<tr>
						<th class="column-item" colspan="2"><?php esc_html_e( 'Item', 'wp-ever-accounting' ); ?></th>
						<?php foreach ( $columns as $key => $label ) : ?>
							<?php if ( 'item' !== $key ) : ?>
								<th class="column-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></th>
							<?php endif; ?>
						<?php endforeach; ?>
					</thead>


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
				</table>
			</div>

		</div>
	</div>
</form>

<script type="text/html" id="tmpl-eac-invoice-line">
	<tr class="line-item" data-item-id="{{ data.item_id }}">
		<?php foreach ( $columns as $key => $label ) : ?>
			<?php if ( 'item' === $key ) : ?>
				<td class="line-<?php echo esc_attr( $key ); ?>__item" colspan="2">
					hello
				</td>
			<?php else : ?>
				<td class="line-item__<?php echo esc_attr( $key ); ?>">
					Jello
				</td>
			<?php endif; ?>
		<?php endforeach; ?>
	</tr>
</script>
