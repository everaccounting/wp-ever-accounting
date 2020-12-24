<?php
/**
 * Admin Items List Page.
 * Page: Items
 * Tab: Items
 *
 * @since       1.1.0
 * @subpackage  Admin/Items
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

include( EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-item-list-table.php' );
$items_table = new EAccounting_Item_List_Table();
$items_table->prepare_items();
$add_url = eaccounting_admin_url(
	array(
		'page'   => 'ea-items',
		'tab'    => 'items',
		'action' => 'add',
	)
);
?>
<h1 class="wp-heading-inline"><?php _e( 'Items', 'wp-ever-accounting' ); ?></h1>
<a class="page-title-action" href="<?php echo esc_url( $add_url ); ?>">
	<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
</a>
<?php do_action( 'eaccounting_items_table_top' ); ?>
<form id="ea-items-table" method="get" action="<?php echo admin_url(); ?>">
	<?php
	$items_table->views();
	$items_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'ea-items' );
	$items_table->display();
	?>
	<input type="hidden" name="page" value="ea-items"/>
	<input type="hidden" name="tab" value="items"/>
</form>
<?php do_action( 'eaccounting_items_table_bottom' ); ?>
<?php
eaccounting_enqueue_js(
		"
	jQuery('.item-status').on('change', function(e){
		jQuery.post('" . eaccounting()->ajax_url() . "', {
			action:'eaccounting_edit_item',
			id: $(this).data('id'),
			enabled: $(this).is(':checked'),
			nonce: '" . wp_create_nonce( 'ea_edit_item' ) . "',
		}, function(json){
			$.eaccounting_notice(json);
		});
	});
"
);
