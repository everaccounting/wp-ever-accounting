<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Contacts_Page {



	public static function output() {
		ob_start();
		echo '<div class="wrap ea-page-wrap">';
		if ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'add_contact' ) {
			eaccounting_get_views( 'edit-contact.php', array(
				'id'         => null,
				'first_name' => 'Manik',
				'last_name'  => '',
				'email'      => '',
				'phone'      => '',
				'tax_number' => '',
				'address'    => '',
				'city'       => '',
				'state'      => '',
				'postcode'   => '',
				'country'    => '',
				'website'    => '',
				'status'     => 'active',
				'note'       => '',
				'types'      => [ 'vendor', 'customer' ],
			) );
		} elseif ( isset( $_GET['eaccounting-action'] ) && $_GET['eaccounting-action'] == 'edit_contact' ) {
			$contact_id = empty( $_GET['contact'] ) ? null : absint( $_GET['contact'] );
			$contact    = eaccounting_get_contact( $contact_id );
			eaccounting_get_views( 'edit-contact.php', array(
				'id'         => $contact_id,
				'first_name' => isset( $contact->first_name ) ? $contact->first_name : '',
				'last_name'  => isset( $contact->last_name ) ? $contact->last_name : '',
				'email'      => isset( $contact->email ) ? $contact->email : '',
				'phone'      => isset( $contact->phone ) ? $contact->phone : '',
				'tax_number' => isset( $contact->tax_number ) ? $contact->tax_number : '',
				'address'    => isset( $contact->address ) ? $contact->address : '',
				'city'       => isset( $contact->city ) ? $contact->city : '',
				'state'      => isset( $contact->state ) ? $contact->state : '',
				'postcode'   => isset( $contact->postcode ) ? $contact->postcode : '',
				'country'    => isset( $contact->country ) ? $contact->country : '',
				'website'    => isset( $contact->website ) ? $contact->website : '',
				'status'     => isset( $contact->status ) ? $contact->status : '',
				'note'       => isset( $contact->note ) ? $contact->note : '',
				'types'      => isset( $contact->types ) ? $contact->types : '',
			) );
		} else {

		}

		echo '</div>';
		$html = ob_get_contents();
		ob_get_clean();
		echo $html;
	}
}
