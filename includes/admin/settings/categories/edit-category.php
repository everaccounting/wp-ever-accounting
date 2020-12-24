<?php
/**
 * Admin Category Edit Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Settings/Categories
 * @since       1.0.2
 */


defined( 'ABSPATH' ) || exit();
$category_id = isset( $_REQUEST['category_id'] ) ? absint( $_REQUEST['category_id'] ) : null;
try {
	$category = new \EverAccounting\Models\Category( $category_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
$back_url = remove_query_arg( array( 'action', 'category_id' ) );
?>

<div class="ea-card">
	<div class="ea-card__header">
		<h3 class="ea-card__title"><?php echo $category->exists() ? __( 'Update Category', 'wp-ever-accounting' ) : __( 'Add Category', 'wp-ever-accounting' ); ?></h3>
		<button onclick="history.go(-1);" class="button-secondary"><?php _e( 'Go Back', 'wp-ever-accounting' ); ?></button>
	</div>

	<div class="ea-card__inside">
		<form id="ea-category-form" method="post">
			<div class="ea-row">
				<?php
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Name', 'wp-ever-accounting' ),
						'name'          => 'name',
						'placeholder'   => __( 'Enter Name', 'wp-ever-accounting' ),
						'value'         => $category->get_name(),
						'required'      => true,
					)
				);

				eaccounting_select2(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Type', 'wp-ever-accounting' ),
						'name'          => 'type',
						'value'         => $category->get_type(),
						'options'       => eaccounting_get_category_types(),
						'placeholder'   => __( 'Select Type', 'wp-ever-accounting' ),
						'required'      => true,
					)
				);

				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Color', 'wp-ever-accounting' ),
						'name'          => 'color',
						'placeholder'   => __( 'Enter Color', 'wp-ever-accounting' ),
						'value'         => $category->get_color(),
						'default'       => eaccounting_get_random_color(),
						'data_type'     => 'color',
						'style'         => 'width: calc(100% - 3em) !important;',
						'required'      => true,
					)
				);

				// eaccounting_toggle( array(
				// 'wrapper_class' => 'ea-col-6',
				// 'label'         => __( 'Enabled', 'wp-ever-accounting' ),
				// 'name'          => 'enabled',
				// 'value'         => $category->get_enabled( 'edit' ),
				// ) );

				eaccounting_hidden_input(
					array(
						'name'  => 'id',
						'value' => $category->get_id(),
					)
				);

				eaccounting_hidden_input(
					array(
						'name'  => 'action',
						'value' => 'eaccounting_edit_category',
					)
				);

				?>
			</div>
			<?php
			wp_nonce_field( 'ea_edit_category' );
			submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
			?>

		</form>
	</div>
</div>
