<?php
/**
 * Admin Taxes Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Misc/Tax
 * @since       1.1.0
 */
defined( 'ABSPATH' ) || exit();


function eaccounting_misc_taxes_tab() {
	if ( ! current_user_can( 'ea_manage_currency' ) ) {
		wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
	}
	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;

	if ( in_array( $action, array( 'edit', 'add' ), true ) ) {
		require_once dirname( __FILE__ ) . '/edit-tax.php';
	} else {
		?>
		<h1>
			<?php _e( 'Taxes', 'wp-ever-accounting' ); ?>
			<a class="page-title-action" href="
			<?php
			echo eaccounting_admin_url(
				array(
					'tab'    => 'taxes',
					'action' => 'add',
				)
			);
			?>
			"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
			<a class="page-title-action" href="
			<?php
			echo eaccounting_admin_url(
				array(
					'page' => 'ea-tools',
					'tab'  => 'import',
				)
			);
			?>
			"><?php _e( 'Import', 'wp-ever-accounting' ); ?></a>
		</h1>
		<?php
		require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/list-table-taxes.php';
		$list_table = new \EverAccounting\Admin\ListTables\List_Table_Taxes();
		$list_table->prepare_items();
		?>
		<div class="wrap">
			<?php

			/**
			 * Fires at the top of the admin taxes page.
			 *
			 * Use this hook to add content to this section of taxes.
			 *
			 * @since 1.0.2
			 */
			do_action( 'eaccounting_taxes_page_top' );

			?>
			<form id="ea-taxes-filter" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
				<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-taxes' ); ?>

				<input type="hidden" name="page" value="ea-misc"/>
				<input type="hidden" name="tab" value="taxes"/>

				<?php $list_table->views(); ?>
				<?php $list_table->display(); ?>
			</form>
			<?php
			/**
			 * Fires at the bottom of the admin taxes page.
			 *
			 * Use this hook to add content to this section of taxes Tab.
			 *
			 * @since 1.0.2
			 */
			do_action( 'eaccounting_taxes_page_bottom' );
			?>
		</div>
		<?php
	}
}

add_action( 'eaccounting_misc_tab_taxes', 'eaccounting_misc_taxes_tab' );
