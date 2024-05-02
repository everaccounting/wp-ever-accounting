<?php
/**
 * View: Admin dashboard
 *
 * @since       1.1.0
 * @subpackage  Admin/Views/Overview
 * @package     EverAccounting
 */

defined( 'ABSPATH' ) || exit();

$revenues = eac_get_revenues( array( 'limit' => 5 ) );
$expenses = eac_get_expenses( array( 'limit' => 5 ) );
$accounts = eac_get_accounts(
	array(
		'type'  => 'bank',
		'limit' => 5,
	)
);

?>
<div class="wrap bkit-wrap">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'Dashboard', 'wp-ever-accounting' ); ?>
	</h1>
	<hr class="wp-header-end">

	<?php //require __DIR__ . '/dashboard/summaries.php'; ?>
	<?php include __DIR__ . '/dashboard/cashflow-chart.php'; ?>

	<div class="tw-grid tw-grid-cols-3">
		<div>1</div>
		<div>2</div>
		<div>3</div>
	</div>
</div>
