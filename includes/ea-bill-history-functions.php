<?php
/**
 * EverAccounting Bill History Functions.
 *
 * All bill history related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Exception;
use EverAccounting\Bill_History;

defined( 'ABSPATH' ) || exit;

/**
 * Main function for returning bill_history.
 *
 * @param $bill_history
 *
 * @return Bill_History|null
 * @since 1.1.0
 *
 */
function eaccounting_get_bill_history( $bill_history ) {
	if ( empty( $bill_history ) ) {
		return null;
	}

	try {
		if ( $bill_history instanceof Bill_History ) {
			$_bill_history = $bill_history;
		} elseif ( is_object( $bill_history ) && ! empty( $bill_history->id ) ) {
			$_bill_history = new Bill_History( null );
			$_bill_history->populate( $bill_history );
		} else {
			$_bill_history = new Bill_History( absint( $bill_history ) );
		}

		if ( ! $_bill_history->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid bill history.', 'wp-ever-accounting' ) );
		}

		return $_bill_history;
	} catch ( Exception $exception ) {
		return null;
	}
}


/**
 *  Create new bill history programmatically.
 *
 *  Returns a new bill_history object on success.
 *
 * @param array $args Bill_History arguments.
 *
 * @return Bill_History|WP_Error
 * @since 1.1.0
 *
 */
function eaccounting_insert_bill_history( $args ) {
	try {
		$default_args            = array(
			'id' => null,
		);
		$args                    = (array) wp_parse_args( $args, $default_args );
		$bill_history = new bill_history( $args['id'] );
		$bill_history->set_props( $args );

		//validation
		if ( ! $bill_history->get_date_created() ) {
			$bill_history->set_date_created( time() );
		}

		if ( empty( $bill_history->get_bill_id() ) ) {
			throw new Exception( 'empty_props', __( 'Bill id is required', 'wp-ever-accounting' ) );
		}

		$bill = eaccounting_get_bill( $bill_history->get_bill_id() );
		if ( ! $bill->exists() ) {
			throw new Exception( 'invalid_bill', __( 'Bill not found.', 'wp-ever-accounting' ) );
		}

		if ( empty( $bill_history->get_status( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Status is required', 'wp-ever-accounting' ) );
		}

		if ( empty( $bill_history->get_notify( 'edit' ) ) ) {
			throw new Exception( 'empty_props', __( 'Notify is required', 'wp-ever-accounting' ) );
		}


		$bill_history->save();

	} catch ( Exception $e ) {
		return new WP_Error( $e->getErrorCode(), $e->getMessage() );
	}

	return $bill_history;
}

/**
 * Delete an bill_history.
 *
 * @param $bill_history_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_bill_history( $bill_history_id ) {
	try {
		$bill_history = new Bill_History( $bill_history_id );
		if ( ! $bill_history->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid bill history.', 'wp-ever-accounting' ) );
		}

		$bill_history->delete();

		return empty( $bill_history->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}

