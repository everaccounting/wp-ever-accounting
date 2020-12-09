<?php
/**
 * Admin Api Keys Settings Page.
 *
 * @since       1.1.0
 * @subpackage  Admin/Settings
 * @package     EverAccounting
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit();

function eaccounting_settings_keys_section() {
	if ( ! current_user_can( 'manage_eaccounting' ) ) {
		wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
	}
	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;

	if ( in_array( $action, [ 'edit', 'add' ] ) ) {
		require_once dirname( __FILE__ ) . '/edit-api-keys.php';
	} else { ?>
		<h1>
			<?php _e( 'Api Keys', 'wp-ever-accounting' ); ?>
			<a class="page-title-action" href="<?php echo eaccounting_admin_url( array( 'tab' => 'advanced', 'action' => 'add', ) ); ?>"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
		</h1>
		<?php
		require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/list-table-api-keys.php';
		$list_table = new \EverAccounting\Admin\ListTables\List_Table_Api_Keys();
		$list_table->prepare_items();

		/**
		 * Fires at the top of the admin api_keys page.
		 *
		 * Use this hook to add content to this section of api_keys.
		 *
		 * @since 1.1.0
		 */
		do_action( 'eaccounting_api_keys_page_top' );
		?>
		<form id="ea-api-keys-filter" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-categories' ); ?>

			<input type="hidden" name="page" value="ea-settings"/>
			<input type="hidden" name="tab" value="advanced"/>

			<?php $list_table->views(); ?>
			<?php $list_table->display(); ?>
		</form>
		<?php
		/**
		 * Fires at the bottom of the admin api_keys page.
		 *
		 * Use this hook to add content to this section of advanced Tab.
		 *
		 * @since 1.1.0
		 */
		do_action( 'eaccounting_api_keys_page_bottom' );
		?>
		<?php
	}
}

//todo remove main section hook later
add_action( 'eaccounting_settings_tab_advanced_section_keys', 'eaccounting_settings_keys_section' );
add_action( 'eaccounting_settings_tab_advanced_section_main', 'eaccounting_settings_keys_section' );
