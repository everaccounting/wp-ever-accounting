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
	$financial_start = eaccounting_get_financial_start();
	if ( ( $year_start = date( 'Y-01-01' ) ) !== $financial_start ) {
		$year_start = $financial_start;
	}

	$start_date = empty( $_GET['start_date'] ) ? $year_start : eaccounting_clean( $_GET['start_date'] );
	$end_date   = empty( $_GET['end_date'] ) ? null : eaccounting_clean( $_GET['end_date'] );
	$start      = eaccounting_string_to_datetime( $start_date );

	if ( empty( $end_date ) ) {
		$start_copy = clone $start;
		$end_date   = $start_copy->add( new \DateInterval( 'P1Y' ) )->sub( new \DateInterval( 'P1D' ) )->format( 'Y-m-d' );
	}
	$end = eaccounting_string_to_datetime( $end_date );
	?>
	<div class="wrap">

		<div class="ea-flex-row">
			<div>
				<h1><?php _e( 'Overview', 'wp-ever-accounting' ); ?></h1>
			</div>
			<div>
				<form action="" method="get">
					<input type="text" id="ea-overview-date-range" data-start="<?php echo esc_attr($start->format('Y-m-d'));?>" data-end="<?php echo esc_attr($end->format('Y-m-d'));?>">
					<input type="hidden" name="page" value="eaccounting">
					<input type="hidden" name="start_date" value="">
					<input type="hidden" name="end_date" value="">
				</form>
			</div>
		</div>

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
