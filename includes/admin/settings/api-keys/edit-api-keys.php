<?php
/**
 * Admin Api keys Edit Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Settings/ApiKeys
 * @since       1.1.0
 */

defined( 'ABSPATH' ) || exit();
$api_key_id = isset( $_REQUEST['api_key_id'] ) ? absint( $_REQUEST['api_key_id'] ) : null;

//todo update when adding the function
//try {
//	$api_key = new \EverAccounting\Models\Category( $category_id );
//} catch ( Exception $e ) {
//	wp_die( $e->getMessage() );
//}
$back_url = remove_query_arg( array( 'action', 'id' ) );
?>
<div class="ea-form-card">
	<div class="ea-card ea-form-card__header is-compact">
<!--		<h3 class="ea-form-card__header-title">--><?php ////echo $category->exists() ? __( 'Update Category', 'wp-ever-accounting' ) : __( 'Add Category', 'wp-ever-accounting' ); ?><!--</h3>-->
			<h3 class="ea-form-card__header-title"><?php echo  __( 'Add Api Key', 'wp-ever-accounting' ); ?></h3>
		<a href="<?php echo $back_url; ?>" class="button button-secondary"><span class="dashicons dashicons-arrow-left-alt"></span><?php _e( 'Back', 'wp-ever-accounting' ); ?></a>
	</div>
	<div class="ea-card">
		<form id="ea-api-key-form" method="post">
			<div class="ea-row">
				<?php
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Description', 'wp-ever-accounting' ),
						'name'          => 'description',
						'placeholder'   => __( 'Enter Api Description', 'wp-ever-accounting' ),
						'value'         => '',

					)
				);
				eaccounting_select2(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'User', 'wp-ever-accounting' ),
						'name'          => 'user_id',
						'value'         => '',
						'options'       => eaccoutning_get_admin_users(),
						'placeholder'   => __( 'Select user', 'wp-ever-accounting' ),
						'required'      => true,
					)
				);
				eaccounting_select2(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Permissions', 'wp-ever-accounting' ),
						'name'          => 'permission',
						'value'         => '',
						'options'       => array(
							'read'       => __( 'Read', 'wp-ever-accounting' ),
							'write'      => __( 'Write', 'wp-ever-accounting' ),
							'read_write' => __( 'Read/Write', 'wp-ever-accounting' )
						),
						'default' => 'read',
						'required'      => true,
					)
				);

				eaccounting_hidden_input(
					array(
						'name'  => 'id',
						'value' => 0,
					)
				);

				eaccounting_hidden_input(
					array(
						'name'  => 'action',
						'value' => 'eaccounting_edit_api_key',
					)
				);
				?>
			</div>
			<?php
			wp_nonce_field( 'ea_edit_api_key' );
			submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
			?>
		</form>
	</div>
</div>

