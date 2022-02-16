<?php
/**
 * Ever_Accounting Tests Plugin Handler
 *
 * @since    1.1.3
 * @package  Ever_Accounting\Tests
 * @class    Tests_Plugin
 */

namespace Ever_Accounting\Tests;

defined( 'ABSPATH' ) || exit();

/**
 * Class Tests_Plugin
 *
 * @since 1.1.3
 * @package  Ever_Accounting\Tests
 */
class Tests_Install extends \WP_UnitTestCase {
	/**
	 * Ensure that all version numbers match.
	 */
	public function test_version_numbers() {
		// Get package.json version.
		$package_json = file_get_contents( 'package.json' );
		$package      = json_decode( $package_json );

		// Get main plugin file header version.
		$plugin = get_file_data( 'wp-ever-accounting.php', array( 'Version' => 'Version' ) );

		// Get plugin DB version.
		$db_version = get_option( 'ever_accounting_version' );

		// Compare all versions to the package.json value.
		$this->assertEquals( $package->version, $plugin['Version'], 'Plugin header version does not match package.json' );
		$this->assertEquals( $package->version, $db_version, 'DB version does not match package.json' );
	}

	public function test_database_tables() {
		global $wpdb;
		foreach ( \Ever_Accounting\Lifecycle::get_tables() as $table ) {
			$result = $wpdb->get_results( "SHOW TABLES LIKE '{$table}'" );
			$this->assertTrue( ! empty( $result ), "Table {$table} does not exist" );
		}

		$objects = [
			\Ever_Accounting\Account::class,
			\Ever_Accounting\Account::class,
		];

		foreach ( $objects as $object ) {
			$table   = $object::get_table_name();
			$fields  = $object::get_columns();
			$columns = $wpdb->get_col( "DESCRIBE {$wpdb->prefix}$table" );
			foreach ( $fields as $field ) {
				$this->assertContains( $field, $columns, "Field {$field} does not exist in table {$table}" );
			}
		}
	}
}
