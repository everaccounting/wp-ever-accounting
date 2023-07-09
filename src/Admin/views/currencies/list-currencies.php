<?php
/**
 * View: List Currencies
 *
 * @since    1.1.6
 * @package     EverAccounting
 * @subpackage  Admin/View/Currencies
 */

use EverAccounting\Admin\ListTables\Currencies;

defined( 'ABSPATH' ) || exit();

$list_table = new Currencies();
$action     = $list_table->current_action();
$list_table->process_bulk_action( $action );
$list_table->prepare_items();
$page    = eac_get_input_var( 'page' );
$tab     = eac_get_input_var( 'tab' );
$section = eac_get_input_var( 'section' );

?>
	<div class="eac-section-header">
		<div>
			<h2><?php esc_html_e( 'Currencies', 'wp-ever-accounting' ); ?></h2>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-settings&section=currencies&action=add' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?></a>
		</div>
	</div>
<?php do_action( 'ever_accounting_currencies_table_top' ); ?>
	<form method="get" id="eac-currencies-table">
		<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'search' ); ?>
		<?php $list_table->display(); ?>
		<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>"/>
		<input type="hidden" name="tab" value="general"/>
		<input type="hidden" name="section" value="<?php echo esc_attr( $section ); ?>"/>
	</form>
<?php
do_action( 'ever_accounting_currencies_table_bottom' );
