<?php
/**
 * Importer Interface.
 *
 * @package     EverAccounting
 * @subpackage  Interfaces
 * @since       1.0.2
 */
namespace EverAccounting\Interfaces;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit();

/**
 * Promise for structuring importers.
 *
 * @since 1.0.2
 */
interface Importer {

	/**
	 * Determines whether the current user can perform an import.
	 *
	 * @access public
	 * @since  1.0.2
	 *
	 * @return bool Whether the current user can perform an import.
	 */
	public function can_import();

	/**
	 * Prepares the data for import.
	 *
	 * @access public
	 * @since  1.0.2
	 *
	 * @return array[] Multi-dimensional array of data for import.
	 */
	public function get_data();

	/**
	 * Performs the import process.
	 *
	 * @access public
	 * @since  1.0.2
	 *
	 * @return void
	 */
	public function import();

}
