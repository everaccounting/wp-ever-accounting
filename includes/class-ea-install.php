<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Install {
	/**
	 * Everything need to be done
	 *
	 * @since 1.0.0
	 */
	public static function install() {
		self::create_default_data();
		self::create_tables();
	}

	/**
	 * Delete all data
	 *
	 * @since 1.0.0
	 */
	public static function uninstall() {

	}

	/**
	 * Creat tables
	 * @since 1.0.0
	 */
	public static function create_tables() {
		global $wpdb;
		$wpdb->hide_errors();
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$tables = [
			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_contacts(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `user_id` INT(11) DEFAULT NULL,
			`first_name` VARCHAR(191) NOT NULL,
			`last_name` VARCHAR(191) NOT NULL,
			`email` VARCHAR(191) DEFAULT NULL,
			`tax_number` VARCHAR(50) DEFAULT NULL,
			`phone` VARCHAR(20) DEFAULT NULL,
			`address` VARCHAR(191) DEFAULT NULL,
			`city` VARCHAR(50) DEFAULT NULL,
			`state` VARCHAR(50) DEFAULT NULL,
			`postcode` VARCHAR(20) DEFAULT NULL,
			`country` VARCHAR(20) DEFAULT NULL,
			`website` VARCHAR(191) DEFAULT NULL,
			`note` TEXT DEFAULT NULL,
			`avatar_url` VARCHAR(2083) DEFAULT NULL,
  			`status` ENUM ('active', 'inactive') DEFAULT 'active',
  			`types` VARCHAR(191) DEFAULT NULL COMMENT 'Customer or vendor',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `email`(`email`),
		    KEY `phone`(`phone`),
		    KEY `status`(`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_accounts(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
		    `name` VARCHAR(191) NOT NULL COMMENT 'Account Name',
		    `number` VARCHAR(191) NOT NULL COMMENT 'Account Number',
		    `opening_balance` DOUBLE(15,4) NOT NULL DEFAULT '0.0000',
		    `bank_name` VARCHAR(191) DEFAULT NULL,
		    `bank_phone` VARCHAR(20) DEFAULT NULL,
		    `bank_address` VARCHAR(191) DEFAULT NULL,
		    `status` ENUM ('active', 'inactive') DEFAULT 'active',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_payments(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `account_id` INT(11) NOT NULL,
		  	`paid_at` datetime NOT NULL,
		  	`amount` DOUBLE(15,4) NOT NULL,
		  	`contact_id` INT(11) DEFAULT NULL,
		  	`description` text COLLATE utf8mb4_unicode_ci,
		  	`category_id` INT(11) NOT NULL,
		  	`payment_method` VARCHAR(100) DEFAULT NULL,
		  	`reference` VARCHAR(191) DEFAULT NULL,
			`attachment_url` VARCHAR(2083) DEFAULT NULL,
		  	`parent_id` INT(11) NOT NULL DEFAULT '0',
		    `reconciled` tinyINT(1) NOT NULL DEFAULT '0',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `account_id` (`account_id`),
		    KEY `amount` (`amount`),
		    KEY `contact_id` (`contact_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_revenues(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `account_id` INT(11) NOT NULL,
		  	`paid_at` datetime NOT NULL,
		  	`amount` DOUBLE(15,4) NOT NULL,
		  	`contact_id` INT(11) DEFAULT NULL,
		  	`description` text COLLATE utf8mb4_unicode_ci,
		  	`category_id` INT(11) NOT NULL,
	  		`payment_method` VARCHAR(100) DEFAULT NULL,
		  	`reference` VARCHAR(191) DEFAULT NULL,
			`attachment_url` VARCHAR(2083) DEFAULT NULL,
		  	`parent_id` INT(11) NOT NULL DEFAULT '0',
		    `reconciled` tinyINT(1) NOT NULL DEFAULT '0',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `account_id` (`account_id`),
		    KEY `amount` (`amount`),
		    KEY `contact_id` (`contact_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_transfers(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`payment_id` INT(11) NOT NULL,
  			`revenue_id` INT(11) NOT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `payment_id` (`payment_id`),
		    KEY `revenue_id` (`revenue_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_categories(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  		  	`name` VARCHAR(191) NOT NULL,
		  	`type` VARCHAR(50) NOT NULL,
		  	`color` VARCHAR(20) NOT NULL,
		    `status` ENUM ('active', 'inactive') DEFAULT 'active',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `type` (`type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

		];


		foreach ( $tables as $table ) {
			dbDelta( $table );
		}
	}

	/**
	 * since 1.0.0
	 */
	public static function create_default_data() {
		update_option( 'eaccounting_version', EACCOUNTING_VERSION );
		update_option( 'eaccounting_install_date', date( 'timestamp' ) );

		if ( ! eaccounting_get_categories() ) {
			eaccounting_insert_category( [
				'name'   => __( 'Deposit', 'wp-ever-accounting' ),
				'type'   => 'income',
				'status' => 'active',
			] );

			eaccounting_insert_category( [
				'name'   => __( 'Other', 'wp-ever-accounting' ),
				'type'   => 'expense',
				'status' => 'active',
			] );

			eaccounting_insert_category( [
				'name'   => __( 'Sales', 'wp-ever-accounting' ),
				'type'   => 'income',
				'status' => 'active',
			] );
		}

		//create transfer category
		if ( ! eaccounting_get_category( 'Transfer', 'name' ) ) {
			eaccounting_insert_category( [
				'name'   => __( 'Transfer', 'wp-ever-accounting' ),
				'type'   => 'other',
				'status' => 'active',
			] );
		}

		if ( ! eaccounting_get_accounts() ) {
			eaccounting_insert_account( [
				'name'            => __( 'Cash', 'wp-ever-accounting' ),
				'number'          => '',
				'opening_balance' => '0',
				'status'          => 'active',
			] );
		}

		if ( ! eaccounting_get_contacts() ) {
			eaccounting_insert_contact( [
				'first_name' => 'Demo',
				'last_name'  => 'User',
				'tax_number' => 'XXX-XX-XXXX',
				'email'      => 'demo@user.com',
				'phone'      => '1234567890',
				'address'    => 'Brannan Street',
				'city'       => 'San Francisco',
				'state'      => 'California',
				'postcode'   => '94107',
				'country'    => 'US',
				'website'    => 'http://pluginever.com',
				'avatar_url' => '',
				'status'     => 'active',
				'note'       => 'demo user',
				'types'      => [ 'vendor', 'customer' ],
			] );
		}
	}
}
