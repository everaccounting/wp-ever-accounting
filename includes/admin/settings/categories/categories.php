<?php
/**
 * Admin categories Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Misc/Categories
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();


function eaccounting_misc_categories_tab() {
	if ( ! current_user_can( 'ea_manage_category' ) ) {
		wp_die( __( 'Sorry you are not allowed to access this page.', 'wp-ever-accounting' ) );
	}
	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;

	if ( in_array( $action, [ 'edit', 'add' ] ) ) {
		require_once dirname( __FILE__ ) . '/edit-category.php';
	} else {
		?>
		<h1>
			<?php _e( 'Categories', 'wp-ever-accounting' ); ?>
			<a class="page-title-action" href="
			<?php
			echo eaccounting_admin_url(
				array(
					'tab'    => 'categories',
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
		require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/list-table-categories.php';
		$list_table = new \EverAccounting\Admin\ListTables\List_Table_Categories();
		$list_table->prepare_items();
		?>
		<div class="wrap">
			<?php

			/**
			 * Fires at the top of the admin categories page.
			 *
			 * Use this hook to add content to this section of categories.
			 *
			 * @since 1.0.2
			 */
			do_action( 'eaccounting_categories_page_top' );

			?>
			<form id="ea-categories-filter" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
				<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-categories' ); ?>

				<input type="hidden" name="page" value="ea-misc"/>
				<input type="hidden" name="tab" value="categories"/>

				<?php $list_table->views(); ?>
				<?php $list_table->display(); ?>
			</form>
			<?php
			/**
			 * Fires at the bottom of the admin categories page.
			 *
			 * Use this hook to add content to this section of categories Tab.
			 *
			 * @since 1.0.2
			 */
			do_action( 'eaccounting_categories_page_bottom' );
			?>
		</div>
		<?php
	}
}

add_action( 'eaccounting_settings_tab_categories', 'eaccounting_misc_categories_tab' );
