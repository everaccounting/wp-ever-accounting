<?php
/**
 * Admin Transfers Edit Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Banking/Transfers
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();
$transaction_id = isset( $_REQUEST['transaction_id'] ) ? absint( $_REQUEST['transaction_id'] ) : null;
try {
    $transaction = new \EverAccounting\Transaction( $transaction_id );
} catch ( Exception $e ) {
    wp_die( $e->getMessage() );
}
$back_url = remove_query_arg( array( 'action', 'id' ) );
?>

<div class="ea-form-card">
    <div class="ea-card ea-form-card__header is-compact">
        <h3 class="ea-form-card__header-title"><?php echo $transaction->exists() ? __( 'Update Transfer', 'wp-ever-accounting' ) : __( 'Add Transfer', 'wp-ever-accounting' ); ?></h3>
        <a href="<?php echo $back_url; ?>" class="button button-secondary"><span class="dashicons dashicons-arrow-left-alt"></span><?php _e( 'Back', 'wp-ever-accounting' ); ?></a>
    </div>
    
    <div class="ea-card">
        <form id="ea-revenue-form" method="post" enctype="multipart/form-data">
            <div class="ea-row">
                <?php
                eaccounting_select( array(
                    'wrapper_class' => 'ea-col-6',
                    'label'         => __( 'From Account', 'wp-ever-accounting' ),
                    'name'          => 'from_account_id',
                    'value'         => $transaction->get_account_id(),
                    'options'       => [],
                    'required'      => true,
                    'attr'          => array(
                        'data-footer'      => true,
                        'data-search'      => eaccounting_esc_json( json_encode( array(
                            'nonce'  => wp_create_nonce( 'dropdown-search' ),
                            'type'   => 'account',
                            'action' => 'eaccounting_dropdown_search',
                        ) ), true ),
                        'data-modal'       => eaccounting_esc_json( json_encode( array(
                            'event' => 'ea-init-account-modal',
                            'type'  => 'account',
                            'nonce' => 'edit_account',
                        ) ), true ),
                        'data-placeholder' => __( 'Select account', 'wp-ever-accounting' ),
                    )
                ) );
                eaccounting_select( array(
                    'wrapper_class' => 'ea-col-6',
                    'label'         => __( 'To Account', 'wp-ever-accounting' ),
                    'name'          => 'to_account_id',
                    'value'         => $transaction->get_account_id(),
                    'options'       => [],
                    'required'      => true,
                    'attr'          => array(
                        'data-footer'      => true,
                        'data-search'      => eaccounting_esc_json( json_encode( array(
                            'nonce'  => wp_create_nonce( 'dropdown-search' ),
                            'type'   => 'account',
                            'action' => 'eaccounting_dropdown_search',
                        ) ), true ),
                        'data-modal'       => eaccounting_esc_json( json_encode( array(
                            'event' => 'ea-init-account-modal',
                            'type'  => 'account',
                            'nonce' => 'edit_account',
                        ) ), true ),
                        'data-placeholder' => __( 'Select account', 'wp-ever-accounting' ),
                    )
                ) );
                eaccounting_text_input( array(
                    'label'         => __( 'Amount', 'wp-ever-accounting' ),
                    'name'          => 'amount',
                    'value'         => $transaction->get_amount(),
                    'data_type'     => 'price',
                    'required'      => true,
                    'wrapper_class' => 'ea-col-6',
                    'placeholder'   => __( 'Enter amount', 'wp-ever-accounting' ),
                ) );
                eaccounting_text_input( array(
                    'wrapper_class' => 'ea-col-6',
                    'label'         => __( 'Date', 'wp-ever-accounting' ),
                    'name'          => 'paid_at',
                    'placeholder'   => __( 'Enter date', 'wp-ever-accounting' ),
                    'data_type'     => 'date',
                    'value'         => $transaction->get_paid_at(),
                    'required'      => true,
                ) );
                eaccounting_select( array(
                    'label'         => __( 'Payment Method', 'wp-ever-accounting' ),
                    'name'          => 'payment_method',
                    'placeholder'   => __( 'Enter payment method', 'wp-ever-accounting' ),
                    'wrapper_class' => 'ea-col-6',
                    'required'      => true,
                    'value'         => $transaction->get_payment_method(),
                    'options'       => eaccounting_get_payment_methods(),
                ) );
                eaccounting_text_input( array(
                    'label'         => __( 'Reference', 'wp-ever-accounting' ),
                    'name'          => 'reference',
                    'value'         => $transaction->get_reference(),
                    'required'      => false,
                    'wrapper_class' => 'ea-col-6',
                    'placeholder'   => __( 'Enter reference', 'wp-ever-accounting' ),
                ) );
                eaccounting_textarea( array(
                    'label'         => __( 'Description', 'wp-ever-accounting' ),
                    'name'          => 'description',
                    'value'         => $transaction->get_description(),
                    'required'      => false,
                    'wrapper_class' => 'ea-col-12',
                    'placeholder'   => __( 'Enter description', 'wp-ever-accounting' ),
                ) );
                
                ?>
            </div>
            <?php
            
            wp_create_nonce( 'edit_transfer' );
            
            submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
            ?>
        
        </form>
    </div>
</div>
