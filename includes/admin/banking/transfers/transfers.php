<?php
/**
 * Admin Transfers Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Banking/Transfers
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();


function eaccounting_banking_tab_transfers() {
    $action = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : null;

    if ( in_array( $action, [ 'edit', 'add' ] ) ) {
        include __DIR__ . '/edit-transfer.php';
    } else {
        ?>
        <h1>
            <?php _e( 'Transfers', 'wp-ever-accounting' ); ?>
            <a class="page-title-action" href="<?php echo eaccounting_admin_url( array( 'tab' => 'transfers', 'action' => 'add' ) ); ?>"><?php _e( 'Add New', 'wp-ever-accounting' ); ?></a>
        </h1>
        <?php
		require_once EACCOUNTING_ABSPATH . '/includes/admin/list-tables/list-table-transfers.php';
        $transfers_table = new \EverAccounting\Admin\ListTables\List_Table_Transfers();
        $transfers_table->prepare_items();
        ?>
        <div class="wrap">
            <?php

            /**
             * Fires at the top of the admin transfers page.
             *
             * Use this hook to add content to this section of transfers.
             *
             * @since 1.0.2
             */
            do_action( 'eaccounting_transfers_page_top' );

            ?>
            <form id="ea-transfers-filter" method="get" action="<?php echo esc_url( eaccounting_admin_url() ); ?>">
                <?php //$transfers_table->search_box( __( 'Search', 'wp-ever-accounting' ), 'eaccounting-transfers' ); ?>

                <input type="hidden" name="page" value="ea-banking"/>
                <input type="hidden" name="tab" value="transfers"/>

                <?php $transfers_table->views() ?>
                <?php $transfers_table->display() ?>
            </form>
            <?php
            /**
             * Fires at the bottom of the admin transfers page.
             *
             * Use this hook to add content to this section of transfers Tab.
             *
             * @since 1.0.2
             */
            do_action( 'eaccounting_transfers_page_bottom' );
            ?>
        </div>
        <?php
    }
}

add_action( 'eaccounting_banking_tab_transfers', 'eaccounting_banking_tab_transfers' );
