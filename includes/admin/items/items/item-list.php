<?php
/**
 * Admin Items List Page.
 *
 * @since       1.1.0
 * @subpackage  Admin/Items
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/list-table-items.php';
$list_table = new \EverAccounting\Admin\ListTables\List_Table_Item();
$list_table->prepare_items();
?>

	<h1 class="wp-heading-inline"><?php _e( 'Items', 'wp-ever-accounting' ); ?></h1>
	<a class="page-title-action" href="<?php echo esc_url( admin_url( 'admin.php?page=ea-items&tab=items&action=add' ) ); ?>">
		<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
	</a>

<?php
/**
 * Fires at the top of the admin items page.
 *
 * Use this hook to add content to this section of items.
 *
 * @since 1.1.0
 */
do_action( 'eaccounting_items_page_top' );

?>
	<form id="ea-items-table" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
		<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-items' ); ?>

		<input type="hidden" name="page" value="ea-items"/>
		<input type="hidden" name="tab" value="items"/>

		<?php $list_table->views(); ?>
		<?php $list_table->display(); ?>
	</form>
<?php
/**
 * Fires at the bottom of the admin items page.
 *
 * Use this hook to add content to this section of taxes Tab.
 *
 * @since 1.0.2
 */
do_action( 'eaccounting_items_page_bottom' );
