<?php
/**
 * Render Customer list table
 * Page: Sales
 * Tab: Customers
 *
 * @since       1.0.2
 * @subpackage  Admin/Views/Customers
 * @package     Ever_Accounting
 */

defined( 'ABSPATH' ) || exit();

include( dirname(EVER_ACCOUNTING_FILE ) . '/includes/admin/list-tables/class-customer-list-table.php' );
$customers_table = new Ever_Accounting_Customer_List_Table();
$customers_table->prepare_items();
$add_url = ever_accounting_admin_url(
	array(
		'page'   => 'ea-sales',
		'tab'    => 'customers',
		'action' => 'add',
	)
);
$import_url = ever_accounting_admin_url(
	array(
		'page' => 'ea-tools',
		'tab' => 'import'
	),
	admin_url( 'admin.php' )
);
?>
<h1 class="wp-heading-inline"><?php _e( 'Customers', 'wp-ever-accounting' ); ?></h1>
<a class="page-title-action" href="<?php echo esc_url( $add_url ); ?>">
	<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
</a>

<a class="page-title-action" href="<?php echo esc_url( $import_url ); ?>">
	<?php esc_html_e( 'Import', 'wp-ever-accounting' ); ?>
</a>
<?php do_action( 'ever_accounting_customers_table_top' ); ?>
<form id="ea-customers-table" method="get" action="<?php echo admin_url( 'admin.php' ); ?>">
	<?php
	$customers_table->views();
	$customers_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'ea-customers' );
	$customers_table->display();
	?>
	<input type="hidden" name="page" value="ea-sales"/>
	<input type="hidden" name="tab" value="customers"/>
</form>
<?php do_action( 'ever_accounting_customers_table_bottom' ); ?>
<?php
ever_accounting_enqueue_js(
	"
	jQuery('.customer-status').on('change', function(e){
		jQuery.post('" . ever_accounting_ajax_url() . "', {
			action:'ever_accounting_edit_customer',
			id: $(this).data('id'),
			enabled: $(this).is(':checked'),
			nonce: '" . wp_create_nonce( 'ea_edit_customer' ) . "',
		}, function(json){
			$.eaccounting_notice(json);
		});
	});

	jQuery('.del').on('click',function(e){
		if(confirm('Are you sure you want to delete?')){
			return true;
		} else {
			return false;
		}
	});
"
);
