<?php
/**
 * Admin Overview Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Overview
 * @package     EverAccounting
 */

use EverAccounting\Query_Transaction;

defined( 'ABSPATH' ) || exit();

require_once dirname( __FILE__ ) . '/widgets/widget-total-income.php';
require_once dirname( __FILE__ ) . '/widgets/widget-total-expense.php';
require_once dirname( __FILE__ ) . '/widgets/widget-total-profit.php';
require_once dirname( __FILE__ ) . '/widgets/widget-total-cashflow.php';
require_once dirname( __FILE__ ) . '/widgets/widget-income-categories.php';
require_once dirname( __FILE__ ) . '/widgets/widget-expense-categories.php';
require_once dirname( __FILE__ ) . '/widgets/widget-latest-incomes.php';
require_once dirname( __FILE__ ) . '/widgets/widget-latest-expenses.php';
require_once dirname( __FILE__ ) . '/widgets/widget-account-balances.php';

/**
 * Initializes meta boxes displayed via the Overview screen.
 *
 * @since 1.0.2
 */
function eaccounting_init_overview_meta_boxes() {
	new \EverAccounting\Admin\Overview\Widgets\Total_Income();
	new \EverAccounting\Admin\Overview\Widgets\Total_Expense();
	new \EverAccounting\Admin\Overview\Widgets\Total_Profit();
	new \EverAccounting\Admin\Overview\Widgets\Cash_Flow();
	new \EverAccounting\Admin\Overview\Widgets\Income_Categories();
	new \EverAccounting\Admin\Overview\Widgets\Expense_Categories();
	new \EverAccounting\Admin\Overview\Widgets\Latest_Incomes();
	new \EverAccounting\Admin\Overview\Widgets\Latest_Expenses();
	new \EverAccounting\Admin\Overview\Widgets\Account_Balances();
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
		<div id="ea-overview-widgets-wrap">
			<div class="ea-row">
				<?php do_action( 'eaccounting_add_overview_widget' ); ?>
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
