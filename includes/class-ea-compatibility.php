<?php
/**
 * Compatibility with other plugins of the plugin.
 *
 * @package     EverAccounting
 * @subpackage  Classes
 * @version     1.1.0
 */

defined( 'ABSPATH' ) || exit();

/**
 * Class EverAccounting_Compatibility
 *
 * @since 1.1.0
 */
class EverAccounting_Compatibility {
	/**
	 * EverAccounting_Compatibility constructor.
	 */
	public function __construct() {
		//woocommerce
		add_filter( 'woocommerce_prevent_admin_access', array( $this, 'change_admin_access' ) );
	}

	/**
	 * Change woocommerce admin access for account_manager and accountant
	 *
	 * @return boolean
	 * @since 1.1.0
	 *
	 */
	public function change_admin_access() {
		if ( current_user_can( 'manage_eaccounting' ) ) {
			return false;
		}

		return true;
	}
}

new EverAccounting_Compatibility();