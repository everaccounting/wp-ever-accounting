<?php
/**
 * Render Income list table
 *
 * @since       1.0.2
 * @subpackage  Admin/Sales/Incomes
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

include( EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-income-list-table.php' );
$incomes_table = new EAccounting_Income_List_Table();
$incomes_table->prepare_items();
$add_url = eaccounting_admin_url(
	array(
		'page'   => 'ea-sales',
		'tab'    => 'incomes',
		'action' => 'add',
	)
);
?>
<h1 class="wp-heading-inline"><?php _e( 'Incomes', 'wp-ever-accounting' ); ?></h1>
<a class="page-title-action" href="<?php echo esc_url( $add_url ); ?>">
	<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
</a>
<?php do_action( 'eaccounting_incomes_table_top' ); ?>
<form id="ea-incomes-table" method="get" action="<?php echo admin_url(); ?>">
	<?php
	$incomes_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'ea-incomes' );
	$incomes_table->display();
	?>
	<input type="hidden" name="page" value="ea-sales"/>
	<input type="hidden" name="tab" value="customers"/>
</form>
<?php do_action( 'eaccounting_incomes_table_bottom' ); ?>
