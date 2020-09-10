<?php
/**
 * Admin Transfers Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Banking/Transfers
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();


function eaccounting_settings_currencies_tab() {
	if ( ! current_user_can( 'ea_manage_currency' ) ) {
		wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
	}
	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;

	if ( in_array( $action, [ 'edit', 'add' ] ) ) {
		require_once EACCOUNTING_ABSPATH . '/includes/admin/settings/currencies/edit-currency.php';
	} else {
		?>
		<h1>
			<?php _e( 'Currencies', 'wp-ever-accounting' ); ?>
			<a class="page-title-action" href="<?php echo eaccounting_admin_url( array( 'tab' => 'currencies', 'action' => 'add' ) ); ?>"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
			<a class="page-title-action" href="<?php echo eaccounting_admin_url( array( 'page' => 'ea-tools', 'tab' => 'import' ) ); ?>"><?php _e( 'Import', 'wp-ever-accounting' ); ?></a>
		</h1>
		<?php
		require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/list-table-currencies.php';
		$list_table = new \EverAccounting\Admin\ListTables\List_Table_Currency();
		$list_table->prepare_items();
		?>
		<div class="wrap">
			<?php

			/**
			 * Fires at the top of the admin currencies page.
			 *
			 * Use this hook to add content to this section of currencies.
			 *
			 * @since 1.0.2
			 */
			do_action( 'eaccounting_currencies_page_top' );

			?>
			<form id="ea-currencies-filter" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
				<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-currencies' ); ?>

				<input type="hidden" name="page" value="ea-settings"/>
				<input type="hidden" name="tab" value="currencies"/>

				<?php $list_table->views() ?>
				<?php $list_table->display() ?>
			</form>
			<?php
			/**
			 * Fires at the bottom of the admin currencies page.
			 *
			 * Use this hook to add content to this section of currencies Tab.
			 *
			 * @since 1.0.2
			 */
			do_action( 'eaccounting_currencies_page_bottom' );
			?>
		</div>
		<?php
	}
}

add_action( 'eaccounting_settings_tab_currencies', 'eaccounting_settings_currencies_tab' );
