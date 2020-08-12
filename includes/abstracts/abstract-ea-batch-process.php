<?php
/**
 * Abstract Batch Processor.
 *
 * @package  EverAccounting
 * @subpackage Abstracts
 * @version  1.0.2
 */

namespace EverAccounting\Abstracts;
defined( 'ABSPATH' ) || exit();

/**
 * Class Batch_Process
 * @since 1.0.2
 * @package EverAccounting\Abstracts
 */
abstract class Batch_Process {
	/**
	 * Batch process name.
	 *
	 * @since  1.0.2
	 * @var    string
	 */
	public $process_name;

	/**
	 * The current step being processed.
	 *
	 * @access public
	 * @since  1.0.2
	 * @var    int|string Step number or 'done'.
	 */
	public $step;

	/**
	 * Number of items to process per step.
	 *
	 * @since  1.0.2
	 * @var    int
	 */
	public $per_step = 100;

	/**
	 * Capability needed to perform the current batch process.
	 *
	 * @since  1.0.2
	 * @var    string
	 */
	public $capability = 'manage_options';

	/**
	 * Processes a single step (batch).
	 *
	 * @access public
	 * @since 1.0.2
	 */
	abstract public function process_step();


	/**
	 * Determines if the current user can perform the current batch process.
	 *
	 * @access public
	 * @return bool True if the current user has the needed capability, otherwise false.
	 * @since  1.0.2
	 *
	 */
	public function can_process() {
		return current_user_can( $this->capability );
	}

	/**
	 * Retrieves the calculated completion percentage.
	 *
	 * @access public
	 * @return int Percentage completed.
	 * @since  1.0.2
	 * @abstract
	 *
	 */
	public function get_percentage_completed() {
		$percentage = 0;

		$current_count = $this->get_current_count();
		$total_count   = $this->get_total_count();

		if ( $total_count > 0 ) {
			$percentage = ( $current_count / $total_count ) * 100;
		}

		if ( $percentage > 100 ) {
			$percentage = 100;
		}

		return $percentage;
	}

	/**
	 * Retrieves a message based on the given message code.
	 *
	 * @access public
	 *
	 * @param string $code Message code.
	 *
	 * @return string Message.
	 * @since  1.0.2
	 *
	 */
	public function get_message( $code ) {
		switch ( $code ) {
			case 'done':
				$final_count = $this->get_current_count();

				$message = sprintf(
					_n(
						'%s item was successfully processed.',
						'%s items were successfully processed.',
						$final_count,
						'affiliate-wp'
					), number_format_i18n( $final_count )
				);
				break;

			default:
				$message = '';
				break;
		}

		return $message;
	}

	/**
	 * Defines logic to execute once batch processing is complete.
	 *
	 * @access public
	 *
	 * @param string $batch_id Batch process ID.
	 *
	 * @since  1.0.2
	 *
	 */
	public function finish( $batch_id ) {
		eaccounting()->utils->data->delete_by_match( "^{$batch_id}[0-9a-z\_]+" );
	}

	/**
	 * Calculates and retrieves the offset for the current step.
	 *
	 * @access public
	 * @return int Number of items to offset.
	 * @since 1.0.2
	 *
	 */
	public function get_offset() {
		return ( $this->step - 1 ) * $this->per_step;
	}

	/**
	 * Retrieves the current, stored count of processed items.
	 *
	 * @access protected
	 * @return int Current number of processed items. Default 0.
	 * @see get_percentage_complete()
	 *
	 * @since 1.0.2
	 *
	 */
	protected function get_current_count() {
		return eaccounting()->utils->data->get( "{$this->process_name}_current_count", 0 );
	}

	/**
	 * Sets the current count of processed items.
	 *
	 * @access protected
	 *
	 * @param int $count Number of processed items.
	 *
	 * @since  1.0.2
	 *
	 */
	protected function set_current_count( $count ) {
		eaccounting()->utils->data->write( "{$this->process_name}_current_count", $count );
	}

	/**
	 * Retrieves the total, stored count of items to process.
	 *
	 * @access protected
	 * @return int Current number of processed items. Default 0.
	 * @see get_percentage_complete()
	 *
	 * @since  1.0.2
	 *
	 */
	protected function get_total_count() {
		return eaccounting()->utils->data->get( "{$this->process_name}_total_count", 0 );
	}

	/**
	 * Sets the total count of items to process.
	 *
	 * @access protected
	 *
	 * @param int $count Number of items to process.
	 *
	 * @since  1.0.2
	 *
	 */
	protected function set_total_count( $count ) {
		eaccounting()->utils->data->write( "{$this->process_name}_total_count", $count );
	}

	/**
	 * Deletes the stored current and total counts of processed items.
	 *
	 * @access protected
	 * @since  1.0.2
	 */
	protected function delete_counts() {
		eaccounting()->utils->data->delete( "{$this->process_name}_current_count" );
		eaccounting()->utils->data->delete( "{$this->process_name}_total_count" );
	}

}
