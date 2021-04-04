<?php
/**
 * Plugin Version Tests
 *
 * @package EverAccounting\Tests
 * @since 1.0.0
 */

namespace EverAccounting\Tests;

use EverAccounting\Tests\Framework\Unit_Test_Case;

/**
 * Plugin Version Tests Class
 *
 * @since 1.0.0
 *@package EverAccounting\Tests
 */
class Test_Plugin_Version extends Unit_Test_Case {
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
		$db_version = get_option('eaccounting_version');

		// Compare all versions to the package.json value.
		$this->assertEquals( $package->version, $plugin['Version'], 'Plugin header version does not match package.json' );
		$this->assertEquals( $package->version, $db_version, 'DB version does not match package.json' );
	}
}
