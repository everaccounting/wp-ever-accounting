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

	if ( in_array( $action, array( 'edit', 'add' ), true ) ) {
		require_once dirname( __FILE__ ) . '/edit-category.php';
	} else {
		?>
		<h1>
			<?php _e( 'Categories', 'wp-ever-accounting' ); ?>
			<?php
			echo sprintf(
				'<a class="page-title-action" href="%s">%s</a>',
				esc_url(
					eaccounting_admin_url(
						array(
							'tab'    => 'categories',
							'action' => 'add',
						)
					)
				),
				__( 'Add New', 'wp-ever-accounting' )
			);
			echo sprintf(
				'<a class="page-title-action" href="%s">%s</a>',
				esc_url(
					eaccounting_admin_url(
						array(
							'page' => 'ea-tools',
							'tab'  => 'import',
						)
					)
				),
				__( 'Import', 'wp-ever-accounting' )
			);
			?>
		</h1>
		<?php
		require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-category-list-table.php';
		$list_table = new EAccounting_Category_List_Table();
		$list_table->prepare_items();

		/**
		 * Fires at the top of the admin categories page.
		 *
		 * Use this hook to add content to this section of categories.
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_categories_page_top' );

		?>
		<form id="ea-categories-table" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
			<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-categories' ); ?>

			<input type="hidden" name="page" value="ea-settings"/>
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
	}
}

add_action( 'eaccounting_settings_tab_categories', 'eaccounting_misc_categories_tab' );