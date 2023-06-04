<?php
/**
 * View: List Categories
 *
 * @since    1.1.6
 * @package     EverAccounting
 * @subpackage  Admin/View/Categories
 */

defined( 'ABSPATH' ) || exit();

$list_table = new \EverAccounting\Admin\ListTables\Terms( [ 'group' => 'income_cat' ] );
$action     = $list_table->current_action();
$list_table->process_bulk_action( $action );
$list_table->prepare_items();
$page = eac_get_input_var( 'page' );
$tab  = eac_get_input_var( 'tab' );
?>

	<div class="eac-section-header">
		<div>
			<h2><?php esc_html_e( 'Categories', 'wp-ever-accounting' ); ?></h2>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=eac-sales&tab=incomes&section=categories&action=add' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Add New', 'wp-ever-accounting' ); ?></a>
		</div>
	</div>
	<form method="get" id="eac-categories-table">
		<?php
		$list_table->views();
		$list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'search' );
		$list_table->display();
		?>
		<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>"/>
		<input type="hidden" name="tab" value="<?php echo esc_attr( $tab ); ?>"/>
		<input type="hidden" name="section" value="categories"/>
	</form>
<?php
