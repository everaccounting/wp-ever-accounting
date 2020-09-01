<?php
/**
 * Handle Batch processors.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Utilities
 */
namespace EverAccounting\Utilities;

defined( 'ABSPATH' ) || exit();

use EverAccounting\Abstracts\Registry;

/**
 * Class Batch
 * @since   1.0.2
 *
 * @package EverAccounting\Utilities
 */
class Batch extends Registry {
	/**
	 * Initializes the batch registry.
	 *
	 * @since 1.0.2
	 */
	public function init() {

		$this->includes();
		$this->register_items();
		do_action( 'eaccounting_batch_process_init', $this );
	}

	/**
	 * Brings in the files.
	 *
	 * @since  1.0.2
	 */
	public function includes() {
		//exporters
		require_once( EACCOUNTING_ABSPATH . '/includes/abstracts/abstract-csv-exporter.php' );
		require_once( EACCOUNTING_ABSPATH . '/includes/abstracts/abstract-ea-csv-batch-exporter.php' );
	}

	/**
	 * Register items.
	 *
	 * @since 1.0.2
	 */
	protected function register_items() {
		$this->add_item( 'export-customers', array(
			'class' => '\EverAccounting\Export\Customer_CSV_Export',
			'file'  => EACCOUNTING_ABSPATH . '/includes/export/class-ea-customer-csv-export.php'
		) );
	}

	/**
	 * Add item.
	 *
	 * @since  1.0.2
	 *
	 * @param string $batch_id      Unique item name.
	 *
	 * @param array  $args          {
	 *                              Arguments for registering a new item.
	 *
	 * @type string  $class         Item class.
	 * @type string  $file          Item file containing the class.
	 * }
	 *
	 *
	 * @return \WP_Error|true True on successful registration, otherwise a WP_Error object.
	 */
	public function add_item( $batch_id, $args ) {
		$args = wp_parse_args( $args, array_fill_keys( array( 'class', 'file' ), '' ) );

		if ( empty( $args['class'] ) ) {
			return new \WP_Error( 'invalid_batch_class', __( 'A batch item class must be specified.', 'wp-ever-accounting' ) );
		}

		if ( empty( $args['file'] ) ) {
			return new \WP_Error( 'missing_batch_class_file', __( 'No batch class handler file has been supplied.', 'wp-ever-accounting' ) );
		}

		// 2 if Windows path.
		if ( ! in_array( validate_file( $args['file'] ), array( 0, 2 ), true ) ) {
			return new \WP_Error( 'invalid_batch_class_file', __( 'Invalid batch class handler file has been supplied.', 'wp-ever-accounting' ) );
		}

		return parent::add_item( $batch_id, $args );
	}

}
