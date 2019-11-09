<?php
defined( 'ABSPATH' ) || exit();
$base_url    = admin_url( 'admin.php?page=eaccounting-misc&tab=categories' );
$category_id = empty( $_GET['category'] ) ? false : absint( $_GET['category'] );
$category    = new StdClass();
if ( $category_id ) {
	$category = eaccounting_get_category( $category_id );
}

$title = ! empty( $category->id ) ? __( 'Update Category' ) : __( 'Add Category', 'wp-eaccounting' );
echo sprintf( '<h1 class="wp-heading-inline">%s</h1>', $title );
echo sprintf( '<a href="%s" class="page-title-action">%s</a>', $base_url, __( 'All Categories', 'wp-ever-accounting' ) ); ?>

<div class="ea-card">
	<form action="<?php echo add_query_arg( [ 'eaccounting-action' => 'add_category' ], $base_url ); ?>" method="post">
		<?php

		echo EAccounting_Form::input_control( array(
			'label'         => __( 'Name', 'wp-eaccounting' ),
			'name'          => 'name',
			'icon'          => 'fa fa-id-card-o',
			'required'      => true,
			'wrapper_class' => 'ea-col-6',
		) );

		echo EAccounting_Form::input_control( array(
			'label'         => __( 'SKU', 'wp-eaccounting' ),
			'name'          => 'sku',
			'icon'          => 'fa fa-key',
			'wrapper_class' => 'ea-col-6',
		) );


		echo EAccounting_Form::textarea_control( array(
			'label'         => __( 'Description', 'wp-eaccounting' ),
			'name'          => 'description',
			'wrapper_class' => 'ea-col-6',
		) );

		echo EAccounting_Form::price_control( array(
			'label'         => __( 'Sale Price', 'wp-eaccounting' ),
			'name'          => 'sale_price',
			'default'       => '0',
			'icon'          => 'fa fa-money',
			'wrapper_class' => 'ea-col-6',
		) );


		echo EAccounting_Form::select_control( array(
			'label'   => __( 'Category', 'wp-eaccounting' ),
			'name'    => 'category_id',
			'options' => wp_list_pluck( eaccounting_get_categories( array(
				'per_page' => '-1',
				'status' => 'active',
				'fields'   => array( 'id', 'name' ),
			) ), 'name', 'id' ),
			'icon'    => 'fa fa-folder-open-o',
			'multiple' => true,
			'select2' => true,
		) );

		echo EAccounting_Form::select_control( array(
			'label'   => __( 'Tax', 'wp-eaccounting' ),
			'name'    => 'tax_id',
			'options' => wp_list_pluck( eaccounting_get_taxes( array(
				'per_page' => '-1',
				'status' => 'active',
				'fields'   => array( 'id', 'name' ),
			) ), 'name', 'id' ),
			'icon'    => 'fa fa-percent',
			'select2' => true,
		) );

		echo EAccounting_Form::date_control( array(
			'label'   => __( 'Date', 'wp-eaccounting' ),
			'name'    => 'due-at',
			'icon'    => 'fa fa-calendar',
		) );

		echo EAccounting_Form::color_control( array(
			'label'   => __( 'Color', 'wp-eaccounting' ),
			'name'    => 'color',
		) );

		?>
		<input class="button button-primary" type="submit" value="<?php _e( 'Submit', 'wp-ever-accounting' ); ?>">
	</form>
</div>
