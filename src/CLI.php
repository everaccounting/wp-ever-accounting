<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class CLI
 *
 * @package EverAccounting
 */
class CLI extends Singleton {

	/**
	 * CLI constructor.
	 *
	 * @since 1.1.6
	 */
	protected function __construct() {
		$this->hooks();
	}

	/**
	 * Sets up and hooks WP CLI to our CLI code.
	 */
	private function hooks() {
		\WP_CLI::add_hook( 'after_wp_load', 'EverAccounting\CLI\Seed::register_commands' );
	}
}
