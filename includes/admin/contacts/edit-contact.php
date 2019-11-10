<?php
defined( 'ABSPATH' ) || exit();
$base_url   = admin_url( 'admin.php?page=eaccounting-contacts' );
$contact_id = empty( $_GET['contact'] ) ? false : absint( $_GET['contact'] );
$contact    = new StdClass();
if ( $contact_id ) {
	$contact = eaccounting_get_contact( $contact_id );
}
$title = $contact_id ? __( 'Update Contact' ) : __( 'Add Contact', 'wp-eaccounting' );
?>
<?php echo sprintf( '<h1 class="wp-heading-inline">%s</h1>', $title ); ?>
<?php echo sprintf( '<a href="%s" class="page-title-action">%s</a>', $base_url, __( 'All Contacts', 'wp-eaccounting' ) ); ?>
<?php
if(isset($_GET['feedback'])){
	$type = 'error';
	switch ($_GET['feedback']){
		case 'invalid_wp_user_id':
			$message = __( 'Invalid WP User ID', 'wp-eaccounting' );
			break;
		case 'duplicate_email':
			$message = __( 'The email address is already in used', 'wp-eaccounting' );
			break;
		case 'empty_name':
			$message = __( 'First Name & Last Name is required', 'wp-eaccounting' );
			break;
		case 'success':
			$message = __( 'Contact saved successfully', 'wp-eaccounting' );
			$type = 'success';
			break;
		default:
			$message = __( 'Something went wrong, please try again', 'wp-eaccounting' );
	}

	if(!empty($message)){
		echo sprintf( '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>',
			$type,
			$message
		);
	}
}

?>
<div class="ea-card">
	<form action="" method="post">
		<?php do_action( 'eaccounting_add_contact_form_top' ); ?>

		<div class="ea-row">
			<?php

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'First Name', 'wp-ever-accounting' ),
				'name'          => 'first_name',
				'value'         => isset( $contact->first_name ) ? $contact->first_name : eaccounting_get_posted_value('first_name'),
				'placeholder'   => __( 'John', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-user-circle-o',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Last Name', 'wp-ever-accounting' ),
				'name'          => 'last_name',
				'value'         => isset( $contact->last_name ) ? $contact->last_name : eaccounting_get_posted_value('last_name'),
				'placeholder'   => __( 'Doe', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-user-circle',
				'required'      => true,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Email', 'wp-ever-accounting' ),
				'tyoe'          => 'email',
				'name'          => 'email',
				'value'         => isset( $contact->email ) ? $contact->email :  eaccounting_get_posted_value('email'),
				'placeholder'   => __( 'john@doe.com', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-envelope',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );


			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Phone', 'wp-ever-accounting' ),
				'name'          => 'phone',
				'value'         => isset( $contact->phone ) ? $contact->phone : eaccounting_get_posted_value('phone'),
				'placeholder'   => __( '0987654321', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-phone',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Tax Number', 'wp-ever-accounting' ),
				'name'          => 'tax_number',
				'value'         => isset( $contact->tax_number ) ? $contact->tax_number : eaccounting_get_posted_value('tax_number'),
				'placeholder'   => __( 'xxxxxxx', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-percent',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Address', 'wp-ever-accounting' ),
				'name'          => 'address',
				'value'         => isset( $contact->address ) ? $contact->address : eaccounting_get_posted_value('address'),
				'placeholder'   => __( 'Contact address', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-address-card-o',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'City', 'wp-ever-accounting' ),
				'name'          => 'city',
				'value'         => isset( $contact->city ) ? $contact->city : eaccounting_get_posted_value('city'),
				'placeholder'   => __( 'City', 'wp-ever-accounting' ),
				'icon'          => 'fa  fa-map-marker',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'State', 'wp-ever-accounting' ),
				'name'          => 'state',
				'value'         => isset( $contact->state ) ? $contact->state : eaccounting_get_posted_value('state'),
				'placeholder'   => __( 'State', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-location-arrow',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Postcode', 'wp-ever-accounting' ),
				'name'          => 'postcode',
				'value'         => isset( $contact->postcode ) ? $contact->postcode : eaccounting_get_posted_value('postcode'),
				'placeholder'   => __( 'Contact postcode', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-map-signs',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Country', 'wp-ever-accounting' ),
				'name'          => 'country',
				'value'         => isset( $contact->country ) ? $contact->country : eaccounting_get_posted_value('country'),
				'placeholder'   => __( 'Country', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-map',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::input_control( array(
				'label'         => __( 'Website', 'wp-ever-accounting' ),
				'name'          => 'website',
				'value'         => isset( $contact->website ) ? $contact->website : eaccounting_get_posted_value('website'),
				'placeholder'   => __( 'example.com', 'wp-ever-accounting' ),
				'icon'          => 'fa fa-globe',
				'required'      => false,
				'wrapper_class' => 'ea-col-6',
			) );


			echo EAccounting_Form::status_control( array(
				'name'          => 'status',
				'value'         => isset( $contact->status ) ? $contact->status : eaccounting_get_posted_value('status'),
				'wrapper_class' => 'ea-col-6',
			) );

			echo EAccounting_Form::textarea_control( array(
				'label'         => __( 'Note', 'wp-ever-accounting' ),
				'name'          => 'note',
				'value'         => isset( $contact->note ) ? $contact->note : eaccounting_get_posted_value('note'),
				'wrapper_class' => 'ea-col-12',
			) );

			echo EAccounting_Form::checkboxes_control( array(
				'label'         => __( 'Roles', 'wp-ever-accounting' ),
				'name'          => 'roles',
				'value'         => ['customer'],
				'options'         => eaccounting_get_contact_roles(),
				'wrapper_class' => 'ea-col-12',
			) );


			?>
		</div>

		<?php do_action( 'eaccounting_add_contact_form_bottom' ); ?>
		<p>
			<input type="hidden" name="id" value="<?php echo $contact_id; ?>">
			<input type="hidden" name="eaccounting-action" value="edit_contact">
			<?php wp_nonce_field( 'eaccounting_contact_nonce' ); ?>
			<input class="button button-primary ea-submit" type="submit" value="<?php _e( 'Submit', 'wp-eaccounting' ); ?>">
		</p>
	</form>
</div>
