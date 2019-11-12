<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Install {
	/**
	 * Everything need to be done
	 *
	 * @since 1.0.0
	 */
	public static function install() {
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
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) DEFAULT NULL,
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
  			`status` ENUM ('active', 'inactive') DEFAULT 'active',
  			`types` VARCHAR(191) DEFAULT NULL COMMENT 'Customer or vendor',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `email`(`email`),
		    KEY `phone`(`phone`),
		    KEY `status`(`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_products(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
			`name` VARCHAR(191) NOT NULL,
			`sku` VARCHAR(50) DEFAULT NULL,
			`description` TEXT DEFAULT NULL,
			`sale_price` double(15,4) NOT NULL,
			`purchase_price` double(15,4) NOT NULL,
			`quantity` int(11) NOT NULL,
  			`category_id` int(11) DEFAULT NULL,
  			`status` ENUM ('active', 'inactive') DEFAULT 'active',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `sale_price`(`sale_price`),
		    KEY `purchase_price`(`purchase_price`),
		    KEY `quantity`(`quantity`),
		    KEY `status`(`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_accounts(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
		    `name` varchar(191) NOT NULL COMMENT 'Account Name',
		    `number` varchar(191) NOT NULL COMMENT 'Account Number',
		    `opening_balance` double(15,4) NOT NULL DEFAULT '0.0000',
		    `bank_name` varchar(191) DEFAULT NULL,
		    `bank_phone` varchar(20) DEFAULT NULL,
		    `bank_address` varchar(191) DEFAULT NULL,
		    `status` ENUM ('active', 'inactive') DEFAULT 'active',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_payments(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `account_id` int(11) NOT NULL,
		  	`paid_at` datetime NOT NULL,
		  	`amount` double(15,4) NOT NULL,
		  	`contact_id` int(11) DEFAULT NULL,
		  	`description` text COLLATE utf8mb4_unicode_ci,
		  	`category_id` int(11) NOT NULL,
		  	`payment_method_id` int(11) NOT NULL,
		  	`reference` varchar(191) DEFAULT NULL,
		  	`parent_id` int(11) NOT NULL DEFAULT '0',
		    `reconciled` tinyint(1) NOT NULL DEFAULT '0',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `account_id` (`account_id`),
		    KEY `amount` (`amount`),
		    KEY `contact_id` (`contact_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_revenues(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `account_id` int(11) NOT NULL,
		  	`paid_at` datetime NOT NULL,
		  	`amount` double(15,4) NOT NULL,
		  	`contact_id` int(11) DEFAULT NULL,
		  	`description` text COLLATE utf8mb4_unicode_ci,
		  	`category_id` int(11) NOT NULL,
		  	`payment_method_id` int(11) NOT NULL,
		  	`reference` varchar(191) DEFAULT NULL,
		  	`parent_id` int(11) NOT NULL DEFAULT '0',
		    `reconciled` tinyint(1) NOT NULL DEFAULT '0',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `account_id` (`account_id`),
		    KEY `amount` (`amount`),
		    KEY `contact_id` (`contact_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_transfers(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
  			`payment_id` int(11) NOT NULL,
  			`transaction_id` int(11) NOT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `payment_id` (`payment_id`),
		    KEY `transaction_id` (`transaction_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
		];


		foreach ( $tables as $table ) {
			dbDelta( $table );
		}
	}
}
