<?php
defined( 'ABSPATH' ) || exit();
$base_url   = admin_url( 'admin.php?page=eaccounting-contacts' );
$contact_id = empty( $_GET['contact'] ) ? false : absint( $_GET['contact'] );
$contact    = new StdClass();
//if ( $contact_id ) {
//	$contact = eaccounting_get_contact( $contact_id );
//}
$title = $contact_id ? __( 'Update Contact' ) : __( 'Add Contact', 'wp-ever-accounting' );
?>

<?php echo sprintf( '<h1 class="wp-heading-inline">%s</h1>', $title ); ?>
<?php echo sprintf( '<a href="%s" class="page-title-action">%s</a>', $base_url, __( 'All Contacts', 'wp-ever-accounting' ) ); ?>
<hr class="wp-header-end">

<div class="ea-card">
	<form id="ea-contact-form" action="" method="post">
		<?php wp_enqueue_script( 'eaccounting-contacts' ); ?>
		<?php do_action( 'eaccounting_add_contact_form_top' ); ?>
		<div class="ea-row">
			<?php
			echo eaccounting_input_field( array(
				'label'         => __( 'First Name', 'wp-ever-accounting' ),
				'name'          => 'first_name',
				'value'         => isset( $contact->first_name ) ? $contact->first_name : '',
				'placeholder'   => __( 'John', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-user-circle-o',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Last Name', 'wp-ever-accounting' ),
				'name'          => 'last_name',
				'value'         => isset( $contact->last_name ) ? $contact->last_name : '',
				'placeholder'   => __( 'Doe', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-user-circle',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Email', 'wp-ever-accounting' ),
				'name'          => 'email',
				'value'         => isset( $contact->email ) ? $contact->email : '',
				'placeholder'   => __( 'john@doe.com', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-envelope',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Phone', 'wp-ever-accounting' ),
				'name'          => 'phone',
				'value'         => isset( $contact->phone ) ? $contact->phone : '',
				'placeholder'   => __( '0987654321', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-phone',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Tax Number', 'wp-ever-accounting' ),
				'name'          => 'tax_number',
				'value'         => isset( $contact->tax_number ) ? $contact->tax_number : '',
				'placeholder'   => __( 'xxxxxxx', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-percent',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Address', 'wp-ever-accounting' ),
				'name'          => 'address',
				'value'         => isset( $contact->address ) ? $contact->address : '',
				'placeholder'   => __( 'Contact address', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-address-card-o',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'City', 'wp-ever-accounting' ),
				'name'          => 'city',
				'value'         => isset( $contact->city ) ? $contact->city : '',
				'placeholder'   => __( 'City', 'wp-ever-accounting' ),
				'icon'          => 'fa  fa-map-marker',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'State', 'wp-ever-accounting' ),
				'name'          => 'state',
				'value'         => isset( $contact->state ) ? $contact->state : '',
				'placeholder'   => __( 'State', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-location-arrow',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Postcode', 'wp-ever-accounting' ),
				'name'          => 'postcode',
				'value'         => isset( $contact->postcode ) ? $contact->postcode : '',
				'placeholder'   => __( 'Contact postcode', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-map-signs',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Country', 'wp-ever-accounting' ),
				'name'          => 'country',
				'value'         => isset( $contact->country ) ? $contact->country : '',
				'placeholder'   => __( 'Country', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-map',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_input_field( array(
				'label'         => __( 'Website', 'wp-ever-accounting' ),
				'name'          => 'website',
				'value'         => isset( $contact->website ) ? $contact->website : '',
				'placeholder'   => __( 'example.com', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-globe',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );


			echo eaccounting_switch_field( array(
				'label'         => __( 'Status', 'wp-ever-accounting' ),
				'name'          => 'status',
				'check'         => '1',
				'value'         => isset( $contact->status ) ? $contact->status : '0',
				'wrapper_class' => 'ea-col-6',
			) );

			echo eaccounting_textarea_field( array(
				'label'         => __( 'Note', 'wp-ever-accounting' ),
				'name'          => 'note',
				'value'         => isset( $contact->note ) ? $contact->note : '',
				'wrapper_class' => 'ea-col-12',
			) );


			?>
		</div>


		<?php do_action( 'eaccounting_add_contact_form_bottom' ); ?>
		<p>
			<input type="hidden" name="id" value="<?php echo $contact_id;?>">
			<input type="hidden" name="action" value="eaccounting_add_contact"/>
			<?php wp_nonce_field( 'eaccounting_contact_nonce', 'nonce' ); ?>
			<input class="button button-primary" type="submit" value="<?php _e( 'Submit', 'wp-ever-accounting' ); ?>">
		</p>

	</form>
</div>

