<?php
/**
 * View: List Item
 *
 * @since    1.1.6
 * @package     EverAccounting
 * @subpackage  Admin/View/Items
 */

defined( 'ABSPATH' ) || exit();
$list_table = new \EverAccounting\Admin\ListTables\Items();
$action     = $list_table->current_action();
$list_table->process_bulk_action( $action );
$list_table->prepare_items();
$page = eac_get_request_var( 'page', 'get', '' );
$tab  = eac_get_request_var( 'tab', 'get', '' );

?>
	<div class="eac-page__header">
		<div class="eac-page__header-col">
			<h2 class="eac-page__title"><?php esc_html_e( 'Items', 'ever-accounting' ); ?></h2>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-items&action=add' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Add New', 'ever-accounting' ); ?></a>
			<a class="page-title-action" href=" <?php echo esc_url( admin_url( 'admin.php?page=eac-tools&tab=import' ) ); ?>"><?php esc_html_e( 'Import', 'ever-accounting' ); ?></a>
		</div>
	</div>
<?php do_action( 'ever_accounting_items_table_top' ); ?>
	<form method="get" id="eac-items-table">
		<?php
		$list_table->views();
		$list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'search' );
		$list_table->display();
		?>
		<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>"/>
		<input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>"/>
	</form>
<?php
do_action( 'ever_accounting_items_table_bottom' );
