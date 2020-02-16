<?php
defined( 'ABSPATH' ) || exit();
$base_url  = admin_url( 'admin.php?page=eaccounting-inventory&tab=items' );
$item_id   = empty( $_GET['item'] ) ? false : absint( $_GET['item'] );
$item_rate = new StdClass();
if ( $item_id ) {
	$item_rate = eaccounting_get_product( $item_id );
}


$title = ! empty( $tax_rate->id ) ? __( 'Update Item' ) : __( 'Add Item', 'wp-ever-accounting' );
echo sprintf( '<h1 class="wp-heading-inline">%s</h1>', $title );
echo sprintf( '<a href="%s" class="page-title-action">%s</a>', $base_url, __( 'All Items', 'wp-ever-accounting' ) ); ?>
<div class="ea-card">
	<div class="ea-card-body">
		<form action="<?php echo add_query_arg( [ 'eaccounting-action' => 'add_tax_rate' ], $base_url ); ?>"
		      method="post">
			<?php do_action( 'eaccounting_add_item_form_top' ); ?>
			<div class="ea-row">
				<?php
				echo EAccounting_Form::input_control( array(
					'label'         => __( 'Name', 'wp-ever-eaccounting' ),
					'name'          => 'name',
					'value'         => isset( $item->name ) ? $item->name : '',
					'placeholder'   => __( 'Item Name', 'wp-ever-accounting' ),
					'icon'          => 'fa fa-shopping-basket',
					'required'      => true,
					'wrapper_class' => 'ea-col-6',
				) );


				echo EAccounting_Form::input_control( array(
					'label'         => __( 'SKU', 'wp-ever-accounting' ),
					'name'          => 'sku',
					'value'         => isset( $item->sku ) ? $item->sku : '',
					'icon'          => 'fa fa-key',
					'wrapper_class' => 'ea-col-6',
				) );

				echo EAccounting_Form::price_control( array(
					'label'         => __( 'Sale Price', 'wp-ever-accounting' ),
					'name'          => 'sale_price',
					'value'         => isset( $item->sale_price ) ? eaccounting_price( $item->sale_price ) : '',
					'icon'          => 'fa fa-money',
					'required'      => true,
					'wrapper_class' => 'ea-col-6',
				) );

				echo EAccounting_Form::price_control( array(
					'label'         => __( 'Purchase Price', 'wp-ever-accounting' ),
					'name'          => 'purchase_price',
					'value'         => isset( $item->purchase_price ) ? eaccounting_price( $item->purchase_price ) : '',
					'icon'          => 'fa fa-money',
					'required'      => true,
					'wrapper_class' => 'ea-col-6',
				) );

				echo EAccounting_Form::input_control( array(
					'label'         => __( 'Quantity', 'wp-ever-accounting' ),
					'name'          => 'quantity',
					'required'      => true,
					'value'         => isset( $item->quantity ) ? $item->quantity : '',
					'icon'          => 'fa fa-cubes',
					'wrapper_class' => 'ea-col-6',
				) );

				echo EAccounting_Form::categories_dropdown( array(
					'label'         => __( 'Category', 'wp-ever-accounting' ),
					'name'          => 'category_id',
					'type'          => 'item',
					'value'         => isset( $item->category_id ) ? $item->category_id : '',
					'wrapper_class' => 'ea-col-6',
					'icon'          => 'fa fa-folder-open-o',
				) );

				echo EAccounting_Form::taxes_dropdown( array(
					'label'         => __( 'Tax', 'wp-ever-accounting' ),
					'name'          => 'tax_id',
					'value'         => isset( $item->tax_id ) ? $item->tax_id : '',
					'wrapper_class' => 'ea-col-6',
					'icon'          => 'fa fa-folder-open-o',
				) );

				echo EAccounting_Form::status_control( array(
					'wrapper_class' => 'ea-col-6',
					'value'         => isset( $item->status ) ? $item->status : 'active',
				) );

				echo EAccounting_Form::textarea_control( array(
					'label'         => __( 'Description', 'wp-ever-accounting' ),
					'name'          => 'description',
					'value'         => isset( $item->description ) ? $item->description : '',
					'wrapper_class' => 'ea-col-12',
				) );

				echo EAccounting_Form::media_control([
					'label'         => __( 'Image', 'wp-ever-accounting' ),
					'name'          => 'image_id',
					'value'         => isset( $item->image_id ) ? $item->image_id : '5',
					'wrapper_class' => 'ea-col-6',
				])
				?>

			</div>
			<?php do_action( 'eaccounting_add_item_form_bottom' ); ?>

			<p>
				<input type="hidden" name="eaccounting-action" value="edit_item"/>
				<input type="hidden" name="id" value="<?php echo $item_id; ?>"/>
				<?php wp_nonce_field( 'eaccounting_item_nonce' ); ?>
				<input class="button button-primary" type="submit"
				       value="<?php _e( 'Submit', 'wp-ever-accounting' ); ?>">
			</p>
		</form>
	</div>
</div>
