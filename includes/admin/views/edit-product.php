<?php
defined( 'ABSPATH' ) || exit();
$base_url   = admin_url( 'admin.php?page=eaccounting-products' );
$product_id = empty( $_GET['product'] ) ? false : absint( $_GET['product'] );
$product    = new StdClass();
if ( $product_id ) {
	$product = eaccounting_get_product( $product_id );
}
$title = $product_id ? __( 'Update Product' ) : __( 'Add Product', 'wp-eaccounting' );
?>
<?php echo sprintf( '<h1 class="wp-heading-inline">%s</h1>', $title ); ?>
<?php echo sprintf( '<a href="%s" class="page-title-action">%s</a>', $base_url, __( 'All Products', 'wp-eaccounting' ) ); ?>
<div class="ea-card">
	<form action="" method="post">
		<?php do_action( 'eaccounting_add_product_form_top' ); ?>

		<div class="ea-row">
			<?php

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Name', 'wp-eaccounting' ),
				'name'          => 'name',
				'value'         => isset( $product->name ) ? $product->name : '',
				'placeholder'   => __( 'Product Name', 'wp-eaccounting' ),
				'icon'          => 'fa fa-shopping-basket',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'SKU', 'wp-eaccounting' ),
				'name'          => 'sku',
				'value'         => isset( $product->sku ) ? $product->sku : '',
				'icon'          => 'fa fa-key',
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::price_control( array(
				'label'         => __( 'Sale Price', 'wp-eaccounting' ),
				'name'          => 'sale_price',
				'value'         => isset( $product->sale_price ) ? eaccounting_price( $product->sale_price ) : '',
				'icon'          => 'fa fa-money',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::price_control( array(
				'label'         => __( 'Purchase Price', 'wp-eaccounting' ),
				'name'          => 'purchase_price',
				'value'         => isset( $product->purchase_price ) ? eaccounting_price( $product->purchase_price ) : '',
				'icon'          => 'fa fa-money',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Quantity', 'wp-eaccounting' ),
				'name'          => 'quantity',
				'required'      => true,
				'value'         => isset( $product->quantity ) ? $product->quantity : '',
				'icon'          => 'fa fa-cubes',
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::categories_dropdown( array(
				'name'          => 'category_id',
				'value'         => isset( $product->category_id ) ? $product->category_id : '',
				'wrapper_class' => 'ea-col-6',
				'icon'          => 'fa fa-folder-open-o',
			) );

			echo EAccounting_Form::textarea_control( array(
				'label'         => __( 'Description', 'wp-eaccounting' ),
				'name'          => 'description',
				'value'         => isset( $product->description ) ? $product->description : '',
				'wrapper_class' => 'ea-col-12',
			) );

			echo EAccounting_Form::status_control( array(
				'name'          => 'status',
				'value'         => isset( $product->status ) ? $product->status : 'active',
				'wrapper_class' => 'ea-col-6',
			) );

			?>
		</div>

		<?php do_action( 'eaccounting_add_product_form_bottom' ); ?>
		<p>
			<input type="hidden" name="id" value="<?php echo $product_id; ?>">
			<input type="hidden" name="eaccounting-action" value="edit_product">
			<?php wp_nonce_field( 'eaccounting_product_nonce' ); ?>
			<input class="button button-primary ea-submit" type="submit" value="<?php _e( 'Submit', 'wp-eaccounting' ); ?>">
		</p>
	</form>
</div>
