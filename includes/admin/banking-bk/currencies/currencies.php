<?php
/**
 * Admin Currencies Page
 *
 * @package     EverAccounting
 * @subpackage  Admin/Banking/Currencies
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();


require_once __DIR__ . '/class-currencies-list-table.php';

function eaccounting_banking_tab_currencies() {
	$action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;

	if ( in_array( $action, [ 'add', 'edit' ] ) ) {
		include __DIR__ . '/edit-currency.php';

	} else {
		$list_table = new Currencies_List_Table();
		$list_table->prepare_items();
		?>
		<div class="wrap">
			<h1>
				<?php _e( 'Currencies', 'wp-ever-accounting' ); ?>
				<a href="<?php echo esc_url( eaccounting_admin_url( array(
					'action' => 'add',
					'tab'    => 'currencies'
				) ) ); ?>" class="page-title-action"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
			</h1>
			<?php

			/**
			 * Fires at the top of the admin currencies page.
			 *
			 * Use this hook to add content to this section of currencies.
			 *
			 * @since 1.0.2
			 */
			do_action( 'eaccounting_currencies_page_top' );

			?>
			<form id="ea-currencies-filter" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
				<?php $list_table->search_box( __( 'Search', 'wp-ever-currencies' ), 'eaccounting-currencies' ); ?>

				<input type="hidden" name="page" value="ea-banking"/>
				<input type="hidden" name="tab" value="currencies"/>

				<?php $list_table->views() ?>
				<?php $list_table->display() ?>
			</form>
			<?php
			/**
			 * Fires at the bottom of the admin currencies page.
			 *
			 * Use this hook to add content to this section of currencies Tab.
			 *
			 * @since 1.0.2
			 */
			do_action( 'eaccounting_currencies_page_bottom' );
			?>
		</div>
		<?php
	}
}

add_action( 'eaccounting_banking_tab_currencies', 'eaccounting_banking_tab_currencies' );
