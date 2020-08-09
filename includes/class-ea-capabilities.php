<?php
/**
 * Roles and Capabilities
 *
 * @class       EAccounting_Account
 * @version     1.0.0
 * @package     EverAccounting/Classes
 */
namespace EAccounting;

defined( 'ABSPATH' ) || exit();

class Capabilities {
	/**
	 * Get things going
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_filter( 'map_meta_cap', array( $this, 'map_meta_caps' ), 10, 4 );
	}

	public function get_caps() {
		$caps = array(
			'manage_eaccounting',
			'ea_view_reports',
			'ea_import',
			'ea_export',
			'ea_view_contact',
			'ea_add_contact',
			'ea_delete_contact',
			'ea_view_account',
			'ea_add_account',
			'ea_delete_account',
			'ea_view_transaction',
			'ea_add_transaction',
			'ea_delete_transaction',
		);

		require apply_filters('eaccounting_caps');
	}

	/**
	 * Add new capabilities
	 *
	 * @access public
	 * @return void
	 * @global $wp_roles
	 * @since  1.0.2
	 */
	public function add_caps() {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {
			$wp_roles->add_cap( 'administrator', 'manage_eaccounting' );
		}
	}


	/**
	 * Maps meta capabilities to primitive ones.
	 *
	 *
	 * @param array $caps The user's actual capabilities.
	 * @param string $cap Capability name.
	 * @param int $user_id The user ID.
	 * @param array $args Adds the context to the cap. Typically the object ID.
	 *
	 * @return array (Maybe) modified capabilities.
	 * @since  1.2.0
	 *
	 */
	public function map_meta_caps( $caps, $cap, $user_id, $args ) {
//		switch ( $cap ) {
//
//		}

		return $caps;
	}
}
