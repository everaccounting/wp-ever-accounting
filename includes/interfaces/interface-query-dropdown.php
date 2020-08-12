<?php
namespace EverAccounting\Interfaces;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Promise for structuring exporters.
 *
 * @since 2.0
 */
interface Dropdown {

	/**
	 * Determines whether the current user can perform an export.
	 *
	 * @access public
	 * @since  2.0
	 *
	 * @return bool Whether the current user can perform an export.
	 */
	public function selectAsOption();
}
