<?php
defined('ABSPATH') || exit();

class EAccounting_Install{
	/**
	 * Everything need to be done
	 *
	 * @since 1.0.0
	 */
	public static function install(){
		self::create_tables();
	}

	/**
	 * Delete all data
	 *
	 * @since 1.0.0
	 */
	public static function uninstall(){

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
		    `currency_code` varchar(10) NOT NULL,
		    `opening_balance` double(15,4) NOT NULL DEFAULT '0.0000',
		    `bank_name` varchar(191) DEFAULT NULL,
		    `bank_phone` varchar(20) DEFAULT NULL,
		    `bank_address` varchar(191) DEFAULT NULL,
		    `enabled` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Account enable or disable',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_taxes(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
			`name` VARCHAR(191) NOT NULL COMMENT 'Taxes Name',
			`rate` DOUBLE(15,4) NOT NULL COMMENT 'Taxes Rate',
			`type` VARCHAR(191) NOT NULL DEFAULT 'normal' COMMENT 'Taxes Type',
			`enabled` TINYINT(1) NOT NULL COMMENT 'Tax enable or disable',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
//            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_invoices(
//            `id` bigint(20) NOT NULL AUTO_INCREMENT,
//            `invoice_number` varchar(191) NOT NULL,
//            `order_number` varchar(191) DEFAULT NULL,
//            `invoice_status_code` varchar(191) NOT NULL,
//            `invoiced_at` datetime NOT NULL,
//            `due_at` datetime NOT NULL,
//            `amount` double(15,4) NOT NULL,
//            `currency_code` varchar(191) NOT NULL,
//            `currency_rate` double(15,8) NOT NULL,
//            `customer_id` int(11) NOT NULL,
//            `customer_name` varchar(191) NOT NULL,
//            `customer_email` varchar(191) DEFAULT NULL,
//            `customer_tax_number` varchar(191) DEFAULT NULL,
//            `customer_phone` varchar(191) DEFAULT NULL,
//            `customer_address` text,
//            `notes` text,
//            `category_id` int(11) NOT NULL DEFAULT '1',
//            `parent_id` int(11) NOT NULL DEFAULT '0',
//		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
//		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
//		    PRIMARY KEY (`id`),
//		    KEY `company_id` (`company_id`)
//            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
//            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_invoice_totals(
//            `id` bigint(20) NOT NULL AUTO_INCREMENT,
//            `company_id` int(11) NOT NULL,
//            `invoice_id` int(11) NOT NULL,
//            `code` varchar(191) DEFAULT NULL,
//            `name` varchar(191) NOT NULL,
//            `amount` double(15,4) NOT NULL,
//            `sort_order` int(11) NOT NULL,
//		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
//		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
//		    PRIMARY KEY (`id`),
//		    KEY `company_id` (`company_id`)
//            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
//            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_invoice_payments(
//            `id` bigint(20) NOT NULL AUTO_INCREMENT,
//            `company_id` int(11) NOT NULL,
//            `invoice_id` int(11) NOT NULL,
//            `account_id` int(11) NOT NULL,
//            `amount` double(15,4) NOT NULL,
//            `currency_code` varchar(191) NOT NULL,
//            `currency_rate` double(15,8) NOT NULL,
//            `description` text,
//            `payment_method` varchar(191) NOT NULL,
//            `reference` varchar(191) DEFAULT NULL,
//            `paid_at` datetime NOT NULL,
//            `reconciled` tinyint(1) NOT NULL DEFAULT '0',
//		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
//		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
//		    PRIMARY KEY (`id`),
//		    KEY `company_id` (`company_id`)
//            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
//            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_invoice_payments(
//            `id` bigint(20) NOT NULL AUTO_INCREMENT,
//            `company_id` int(11) NOT NULL,
//            `invoice_id` int(11) NOT NULL,
//            `item_id` int(11) DEFAULT NULL,
//            `name` varchar(191) NOT NULL,
//            `sku` varchar(191) DEFAULT NULL,
//            `quantity` double(7,2) NOT NULL,
//            `price` double(15,4) NOT NULL,
//            `total` double(15,4) NOT NULL,
//            `tax` double(15,4) NOT NULL DEFAULT '0.0000',
//		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
//		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
//		    PRIMARY KEY (`id`),
//		    KEY `company_id` (`company_id`)
//            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
//            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_invoice_item_taxes(
//            `id` bigint(20) NOT NULL AUTO_INCREMENT,
//            `company_id` int(11) NOT NULL,
//            `invoice_id` int(11) NOT NULL,
//            `invoice_item_id` int(11) NOT NULL,
//            `tax_id` int(11) NOT NULL,
//            `name` varchar(191) NOT NULL,
//            `amount` double(15,4) NOT NULL DEFAULT '0.0000',
//		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
//		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
//		    PRIMARY KEY (`id`),
//		    KEY `company_id` (`company_id`)
//            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
//            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_invoice_item_taxes(
//            `id` bigint(20) NOT NULL AUTO_INCREMENT,
//            `company_id` int(11) NOT NULL,
//            `invoice_id` int(11) NOT NULL,
//            `status_code` varchar(191) NOT NULL,
//            `notify` tinyint(1) NOT NULL,
//            `description` text,
//		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
//		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
//		    PRIMARY KEY (`id`),
//		    KEY `company_id` (`company_id`)
//            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
//            "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_payments(
//            `id` bigint(20) NOT NULL AUTO_INCREMENT,
//            `company_id` int(11) NOT NULL,
//            `account_id` int(11) NOT NULL,
//            `paid_at` datetime NOT NULL,
//            `amount` double(15,4) NOT NULL,
//            `currency_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
//            `currency_rate` double(15,8) NOT NULL,
//            `vendor_id` int(11) DEFAULT NULL,
//            `description` text COLLATE utf8mb4_unicode_ci,
//            `category_id` int(11) NOT NULL,
//            `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
//            `reference` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
//            `parent_id` int(11) NOT NULL DEFAULT '0',
//            `reconciled` tinyint(1) NOT NULL DEFAULT '0',
//		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
//		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
//		    PRIMARY KEY (`id`),
//		    KEY `company_id` (`company_id`)
//            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",
		];


		foreach ( $tables as $table ) {
			dbDelta( $table );
		}
	}
}
