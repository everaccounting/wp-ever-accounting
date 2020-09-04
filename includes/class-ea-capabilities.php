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

class Capabilities {
	/**
	 * Get things going
	 *
	 * @since 1.0.2
	 */
	public function __construct() {
		add_filter( 'map_meta_cap', array( $this, 'map_meta_caps' ), 10, 4 );
	}

	/**
	 * Available Capabilities
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public function get_caps() {
		$caps = array(
			'manage_eaccounting',
			'ea_view_reports',
			'ea_update_settings',
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
			'ea_view_category',
			'ea_add_category',
			'ea_delete_category',
			'ea_view_currency',
			'ea_add_currency',
			'ea_delete_currency',
		);

		return apply_filters( 'eaccounting_caps', $caps );
	}

	/**
	 * Add new capabilities
	 *
	 * @since  1.0.2
	 * @global       $wp_roles
	 *
	 * @param string $role
	 *
	 * @return void
	 */
	public function add_caps( $role = 'administrator' ) {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new \WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {
			$caps = $this->get_caps();
			foreach ( $caps as $cap ) {
				$wp_roles->add_cap( $role, $cap );
			}
		}
	}

	/**
	 * Remove core post type capabilities (called on uninstall)
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public function remove_caps( $role = 'administrator' ) {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new \WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {
			$caps = $this->get_caps();
			foreach ( $caps as $cap ) {
				$wp_roles->add_cap( $role, $cap );
			}
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
//		switch ( $cap ) {
//
//		}

		return $caps;
	}
}

new Capabilities();
