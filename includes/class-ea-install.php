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

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_products(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
			`name` VARCHAR(191) NOT NULL,
			`sku` VARCHAR(50) DEFAULT NULL,
			`description` TEXT DEFAULT NULL,
			`sale_price` double(15,4) NOT NULL,
			`purchase_price` double(15,4) NOT NULL,
			`quantity` int(11) NOT NULL,
  			`category_id` int(11) DEFAULT NULL,
  			`tax_id` int(11) DEFAULT NULL,
  			`status` ENUM ('active', 'inactive') DEFAULT 'active',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `sale_price`(`sale_price`),
		    KEY `purchase_price`(`purchase_price`),
		    KEY `quantity`(`quantity`),
		    KEY `status`(`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_taxes(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
			`name` VARCHAR(191) NOT NULL COMMENT 'Taxes Name',
			`rate` DOUBLE(15,4) NOT NULL COMMENT 'Taxes Rate',
			`type` VARCHAR(191) NOT NULL DEFAULT 'normal' COMMENT 'Taxes Type',
			`status` ENUM ('active', 'inactive') DEFAULT 'active',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

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

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_transactions(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `transaction_number` varchar(191) NOT NULL,
            `type` varchar(50) NOT NULL COMMENT 'Type of transaction',
            `order_number` varchar(191) DEFAULT NULL,
            `status` varchar(20) NOT NULL,
            `issued_at` datetime NOT NULL,
            `due_at` datetime NOT NULL,
            `amount` double(15,4) NOT NULL,
            `contact_id` int(11) NOT NULL,
            `contact_name` varchar(191) NOT NULL,
            `contact_email` varchar(191) DEFAULT NULL,
            `contact_tax_number` varchar(50) DEFAULT NULL,
            `contact_phone` varchar(20) DEFAULT NULL,
            `contact_address` text,
            `notes` text,
            `category_id` int(11) NOT NULL DEFAULT '1',
            `parent_id` int(11) NOT NULL DEFAULT '0',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `amount` (`amount`),
		    KEY `contact_id` (`contact_id`),
		    KEY `transaction_number` (`transaction_number`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_transaction_totals(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `transaction_id` int(11) NOT NULL,
            `type` varchar(20) DEFAULT NULL,
            `amount` double(15,4) NOT NULL,
            `sort_order` int(11) NOT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `transaction_id` (`transaction_id`),
		    KEY `type` (`type`),
		    KEY `amount` (`amount`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_transaction_payments(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `transaction_id` int(11) NOT NULL,
            `account_id` int(11) NOT NULL,
            `amount` double(15,4) NOT NULL,
            `description` text,
            `payment_method` varchar(50) NOT NULL,
            `reference` varchar(191) DEFAULT NULL,
            `paid_at` datetime NOT NULL,
            `reconciled` tinyint(1) NOT NULL DEFAULT '0',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `transaction_id` (`transaction_id`),
		    KEY `account_id` (`account_id`),
		    KEY `amount` (`amount`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_transaction_items(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `transaction_id` int(11) NOT NULL,
            `item_id` int(11) DEFAULT NULL,
            `name` varchar(191) NOT NULL,
            `sku` varchar(191) DEFAULT NULL,
            `quantity` double(7,2) NOT NULL,
            `price` double(15,4) NOT NULL,
            `total` double(15,4) NOT NULL,
            `tax` double(15,4) NOT NULL DEFAULT '0.0000',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `transaction_id` (`transaction_id`),
		    KEY `price` (`price`),
		    KEY `total` (`total`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_transaction_item_taxes(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `transaction_id` int(11) NOT NULL,
            `transaction_item_id` int(11) NOT NULL,
            `tax_id` int(11) NOT NULL,
            `name` varchar(191) NOT NULL,
            `amount` double(15,4) NOT NULL DEFAULT '0.0000',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `transaction_id` (`transaction_id`),
		    KEY `transaction_item_id` (`transaction_item_id`),
		    KEY `amount` (`amount`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_transaction_histories(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `transaction_id` int(11) NOT NULL,
            `status` varchar(120) NOT NULL,
            `notify` tinyint(1) NOT NULL,
            `description` text,
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `transaction_id` (`transaction_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_payments(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `account_id` int(11) NOT NULL,
		  	`paid_at` datetime NOT NULL,
		  	`amount` double(15,4) NOT NULL,
		  	`contact_id` int(11) DEFAULT NULL,
		  	`description` text COLLATE utf8mb4_unicode_ci,
		  	`category_id` int(11) NOT NULL,
		  	`payment_method` varchar(191) NOT NULL,
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
		  	`payment_method` varchar(191) NOT NULL,
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


			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_reconciliations(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `account_id` int(11) NOT NULL,
		  	`started_at` datetime NOT NULL,
		  	`ended_at` datetime NOT NULL,
		  	`closing_balance` double(15,4) NOT NULL DEFAULT '0.0000',
		    `reconciled` tinyint(1) NOT NULL DEFAULT '0',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `account_id` (`account_id`),
		    KEY `started_at` (`started_at`),
		    KEY `ended_at` (`ended_at`),
		    KEY `closing_balance` (`closing_balance`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_recurring(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
  			`recurable_id` int(10) unsigned NOT NULL,
			`recurable_type` varchar(191) NOT NULL,
            `frequency` varchar(191) NOT NULL,
            `interval` int(11) NOT NULL DEFAULT '1',
            `started_at` datetime NOT NULL,
            `count` int(11) NOT NULL DEFAULT '0',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`)
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

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_categories(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
  		  	`name` varchar(191) NOT NULL,
		  	`type` varchar(50) NOT NULL,
		  	`color` varchar(20) NOT NULL,
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
}
