<?php
/**
 * Roles and Capabilities.
 *
 * @package     EverAccounting
 * @version     1.0.2
 *
 */

namespace EAccounting;

defined( 'ABSPATH' ) || exit();

class Roles {
	/**
	 * Get things going
	 *
	 * @since 1.0.2
	 */
	public function __construct() {
		add_filter( 'map_meta_cap', array( $this, 'map_meta_caps' ), 10, 4 );
	}

	/**
	 * Add new roles for the plugin.
	 *
	 * @since 1.0.2
	 */
	public function add_roles() {
		//manage can do everything like admin
		add_role( 'ea_manager', __( 'Accounting Manager', 'wp-ever-accounting' ), array(
			'manage_eaccounting' => true,
			'ea_manage_report'    => true,
			'ea_manage_options'  => true,
			'ea_import'          => true,
			'ea_export'          => true,
			'ea_manage_customer' => true,
			'ea_manage_vendor'   => true,
			'ea_manage_account'  => true,
			'ea_manage_payment'  => true,
			'ea_manage_revenue'  => true,
			'ea_manage_transfer' => true,
			'ea_manage_category' => true,
			'ea_manage_currency' => true,
			'read'               => true,
		) );

		//accountant can create and view everything without settings and export
		add_role( 'ea_accountant', __( 'Accountant', 'wp-ever-accounting' ), array(
			'manage_eaccounting' => true,
			'ea_manage_customer' => true,
			'ea_manage_vendor'   => true,
			'ea_manage_account'  => true,
			'ea_manage_payment'  => true,
			'ea_manage_revenue'  => true,
			'ea_manage_transfer' => true,
			'ea_manage_category' => true,
			'ea_manage_currency' => true,
			'read'               => true,
		) );

		//add caps to admin
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new \WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {
			$wp_roles->add_cap('administrator', 'manage_eaccounting');
			$wp_roles->add_cap('administrator', 'ea_manage_report');
			$wp_roles->add_cap('administrator', 'ea_manage_options');
			$wp_roles->add_cap('administrator', 'ea_import');
			$wp_roles->add_cap('administrator', 'ea_export');
			$wp_roles->add_cap('administrator', 'ea_manage_customer');
			$wp_roles->add_cap('administrator', 'ea_manage_vendor');
			$wp_roles->add_cap('administrator', 'ea_manage_account');
			$wp_roles->add_cap('administrator', 'ea_manage_payment');
			$wp_roles->add_cap('administrator', 'ea_manage_revenue');
			$wp_roles->add_cap('administrator', 'ea_manage_transfer');
			$wp_roles->add_cap('administrator', 'ea_manage_category');
			$wp_roles->add_cap('administrator', 'ea_manage_currency');
		}
	}


	/**
	 * Maps meta capabilities to primitive ones.
	 *
	 *
	 * @param array  $caps    The user's actual capabilities.
	 * @param string $cap     Capability name.
	 * @param int    $user_id The user ID.
	 * @param array  $args    Adds the context to the cap. Typically the object ID.
	 *
	 * @return array (Maybe) modified capabilities.
	 * @since  1.0.2
	 *
	 */
	public function map_meta_caps( $caps, $cap, $user_id, $args ) {
		switch ( $cap ) {

		}

		return $caps;
	}
}
