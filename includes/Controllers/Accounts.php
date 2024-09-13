<?php

namespace EverAccounting\Controllers;

use EverAccounting\Models\Account;

defined( 'ABSPATH' ) || exit;

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
