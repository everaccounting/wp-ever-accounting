<?php
defined( 'ABSPATH' ) || exit();
?>
<div class="wrap ea-wrapper">
	<?php
	if ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'add_category' ) {
		eaccounting_get_views( 'edit-category.php' );
	} elseif ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'edit_category' ) {
		eaccounting_get_views( 'edit-category.php' );
	} else {
		require_once EACCOUNTING_ADMIN_ABSPATH . '/tables/class-ea-categories-list-table.php';
		$list_table = new EAccounting_Categories_List_Table();
		$list_table->prepare_items();
		$base_url = admin_url( 'admin.php?page=eaccounting-categories' );
		?>

		<h1 class="wp-heading-inline"><?php _e( 'Categories', 'wp-ever-accounting' ); ?></h1>
		<a href="<?php echo esc_url( add_query_arg( array( 'eaccounting-action' => 'add_category' ), $base_url ) ); ?>"
		   class="page-title-action">
			<?php _e( 'Add New', 'wp-ever-accounting' ); ?>
		</a>
		<?php do_action( 'eaccounting_categories_page_top' ); ?>
		<form method="get" action="<?php echo esc_url( $base_url ); ?>">
			<div class="ea-list-table">
				<?php $list_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-contacts' ); ?>
				<input type="hidden" name="page" value="eaccounting-categories"/>
				<?php $list_table->views() ?>
				<?php $list_table->display() ?>
			</div>
		</form>
		<?php
		do_action( 'eaccounting_categories_page_bottom' );
	}
	?>
</div>
