<?php
defined( 'ABSPATH' ) || exit();
$base_url    = admin_url( 'admin.php?page=eaccounting-misc&tab=categories' );
$category_id = empty( $_GET['category'] ) ? false : absint( $_GET['category'] );
$category    = new StdClass();
if ( $category_id ) {
	$category = eaccounting_get_category( $category_id );
}
$title = ! empty( $category->id ) ? __( 'Update Category', 'wp-ever-accounting' ) : __( 'Add Category', 'wp-ever-accounting' );
echo sprintf( '<h1 class="wp-heading-inline">%s</h1>', $title );
echo sprintf( '<a href="%s" class="page-title-action">%s</a>', $base_url, __( 'All Categories', 'wp-ever-accounting' ) ); ?>
<?php if(!empty($category->id)):?>
	<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting-action' => 'add_category' ), $base_url ) ); ?>" class="page-title-action">
		<?php _e( 'Add New', 'wp-ever-accounting' ); ?>
	</a>
<?php  endif; ?>
<div class="ea-card">
	<div class="ea-card-body">
		<form action="<?php echo add_query_arg( [ 'eaccounting-action' => 'add_category' ], $base_url ); ?>"
		      method="post">
			<?php do_action( 'eaccounting_add_category_form_top' ); ?>
			<div class="ea-row">
				<?php
				echo EAccounting_Form::input_control( array(
					'label'         => __( 'Name', 'wp-ever-accounting' ),
					'name'          => 'name',
					'value'         => isset( $category->name ) ? $category->name : '',
					'icon'          => 'fa fa-id-card-o',
					'required'      => true,
					'wrapper_class' => 'ea-col-6',
				) );

				echo EAccounting_Form::select_control( array(
					'label'         => __( 'Type', 'wp-ever-accounting' ),
					'name'          => 'type',
					'selected'      => isset( $category->type ) ? $category->type : '',
					'options'       => eaccounting_get_category_types(),
					'icon'          => 'fa fa-bars',
					'required'      => true,
					'select2'       => true,
					'wrapper_class' => 'ea-col-6',
				) );

				echo EAccounting_Form::color_control( array(
					'label'         => __( 'Color', 'wp-ever-accounting' ),
					'name'          => 'color',
					'value'         => isset( $category->color ) ? $category->color : '',
					'default'       => eaccounting_get_random_hex_color(),
					'wrapper_class' => 'ea-col-6',
				) );

				echo EAccounting_Form::status_control( array(
					'label'         => __( 'Status', 'wp-ever-accounting' ),
					'value'         => isset( $category->status ) ? $category->status : 'active',
					'wrapper_class' => 'ea-col-6',
				) );

				?>
			</div>
			<?php do_action( 'eaccounting_add_category_form_bottom' ); ?>
			<p>
				<input type="hidden" name="eaccounting-action" value="edit_category"/>
				<input type="hidden" name="id" value="<?php echo $category_id; ?>"/>
				<?php wp_nonce_field( 'eaccounting_category_nonce' ); ?>
				<input class="button button-primary" type="submit"
				       value="<?php _e( 'Submit', 'wp-ever-accounting' ); ?>">
			</p>
		</form>
	</div>
</div>
