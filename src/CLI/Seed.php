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
		\WP_CLI::line( 'Seeding customers...' );
		\WP_CLI::runcommand( 'eac seed customers' );
	}

	/**
	 * Creates seed data for customers.
	 *
	 * ## OPTIONS
	 * [--number=<number>]
	 * : The number of customers to create. Default 100.
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 * wp eac seed customers
	 *
	 * @param array $args WP-CLI positional arguments.
	 * @param array $assoc_args WP-CLI associative arguments.
	 */
	public static function seed_customers( $args, $assoc_args ) {
		$number = isset( $assoc_args['number'] ) ? absint( $assoc_args['number'] ) : 100;
		$endpoint = "https://randomuser.me/api/1.4/?results={$number}&inc=name,email,phone,picture,location,dob,registered";
		$response = wp_remote_get( $endpoint );
		if ( is_wp_error( $response ) ) {
			\WP_CLI::error( 'Error fetching data from randomuser.me' );
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );
		if ( ! isset( $data['results'] ) ) {
			\WP_CLI::error( 'Error fetching data from randomuser.me' );
		}


		// progress bar
		$progress = \WP_CLI\Utils\make_progress_bar( 'Creating customers....', $number );
		foreach ( $data['results'] as $result ) {
			// country name to country code.
			eac_insert_customer(array(
				'name'          => $result['name']['first'] . ' ' . $result['name']['last'],
				'company'       => '',
				'email'         => $result['email'],
				'phone'         => $result['phone'],
				'address_1'     => implode( ' ', $result['location']['street'] ),
				'address_2'     => '',
				'city'          => $result['location']['city'],
				'state'         => $result['location']['state'],
				'postcode'      => $result['location']['postcode'],
				'country'       => $result['location']['country'],
				'website'       => '',
				'vat_number'    => '',
				'status'        => 'active',
				'thumbnail_id'  => null,
				'created_via'   => 'seed',
				'currency_code' => '',
				'creator_id'    => null,
				'updated_at'    => null,
				'created_at'    => null,
			));
			$progress->tick();
		}
		$progress->finish();

		\WP_CLI::success( "Created {$number} customers." );
	}
}
