<?php
/**
 * Admin Invoices Form.
 * Page: Sales
 * Tab: Invoices
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $document \EverAccounting\Models\Invoice Invoice object.
 */

defined( 'ABSPATH' ) || exit;

$columns = array(
	'item'     => __( 'Item', 'wp-ever-accounting' ),
	'price'    => __( 'Price', 'wp-ever-accounting' ),
	'quantity' => __( 'Quantity', 'wp-ever-accounting' ),
	'tax'      => __( 'Tax', 'wp-ever-accounting' ),
	'subtotal' => __( 'Subtotal', 'wp-ever-accounting' ),
	'actions'  => '&nbsp;',
);
?>
	<form id="eac-invoice-form" method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>" class="eac-document-form">
		<div class="eac-document-form__section document-items">
			<table class="eac-document-form__items">
				<thead>
				<tr>
					<?php foreach ( $columns as $key => $label ) : ?>
						<?php if ( 'item' === $key ) : ?>
							<th class="line-<?php echo esc_attr( $key ); ?>" colspan="2"><?php echo esc_html( $label ); ?></th>
						<?php else : ?>
							<th class="line-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></th>
						<?php endif; ?>
					<?php endforeach; ?>
				</thead>
				<tbody>
				<?php foreach ( $document->get_items() as $item_key => $item ) : ?>
				<tr>
					<?php foreach ( $columns as $key => $label ) : ?>
						<?php if ( 'item' === $key ) : ?>
							<td class="line-<?php echo esc_attr( $key ); ?>-name" colspan="2">
								<?php
								printf( '<input type="hidden" name="items[%s][id]" value="%s"/>', esc_attr( $item_key ), esc_attr( $item->id ) );
								printf( '<input type="hidden" name="items[%s][item_id]" value="%s"/>', esc_attr( $item_key ), esc_attr( $item->item_id ) );
								printf( '<input class="item-name" type="text" name="items[%s][name]" value="%s" readonly/>', esc_attr( $item_key ), esc_attr( $item->name ) );
								printf( '<textarea class="item-description" name="items[%s][description]" placeholder="%s" maxlength="160">%s</textarea>', esc_attr( $item_key ), esc_attr__( 'Description', 'wp-ever-accounting' ), esc_textarea( $item->get_description ) );
								?>
								<?php if ( $item->taxable ) : ?>
									<select name="items[<?php echo esc_attr( $item_key ); ?>][tax_ids]" class="item-taxes eac_select2 " data-action="eac_json_search" data-type="tax" data-placeholder="<?php esc_attr_e( 'Select a tax', 'wp-ever-accounting' ); ?>" multiple>
										<?php foreach ( $item->get_taxes() as $tax ) : ?>
											<option value="<?php echo esc_attr( $tax->tax_id ); ?>" selected="selected"><?php echo esc_html( $tax->name ); ?></option>
										<?php endforeach; ?>
									</select>
								<?php endif; ?>
							</td>
						<?php else : ?>
							<td class="line-<?php echo esc_attr( $key ); ?>">
								<?php
								switch ( $key ) {
									case 'price':
										printf( '<input class="line-price trigger-update" type="number" name="items[%s][price]" value="%s" placeholder="%s" />', esc_attr( $item_key ), esc_attr( $item->price ), esc_attr__( 'Price', 'wp-ever-accounting' ) );
										break;
									case 'quantity':
										printf( '<input class="line-quantity trigger-update" type="number" name="items[%s][quantity]" value="%s" placeholder="%s" />', esc_attr( $item_key ), esc_attr( $item->quantity ), esc_attr__( 'Quantity', 'wp-ever-accounting' ) );
										break;
									case 'tax':
										echo esc_html( eac_format_money( $item->tax_total, $document->currency_code ) );
										break;
									case 'subtotal':
										echo esc_html( eac_format_money( $item->subtotal, $document->currency_code ) );
										break;
									case 'actions':
										echo '<a href="#" class="remove-line-item"><span class="dashicons dashicons-trash"></span></a>';
										break;
									default:
										// code...
										break;
								}
								?>
							</td>

						<?php endif; ?>
					<?php endforeach; ?>
					<?php endforeach; ?>
				</tr>
				<tr>
					<td class="line-item" colspan="2">
						<div class="bkit-input-group">
							<select class="add-item eac_select2" data-action="eac_json_search" data-type="item" name="items[<?php echo esc_attr( PHP_INT_MAX ); ?>][item_id]" data-placeholder="<?php esc_attr_e( 'Select an item', 'wp-ever-accounting' ); ?>"></select>
							<a class="button" href="<?php echo esc_url( eac_action_url( 'action=get_html_response&html_type=edit_item' ) ); ?>" title="<?php esc_attr_e( 'Add New Item', 'wp-ever-accounting' ); ?>">
								<span class="dashicons dashicons-plus"></span>
							</a>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
		</div>

		<?php wp_nonce_field( 'eac_edit_invoice' ); ?>
		<input type="hidden" name="id" value="<?php echo esc_attr( $document->id ); ?>"/>
		<input type="hidden" name="action" value="eac_edit_invoice"/>
	</form>
<?php
