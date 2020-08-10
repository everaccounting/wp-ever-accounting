<?php
/**
 * Admin Customer Edit Page.
 *
 * @package     EverAccounting
 * @subpackage  Admin/Sales/Customers
 * @since       1.0.2
 */
defined( 'ABSPATH' ) || exit();
$customer_id = isset( $_REQUEST['customer_id'] ) ? absint( $_REQUEST['customer_id'] ) : null;
try {
    $customer = new \EverAccounting\Contact( $customer_id );
} catch ( Exception $e ) {
    wp_die( $e->getMessage() );
}
$back_url = remove_query_arg( array( 'action', 'id' ) );
?>

<div class="ea-form-card">
    <div class="ea-card ea-form-card__header is-compact">
        <h3 class="ea-form-card__header-title"><?php echo $customer->exists() ? __( 'Update Customer', 'wp-ever-accounting' ) : __( 'Add Customer', 'wp-ever-accounting' ); ?></h3>
        <a href="<?php echo $back_url; ?>" class="button button-secondary"><span class="dashicons dashicons-arrow-left-alt"></span><?php _e( 'Back', 'wp-ever-accounting' ); ?></a>
    </div>

    <div class="ea-card">
        <form id="ea-revenue-form" method="post" enctype="multipart/form-data">
            <div class="ea-row">
                <?php
                eaccounting_text_input( array(
                    'wrapper_class' => 'ea-col-6',
                    'label'         => __( 'Name', 'wp-ever-accounting' ),
                    'name'          => 'name',
                    'placeholder'   => __( 'Enter name', 'wp-ever-accounting' ),
                    'value'         => $customer->get_name(),
                    'required'      => true,
                ) );
                eaccounting_select( array(
                    'wrapper_class' => 'ea-col-6',
                    'label'         => __( 'Currency', 'wp-ever-accounting' ),
                    'name'          => 'currency_code',
                    'value'         => $customer->get_currency_code(),
                    'options'       => [],
                    'required'      => true,
                    'attr'          => array(
                        'data-footer'      => true,
                        'data-search'      => eaccounting_esc_json( json_encode( array(
                            'nonce'  => wp_create_nonce( 'dropdown-search' ),
                            'type'   => 'currency',
                            'action' => 'eaccounting_dropdown_search',
                        ) ), true ),
                        'data-modal'       => eaccounting_esc_json( json_encode( array(
                            'event' => 'ea-init-account-modal',
                            'type'  => 'currency',
                            'nonce' => 'edit_currency',
                        ) ), true ),
                        'data-placeholder' => __( 'Select currency', 'wp-ever-accounting' ),
                    )
                ) );
                eaccounting_text_input( array(
                    'wrapper_class' => 'ea-col-6',
                    'label'         => __( 'Email', 'wp-ever-accounting' ),
                    'name'          => 'email',
                    'placeholder'   => __( 'Enter email', 'wp-ever-accounting' ),
                    'data_type'     => 'email',
                    'value'         => $customer->get_email(),
                ) );
                eaccounting_text_input( array(
                    'wrapper_class' => 'ea-col-6',
                    'label'         => __( 'Phone', 'wp-ever-accounting' ),
                    'name'          => 'phone',
                    'placeholder'   => __( 'Enter phone', 'wp-ever-accounting' ),
                    'value'         => $customer->get_phone(),
                ) );
                eaccounting_text_input( array(
                    'wrapper_class' => 'ea-col-6',
                    'label'         => __( 'Fax', 'wp-ever-accounting' ),
                    'name'          => 'fax',
                    'placeholder'   => __( 'Enter fax', 'wp-ever-accounting' ),
                    'value'         => $customer->get_fax(),
                ) );
                eaccounting_text_input( array(
                    'wrapper_class' => 'ea-col-6',
                    'label'         => __( 'Tax Number', 'wp-ever-accounting' ),
                    'name'          => 'tax_number',
                    'placeholder'   => __( 'Enter tax number', 'wp-ever-accounting' ),
                    'value'         => $customer->get_tax_number(),
                ) );
                eaccounting_text_input( array(
                    'wrapper_class' => 'ea-col-6',
                    'label'         => __( 'Website', 'wp-ever-accounting' ),
                    'name'          => 'website',
                    'placeholder'   => __( 'Enter website', 'wp-ever-accounting' ),
                    'data_type'     => 'url',
                    'value'         => $customer->get_website(),
                ) );
                eaccounting_text_input( array(
                    'wrapper_class' => 'ea-col-6',
                    'label'         => __( 'Birth Date', 'wp-ever-accounting' ),
                    'name'          => 'birth_date',
                    'placeholder'   => __( 'Enter birth date', 'wp-ever-accounting' ),
                    'data_type'     => 'date',
                    'value'         => $customer->get_birth_date(),
                ) );
                eaccounting_textarea( array(
                    'wrapper_class' => 'ea-col-6',
                    'label'         => __( 'Note', 'wp-ever-accounting' ),
                    'name'          => 'note',
                    'placeholder'   => __( 'Enter note', 'wp-ever-accounting' ),
                    'value'         => $customer->get_note(),
                ) );
                eaccounting_textarea( array(
                    'wrapper_class' => 'ea-col-6',
                    'label'         => __( 'Address', 'wp-ever-accounting' ),
                    'name'          => 'address',
                    'placeholder'   => __( 'Enter address', 'wp-ever-accounting' ),
                    'value'         => $customer->get_address(),
                ) );
                eaccounting_select( array(
                    'wrapper_class' => 'ea-col-6',
                    'label'         => __( 'Country', 'wp-ever-accounting' ),
                    'name'          => 'country',
                    'placeholder'   => __( 'Enter country', 'wp-ever-accounting' ),
                    'value'         => $customer->get_country(),
                    'options'       => eaccounting_get_countries(),
                ) );
                
                ?>
            </div>
            <?php
            
            wp_create_nonce( 'edit_customer' );
            
            submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
            ?>

        </form>
    </div>
</div>
