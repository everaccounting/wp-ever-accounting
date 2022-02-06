<?php
/** Deprecated Functions
 *
 * @since 1.1.3
 * @package EverAccounting/Functions
 */


/**
 * Get contact types.
 *
 * @deprecatd 1.1.3
 * @since 1.1.0
 * @return array
 */
function eaccounting_get_contact_types() {
	return \EverAccounting\Contacts::get_types();
}

