<?php
/**
 * View: List Tax Rates
 *
 * @since    1.1.6
 * @package     EverAccounting
 * @subpackage  Admin/View/Vendors
 */

defined( 'ABSPATH' ) || exit();

$list_table = new \EverAccounting\Admin\ListTables\Taxes();
$action     = $list_table->current_action();
$list_table->process_bulk_action( $action );
$list_table->prepare_items();
$page    = eac_get_input_var( 'page' );
$tab     = eac_get_input_var( 'tab' );
$section = eac_get_input_var( 'section' );
?>
	<div class="eac-section-header">
		<div>
			<h2><?php esc_html_e( 'Tax rates', 'wp-ever-accounting' ); ?></h2>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-settings&tab=taxes&section=rates&action=add' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?></a>
			<a class="page-title-action" href=" <?php echo esc_url( admin_url( 'admin.php?page=eac-tools&tab=import' ) ); ?>"><?php esc_html_e( 'Import', 'wp-ever-accounting' ); ?></a>
		</div>
	</div>
<?php do_action( 'ever_accounting_tax_table_top' ); ?>
	<form method="get" id="eac-tax-table">
		<?php
		$list_table->views();
		$list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'search' );
		$list_table->display();
		?>
		<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>"/>
		<input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>"/>
		<input type="hidden" name="section" value="<?php echo esc_attr( $section ); ?>"/>
	</form>
<?php
do_action( 'ever_accounting_tax_table_bottom' );
