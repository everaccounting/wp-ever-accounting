<?php

class PluginTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * @var \WpunitTester
	 */
	protected $tester;


	public function test_plugin_activation() {
		$plugin = eaccounting();
		$this->assertEquals( $plugin->get_version(), get_option( 'eaccounting_version' ) );
	}

	public function test_plugin_version() {
		$plugin = eaccounting();
		// Expect the plugin version to be the same as the one in the plugin header.
		$this->assertEquals( $plugin->get_version(), get_plugin_data( EACCOUNTING_PLUGIN_FILE )['Version'] );
		// Expect the plugin version to be the same as the one in package.json.
		// Show a line in the test output if the version in package.json is not the same as the one in the plugin header.

		$this->assertEquals( $plugin->get_version(), json_decode( file_get_contents( EACCOUNTING_ABSPATH . '/package.json' ) )->version );
	}

	public function test_plugin_database() {
//		global $wpdb;
//		$tables = \EverAccounting\Install::get_tables();
//		foreach ( $tables as $table ) {
//			$result = $wpdb->get_col( "SHOW TABLES LIKE {$table}" );
//			codecept_debug( "Checking table {$table}");
//			codecept_debug( $result );
//			$this->assertNotEmpty( $result );
//		}
	}

//	public function test_cron() {
//		$this->assertNotEmpty( wp_get_schedules() );
//		$this->assertNotEmpty( wp_next_scheduled( 'wc_serial_numbers_hourly_event' ) );
//		$this->assertNotEmpty( wp_next_scheduled( 'wc_serial_numbers_daily_event' ) );
//	}
//
//	public function test_tables() {
//		// Verify that the tables are created and have the correct columns.
//		global $wpdb;
//		$tables = $wpdb->get_results( "SHOW TABLES LIKE '{$wpdb->prefix}serial_numbers%'" );
//		$this->assertNotEmpty( $tables );
//
//		$tables = $wpdb->get_results( "SHOW TABLES LIKE '{$wpdb->prefix}serial_numbers_activations%'" );
//		$this->assertNotEmpty( $tables );
//	}
}
