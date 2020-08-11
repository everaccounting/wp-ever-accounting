<?php
/**
 * Exporter Interface.
 *
 * @package     EverAccounting
 * @subpackage  Interfaces
 * @since       1.0.2
 */
namespace EverAccounting\Interfaces;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit();

/**
 * Promise for structuring exporters.
 *
 * @since       1.0.2
 */
interface Exporter {

	/**
	 * Determines whether the current user can perform an export.
	 *
	 * @access public
	 * @since       1.0.2
	 *
	 * @return bool Whether the current user can perform an export.
	 */
	public function can_export();

	/**
	 * Handles sending appropriate headers depending on the type of export.
	 *
	 * @access public
	 * @since       1.0.2
	 *
	 * @return void
	 */
	public function headers();

	/**
	 * Retrieves the data for export.
	 *
	 * @access public
	 * @since       1.0.2
	 *
	 * @return array[] Multi-dimensional array of data for export.
	 */
	public function get_data();

	/**
	 * Performs the export process.
	 *
	 * @access public
	 * @since       1.0.2
	 *
	 * @return void
	 */
	public function export();

}
