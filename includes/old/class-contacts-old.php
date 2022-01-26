<?php
/**
 * Contacts class.
 *
 * Handle contact insert, update, delete & retrieve from database.
 *
 * @version   1.1.3
 * @package   EverAccounting
 */

namespace EverAccounting\Old;

defined( 'ABSPATH' ) || exit;

class Contacts_Old {

	/**
	 * Contacts construct.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {
	}

	/**
	 * Get contact types.
	 *
	 * @since 1.1.3
	 * @return array
	 */
	public static function get_types() {
		return apply_filters(
			'eaccounting_contact_types',
			array(
				'customer' => __( 'Customer', 'wp-ever-accounting' ),
				'vendor'   => __( 'Vendor', 'wp-ever-accounting' ),
			)
		);
	}

	/**
	 * Get contact
	 *
	 * @param int $id Customer ID
	 * @param string $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N. Default OBJECT.
	 *
	 * @since 1.0.0
	 */
	public static function get_contact( $id, $output = OBJECT ) {
		if ( empty( $id ) ) {
			return null;
		}

		if ( $id instanceof Contact ) {
			$contact = $id;
		} else {
			$contact = new Contact( $id );
		}

		if ( ! $contact->exists() ) {
			return null;
		}

		if ( ARRAY_A === $output ) {
			return $contact->get_data();
		}

		if ( ARRAY_N === $output ) {
			return array_values( $contact->get_data() );
		}

		return $contact;
	}
}
