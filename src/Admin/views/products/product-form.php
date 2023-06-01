<?php
/**
 * View: Item Form.
 *
 * @since 1.1.0
 * @package EverAccounting
 * @var \EverAccounting\Models\Product $product Item object.
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
						'type'        => 'text',
						'name'        => 'name',
						'label'       => __( 'Name', 'wp-ever-accounting' ),
						'value'       => $product->get_name(),
						'placeholder' => __( 'Laptop', 'wp-ever-accounting' ),
						'required'    => true,
						'class'       => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'type'        => 'price',
						'name'        => 'price',
						'label'       => __( 'Price', 'wp-ever-accounting' ),
						'value'       => $product->get_price(),
						'placeholder' => __( '1000.00', 'wp-ever-accounting' ),
						'required'    => true,
						'class'       => 'eac-col-6',
						'prefix'      => eac_get_base_currency(),
						/* translators: %s: currency symbol */
						'description' => sprintf( __( 'Enter the price of the item in %s.', 'wp-ever-accounting' ), eac_get_base_currency() ),
					)
				);
				// unit.
				eac_input_field(
					array(
						'type'        => 'select',
						'name'        => 'unit',
						'label'       => __( 'Unit', 'wp-ever-accounting' ),
						'value'       => $product->get_unit(),
						'options'     => eac_get_unit_types(),
						'placeholder' => __( 'Select item measurement unit', 'wp-ever-accounting' ),
						'class'       => 'eac-col-6',
					)
				);
				eac_input_field(
					array(
						'type'        => 'category',
						'name'        => 'category_id',
						'label'       => __( 'Category', 'wp-ever-accounting' ),
						'value'       => $product->get_category_id(),
						'placeholder' => __( 'Select item category', 'wp-ever-accounting' ),
						'query_args'  => 'type=item',
						'class'       => 'eac-col-6',
						'suffix'      => sprintf(
							'<a class="button" href="%s" title="%s"><span class="dashicons dashicons-plus"></span></a>',
							esc_url( eac_action_url( 'action=get_html_response&html_type=edit_category&subtype=product' ) ),
							__( 'Add Category', 'wp-ever-accounting' )
						),
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
				if ( eac_tax_enabled() ) :
					eac_input_field(
						array(
							'type'        => 'select',
							'name'        => 'is_taxable',
							'label'       => __( 'Taxable', 'wp-ever-accounting' ),
							'value'       => $product->get_taxable(),
							'options'     => array(
								'yes' => __( 'Yes', 'wp-ever-accounting' ),
								'no'  => __( 'No', 'wp-ever-accounting' ),
							),
							'placeholder' => __( 'Select taxable status', 'wp-ever-accounting' ),
							'class'       => 'eac-col-6',
						)
					);
					// tax rates.
					eac_input_field(
						array(
							'type'        => 'tax',
							'name'        => 'tax_ids',
							'multiple'    => true,
							'label'       => __( 'Taxes', 'wp-ever-accounting' ),
							'value'       => $product->get_tax_ids(),
							'placeholder' => __( 'Select tax rate', 'wp-ever-accounting' ),
							'class'       => 'eac-col-6',
							'tooltip'     => __( 'The selected tax rates will be applied to this item.', 'wp-ever-accounting' ),
							'suffix'      => sprintf(
								'<a class="button" href="%s" title="%s"><span class="dashicons dashicons-plus"></span></a>',
								esc_url( eac_action_url( 'action=get_html_response&html_type=edit_tax' ) ),
								__( 'Add Tax Rate', 'wp-ever-accounting' )
							),
						)
					);
				endif;
				eac_input_field(
					array(
						'type'        => 'textarea',
						'name'        => 'description',
						'label'       => __( 'Description', 'wp-ever-accounting' ),
						'value'       => $product->get_description(),
						'placeholder' => __( 'Enter item description', 'wp-ever-accounting' ),
						'class'       => 'eac-col-12',
					)
				);
				?>
			</div>
		</div>
	</div>

	<?php wp_nonce_field( 'eac_edit_product' ); ?>
	<input type="hidden" name="action" value="eac_edit_product">
	<input type="hidden" name="id" value="<?php echo esc_attr( $product->get_id() ); ?>">
</form>

