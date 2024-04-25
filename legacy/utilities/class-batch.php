<?php
/**
 * Handle Batch processors.
 *
 * @since   1.0.2
 *
 * @package EAccounting\Utilities
 */

namespace EAccounting\Utilities;

use EAccounting\Abstracts\Registry;

defined( 'ABSPATH' ) || exit();


/**
 * Class Batch
 *
 * @since   1.0.2
 *
 * @package EAccounting\Utilities
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
		// exporters.
		require_once EACCOUNTING_ABSPATH . '/includes/abstracts/abstract-csv-exporter.php';
		// importers.
		require_once EACCOUNTING_ABSPATH . '/includes/abstracts/abstract-csv-importer.php';
	}

	/**
	 * Register items.
	 *
	 * @since 1.0.2
	 */
	protected function register_items() {
		$this->add_item(
			'export-customers',
			array(
				'class' => '\EAccounting\Export\Customers',
				'file'  => EACCOUNTING_ABSPATH . '/includes/export/class-customers.php',
			)
		);
		$this->add_item(
			'export-vendors',
			array(
				'class' => '\EAccounting\Export\Vendors',
				'file'  => EACCOUNTING_ABSPATH . '/includes/export/class-vendors.php',
			)
		);
		$this->add_item(
			'export-accounts',
			array(
				'class' => '\EAccounting\Export\Accounts',
				'file'  => EACCOUNTING_ABSPATH . '/includes/export/class-accounts.php',
			)
		);
		$this->add_item(
			'export-categories',
			array(
				'class' => '\EAccounting\Export\Categories',
				'file'  => EACCOUNTING_ABSPATH . '/includes/export/class-categories.php',
			)
		);
		$this->add_item(
			'export-currencies',
			array(
				'class' => '\EAccounting\Export\Currencies',
				'file'  => EACCOUNTING_ABSPATH . '/includes/export/class-currencies.php',
			)
		);
		$this->add_item(
			'export-payments',
			array(
				'class' => '\EAccounting\Export\Payments',
				'file'  => EACCOUNTING_ABSPATH . '/includes/export/class-payments.php',
			)
		);
		$this->add_item(
			'export-revenues',
			array(
				'class' => '\EAccounting\Export\Revenues',
				'file'  => EACCOUNTING_ABSPATH . '/includes/export/class-revenues.php',

			)
		);
		$this->add_item(
			'export-items',
			array(
				'class' => '\EAccounting\Export\Items',
				'file'  => EACCOUNTING_ABSPATH . '/includes/export/class-items.php',

			)
		);

		$this->add_item(
			'import-customers',
			array(
				'class' => '\EAccounting\Import\Customers',
				'file'  => EACCOUNTING_ABSPATH . '/includes/import/class-customers.php',
			)
		);
		$this->add_item(
			'import-vendors',
			array(
				'class' => '\EAccounting\Import\Vendors',
				'file'  => EACCOUNTING_ABSPATH . '/includes/import/class-vendors.php',
			)
		);
		$this->add_item(
			'import-revenues',
			array(
				'class' => '\EAccounting\Import\Revenues',
				'file'  => EACCOUNTING_ABSPATH . '/includes/import/class-revenues.php',
			)
		);
		$this->add_item(
			'import-payments',
			array(
				'class' => '\EAccounting\Import\Payments',
				'file'  => EACCOUNTING_ABSPATH . '/includes/import/class-payments.php',
			)
		);
		$this->add_item(
			'import-accounts',
			array(
				'class' => '\EAccounting\Import\Accounts',
				'file'  => EACCOUNTING_ABSPATH . '/includes/import/class-accounts.php',
			)
		);
		$this->add_item(
			'import-items',
			array(
				'class' => '\EAccounting\Import\Items',
				'file'  => EACCOUNTING_ABSPATH . '/includes/import/class-items.php',
			)
		);

		$this->add_item(
			'import-currencies',
			array(
				'class' => '\EAccounting\Import\Currencies',
				'file'  => EACCOUNTING_ABSPATH . '/includes/import/class-currencies.php',
			)
		);
		$this->add_item(
			'import-categories',
			array(
				'class' => '\EAccounting\Import\Categories',
				'file'  => EACCOUNTING_ABSPATH . '/includes/import/class-categories.php',
			)
		);

	}

	/**
	 * Add item.
	 *
	 * @param string $batch_id Unique item name.
	 *
	 * @param array  $args {
	 *                                Arguments for registering a new item.
	 *
	 * @type string $class Item class.
	 * @type string $file Item file containing the class.
	 * }
	 *
	 * @return \WP_Error|true True on successful registration, otherwise a WP_Error object.
	 * @since  1.0.2
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
