<?php
/**
 * Render Vendor list table
 * Page: Expenses
 * Tab: Vendors
 *
 * @since       1.0.2
 * @subpackage  Admin/View/Vendors
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

include( EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-vendor-list-table.php' );
$vendors_table = new EAccounting_Vendor_List_Table();
$vendors_table->prepare_items();
$add_url = eaccounting_admin_url(
	array(
		'page'   => 'ea-expenses',
		'tab'    => 'vendors',
		'action' => 'add',
	)
);
?>
	<h1 class="wp-heading-inline"><?php _e( 'Vendors', 'wp-ever-accounting' ); ?></h1>
	<a class="page-title-action" href="<?php echo esc_url( $add_url ); ?>">
		<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
	</a>
<?php do_action( 'eaccounting_vendors_table_top' ); ?>
	<form id="ea-vendors-table" method="get" action="<?php echo admin_url(); ?>">
		<?php
		$vendors_table->views();
		$vendors_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'ea-vendors' );
		$vendors_table->display();
		?>
		<input type="hidden" name="page" value="ea-expenses"/>
		<input type="hidden" name="tab" value="vendors"/>
	</form>
<?php do_action( 'eaccounting_vendors_table_bottom' ); ?>
