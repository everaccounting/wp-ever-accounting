<?php
/**
 * Handle Batch processors.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Utilities
 */

namespace Ever_Accounting\Utilities;

use Ever_Accounting\Abstracts\Registry;

defined( 'ABSPATH' ) || exit();


/**
 * Class Batch
 *
 * @since   1.0.2
 *
 * @package Ever_Accounting\Utilities
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
		do_action( 'ever_accounting_batch_process_init', $this );
	}

	/**
	 * Brings in the files.
	 *
	 * @since  1.0.2
	 */
	public function includes() {
		// exporters
		require_once dirname( EVER_ACCOUNTING_FILE ) . '/includes/abstracts/abstract-csv-exporter.php';
		// importers
		require_once dirname( EVER_ACCOUNTING_FILE ) . '/includes/abstracts/abstract-csv-importer.php';
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
				'class' => '\Ever_Accounting\Export\Customers',
				'file'  => dirname( EVER_ACCOUNTING_FILE ) . '/includes/export/class-customers.php',
			)
		);
		$this->add_item(
			'export-vendors',
			array(
				'class' => '\Ever_Accounting\Export\Vendors',
				'file'  => dirname( EVER_ACCOUNTING_FILE ) . '/includes/export/class-vendors.php',
			)
		);
		$this->add_item(
			'export-accounts',
			array(
				'class' => '\Ever_Accounting\Export\Accounts',
				'file'  => dirname( EVER_ACCOUNTING_FILE ) . '/includes/export/class-accounts.php',
			)
		);
		$this->add_item(
			'export-categories',
			array(
				'class' => '\Ever_Accounting\Export\Categories',
				'file'  => dirname( EVER_ACCOUNTING_FILE ) . '/includes/export/class-categories.php',
			)
		);
		$this->add_item(
			'export-currencies',
			array(
				'class' => '\Ever_Accounting\Export\Currencies',
				'file'  => dirname( EVER_ACCOUNTING_FILE ) . '/includes/export/class-currencies.php',
			)
		);
		$this->add_item(
			'export-payments',
			array(
				'class' => '\Ever_Accounting\Export\Payments',
				'file'  => dirname( EVER_ACCOUNTING_FILE ) . '/includes/export/class-payments.php',
			)
		);
		$this->add_item(
			'export-revenues',
			array(
				'class' => '\Ever_Accounting\Export\Revenues',
				'file'  => dirname( EVER_ACCOUNTING_FILE ) . '/includes/export/class-revenues.php',

			)
		);
		$this->add_item(
			'export-items',
			array(
				'class' => '\Ever_Accounting\Export\Items',
				'file'  => dirname( EVER_ACCOUNTING_FILE ) . '/includes/export/class-items.php',

			)
		);

		$this->add_item(
			'import-customers',
			array(
				'class' => '\Ever_Accounting\Import\Customers',
				'file'  => dirname( EVER_ACCOUNTING_FILE ) . '/includes/import/class-customers.php',
			)
		);
		$this->add_item(
			'import-vendors',
			array(
				'class' => '\Ever_Accounting\Import\Vendors',
				'file'  => dirname( EVER_ACCOUNTING_FILE ) . '/includes/import/class-vendors.php',
			)
		);
		$this->add_item(
			'import-revenues',
			array(
				'class' => '\Ever_Accounting\Import\Revenues',
				'file'  => dirname( EVER_ACCOUNTING_FILE ) . '/includes/import/class-revenues.php',
			)
		);
		$this->add_item(
			'import-payments',
			array(
				'class' => '\Ever_Accounting\Import\Payments',
				'file'  => dirname( EVER_ACCOUNTING_FILE ) . '/includes/import/class-payments.php',
			)
		);
		$this->add_item(
			'import-accounts',
			array(
				'class' => '\Ever_Accounting\Import\Accounts',
				'file'  => dirname( EVER_ACCOUNTING_FILE ) . '/includes/import/class-accounts.php',
			)
		);
		$this->add_item(
			'import-items',
			array(
				'class' => '\Ever_Accounting\Import\Items',
				'file'  => dirname( EVER_ACCOUNTING_FILE ) . '/includes/import/class-items.php',
			)
		);

		$this->add_item(
			'import-currencies',
			array(
				'class' => '\Ever_Accounting\Import\Currencies',
				'file'  => dirname( EVER_ACCOUNTING_FILE ) . '/includes/import/class-currencies.php',
			)
		);
		$this->add_item(
			'import-categories',
			array(
				'class' => '\Ever_Accounting\Import\Categories',
				'file'  => dirname( EVER_ACCOUNTING_FILE ) . '/includes/import/class-categories.php',
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
