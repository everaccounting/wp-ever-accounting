<?php
defined('ABSPATH') || exit();

class EAccounting_CLI{
	/**
	 * Load required files and hooks to make the CLI work.
	 */
	public function __construct() {
		$this->includes();
		$this->add_commands();
	}

	/**
	 * Load command files.
	 */
	private function includes() {
		require_once dirname( __FILE__ ) . '/cli/class-ea-cli-operations.php';
		require_once dirname( __FILE__ ) . '/cli/class-ea-cli-generator.php';
	}

	/**
	 * Sets up and hooks WP CLI to our CLI code.
	 */
	private function add_commands() {
		WP_CLI::add_command( 'ea truncate', array( 'EAccounting_CLI_Operations', 'truncate') );
		WP_CLI::add_command( 'ea make accounts', array( 'EAccounting_CLI_Generator', 'make_accounts') );
		WP_CLI::add_command( 'ea make taxes', array( 'EAccounting_CLI_Generator', 'make_taxes') );
		WP_CLI::add_command( 'ea make categories', array( 'EAccounting_CLI_Generator', 'make_categories') );
	}
}

new EAccounting_CLI();
