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

$categories = eac_get_categories(
	array(
		'limit'  => - 1,
		'type'   => 'item',
		'status' => 'active',
	)
);
?>

<form method="post" action="<?php echo esc_html( admin_url( 'admin-post.php' ) ); ?>">
	<div class="eac-poststuff">
		<div class="column-1">
			<div class="eac-card">
				<div class="eac-card__header">
					<h2 class="eac-card__title"><?php esc_html_e( 'Item details', 'wp-ever-accounting' ); ?></h2>
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
					eac_form_field(
						array(
							'type'           => 'text',
							'name'           => 'price',
							'label'          => __( 'Price', 'wp-ever-accounting' ),
							'value'          => $item->price,
							'placeholder'    => __( '1000.00', 'wp-ever-accounting' ),
							'class'          => 'eac_inputmask',
							'required'       => true,
							'prefix'         => eac_get_currency_symbol(),
							/* translators: %s: currency symbol */
							'tooltip'        => sprintf( __( 'Enter the price of the item in %s.', 'wp-ever-accounting' ), eac_get_base_currency() ),
							'data-inputmask' => '"alias": "decimal","placeholder": "0.00", "rightAlign": false',
						)
					);
					eac_form_field(
						array(
							'type'           => 'text',
							'name'           => 'cost',
							'label'          => __( 'Cost', 'wp-ever-accounting' ),
							'value'          => $item->cost,
							'class'          => 'eac_inputmask',
							'prefix'         => eac_get_currency_symbol(),
							/* translators: %s: currency symbol */
							'tooltip'        => sprintf( __( 'Enter the cost of the item in %s.', 'wp-ever-accounting' ), eac_get_base_currency() ),
							'data-inputmask' => '"alias": "decimal","placeholder": "0.00", "rightAlign": false',
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
							'options'     => Item::get_units(),
							'placeholder' => __( 'Select unit', 'wp-ever-accounting' ),
							'class'       => 'eac-select2',
						)
					);
					// taxable.
					eac_form_field(
						array(
							'type'    => 'select',
							'name'    => 'taxable',
							'label'   => __( 'Taxable', 'wp-ever-accounting' ),
							'value'   => filter_var( $item->taxable, FILTER_VALIDATE_BOOLEAN ) ? 'yes' : 'no',
							'options' => array(
								'yes' => __( 'Yes', 'wp-ever-accounting' ),
								'no'  => __( 'No', 'wp-ever-accounting' ),
							),
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

				<div class="eac-card__section">
					Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium, tempora!
				</div>
			</div>
		</div><!-- .column-1 -->

		<div class="column-2">
			<div class="eac-card">
				<div class="eac-card__header">
					<h2 class="eac-card__title"><?php esc_html_e( 'Actions', 'wp-ever-accounting' ); ?></h2>
				</div>

				<div class="eac-card__body">
					<?php
					eac_form_field(
						array(
							'type'    => 'select',
							'name'    => 'status',
							'label'   => __( 'Status', 'wp-ever-accounting' ),
							'value'   => $item->status,
							'options' => array(
								'active'   => __( 'Active', 'wp-ever-accounting' ),
								'inactive' => __( 'Inactive', 'wp-ever-accounting' ),
							),
						)
					);
					?>
				</div>

				<div class="eac-card__footer">
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
						<button class="button button-primary eac-w-100"><?php esc_html_e( 'Add Item', 'wp-ever-accounting' ); ?></button>
					<?php endif; ?>
				</div>
			</div>
		</div><!-- .column-2 -->
	</div><!-- .eac-poststuff -->
</form>
