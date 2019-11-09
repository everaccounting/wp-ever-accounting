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
		<?php do_action( 'eaccounting_add_category_form_top' ); ?>
		<div class="ea-row">
			<?php
			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Name', 'wp-eaccounting' ),
				'name'          => 'name',
				'icon'          => 'fa fa-id-card-o',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
				'object'        => $category,
			) );

			echo EAccounting_Form::select_control( array(
				'label'         => __( 'Type', 'wp-eaccounting' ),
				'name'          => 'type',
				'options'       => eaccounting_get_category_types(),
				'icon'          => 'fa fa-bars',
				'required'      => true,
				'select2'       => true,
				'wrapper_class' => 'ea-col-6',
				'object'        => $category,
			) );

			echo EAccounting_Form::color_control( array(
				'label'         => __( 'Color', 'wp-eaccounting' ),
				'name'          => 'color',
				'default'       => eaccounting_get_random_hex_color(),
				'wrapper_class' => 'ea-col-6',
				'object'        => $category,
			) );

			echo EAccounting_Form::switch_control( array(
				'label'         => __( 'Status', 'wp-eaccounting' ),
				'name'          => 'status',
				'check'         => 'active',
				'wrapper_class' => 'ea-col-6',
				'object'        => $category,
			) );

			?>
		</div>
		<?php do_action( 'eaccounting_add_category_form_bottom' ); ?>
		<p>
			<input type="hidden" name="eaccounting-action" value="edit_category"/>
			<input type="hidden" name="id" value="<?php echo $category_id; ?>"/>
			<?php wp_nonce_field( 'eaccounting_category_nonce'); ?>
			<input class="button button-primary" type="submit" value="<?php _e( 'Submit', 'wp-ever-accounting' ); ?>">
		</p>
	</form>
</div>
