<?php
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'Faker\Factory' ) && file_exists( EACCOUNTING_ABSPATH . '/vendor/autoload.php' ) ) {
	require_once( EACCOUNTING_ABSPATH . '/vendor/autoload.php' );
}

class_exists( 'Faker\Factory' ) || exit();

class EAccounting_CLI_Generator extends \WP_CLI_Command {

	public static function make_accounts( $args ) {
		list( $amount ) = $args;
		$progress = \WP_CLI\Utils\make_progress_bar( 'Generating accounting', $amount );

		$faker     = \Faker\Factory::create( 'en_US' );
		$generated = 0;
		for ( $i = 1; $i <= $amount; $i ++ ) {
			$created = eaccounting_insert_account( array(
				'name'            => $faker->name,
				'number'          => $faker->numberBetween( 1, 10 ),
				'opening_balance' => $faker->randomNumber(),
				'bank_name'       => $faker->name,
				'bank_phone'      => $faker->phoneNumber,
				'bank_address'    => $faker->address,
				'status'          => $faker->numberBetween( 0, 1 ),
				'updated_at'      => $faker->date(),
				'created_at'      => $faker->date(),
			) );

			if ( is_wp_error( $created ) ) {
				\WP_CLI::error( $created->get_error_message() );
			}

			if ( ! is_wp_error( $created ) && $created ) {
				$generated ++;
			}
		}


		WP_CLI::success( sprintf( "Total generated : %d", $generated ) );
	}

	public static function make_taxes( $args ) {
		list( $amount ) = $args;
		$progress  = \WP_CLI\Utils\make_progress_bar( 'Generating tax rates', $amount );
		$faker     = \Faker\Factory::create( 'en_US' );
		$generated = 0;
		for ( $i = 1; $i <= $amount; $i ++ ) {
			$created = eaccounting_insert_tax( array(
				'name'            => $faker->name,
				'number'          => $faker->numberBetween( 1, 10 ),
				'currency_code'   => $faker->currencyCode,
				'opening_balance' => $faker->randomNumber(),
				'bank_name'       => $faker->name,
				'bank_phone'      => $faker->phoneNumber,
				'bank_address'    => $faker->address,
				'enabled'         => $faker->numberBetween( 0, 1 ),
				'updated_at'      => $faker->date(),
				'created_at'      => $faker->date(),
			) );

			if ( is_wp_error( $created ) ) {
				global $wpdb;
				\WP_CLI::error( $created->get_error_message() );
			}

			if ( ! is_wp_error( $created ) && $created ) {
				$generated ++;
			}
		}


		WP_CLI::success( sprintf( "Total generated : %d", $generated ) );
	}

	public static function make_categories( $args ) {
		list( $amount ) = $args;
		$progress  = \WP_CLI\Utils\make_progress_bar( 'Generating categories', $amount );
		$faker     = \Faker\Factory::create( 'en_US' );
		$generated = 0;
		for ( $i = 1; $i <= $amount; $i ++ ) {
			$created = eaccounting_insert_category( array(
				'name'       => $faker->name,
				'type'       => $faker->randomElement( array_keys( eaccounting_get_category_types() ) ),
				'color'      => $faker->hexColor,
				'status'     => $faker->randomElement( [ 'active', 'inactive' ] ),
				'updated_at' => $faker->date(),
				'created_at' => $faker->date(),
			) );

			if ( is_wp_error( $created ) ) {
				global $wpdb;
				\WP_CLI::error( $created->get_error_message() );
			}

			if ( ! is_wp_error( $created ) && $created ) {
				$generated ++;
			}
		}


		WP_CLI::success( sprintf( "Total generated : %d", $generated ) );
	}

	public static function make_products( $args ) {
		list( $amount ) = $args;
		$progress = \WP_CLI\Utils\make_progress_bar( 'Generating products', $amount );
		$faker    = \Faker\Factory::create();
		$faker->addProvider( new \Bezhanov\Faker\Provider\Commerce( $faker ) );

		$generated = 0;
		for ( $i = 1; $i <= $amount; $i ++ ) {
			$created = eaccounting_insert_product( array(
				'name'           => $faker->productName,
				'sku'            => '',
				'description'    => $faker->realText( 100 ),
				'sale_price'     => $faker->randomNumber( 2 ),
				'purchase_price' => $faker->randomNumber( 2 ),
				'quantity'       => $faker->randomNumber( 2 ),
				'category_id'    => '',
				'status'         => $faker->randomElement( [ 'active', 'inactive' ] ),
				'updated_at'     => $faker->date(),
				'created_at'     => $faker->date(),
			) );

			if ( is_wp_error( $created ) ) {
				global $wpdb;
				\WP_CLI::error( $created->get_error_message() );
			}

			if ( ! is_wp_error( $created ) && $created ) {
				$generated ++;
			}
		}


		WP_CLI::success( sprintf( "Total generated : %d", $generated ) );
	}


	public static function make_contacts( $args ) {
		list( $amount ) = $args;
		$progress = \WP_CLI\Utils\make_progress_bar( 'Generating products', $amount );
		$faker    = \Faker\Factory::create();
		$generated = 0;
		for ( $i = 1; $i <= $amount; $i ++ ) {
			$created = eaccounting_insert_contact( array(
				'first_name' => $faker->firstName,
				'last_name'  => $faker->lastName,
				'tax_number' => $faker->bankAccountNumber,
				'email'      => $faker->safeEmail,
				'phone'      => $faker->phoneNumber,
				'address'    => $faker->address,
				'city'       => $faker->city,
				'state'      => $faker->country,
				'postcode'   => $faker->postcode,
				'country'    => $faker->countryCode,
				'website'    => $faker->url,
				'status'     => $faker->randomElement(['active', 'inactive']),
				'note'       => $faker->realText(10),
				'roles'      => $faker->randomElement(array_keys(eaccounting_get_contact_roles())),
				'created_at' => $faker->date(),
				'updated_at' => $faker->date(),
			) );

//			if ( is_wp_error( $created ) ) {
//				global $wpdb;
//				error_log($wpdb->last_error);
//				\WP_CLI::error( $created->get_error_message() );
//			}

			if ( ! is_wp_error( $created ) && $created ) {
				$generated ++;
			}
		}


		WP_CLI::success( sprintf( "Total generated : %d", $generated ) );
	}


	public static function make_revenues( $args ) {
		list( $amount ) = $args;
		$progress = \WP_CLI\Utils\make_progress_bar( 'Generating revenues', $amount );
		$faker    = \Faker\Factory::create();
		$generated = 0;
		for ( $i = 1; $i <= $amount; $i ++ ) {
			$created = eaccounting_insert_revenue( array(
				'account_id' => self::get_random_account(),
				'paid_at'    => $faker->date(),
				'amount'     => $faker->randomNumber(),
				'contact_id' => self::get_random_contact(),
				'description'=> $faker->realText( 100 ),
				'category_id'=> self::get_random_category(),
				'reference'  => $faker->realText(10),
				'parent_id'  => '',
				'reconciled'  => '',
				'created_at' => $faker->date(),
				'updated_at' => $faker->date(),
			) );

			if ( ! is_wp_error( $created ) && $created ) {
				$generated ++;
			}
		}


		WP_CLI::success( sprintf( "Total generated : %d", $generated ) );
	}

	public static function get_random_account(){
		global $wpdb;
		return $wpdb->get_var("SELECT id from $wpdb->ea_accounts ORDER by rand() LIMIT 1");
	}

	public static function get_random_contact(){
		global $wpdb;
		return $wpdb->get_var("SELECT id from $wpdb->ea_contacts ORDER by rand() LIMIT 1");
	}

	public static function get_random_category(){
		global $wpdb;
		return $wpdb->get_var("SELECT id from $wpdb->ea_categories ORDER by rand() LIMIT 1");
	}
}
