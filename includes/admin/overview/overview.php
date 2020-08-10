<?php
/**
 * Admin Overview Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Overview
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();

require_once dirname( __FILE__ ) . '/metaboxes/class-ea-expense-by-category.php';

/**
 * Initializes meta boxes displayed via the Overview screen.
 *
 * @since 1.0.2
 */
function eaccounting_init_overview_meta_boxes() {
	new \EverAccounting\Admin\Overview\Expense_By_Category();
	/**
	 * Fires after all core Overview meta boxes have been instantiated.
	 *
	 * @since 1.0.2
	 */
	do_action( 'eaccounting_init_overview_meta_boxes' );
}

/**
 * render overview page.
 *
 * @since 1.0.2
 */
function eaccounting_admin_overview_page() {
	eaccounting_init_overview_meta_boxes();
	?>
	<div class="wrap">

		<h2><?php _e( 'Overview', 'wp-ever-accounting' ); ?></h2>

		<?php
		/**
		 * Fires at the top of the Overview page, in the area used for Overview meta-boxes.
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_overview_meta_boxes' );
		?>

		<div id="ea-dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder">

				<div id="postbox-container-1" class="postbox-container">
					<?php do_meta_boxes( 'toplevel_page_eaccounting', 'primary', null ); ?>
				</div>

				<div id="postbox-container-2" class="postbox-container">
					<?php do_meta_boxes( 'toplevel_page_eaccounting', 'secondary', null ); ?>
				</div>

				<div id="postbox-container-3" class="postbox-container">
					<?php do_meta_boxes( 'toplevel_page_eaccounting', 'tertiary', null ); ?>
				</div>

			</div>
		</div>

		<?php
		/**
		 * Fires at the bottom of the Overview admin screen.
		 *
		 * @since 1.0.2
		 */
		do_action( 'eaccounting_overview_bottom' );
		?>

	</div>
	<?php
}
