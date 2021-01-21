<?php
/**
 * Handle Batch processors.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Utilities
 */

namespace EverAccounting\Utilities;

use EverAccounting\Abstracts\Registry;

defined( 'ABSPATH' ) || exit();


/**
 * Class Batch
 *
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
		// exporters
		require_once EACCOUNTING_ABSPATH . '/includes/abstracts/abstract-csv-exporter.php';
		// importers
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
				'class' => '\EverAccounting\Export\Export_Customers',
				'file'  => EACCOUNTING_ABSPATH . '/includes/export/class-ea-export-customers.php',
			)
		);
		$this->add_item(
			'export-vendors',
			array(
				'class' => '\EverAccounting\Export\Export_Vendors',
				'file'  => EACCOUNTING_ABSPATH . '/includes/export/class-ea-export-vendors.php',
			)
		);
		$this->add_item(
			'export-accounts',
			array(
				'class' => '\EverAccounting\Export\Export_Accounts',
				'file'  => EACCOUNTING_ABSPATH . '/includes/export/class-ea-export-accounts.php',
			)
		);
		$this->add_item(
			'export-categories',
			array(
				'class' => '\EverAccounting\Export\Export_Categories',
				'file'  => EACCOUNTING_ABSPATH . '/includes/export/class-ea-export-categories.php',
			)
		);
		$this->add_item(
			'export-currencies',
			array(
				'class' => '\EverAccounting\Export\Export_Currencies',
				'file'  => EACCOUNTING_ABSPATH . '/includes/export/class-ea-export-currencies.php',
			)
		);
		$this->add_item(
			'export-payments',
			array(
				'class' => '\EverAccounting\Export\Export_Payments',
				'file'  => EACCOUNTING_ABSPATH . '/includes/export/class-ea-export-payments.php',
			)
		);
		$this->add_item(
			'export-revenues',
			array(
				'class' => '\EverAccounting\Export\Export_Revenues',
				'file'  => EACCOUNTING_ABSPATH . '/includes/export/class-ea-export-revenues.php',

			)
		);
		$this->add_item(
			'export-items',
			array(
				'class' => '\EverAccounting\Export\Export_Items',
				'file'  => EACCOUNTING_ABSPATH . '/includes/export/class-ea-export-items.php',

			)
		);

		$this->add_item(
			'import-customers',
			array(
				'class' => '\EverAccounting\Import\Import_Customers',
				'file'  => EACCOUNTING_ABSPATH . '/includes/import/class-ea-import-customers.php',
			)
		);
		$this->add_item(
			'import-vendors',
			array(
				'class' => '\EverAccounting\Import\Import_Vendors',
				'file'  => EACCOUNTING_ABSPATH . '/includes/import/class-ea-import-vendors.php',
			)
		);
		$this->add_item(
			'import-revenues',
			array(
				'class' => '\EverAccounting\Import\Import_Revenues',
				'file'  => EACCOUNTING_ABSPATH . '/includes/import/class-ea-import-revenues.php',
			)
		);
		$this->add_item(
			'import-payments',
			array(
				'class' => '\EverAccounting\Import\Import_Payments',
				'file'  => EACCOUNTING_ABSPATH . '/includes/import/class-ea-import-payments.php',
			)
		);
		$this->add_item(
			'import-accounts',
			array(
				'class' => '\EverAccounting\Import\Import_Accounts',
				'file'  => EACCOUNTING_ABSPATH . '/includes/import/class-ea-import-accounts.php',
			)
		);
		$this->add_item(
			'import-items',
			array(
				'class' => '\EverAccounting\Import\Import_Items',
				'file'  => EACCOUNTING_ABSPATH . '/includes/import/class-ea-import-items.php',
			)
		);

		$this->add_item(
			'import-currencies',
			array(
				'class' => '\EverAccounting\Import\Import_Currencies',
				'file'  => EACCOUNTING_ABSPATH . '/includes/import/class-ea-import-currencies.php',
			)
		);
		$this->add_item(
			'import-categories',
			array(
				'class' => '\EverAccounting\Import\Import_Categories',
				'file'  => EACCOUNTING_ABSPATH . '/includes/import/class-ea-import-categories.php',
			)
		);

	}

	/**
	 * Add item.
	 *
	 * @param string $batch_id Unique item name.
	 *
	 * @param array $args {
	 *                               Arguments for registering a new item.
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
