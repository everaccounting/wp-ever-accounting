<?php
/**
 * EverAccounting Contact Functions.
 *
 * Contact related functions.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit();

/**
 * Get contact types.
 *
 * @return array
 * @since 1.1.0
 */
function eaccounting_get_contact_types() {
	return apply_filters(
		'eaccounting_contact_types',
		array(
			'customer' => __( 'Customer', 'wp-ever-accounting' ),
			'vendor'   => __( 'Vendor', 'wp-ever-accounting' ),
		)
	);
}


/**
 * Get customer.
 *
 * @param $customer
 *
 * @return \EverAccounting\Models\Customer|null
 * @since 1.1.0
 *
 */
function eaccounting_get_customer( $customer ) {
	if ( empty( $customer ) ) {
		return null;
	}

	$result = new EverAccounting\Models\Customer( $customer );

	return $result->exists() ? $result : null;
}


/**
 *  Create new customer programmatically.
 *
 *  Returns a new customer object on success.
 *
 * @param array $args {
 * An array of elements that make up an customer to update or insert.
 *
 * @type int $id ID of the contact. If equal to something other than 0,
 *                               the post with that ID will be updated. Default 0.
 * @type int $user_id user_id of the contact. Default null.
 * @type string $name name of the contact. Default not null.
 * @type string $email email of the contact. Default null.
 * @type string $phone phone of the contact. Default null.
 * @type string $fax fax of the contact. Default null.
 * @type string $fax fax of the contact. Default null.
 * @type string $birth_date date of birth of the contact. Default null.
 * @type string $address address of the contact. Default null.
 * @type string $country country of the contact. Default null.
 * @type string $website website of the contact. Default null.
 * @type string $tax_number tax_number of the contact. Default null.
 * @type string $currency_code currency_code of the contact. Default null.
 * @type string $note Additional note of the contact. Default null.
 * @type string $attachment Attachment attached with contact. Default null.
 *
 * }
 *
 * @return EverAccounting\Models\Customer|\WP_Error
 * @since 1.1.0
 *
 */
function eaccounting_insert_customer( $args ) {
	$customer = new EverAccounting\Models\Customer( $args );

	return $customer->save();
}

/**
 * Delete a customer.
 *
 * @param $customer_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_customer( $customer_id ) {
	$customer = new EverAccounting\Models\Customer( $customer_id );
	if ( ! $customer->exists() ) {
		return false;
	}

	return \EverAccounting\Repositories\Customers::instance()->delete( $customer->get_id() );
}

/**
 * Get customers items.
 *
 * @param array $args {
 *
 * @type int $id ID of the contact.
 * @type int $user_id user_id of the contact.
 * @type string $name name of the contact.
 * @type string $email email of the contact.
 * @type string $phone phone of the contact.
 * @type string $fax fax of the contact.
 * @type string $fax fax of the contact.
 * @type string $birth_date date of birth of the contact.
 * @type string $address address of the contact.
 * @type string $country country of the contact.
 * @type string $website website of the contact.
 * @type string $tax_number tax_number of the contact.
 * @type string $currency_code currency_code of the contact.
 *
 * }
 *
 * @param bool $callback
 *
 * @return array|int
 * @since 1.1.0
 *
 */
function eaccounting_get_customers( $args = array(), $callback = true ) {
	return \EverAccounting\Repositories\Customers::instance()->get_items(
		$args,
		function ( $item ) use ( $callback ) {
			if ( $callback ) {
				$category = new \EverAccounting\Models\Customer();
				$category->set_props( $item );
				$category->set_object_read( true );

				return $category;
			}

			return $item;
		}
	);
}


/**
 * Get vendor.
 *
 * @param $vendor
 *
 * @return \EverAccounting\Models\Vendor|null
 * @since 1.1.0
 *
 */
function eaccounting_get_vendor( $vendor ) {
	if ( empty( $vendor ) ) {
		return null;
	}

	$result = new EverAccounting\Models\Vendor( $vendor );

	return $result->exists() ? $result : null;
}


/**
 *  Create new vendor programmatically.
 *
 *  Returns a new vendor object on success.
 *
 * @param array $args {
 * An array of elements that make up a vendor to update or insert.
 *
 * @type int $id ID of the contact. If equal to something other than 0,
 *                               the post with that ID will be updated. Default 0.
 * @type int $user_id user_id of the contact. Default null.
 * @type string $name name of the contact. Default not null.
 * @type string $email email of the contact. Default null.
 * @type string $phone phone of the contact. Default null.
 * @type string $fax fax of the contact. Default null.
 * @type string $fax fax of the contact. Default null.
 * @type string $birth_date date of birth of the contact. Default null.
 * @type string $address address of the contact. Default null.
 * @type string $country country of the contact. Default null.
 * @type string $website website of the contact. Default null.
 * @type string $tax_number tax_number of the contact. Default null.
 * @type string $currency_code currency_code of the contact. Default null.
 * @type string $note Additional note of the contact. Default null.
 * @type string $attachment Attachment attached with contact. Default null.
 *
 * }
 *
 * @return EverAccounting\Models\Vendor|\WP_Error
 * @since 1.1.0
 *
 */
function eaccounting_insert_vendor( $args ) {
	$vendor = new EverAccounting\Models\Vendor( $args );

	return $vendor->save();
}

/**
 * Delete a vendor.
 *
 * @param $vendor_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_vendor( $vendor_id ) {
	$vendor = new EverAccounting\Models\Vendor( $vendor_id );
	if ( ! $vendor->exists() ) {
		return false;
	}

	return \EverAccounting\Repositories\Vendors::instance()->delete( $vendor->get_id() );
}

/**
 * Get vendors items.
 *
 * @param array $args {
 *
 * @type int $id ID of the contact.
 * @type int $user_id user_id of the contact.
 * @type string $name name of the contact.
 * @type string $email email of the contact.
 * @type string $phone phone of the contact.
 * @type string $fax fax of the contact.
 * @type string $fax fax of the contact.
 * @type string $birth_date date of birth of the contact.
 * @type string $address address of the contact.
 * @type string $country country of the contact.
 * @type string $website website of the contact.
 * @type string $tax_number tax_number of the contact.
 * @type string $currency_code currency_code of the contact.
 *
 * }
 *
 * @param bool $callback
 *
 * @return array|int
 * @since 1.1.0
 *
 */
function eaccounting_get_vendors( $args = array(), $callback = true ) {
	return \EverAccounting\Repositories\Vendors::instance()->get_items(
		$args,
		function ( $item ) use ( $callback ) {
			if ( $callback ) {
				$contact = new \EverAccounting\Models\Vendor();
				$contact->set_props( $item );
				$contact->set_object_read( true );

				return $contact;
			}

			return $item;
		}
	);
}
