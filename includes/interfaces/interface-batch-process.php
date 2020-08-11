<?php
/**
 * Batch Process Interface.
 *
 * @package     EverAccounting
 * @subpackage  Interfaces
 * @since       1.0.2
 */
namespace EverAccounting\Interfaces;
defined( 'ABSPATH' ) || exit();

/**
 * Base interface for registering a batch process.
 *
 * @since 1.0.2
 */
Interface Batch_Process {

	/**
	 * Determines if the current user can perform the current batch process.
	 *
	 * @access public
	 * @since 1.0.2
	 *
	 * @return bool True if the current user has the needed capability, otherwise false.
	 */
	public function can_process();

	/**
	 * Processes a single step (batch).
	 *
	 * @access public
	 * @since 1.0.2
	 */
	public function process_step();

	/**
	 * Retrieves the calculated completion percentage.
	 *
	 * @access public
	 * @since 1.0.2
	 *
	 * @return int Percentage completed.
	 */
	public function get_percentage_complete();

	/**
	 * Retrieves a message based on the given message code.
	 *
	 * @access public
	 * @since 1.0.2
	 *
	 * @param string $code Message code.
	 * @return string Message.
	 */
	public function get_message( $code );

	/**
	 * Defines logic to execute once batch processing is complete.
	 *
	 * @access public
	 * @since 1.0.2
	 *
	 * @param string $batch_id Batch process ID.
	 */
	public function finish( $batch_id );

}
