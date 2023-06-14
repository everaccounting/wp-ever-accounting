<?php
/**
 * View: Item Form.
 *
 * @since 1.1.0
 * @package EverAccounting
 * @var \EverAccounting\Models\Item $item Item object.
 */

defined( 'ABSPATH' ) || exit;

$categories = eac_get_categories( array( 'include' => $item->get_category_id() ) );
$taxes      = eac_get_taxes( array( 'include' => $item->get_tax_ids() ) );
?>

<form id="eac-item-form" class="eac-ajax-form" method="post">
	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'Item Details', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				eac_form_field(
					array(
						'type'        => 'text',
						'name'        => 'name',
						'label'       => __( 'Name', 'wp-ever-accounting' ),
						'value'       => $item->get_name(),
						'placeholder' => __( 'Laptop', 'wp-ever-accounting' ),
						'required'    => true,
						'class'       => 'eac-col-6',
					)
				);
				eac_form_field(
					array(
						'type'        => 'money',
						'name'        => 'price',
						'label'       => __( 'Price', 'wp-ever-accounting' ),
						'value'       => eac_sanitize_money( $item->get_price() ),
						'placeholder' => __( '1000.00', 'wp-ever-accounting' ),
						'required'    => true,
						'class'       => 'eac-col-6',
						'prefix'      => eac_get_base_currency(),
						/* translators: %s: currency symbol */
						'description' => sprintf( __( 'Enter the price of the item in %s.', 'wp-ever-accounting' ), eac_get_base_currency() ),
					)
				);
				eac_form_field(
					array(
						'type'        => 'select',
						'name'        => 'category_id',
						'label'       => __( 'Category', 'wp-ever-accounting' ),
						'value'       => $item->get_category_id(),
						'options'     => wp_list_pluck( $categories, 'formatted_name', 'id' ),
						'placeholder' => __( 'Select item category', 'wp-ever-accounting' ),
						'class'       => 'eac-col-6',
						'input_class' => 'eac-select2',
						'attrs'       => 'data-action=eac_json_search&data-type=item_category',
						'suffix'      => sprintf(
							'<a class="button" href="%s" title="%s"><span class="dashicons dashicons-plus"></span></a>',
							esc_url( eac_action_url( 'action=get_html_response&html_type=edit_item_category' ) ),
							__( 'Add Category', 'wp-ever-accounting' )
						),
					)
				);
				// type.
				eac_form_field(
					array(
						'type'        => 'select',
						'name'        => 'type',
						'required'    => true,
						'select2'     => true,
						'default'     => 'product',
						'label'       => __( 'Type', 'wp-ever-accounting' ),
						'value'       => $item->get_type(),
						'options'     => eac_get_item_types(),
						'placeholder' => __( 'Select item type', 'wp-ever-accounting' ),
						'class'       => 'eac-col-6',
					)
				);
				// unit.
				eac_form_field(
					array(
						'type'        => 'select',
						'name'        => 'unit',
						'select2'     => true,
						'label'       => __( 'Unit', 'wp-ever-accounting' ),
						'value'       => $item->get_unit(),
						'default'     => 'unit',
						'options'     => eac_get_unit_types(),
						'placeholder' => __( 'Select item measurement unit', 'wp-ever-accounting' ),
						'class'       => 'eac-col-6',
						'input_class' => 'eac-select2',
					)
				);
				?>
			</div>
		</div>
	</div>

	<div class="eac-card">
		<div class="eac-card__header">
			<h2 class="eac-card__title"><?php esc_html_e( 'More Information', 'wp-ever-accounting' ); ?></h2>
		</div>
		<div class="eac-card__body">
			<div class="eac-columns">
				<?php
				if ( eac_tax_enabled() ) :
					eac_form_field(
						array(
							'type'        => 'select',
							'name'        => 'is_taxable',
							'label'       => __( 'Taxable', 'wp-ever-accounting' ),
							'value'       => $item->get_taxable(),
							'options'     => array(
								'yes' => __( 'Yes', 'wp-ever-accounting' ),
								'no'  => __( 'No', 'wp-ever-accounting' ),
							),
							'placeholder' => __( 'Select taxable status', 'wp-ever-accounting' ),
							'class'       => 'eac-col-6',
						)
					);
					// tax rates.
					eac_form_field(
						array(
							'type'        => 'select',
							'name'        => 'tax_ids',
							'multiple'    => true,
							'label'       => __( 'Taxes', 'wp-ever-accounting' ),
							'value'       => $item->get_tax_ids(),
							'options'     => wp_list_pluck( $taxes, 'formatted_name', 'id' ),
							'input_class' => 'eac-select2',
							'attrs'       => 'data-action=eac_json_search&type=tax',
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
				eac_form_field(
					array(
						'type'        => 'textarea',
						'name'        => 'description',
						'label'       => __( 'Description', 'wp-ever-accounting' ),
						'value'       => $item->get_description(),
						'placeholder' => __( 'Enter item description', 'wp-ever-accounting' ),
						'class'       => 'eac-col-12',
					)
				);
				?>
			</div>
		</div>
	</div>

	<?php wp_nonce_field( 'eac_edit_item' ); ?>
	<input type="hidden" name="currency" value="<?php echo esc_attr( eac_get_base_currency() ); ?>">
	<input type="hidden" name="action" value="eac_edit_item">
	<input type="hidden" name="id" value="<?php echo esc_attr( $item->get_id() ); ?>">
</form>
