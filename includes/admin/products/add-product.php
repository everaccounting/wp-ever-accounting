<?php
defined( 'ABSPATH' ) || exit();
$base_url   = admin_url( 'admin.php?page=eaccounting-products' );
$product_id = empty( $_GET['product'] ) ? false : absint( $_GET['product'] );
$product    = new StdClass();
if ( $product_id ) {
	$product = eaccounting_get_product( $product_id );
}
$title = $product_id ? __( 'Update Product' ) : __( 'Add Product', 'wp-ever-accounting' );
?>

<?php echo sprintf( '<h1 class="wp-heading-inline">%s</h1>', $title ); ?>
<?php echo sprintf( '<a href="%s" class="page-title-action">%s</a>', $base_url, __( 'All Products', 'wp-ever-accounting' ) ); ?>
<hr class="wp-header-end">

<div class="ea-card">
	<form id="ea-product-form" action="" method="post">
		<?php wp_enqueue_script( 'eaccounting-products' ); ?>
		<?php do_action( 'eaccounting_add_product_form_top' ); ?>
		<div class="ea-row">
			<?php
			echo eaccounting_input_field( array(
				'label'         => __( 'Name', 'wp-ever-accounting' ),
				'name'          => 'name',
				'value'         => isset( $product->id ) ? $product->id : '',
				'placeholder'   => __( 'Product Name', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-shopping-basket',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'SKU', 'wp-ever-accounting' ),
				'name'          => 'sku',
				'value'         => isset( $product->sku ) ? $product->sku : '',
				'placeholder'   => __( 'Product SKU', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-key',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Sale Price', 'wp-ever-accounting' ),
				'name'          => 'sale_price',
				'value'         => isset( $product->sale_price ) ? $product->sale_price : '',
				'placeholder'   => __( '$120', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-money',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Purchase Price', 'wp-ever-accounting' ),
				'name'          => 'purchase_price',
				'value'         => isset( $product->purchase_price ) ? $product->purchase_price : '',
				'placeholder'   => __( '$100', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-money',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Quantity', 'wp-ever-accounting' ),
				'name'          => 'quantity',
				'value'         => isset( $product->quantity ) ? $product->quantity : '',
				'placeholder'   => __( '100', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-cubes',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_categories_dropdown( array(
				'name'          => 'category_id',
				'value'         => isset( $product->category_id ) ? $product->category_id : '',
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_taxes_dropdown( array(
				'wrapper_class' => 'ea-col-6',
				'value'         => isset( $product->tax_id ) ? $product->tax_id : '',
			) );
			echo eaccounting_switch_field( array(
				'label'         => __( 'Status', 'wp-ever-accounting' ),
				'name'          => 'status',
				'check'         => '1',
				'value'         => isset( $product->status ) ? $product->status : '0',
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_textarea_field( array(
				'label'         => __( 'Description', 'wp-ever-accounting' ),
				'name'          => 'description',
				'value'         => isset( $product->description ) ? $product->description : '',
				'wrapper_class' => 'ea-col-12',
			) );


			?>
		</div>


		<?php do_action( 'eaccounting_add_product_form_bottom' ); ?>
		<p>
			<input type="hidden" name="id" value="<?php echo $product_id;?>">
			<input type="hidden" name="action" value="eaccounting_add_product"/>
			<?php wp_nonce_field( 'eaccounting_product_nonce', 'nonce' ); ?>
			<input class="button button-primary" type="submit" value="<?php _e( 'Submit', 'wp-ever-accounting' ); ?>">
		</p>

	</form>
</div>

