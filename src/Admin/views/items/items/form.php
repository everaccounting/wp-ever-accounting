<?php
/**
 * Item form
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $item \EverAccounting\Models\Item Item object.
 */

defined( 'ABSPATH' ) || exit;

$categories = eac_get_categories(
	array(
		'limit'  => - 1,
		'type'   => 'item',
		'status' => 'active',
	)
);
?>

<form method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<span data-wp-text="name"></span>
	<div class="bkit-poststuff">
		<div class="column-1">
			<div class="bkit-card">
				<div class="bkit-card__header">
					<h2 class="bkit-card__title"><?php esc_html_e( 'Item details', 'wp-ever-accounting' ); ?></h2>
				</div>

				<div class="bkit-card__body grid--fields">

					<?php
					eac_form_group(
						array(
							'type'        => 'text',
							'name'        => 'name',
							'label'       => __( 'Name', 'wp-ever-accounting' ),
							'value'       => $item->name,
							'placeholder' => __( 'Laptop', 'wp-ever-accounting' ),
							'required'    => true,
						)
					);
					eac_form_group(
						array(
							'type'        => 'select',
							'name'        => 'type',
							'required'    => true,
							'default'     => 'product',
							'label'       => __( 'Type', 'wp-ever-accounting' ),
							'value'       => $item->type,
							'options'     => eac_get_item_types(),
							'placeholder' => __( 'Select type', 'wp-ever-accounting' ),
						)
					);
					eac_form_group(
						array(
							'type'        => 'text',
							'name'        => 'price',
							'label'       => __( 'Price', 'wp-ever-accounting' ),
							'value'       => $item->price,
							'placeholder' => __( '1000.00', 'wp-ever-accounting' ),
							'required'    => true,
							'prefix'      => eac_get_base_currency(),
							/* translators: %s: currency symbol */
							'desc'        => sprintf( __( 'Enter the price of the item in %s.', 'wp-ever-accounting' ), eac_get_base_currency() ),
						)
					);
					eac_form_group(
						array(
							'type'        => 'text',
							'name'        => 'cost',
							'label'       => __( 'Cost', 'wp-ever-accounting' ),
							'value'       => $item->cost,
							'placeholder' => __( '1000.00', 'wp-ever-accounting' ),
							'prefix'      => eac_get_base_currency(),
							/* translators: %s: currency symbol */
							'desc'        => sprintf( __( 'Enter the cost of the item in %s.', 'wp-ever-accounting' ), eac_get_base_currency() ),
						)
					);
					eac_form_group(
						array(
							'type'             => 'select',
							'name'             => 'category_id',
							'label'            => __( 'Category', 'wp-ever-accounting' ),
							'value'            => $item->category_id,
							'options'          => wp_list_pluck( $categories, 'formatted_name', 'id' ),
							'data-placeholder' => __( 'Select item category', 'wp-ever-accounting' ),
							'class'            => 'eac-select2',
							'suffix'           => sprintf(
								'<a class="addon" href="%s" title="%s"><span class="dashicons dashicons-plus"></span></a>',
								esc_url( 'admin.php?page=eac-misc&tab=categories&add=yes' ),
								__( 'Add Category', 'wp-ever-accounting' )
							),
						)
					);
					eac_form_group(
						array(
							'type'        => 'select',
							'name'        => 'unit',
							'label'       => __( 'Unit', 'wp-ever-accounting' ),
							'value'       => $item->unit,
							'options'     => eac_get_unit_types(),
							'placeholder' => __( 'Select unit', 'wp-ever-accounting' ),
							'class'       => 'eac-select2',
						)
					);
					// taxable.
					eac_form_group(
						array(
							'type'    => 'select',
							'name'    => 'taxable',
							'label'   => __( 'Taxable', 'wp-ever-accounting' ),
							'value'   => $item->taxable,
							'options' => array(
								'yes' => __( 'Yes', 'wp-ever-accounting' ),
								'no'  => __( 'No', 'wp-ever-accounting' ),
							),
						)
					);
					// tax_ids.
					eac_form_group(
						array(
							'type'      => 'select',
							'name'      => 'tax_ids[]',
							'label'     => __( 'Taxes', 'wp-ever-accounting' ),
							'value'     => $item->tax_ids,
							// 'options'  => wp_list_pluck(
							// eac_get_taxes(
							// array(
							// 'limit'  => - 1,
							// 'status' => 'active',
							// )
							// ),
							// 'name',
							// 'id'
							// ),
								'class' => 'eac-select2',
							'multiple'  => true,
							'desc'      => __( 'The selected tax rates will be applied to this item.', 'wp-ever-accounting' ),
						)
					);

					eac_form_group(
						array(
							'type'          => 'textarea',
							'name'          => 'description',
							'label'         => __( 'Description', 'wp-ever-accounting' ),
							'value'         => $item->description,
							'wrapper_class' => 'is--full',
						)
					);

					?>
				</div>
			</div>
		</div><!-- .column-1 -->

		<div class="column-2">
			<div class="bkit-card">
				<div class="bkit-card__header">
					<h2 class="bkit-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
				</div>

				<div class="bkit-card__body">
					<div class="bkit-form-group">
						<label for="status">
							<?php esc_html_e( 'Status', 'wp-ever-accounting' ); ?>
						</label>
						<select name="status" id="status">
							<option value="active" <?php selected( 'active', $item->status ); ?>><?php esc_html_e( 'Active', 'wp-ever-accounting' ); ?></option>
							<option value="inactive" <?php selected( 'inactive', $item->status ); ?>><?php esc_html_e( 'Inactive', 'wp-ever-accounting' ); ?></option>
						</select>
					</div>
				</div>

				<div class="bkit-card__footer">
					<?php if ( $item->exists() ) : ?>
						<input type="hidden" name="id" value="<?php echo esc_attr( $item->id ); ?>"/>
					<?php endif; ?>
					<input type="hidden" name="action" value="eac_edit_item"/>
					<?php wp_nonce_field( 'eac_edit_item' ); ?>
					<?php if ( $item->exists() ) : ?>
						<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-items&id=' . $item->id ) ), 'bulk-items' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
					<?php endif; ?>
					<?php if ( $item->exists() ) : ?>
						<button class="button button-primary"><?php esc_html_e( 'Update Item', 'wp-ever-accounting' ); ?></button>
					<?php else : ?>
						<button class="button button-primary bkit-w-100"><?php esc_html_e( 'Add Item', 'wp-ever-accounting' ); ?></button>
					<?php endif; ?>
				</div>
			</div>
		</div><!-- .column-2 -->
	</div><!-- .bkit-poststuff -->
</form>