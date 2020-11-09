<?php
/**
 * Deprecated functions
 *
 * Where functions come to die.
 *
 * @author   EverAccounting
 * @category Core
 * @package  EverAccounting\Functions
 * @version  1.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * @since      1.0.0
 * @deprecated 1.1.0
 *
 * @param $account
 *
 * @return mixed
 */
function eaccounting_get_account( $account ) {
	return eaccounting()->account->get( $account );
}


/**
 * @param $args
 * @since 1.1.0
 *
 * @return mixed
 */
function eaccounting_insert_account( $args ) {
	return eaccounting()->account->insert( $args );
}

/**
 * @since      1.0.0
 * @deprecated 1.1.0
 *
 * @param $account_id
 *
 * @return bool
 */
function eaccounting_delete_account( $account_id ) {
	return eaccounting()->account->delete( $account_id );
}
