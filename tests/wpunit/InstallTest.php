<?php

class InstallTest extends \Codeception\TestCase\WPTestCase { // phpcs:ignore

	protected $tester;


	public function testPluginActivation() {
		$plugin = ever_accounting();
		$this->assertTrue( $plugin->is_plugin_active( $plugin->get_basename() ) );
		$this->assertEquals( $plugin->get_version(), $plugin->get_db_version() );
	}

	public function testPluginTables() {
		global $wpdb;
		$tables = \EverAccounting\Installer::get_tables();

		// Verify the following tables exist.
		// Get all tables in the database and check one by one.
		$all_tables = $wpdb->get_col( 'SHOW TABLES' );
		foreach ( $tables as $table ) {
			$this->assertContains( $table, $all_tables );
		}
	}
}
