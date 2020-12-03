<?php
/**
 * Admin Vendor Edit Page.
 *
 * @since       1.0.2
 * @subpackage  Admin/Expenses/Vendors
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();
$vendor_id = isset( $_REQUEST['vendor_id'] ) ? absint( $_REQUEST['vendor_id'] ) : null;
try {
	$vendor = new \EverAccounting\Models\Vendor( $vendor_id );
} catch ( Exception $e ) {
	wp_die( $e->getMessage() );
}
if ( $vendor->exists() && 'vendor' !== $vendor->get_type() ) {
	echo __( 'Unknown vendor ID', 'wp-ever-accounting' );
	exit();
}
$back_url = remove_query_arg( array( 'action', 'id' ) );
?>

<div class="ea-form-card">
	<div class="ea-card ea-form-card__header is-compact">
		<h3 class="ea-form-card__header-title"><?php echo $vendor->exists() ? __( 'Update Vendor', 'wp-ever-accounting' ) : __( 'Add Vendor', 'wp-ever-accounting' ); ?></h3>
		<a href="<?php echo $back_url; ?>" class="button button-secondary"><span class="dashicons dashicons-arrow-left-alt"></span><?php _e( 'Back', 'wp-ever-accounting' ); ?></a>
	</div>

	<div class="ea-card">
		<form id="ea-vendor-form" method="post" enctype="multipart/form-data">
			<div class="ea-row">
				<?php
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Name', 'wp-ever-accounting' ),
						'name'          => 'name',
						'placeholder'   => __( 'Enter name', 'wp-ever-accounting' ),
						'value'         => $vendor->get_name(),
						'required'      => true,
					)
				);

				eaccounting_currency_dropdown(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Currency', 'wp-ever-accounting' ),
						'name'          => 'currency_code',
						'value'         => $vendor->get_currency_code(),
						'required'      => true,
						'creatable'     => true,
					)
				);

				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Email', 'wp-ever-accounting' ),
						'name'          => 'email',
						'placeholder'   => __( 'Enter email', 'wp-ever-accounting' ),
						'data_type'     => 'email',
						'value'         => $vendor->get_email(),
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Phone', 'wp-ever-accounting' ),
						'name'          => 'phone',
						'placeholder'   => __( 'Enter phone', 'wp-ever-accounting' ),
						'value'         => $vendor->get_phone(),
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Fax', 'wp-ever-accounting' ),
						'name'          => 'fax',
						'placeholder'   => __( 'Enter fax', 'wp-ever-accounting' ),
						'value'         => $vendor->get_fax(),
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Tax Number', 'wp-ever-accounting' ),
						'name'          => 'tax_number',
						'placeholder'   => __( 'Enter tax number', 'wp-ever-accounting' ),
						'value'         => $vendor->get_tax_number(),
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Website', 'wp-ever-accounting' ),
						'name'          => 'website',
						'placeholder'   => __( 'Enter website', 'wp-ever-accounting' ),
						'data_type'     => 'url',
						'value'         => $vendor->get_website(),
					)
				);
				eaccounting_text_input(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Birth Date', 'wp-ever-accounting' ),
						'name'          => 'birth_date',
						'placeholder'   => __( 'Enter birth date', 'wp-ever-accounting' ),
						'data_type'     => 'date',
						'value'         => $vendor->get_birth_date(),
					)
				);
				eaccounting_textarea(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Note', 'wp-ever-accounting' ),
						'name'          => 'note',
						'placeholder'   => __( 'Enter note', 'wp-ever-accounting' ),
						'value'         => $vendor->get_note(),
					)
				);
				eaccounting_textarea(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Address', 'wp-ever-accounting' ),
						'name'          => 'address',
						'placeholder'   => __( 'Enter address', 'wp-ever-accounting' ),
						'value'         => $vendor->get_address(),
					)
				);
				eaccounting_country_dropdown(
					array(
						'wrapper_class' => 'ea-col-6',
						'label'         => __( 'Country', 'wp-ever-accounting' ),
						'name'          => 'country',
						'value'         => $vendor->get_country(),
					)
				);
				eaccounting_hidden_input(
					array(
						'name'  => 'id',
						'value' => $vendor->get_id(),
					)
				);
				eaccounting_hidden_input(
					array(
						'name'  => 'type',
						'value' => 'vendor',
					)
				);
				eaccounting_hidden_input(
					array(
						'name'  => 'action',
						'value' => 'eaccounting_edit_vendor',
					)
				);
				?>
			</div>
			<?php

			wp_nonce_field( 'ea_edit_vendor' );

			submit_button( __( 'Submit', 'wp-ever-accounting' ), 'primary', 'submit' );
			?>

		</form>
	</div>
</div>
