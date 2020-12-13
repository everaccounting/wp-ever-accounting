<?php
/**
 * Admin API keys Edit Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Settings/ApiKeys
 * @since       1.1.0
 */

defined( 'ABSPATH' ) || exit();
$api_key_id = isset( $_REQUEST['api_key_id'] ) ? absint( $_REQUEST['api_key_id'] ) : null;

try {
	$api_key = new \EverAccounting\Models\ApiKey( $api_key_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
$back_url = remove_query_arg( array( 'action', 'id' ) );

?>
<div class="ea-form-card">
	<div class="ea-card ea-form-card__header is-compact">
		<h3 class="ea-form-card__header-title"><?php echo $api_key->exists() ? __( 'Update API Key', 'wp-ever-accounting' ) : __( 'Add API Key', 'wp-ever-accounting' ); ?></h3>
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
						'value'         => $api_key->get_description(),

					)
				);
				eaccounting_select2(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'User', 'wp-ever-accounting' ),
						'name'          => 'user_id',
						'value'         => $api_key->get_user_id(),
						'options'       => eaccounting_get_admin_users(),
						'placeholder'   => __( 'Select user', 'wp-ever-accounting' ),
						'required'      => true,
					)
				);
				eaccounting_select2(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Permissions', 'wp-ever-accounting' ),
						'name'          => 'permission',
						'value'         => $api_key->get_permission(),
						'options'       => array(
							'read'       => __( 'Read', 'wp-ever-accounting' ),
							'write'      => __( 'Write', 'wp-ever-accounting' ),
							'read_write' => __( 'Read/Write', 'wp-ever-accounting' )
						),
						'default'       => 'read',
						'required'      => true,
					)
				);
				if ( $api_key->exists() ) {
					eaccounting_text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'API Key ending in', 'wp-ever-accounting' ),
							'name'          => 'truncated_key',
							'value'         => $api_key->get_truncated_key(),
							'readonly' => true
						)
					);

					$restricted_last_access_value = array(
						null,
						'0000-00-00 00:00:00',
						'0000-00-00'

					);

					eaccounting_text_input(
						array(
							'wrapper_class' => 'ea-col-6',
							'label'         => __( 'Last access', 'wp-ever-accounting' ),
							'name'          => 'last_access',
							'value'         => ( ! in_array( $api_key->get_last_access(),$restricted_last_access_value ) ) ? $api_key->get_last_access() : __('Unknown','wp-ever-accounting'),
							'readonly' => true
						)
					);
				}
				eaccounting_hidden_input(
					array(
						'name'  => 'id',
						'value' => $api_key->get_id(),
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
		<div id="api-key-container" style="display: none">
			<div class="notice notice-warning notice-large api-key-notice" style="display: none">
				<?php
				echo sprintf(
						'<p><strong>%s:</strong> %s',
						__( 'Note', 'wp-ever-accounting' ),
						__( 'API Key generated successfully. Make sure to copy your new keys now as the secret key will be hidden once you leave this page.', 'wp-ever-accounting'
						)
				);
				?>
			</div>
			<div class="ea-row">
				<div class="ea-form-field ea-col-8">
					<label class="ea-label" for="api_key"><?php _e( 'API Key', 'wp-ever-accounting' ); ?></label>
					<input type="text" class="ea-input-control short" style="" name="api_key" id="api_key" value="" placeholder="" readonly="readonly">
					<button type="button" class="button-secondary copy-btn"><?php _e('Copy','wp-ever-accounting');?></button>
				</div>
				<div class="ea-form-field ea-col-8">
					<label class="ea-label" for="api_secret"><?php _e( 'API Secret', 'wp-ever-accounting' ); ?></label>
					<input type="text" class="ea-input-control short" style="" name="api_secret" id="api_secret" value="" placeholder="" readonly="readonly">
					<button type="button" class="button-secondary copy-btn"><?php _e('Copy','wp-ever-accounting');?></button>
				</div>

			</div>
		</div>
	</div>
</div>

