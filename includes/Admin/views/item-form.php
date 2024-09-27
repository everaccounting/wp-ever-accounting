<?php
/**
 * Item form
 *
 * @package EverAccounting
 * @version 1.0.0
 * @var $item Item Item object.
 */

use EverAccounting\Models\Item;

defined( 'ABSPATH' ) || exit;
?>

<form method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<div class="eac-poststuff">
		<div class="column-1">
			<div class="eac-card">
				<div class="eac-card__header">
					<h3 class="eac-card__title"><?php esc_html_e( 'Item Attributes', 'wp-ever-accounting' ); ?></h3>
				</div>

				<div class="eac-card__body grid--fields">

					<?php
					eac_form_field(
						array(
							'label'       => __( 'Name', 'wp-ever-accounting' ),
							'type'        => 'text',
							'name'        => 'name',
							'value'       => $item->name,
							'placeholder' => __( 'Laptop', 'wp-ever-accounting' ),
							'required'    => true,
						)
					);
					eac_form_field(
						array(
							'type'     => 'select',
							'name'     => 'type',
							'required' => true,
							'default'  => 'product',
							'label'    => __( 'Type', 'wp-ever-accounting' ),
							'value'    => $item->type,
							'options'  => EAC()->items->get_types(),
							'tooltip'  => __( 'Select the item type: Standard for regular products eligible for discounts, or Fee for extra charges that do not support discounts.', 'wp-ever-accounting' ),
						)
					);
					eac_form_field(
						array(
							'type'          => 'text',
							'name'          => 'price',
							'label'         => __( 'Price', 'wp-ever-accounting' ),
							'value'         => $item->price,
							'placeholder'   => __( '10.00', 'wp-ever-accounting' ),
							'required'      => true,
							/* translators: %s: currency symbol */
							'tooltip'       => sprintf( __( 'Enter the price of the item in %s.', 'wp-ever-accounting' ), eac_base_currency() ),
							'class'         => 'eac_amount',
							'data-currency' => eac_base_currency(),
						)
					);
					eac_form_field(
						array(
							'type'          => 'text',
							'name'          => 'cost',
							'label'         => __( 'Cost', 'wp-ever-accounting' ),
							'value'         => $item->cost,
							'placeholder'   => __( '8.00', 'wp-ever-accounting' ),
							/* translators: %s: currency symbol */
							'tooltip'       => sprintf( __( 'Enter the cost of the item in %s.', 'wp-ever-accounting' ), eac_base_currency() ),
							'class'         => 'eac_amount',
							'data-currency' => eac_base_currency(),
						)
					);
					eac_form_field(
						array(
							'type'             => 'select',
							'name'             => 'category_id',
							'label'            => __( 'Category', 'wp-ever-accounting' ),
							'value'            => $item->category_id,
							'options'          => array( $item->category ),
							'option_label'     => 'formatted_name',
							'option_value'     => 'id',
							'data-placeholder' => __( 'Select item category', 'wp-ever-accounting' ),
							'class'            => 'eac_select2',
							'data-action'      => 'eac_json_search',
							'data-type'        => 'category',
							'data-subtype'     => 'item',
							'suffix'           => sprintf(
								'<a class="addon" href="%s" target="_blank" title="%s"><span class="dashicons dashicons-plus"></span></a>',
								esc_url( 'admin.php?page=eac-misc&tab=categories&add=yes' ),
								__( 'Add Category', 'wp-ever-accounting' )
							),
						)
					);
					eac_form_field(
						array(
							'type'        => 'select',
							'name'        => 'unit',
							'label'       => __( 'Unit', 'wp-ever-accounting' ),
							'value'       => $item->unit,
							'options'     => EAC()->items->get_units(),
							'placeholder' => __( 'Select unit', 'wp-ever-accounting' ),
							'class'       => 'eac-select2',
						)
					);
					// tax_ids.
					eac_form_field(
						array(
							'type'         => 'select',
							'multiple'     => true,
							'name'         => 'tax_ids',
							'label'        => __( 'Taxes', 'wp-ever-accounting' ),
							'value'        => $item->tax_ids,
							'options'      => $item->taxes,
							'option_label' => 'formatted_name',
							'option_value' => 'id',
							'class'        => 'eac_select2',
							'data-action'  => 'eac_json_search',
							'data-type'    => 'tax',
							'tooltip'      => __( 'The selected tax rates will be applied to this item.', 'wp-ever-accounting' ),
						)
					);

					eac_form_field(
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
			</div><!-- .eac-card -->
		</div><!-- .column-1 -->

		<div class="column-2">
			<div class="eac-card">
				<div class="eac-card__header">
					<h3 class="eac-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h3>
				</div>
				<div class="eac-card__footer">
					<?php if ( $item->exists() ) : ?>
						<a class="eac_confirm_delete del" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'action', 'delete', admin_url( 'admin.php?page=eac-items&id=' . $item->id ) ), 'bulk-items' ) ); ?>"><?php esc_html_e( 'Delete', 'wp-ever-accounting' ); ?></a>
						<button class="button button-primary"><?php esc_html_e( 'Update Item', 'wp-ever-accounting' ); ?></button>
					<?php else : ?>
						<button class="button button-primary eac-width-full"><?php esc_html_e( 'Add Item', 'wp-ever-accounting' ); ?></button>
					<?php endif; ?>
				</div>
			</div>
		</div><!-- .column-2 -->
	</div><!-- .eac-poststuff -->

	<input type="hidden" name="id" value="<?php echo esc_attr( $item->id ); ?>"/>
	<input type="hidden" name="action" value="eac_edit_item"/>
	<?php wp_nonce_field( 'eac_edit_item' ); ?>
</form>
