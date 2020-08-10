<?php
/**
 * Implements a batch process registry class
 * @since 1.0.2
 */

namespace EverAccounting\Utilities;

use EverAccounting\Abstracts\Registry;

/**
 * Class Batch_Process_Registry
 * @since 1.0.2
 * @package EverAccounting\Utilities
 */
class Batch_Process_Registry extends Registry {

	/**
	 * Initializes the batch registry.
	 *
	 * @since 1.0.2
	 */
	public function init() {

		$this->includes();
		$this->register_core_processes();

		do_action( 'eaccounting_batch_process_init', $this );
	}

	/**
	 * Brings in core process files.
	 *
	 * @access public
	 * @since  2.0
	 */
	public function includes() {

	}

	/**
	 * Registers core batch processes.
	 *
	 * @access protected
	 * @since  1.0.2
	 */
	protected function register_core_processes() {
		// Export Customers.
		$this->register_process( 'export-customers', array(
			'class' => 'AffWP\Utils\Batch_Process\Export_Affiliates',
			'file'  => AFFILIATEWP_PLUGIN_DIR . 'includes/admin/tools/export/class-batch-export-affiliates.php',
		) );
	}

	/**
	 * Registers a new batch process.
	 *
	 * @access public
	 *
	 * @param string $process_name Unique batch process name.
	 * @param array $process_args {
	 *     Arguments for registering a new batch process.
	 *
	 * @type string $class Batch processor class to use.
	 * @type string $file File containing the batch processor class.
	 * }
	 * @return \WP_Error|true True on successful registration, otherwise a WP_Error object.
	 * @since  1.0.2
	 *
	 */
	public function register_process( $process_name, $process_args ) {
		$process_args = wp_parse_args( $process_args, array_fill_keys( array( 'class', 'file' ), '' ) );

		if ( empty( $process_args['class'] ) ) {
			return new \WP_Error( 'invalid_batch_class', __( 'A batch process class must be specified.', 'wp-ever-accounting' ) );
		}

		if ( empty( $process_args['file'] ) ) {
			return new \WP_Error( 'missing_batch_class_file', __( 'No batch class handler file has been supplied.', 'wp-ever-accounting' ) );
		}

		// 2 if Windows path.
		if ( ! in_array( validate_file( $process_args['file'] ), array( 0, 2 ), true ) ) {
			return new \WP_Error( 'invalid_batch_class_file', __( 'An invalid batch class handler file has been supplied.', 'wp-ever-accounting' ) );
		}

		return $this->add_item( $process_name, $process_args );
	}

	/**
	 * Removes a batch process from the registry by ID.
	 *
	 * @access public
	 *
	 * @param $process_name
	 *
	 * @since  1.0.2
	 */
	public function remove_process( $process_name ) {
		$this->remove_item( $process_name );
	}
}
