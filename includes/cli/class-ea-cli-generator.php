<?php
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'Faker\Factory' ) && file_exists( EVER_ACCOUNTING_ABSPATH . '/vendor/autoload.php' ) ) {
	require_once( EVER_ACCOUNTING_ABSPATH . '/vendor/autoload.php' );
}

class_exists( 'Faker\Factory' ) || exit();

class EAccounting_CLI_Generator extends \WP_CLI_Command {

	public static function make_accounts( $args ) {
		list( $amount ) = $args;
		$progress = \WP_CLI\Utils\make_progress_bar( 'Generating accounting', $amount );

		$faker       = \Faker\Factory::create( 'en_US' );
		$generated = 0;
		for ( $i = 1; $i <= $amount; $i ++ ) {
			$created = eaccounting_insert_account(array(
				'name'            => $faker->name,
				'number'          => $faker->numberBetween(1, 10),
				'currency_code'   => $faker->currencyCode,
				'opening_balance' => $faker->randomNumber(),
				'bank_name'       => $faker->name,
				'bank_phone'      => $faker->phoneNumber,
				'bank_address'    => $faker->address,
				'status'         => $faker->numberBetween(0, 1),
				'updated_at'      => $faker->date(),
				'created_at'      => $faker->date(),
			));

			if(is_wp_error($created)){
				\WP_CLI::error($created->get_error_message());
			}

			if(!is_wp_error($created) && $created){
				$generated ++ ;
			}
		}


		WP_CLI::success( sprintf( "Total generated : %d", $generated ) );
	}

	public static function make_taxes( $args ) {
		list( $amount ) = $args;
		$progress = \WP_CLI\Utils\make_progress_bar( 'Generating tax rates', $amount );
		$faker       = \Faker\Factory::create( 'en_US' );
		$generated = 0;
		for ( $i = 1; $i <= $amount; $i ++ ) {
			$created = eaccounting_insert_tax(array(
				'name'            => $faker->name,
				'number'          => $faker->numberBetween(1, 10),
				'currency_code'   => $faker->currencyCode,
				'opening_balance' => $faker->randomNumber(),
				'bank_name'       => $faker->name,
				'bank_phone'      => $faker->phoneNumber,
				'bank_address'    => $faker->address,
				'enabled'         => $faker->numberBetween(0, 1),
				'updated_at'      => $faker->date(),
				'created_at'      => $faker->date(),
			));

			if(is_wp_error($created)){
				global $wpdb;
				\WP_CLI::error($created->get_error_message());
			}

			if(!is_wp_error($created) && $created){
				$generated ++ ;
			}
		}


		WP_CLI::success( sprintf( "Total generated : %d", $generated ) );
	}

}
