<?php
/**
 * Handle Batch processors.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Legacy\Utilities
 */

namespace EverAccounting\Legacy\Utilities;

use EverAccounting\Legacy\Abstracts\Registry;

defined( 'ABSPATH' ) || exit();


/**
 * Class Batch
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Legacy\Utilities
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
		require_once EACCOUNTING_LEGACY_PATH . '/abstracts/abstract-csv-exporter.php';
		// importers.
		require_once EACCOUNTING_LEGACY_PATH . '/abstracts/abstract-csv-importer.php';
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
				'class' => '\EverAccounting\Legacy\Export\Customers',
				'file'  => EACCOUNTING_LEGACY_PATH . '/export/class-customers.php',
			)
		);
		$this->add_item(
			'export-vendors',
			array(
				'class' => '\EverAccounting\Legacy\Export\Vendors',
				'file'  => EACCOUNTING_LEGACY_PATH . '/export/class-vendors.php',
			)
		);
		$this->add_item(
			'export-accounts',
			array(
				'class' => '\EverAccounting\Legacy\Export\Accounts',
				'file'  => EACCOUNTING_LEGACY_PATH . '/export/class-accounts.php',
			)
		);
		$this->add_item(
			'export-categories',
			array(
				'class' => '\EverAccounting\Legacy\Export\Categories',
				'file'  => EACCOUNTING_LEGACY_PATH . '/export/class-categories.php',
			)
		);
		$this->add_item(
			'export-currencies',
			array(
				'class' => '\EverAccounting\Legacy\Export\Currencies',
				'file'  => EACCOUNTING_LEGACY_PATH . '/export/class-currencies.php',
			)
		);
		$this->add_item(
			'export-payments',
			array(
				'class' => '\EverAccounting\Legacy\Export\Payments',
				'file'  => EACCOUNTING_LEGACY_PATH . '/export/class-payments.php',
			)
		);
		$this->add_item(
			'export-revenues',
			array(
				'class' => '\EverAccounting\Legacy\Export\Revenues',
				'file'  => EACCOUNTING_LEGACY_PATH . '/export/class-revenues.php',

			)
		);
		$this->add_item(
			'export-items',
			array(
				'class' => '\EverAccounting\Legacy\Export\Items',
				'file'  => EACCOUNTING_LEGACY_PATH . '/export/class-items.php',

			)
		);

		$this->add_item(
			'import-customers',
			array(
				'class' => '\EverAccounting\Legacy\Import\Customers',
				'file'  => EACCOUNTING_LEGACY_PATH . '/import/class-customers.php',
			)
		);
		$this->add_item(
			'import-vendors',
			array(
				'class' => '\EverAccounting\Legacy\Import\Vendors',
				'file'  => EACCOUNTING_LEGACY_PATH . '/import/class-vendors.php',
			)
		);
		$this->add_item(
			'import-revenues',
			array(
				'class' => '\EverAccounting\Legacy\Import\Revenues',
				'file'  => EACCOUNTING_LEGACY_PATH . '/import/class-revenues.php',
			)
		);
		$this->add_item(
			'import-payments',
			array(
				'class' => '\EverAccounting\Legacy\Import\Payments',
				'file'  => EACCOUNTING_LEGACY_PATH . '/import/class-payments.php',
			)
		);
		$this->add_item(
			'import-accounts',
			array(
				'class' => '\EverAccounting\Legacy\Import\Accounts',
				'file'  => EACCOUNTING_LEGACY_PATH . '/import/class-accounts.php',
			)
		);
		$this->add_item(
			'import-items',
			array(
				'class' => '\EverAccounting\Legacy\Import\Items',
				'file'  => EACCOUNTING_LEGACY_PATH . '/import/class-items.php',
			)
		);

		$this->add_item(
			'import-currencies',
			array(
				'class' => '\EverAccounting\Legacy\Import\Currencies',
				'file'  => EACCOUNTING_LEGACY_PATH . '/import/class-currencies.php',
			)
		);
		$this->add_item(
			'import-categories',
			array(
				'class' => '\EverAccounting\Legacy\Import\Categories',
				'file'  => EACCOUNTING_LEGACY_PATH . '/import/class-categories.php',
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
