<?php
/**
 * EverAccounting Contact Functions.
 *
 * Contact related functions.
 *
 * @since   1.0.2
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit();

/**
 * Get contact types.
 *
 * @since 1.0.2
 * @return array
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
 * @since 1.0.2
 *
 * @param $customer
 *
 * @return \EverAccounting\Models\Customer|null
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
 * @since 1.0.2
 *
 * @param array $args customer arguments.
 *
 * @return EverAccounting\Models\Customer|\WP_Error
 */
function eaccounting_insert_customer( $args ) {
	$customer = new EverAccounting\Models\Customer( $args );
	return $customer->save();
}

/**
 * Delete a customer.
 *
 * @since 1.0.2
 *
 * @param $customer_id
 *
 * @return bool
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
 * @since 1.1.0
 *
 * @param array $args
 *
 * @param bool  $callback
 *
 * @return array|int
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
 * @since 1.0.2
 *
 * @param $vendor
 *
 * @return \EverAccounting\Models\Vendor|null
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
 * @since 1.0.2
 *
 * @param array $args
 *
 * @return EverAccounting\Models\Vendor|\WP_Error
 */
function eaccounting_insert_vendor( $args ) {
	$vendor = new EverAccounting\Models\Vendor( $args );

	return $vendor->save();
}

/**
 * Delete a vendor.
 *
 * @since 1.0.2
 *
 * @param $vendor_id
 *
 * @return bool
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
 * @since 1.1.0
 *
 * @param array $args
 *
 * @param bool  $callback
 *
 * @return array|int
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
