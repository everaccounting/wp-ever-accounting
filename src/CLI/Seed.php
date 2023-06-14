<?php

namespace EverAccounting\CLI;

defined( 'ABSPATH' ) || exit;

/**
 * Class CLI
 *
 * @package EverAccounting
 */
class Seed {

	/**
	 * Registers a command for creating seed data.
	 */
	public static function register_commands() {
		\WP_CLI::add_command( 'eac seed', array( __CLASS__, 'seed' ) );
	}

	/**
	 * Creates seed data.
	 *
	 * ## EXAMPLES
	 *
	 * wp eac seed
	 *
	 * @param array $args WP-CLI positional arguments.
	 * @param array $assoc_args WP-CLI associative arguments.
	 */
	public static function seed( $args, $assoc_args ) {

	}
}
