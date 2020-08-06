<?php
/**
 * Admin Transactions Page
 *
 * @package     EverAccounting
 * @subpackage  Admin/Sales
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();
/**
 * render transactions page.
 *
 * @since 1.0.2
 */
function eaccounting_admin_transactions_page() {
	ob_start();
	?>
	<div class="wrap">
		<h2>
			<?php _e('Transactions', 'wp-ever-accounting') ?>
		</h2>
		<div id="tab_container">

		</div><!-- #tab_container-->
	</div><!-- .wrap -->
	<?php
	echo ob_get_clean();
}
