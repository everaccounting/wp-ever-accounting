<?php
/**
 * EverAccounting Tax functions.
 *
 * Functions related to taxes.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Exception;
use EverAccounting\Tax;

defined( 'ABSPATH' ) || exit;

/**
 * Get tax types.
 *
 * @since 1.1.0
 * @return array
 */
function eaccounting_get_tax_types() {
	$types = array(
		'fixed'     => __( 'Fixed', 'wp-ever-accounting' ),
		'normal'    => __( 'Normal', 'wp-ever-accounting' ),
		'inclusive' => __( 'Inclusive', 'wp-ever-accounting' ),
		'compound'  => __( 'Compound', 'wp-ever-accounting' ),
	);

	return apply_filters( 'eaccounting_tax_types', $types );
}

/**
 * Main function for returning tax.
 *
 * @since 1.1.0
 *
 * @param $tax
 *
 * @return \EverAccounting\Tax|null
 */
function eaccounting_get_tax( $tax ) {
	if ( empty( $tax ) ) {
		return null;
	}

	try {
		if ( $tax instanceof Tax ) {
			$_tax = $tax;
		} elseif ( is_object( $tax ) && ! empty( $tax->id ) ) {
			$_tax = new Tax( null );
			$_tax->populate( $tax );
		} else {
			$_tax = new Tax( absint( $tax ) );
		}

		if ( ! $_tax->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid tax.', 'wp-ever-accounting' ) );
		}

		return $_tax;
	} catch ( Exception $exception ) {
		return null;
	}
}

/**
 *  Create new tax programmatically.
 *
 *  Returns a new tax object on success.
 *
 * @since 1.1.0
 *
 * @param array $args Account arguments.
 *
 * @return Tax|WP_Error
 */
function eaccounting_insert_tax( $args ) {
	try {
		$default_args = array(
			'id' => null,
		);
		$args         = (array) wp_parse_args( $args, $default_args );
		$tax          = new Tax( $args['id'] );
		$tax->set_props( $args );

		//validation
		if ( ! $tax->get_date_created() ) {
			$tax->set_date_created( time() );
		}

		if ( empty( $tax->get_name() ) ) {
			throw new Exception( 'empty_props', __( 'Tax Name is required', 'wp-ever-accounting' ) );
		}
		if ( empty( $tax->get_rate() ) ) {
			throw new Exception( 'empty_props', __( 'Tax rate is required', 'wp-ever-accounting' ) );
		}
		if ( empty( $tax->get_type() ) ) {
			throw new Exception( 'empty_props', __( 'Tax type is required', 'wp-ever-accounting' ) );
		}

		$tax->save();
	} catch ( Exception $e ) {
		return new WP_Error( $e->getErrorCode(), $e->getMessage() );
	}

	return $tax;
}

/**
 * Delete an tax.
 *
 * @since 1.1.0
 *
 * @param $tax_id
 *
 * @return bool
 */
function eaccounting_delete_tax( $tax_id ) {
	try {
		$tax = new Tax( $tax_id );
		if ( ! $tax->exists() ) {
			throw new Exception( 'invalid_id', __( 'Invalid tax.', 'wp-ever-accounting' ) );
		}

		$tax->delete();

		return empty( $tax->get_id() );

	} catch ( Exception $exception ) {
		return false;
	}
}
