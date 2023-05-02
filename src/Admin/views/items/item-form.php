<?php
/**
 * View: Item Form.
 *
 * @since 1.1.0
 * @package EverAccounting
 * @var \EverAccounting\Models\Item $item Item object.
 */

defined( 'ABSPATH' ) || exit;
?>

<form id="eac-item-form" class="eac-form" method="post">
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Item Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_input_field(
					array(
						'type'          => 'text',
						'name'          => 'name',
						'label'         => __( 'Name', 'wp-ever-accounting' ),
						'value'         => $item->get_name(),
						'placeholder'   => __( 'Enter item name', 'wp-ever-accounting' ),
						'required'      => true,
						'wrapper_class' => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'type'          => 'select',
						'name'          => 'type',
						'label'         => __( 'Type', 'wp-ever-accounting' ),
						'value'         => $item->get_type(),
						'placeholder'   => __( 'Select item type', 'wp-ever-accounting' ),
						'options'       => array(
							'product' => __( 'Product', 'wp-ever-accounting' ),
							'service' => __( 'Service', 'wp-ever-accounting' ),
						),
						'required'      => true,
						'wrapper_class' => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'type'          => 'number',
						'name'          => 'price',
						'label'         => __( 'Price', 'wp-ever-accounting' ),
						'value'         => $item->get_price(),
						'placeholder'   => __( 'Enter item price', 'wp-ever-accounting' ),
						'required'      => true,
						'wrapper_class' => 'eac-col-6',
						'prefix'        => eac_get_default_currency(),
						/* translators: %s: currency symbol */
						'description'   => sprintf( __( 'Enter the price of the item in %s.', 'wp-ever-accounting' ), eac_get_default_currency() ),
					)
				);
				// unit.
				eac_input_field(
					array(
						'type'          => 'select',
						'name'          => 'unit',
						'label'         => __( 'Unit', 'wp-ever-accounting' ),
						'value'         => $item->get_unit(),
						'options'       => eac_get_item_units(),
						'placeholder'   => __( 'Enter item unit', 'wp-ever-accounting' ),
						'required'      => true,
						'wrapper_class' => 'eac-col-6',
					)
				);

				?>
			</div>
		</div>
		<div class="eac-card__separator"></div>
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'More Information', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_input_field(
					array(
						'type'          => 'category',
						'name'          => 'category_id',
						'label'         => __( 'Category', 'wp-ever-accounting' ),
						'value'         => $item->get_category_id(),
						'placeholder'   => __( 'Select item category', 'wp-ever-accounting' ),
						'wrapper_class' => 'eac-col-6',
						'subtype'       => 'item',
						'suffix'        => '<button class="eac-add-category" type="button"><span class="dashicons dashicons-plus"></span></button>',
					)
				);
				eac_input_field(
					array(
						'type'          => 'text',
						'name'          => 'sku',
						'label'         => __( 'SKU', 'wp-ever-accounting' ),
						'value'         => $item->get_sku(),
						'placeholder'   => __( 'Enter item SKU', 'wp-ever-accounting' ),
						'wrapper_class' => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'type'          => 'textarea',
						'name'          => 'description',
						'label'         => __( 'Description', 'wp-ever-accounting' ),
						'value'         => $item->get_description(),
						'placeholder'   => __( 'Enter item description', 'wp-ever-accounting' ),
						'wrapper_class' => 'eac-col-12',
					)
				);
				?>
			</div>
		</div>
	</div>

	<?php wp_nonce_field( 'eac_edit_item' ); ?>
	<input type="hidden" name="action" value="eac_edit_item">
	<input type="hidden" name="id" value="<?php echo esc_attr( $item->get_id() ); ?>">
</form>

