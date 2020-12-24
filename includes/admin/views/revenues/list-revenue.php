<?php
/**
 * Render Revenue list table
 * Page: Sales
 * Tab: Revenue
 *
 * @since       1.0.2
 * @subpackage  Admin/Views/Revenues
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

include( EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-revenue-list-table.php' );
$revenues_table = new EAccounting_Revenue_List_Table();
$revenues_table->prepare_items();
$add_url = eaccounting_admin_url(
	array(
		'page'   => 'ea-sales',
		'tab'    => 'revenues',
		'action' => 'add',
	)
);
?>
<h1 class="wp-heading-inline"><?php _e( 'Revenues', 'wp-ever-accounting' ); ?></h1>
<a class="page-title-action" href="<?php echo esc_url( $add_url ); ?>">
	<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
</a>
<?php do_action( 'eaccounting_revenues_table_top' ); ?>
<form id="ea-incomes-table" method="get" action="<?php echo admin_url( 'admin.php' ); ?>">
	<?php
	$revenues_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'ea-revenues' );
	$revenues_table->display();
	?>
	<input type="hidden" name="page" value="ea-sales"/>
	<input type="hidden" name="tab" value="revenues"/>
</form>
<?php do_action( 'eaccounting_revenues_table_bottom' ); ?>
