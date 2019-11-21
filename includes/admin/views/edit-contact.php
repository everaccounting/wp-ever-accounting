<?php
defined( 'ABSPATH' ) || exit();
$base_url = admin_url( 'admin.php?page=eaccounting-contacts' );
$id       = empty( $_GET['contact'] ) ? null : absint( $_GET['contact'] );
$contact  = new EAccounting_Contact( $id );
$title    = $contact->get_id() ? __( 'Update Contact', 'wp-ever-accounting' ) : __( 'Add Contact', 'wp-ever-accounting' );
printf( '<h1 class="wp-heading-inline">%s</h1>', $title );
printf( '<a href="%s" class="page-title-action">%s</a>', $base_url, __( 'All Contacts', 'wp-ever-accounting' ) );
?>
<div class="ea-card">
	<form id="ea-contact-form" action="" method="post">
		<?php do_action( 'eaccounting_add_contact_form_top' ); ?>
		<div class="ea-row">
			<?php
			echo EAccounting_Form::input_control( array(
				'label'         => __( 'First Name', 'wp-ever-accounting' ),
				'name'          => 'first_name',
				'value'         => $contact->get_first_name(),
				'placeholder'   => __( 'John', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-user-circle-o',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Last Name', 'wp-ever-accounting' ),
				'name'          => 'last_name',
				'value'         => $contact->get_last_name(),
				'placeholder'   => __( 'Doe', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-user-circle',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Email', 'wp-ever-accounting' ),
				'tyoe'          => 'email',
				'name'          => 'email',
				'value'         => $contact->get_email(),
				'placeholder'   => __( 'john@doe.com', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-envelope',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );


			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Phone', 'wp-ever-accounting' ),
				'name'          => 'phone',
				'value'         => $contact->get_phone(),
				'placeholder'   => __( '0987654321', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-phone',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Tax Number', 'wp-ever-accounting' ),
				'name'          => 'tax_number',
				'value'         => $contact->get_tax_number(),
				'placeholder'   => __( 'xxxxxxx', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-percent',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Address', 'wp-ever-accounting' ),
				'name'          => 'address',
				'value'         => $contact->get_address(),
				'placeholder'   => __( 'Contact address', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-address-card-o',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'City', 'wp-ever-accounting' ),
				'name'          => 'city',
				'value'         => $contact->get_city(),
				'placeholder'   => __( 'City', 'wp-ever-accounting' ),
				'icon'          => 'fa  fa-map-marker',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'State', 'wp-ever-accounting' ),
				'name'          => 'state',
				'value'         => $contact->get_state(),
				'placeholder'   => __( 'State', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-location-arrow',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Postcode', 'wp-ever-accounting' ),
				'name'          => 'postcode',
				'value'         => $contact->get_postcode(),
				'placeholder'   => __( 'Contact postcode', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-map-signs',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::select_control( array(
				'label'         => __( 'Country', 'wp-ever-accounting' ),
				'name'          => 'country',
				'default'       => 'US',
				'selected'      => $contact->get_country(),
				'options'       => eaccounting_get_countries(),
				'placeholder'   => __( 'Country', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-map',
				'required'      => false,
				'select2'       => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Website', 'wp-ever-accounting' ),
				'name'          => 'website',
				'value'         => $contact->get_website(),
				'placeholder'   => __( 'example.com', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-globe',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );


			echo EAccounting_Form::status_control( array(
				'name'          => 'status',
				'value'         => $contact->get_status(),
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::checkboxes_control( array(
				'label'         => __( 'Roles', 'wp-ever-accounting' ),
				'name'          => 'types',
				'selected'      => $contact->get_types(),
				'options'       => eaccounting_get_contact_types(),
				'wrapper_class' => 'ea-col-12',
			) );


			echo EAccounting_Form::textarea_control( array(
				'label'         => __( 'Note', 'wp-ever-accounting' ),
				'name'          => 'note',
				'value'         => $contact->get_note(),
				'wrapper_class' => 'ea-col-12',
			) );

			echo EAccounting_Form::file_control( array(
				'label'         => __( 'Avatar', 'wp-ever-accounting' ),
				'name'          => 'avatar_url',
				'value'         => $contact->get_avatar_url(),
				'icon'          => 'fa fa-file-text-o',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );
			?>


		</div>
		<?php do_action( 'eaccounting_add_contact_form_bottom' ); ?>
		<p>
			<input type="hidden" name="id" value="<?php echo $id ?>">
			<input type="hidden" name="eaccounting-action" value="edit_contact">
			<?php wp_nonce_field( 'eaccounting_edit_contact' ); ?>
			<input class="button button-primary ea-submit" type="submit" value="<?php _e( 'Submit', 'wp-ever-accounting' ); ?>">
		</p>
	</form>
</div>

