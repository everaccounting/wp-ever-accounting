<?php

namespace EverAccounting\Controllers;

use EverAccounting\Models\Account;

defined( 'ABSPATH' ) || exit;

/**
 * Accounts controller.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Controllers
 */
class Accounts {

	/**
	 * Main function for returning account.
	 *
	 * @param mixed $account Account ID or object.
	 *
	 * @since 1.1.6
	 *
	 * @return Account|null
	 */
	function get( $account ) {
		return Account::find( $account );
	}

}
