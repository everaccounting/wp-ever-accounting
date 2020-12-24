<?php
/**
 * Render Currency list table
 *
 * @since       1.0.2
 * @subpackage  Admin/Settings/Currency
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

include( EACCOUNTING_ABSPATH . '/includes/admin/list-tables/class-ea-currency-list-table.php' );
$currency_table = new EAccounting_Currency_List_Table();
$currency_table->prepare_items();
$add_url = eaccounting_admin_url(
	array(
		'page'   => 'ea-settings',
		'tab'    => 'currencies',
		'action' => 'add',
	)
);
?>
	<h1 class="wp-heading-inline"><?php _e( 'Currencies', 'wp-ever-accounting' ); ?></h1>
	<a class="page-title-action" href="<?php echo esc_url( $add_url ); ?>">
		<?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?>
	</a>
<?php do_action( 'eaccounting_currencies_table_top' ); ?>
	<form id="ea-customers-table" method="get" action="<?php echo admin_url(); ?>">
		<?php
		$currency_table->views();
		$currency_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'ea-currencies' );
		$currency_table->display();
		?>
		<input type="hidden" name="page" value="ea-settings"/>
		<input type="hidden" name="tab" value="currencies"/>
	</form>
<?php do_action( 'eaccounting_currencies_table_bottom' ); ?>
<?php
eaccounting_enqueue_js(
		"
	jQuery('.currency-status').on('change', function(e){
		jQuery.post('" . eaccounting()->ajax_url() . "', {
			action:'eaccounting_edit_currency',
			id: $(this).data('id'),
			enabled: $(this).is(':checked'),
			nonce: '" . wp_create_nonce( 'ea_edit_currency' ) . "',
		}, function(json){
			$.eaccounting_notice(json);
		});
	});
"
);
