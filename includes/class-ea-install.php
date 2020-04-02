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
		self::create_default_data();
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
			`phone` VARCHAR(20) DEFAULT NULL,
			`address` VARCHAR(191) DEFAULT NULL,
			`city` VARCHAR(50) DEFAULT NULL,
			`state` VARCHAR(50) DEFAULT NULL,
			`postcode` VARCHAR(20) DEFAULT NULL,
			`country` VARCHAR(20) DEFAULT NULL,
			`website` VARCHAR(191) DEFAULT NULL,
			`note` TEXT DEFAULT NULL,
			`avatar_url` VARCHAR(2083) DEFAULT NULL,
			`tax_number` VARCHAR(50) DEFAULT NULL,
			`currency_code` varchar(191) NOT NULL DEFAULT 'USD',
  			`types` VARCHAR(191) DEFAULT NULL COMMENT 'Customer or vendor',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `email`(`email`),
		    KEY `phone`(`phone`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_accounts(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
		    `name` VARCHAR(191) NOT NULL COMMENT 'Account Name',
		    `number` VARCHAR(191) NOT NULL COMMENT 'Account Number',
		    `opening_balance` DOUBLE(15,4) NOT NULL DEFAULT '0.0000',
		    `currency_code` varchar(191) NOT NULL DEFAULT 'USD',
		    `bank_name` VARCHAR(191) DEFAULT NULL,
		    `bank_phone` VARCHAR(20) DEFAULT NULL,
		    `bank_address` VARCHAR(191) DEFAULT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_items(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
			`name` VARCHAR(191) NOT NULL,
			`sku` VARCHAR(50) DEFAULT NULL,
			`description` TEXT DEFAULT NULL,
			`sale_price` double(15,4) NOT NULL,
			`purchase_price` double(15,4) NOT NULL,
			`quantity` int(11) NOT NULL,
			`tax_id` int(11) DEFAULT NULL,
			`image_id` int(11) DEFAULT NULL,
  			`category_id` int(11) DEFAULT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `sale_price`(`sale_price`),
		    KEY `purchase_price`(`purchase_price`),
		    KEY `quantity`(`quantity`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_payments(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `account_id` INT(11) NOT NULL,
		  	`paid_at` datetime NOT NULL,
		  	`amount` DOUBLE(15,4) NOT NULL,
		  	`contact_id` INT(11) DEFAULT NULL,
		  	`description` text,
		  	`category_id` INT(11) NOT NULL,
		  	`currency_code` varchar(191) NOT NULL DEFAULT 'USD',
		  	`currency_rate` double(15,8) NOT NULL DEFAULT 1,
		  	`payment_method` VARCHAR(100) DEFAULT NULL,
		  	`reference` VARCHAR(191) DEFAULT NULL,
			`file_ids` VARCHAR(199) DEFAULT NULL,
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
		  	`description` text,
		  	`category_id` INT(11) NOT NULL,
		  	`currency_code` varchar(191) NOT NULL DEFAULT 'USD',
		  	`currency_rate` double(15,8) NOT NULL DEFAULT 1,
	  		`payment_method` VARCHAR(100) DEFAULT NULL,
		  	`reference` VARCHAR(191) DEFAULT NULL,
			`file_ids` VARCHAR(199) DEFAULT NULL,
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
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `type` (`type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_taxes(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
			`name` VARCHAR(191) NOT NULL COMMENT 'Taxes Name',
			`rate` DOUBLE(15,4) NOT NULL COMMENT 'Taxes Rate',
			`type` VARCHAR(191) NOT NULL DEFAULT 'normal' COMMENT 'Taxes Type',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_invoices(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
			`invoice_number` varchar(191) NOT NULL,
			`order_number` varchar(191) NOT NULL,
			`invoiced_at` datetime NOT NULL,
		    `due_at` datetime NOT NULL,
		    `amount` double(15,4) NOT NULL,
 			`currency_code` varchar(191) NOT NULL DEFAULT 'USD',
		  	`currency_rate` double(15,8) NOT NULL DEFAULT 1,
		    `contact_id` int(11) NOT NULL,
		    `contact_name` varchar(191) NOT NULL,
		    `contact_email` varchar(191) DEFAULT NULL,
		    `contact_tax_number` varchar(191) DEFAULT NULL,
		    `contact_phone` varchar(191) DEFAULT NULL,
		    `contact_address` text,
		    `notes` text,
		    `created_at` timestamp NULL DEFAULT NULL,
		    `updated_at` timestamp NULL DEFAULT NULL,
		    `category_id` int(11) NOT NULL DEFAULT '1',
		    `parent_id` int(11) NOT NULL DEFAULT '0',
		    PRIMARY KEY (`id`),
		    KEY `amount` (`amount`),
		    KEY `contact_id` (`contact_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_invoice_totals(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `invoice_id` int(11) NOT NULL,
            `type` varchar(20) DEFAULT NULL,
            `amount` double(15,4) NOT NULL,
            `sort_order` int(11) NOT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `invoice_id` (`invoice_id`),
		    KEY `type` (`type`),
		    KEY `amount` (`amount`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_invoice_statuses(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
			`name` varchar(191) NOT NULL,
			`created_at` timestamp NULL DEFAULT NULL,
			`updated_at` timestamp NULL DEFAULT NULL,
		    PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_invoice_payments(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `invoice_id` int(11) NOT NULL,
            `account_id` int(11) NOT NULL,
            `paid_at` datetime NOT NULL,
            `amount` double(15,4) NOT NULL,
 			`currency_code` varchar(191) NOT NULL DEFAULT 'USD',
		  	`currency_rate` double(15,8) NOT NULL DEFAULT 1,
            `description` text,
     		`payment_method` VARCHAR(100) DEFAULT NULL,
            `reference` varchar(191) DEFAULT NULL,
            `reconciled` tinyint(1) NOT NULL DEFAULT '0',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `invoice_id` (`invoice_id`),
		    KEY `account_id` (`account_id`),
		    KEY `amount` (`amount`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_invoice_items(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `invoice_id` int(11) NOT NULL,
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
		    KEY `invoice_id` (`invoice_id`),
		    KEY `price` (`price`),
		    KEY `total` (`total`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_invoice_item_taxes(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `invoice_id` int(11) NOT NULL,
            `invoice_item_id` int(11) NOT NULL,
            `tax_id` int(11) NOT NULL,
            `name` varchar(191) NOT NULL,
            `amount` double(15,4) NOT NULL DEFAULT '0.0000',
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `invoice_id` (`invoice_id`),
		    KEY `invoice_item_id` (`invoice_item_id`),
		    KEY `amount` (`amount`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_invoice_histories(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `invoice_id` int(11) NOT NULL,
            `notify` tinyint(1) NOT NULL,
            `description` text,
		    `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `invoice_id` (`invoice_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_currencies(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
			`name` varchar(100) NOT NULL,
			`code` varchar(10) NOT NULL,
			`rate` double(15,8) NOT NULL,
	   		`created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `rate` (`rate`),
		    KEY `code` (`code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_files(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
			`name` varchar(199) NOT NULL,
			`path` varchar(199) NOT NULL,
			`extension` varchar(28) NOT NULL,
			`mime_type` varchar(128) NOT NULL,
			`size` int(10) unsigned NOT NULL,
	   		`created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date',
		    PRIMARY KEY (`id`),
		    KEY `name` (`name`),
		    KEY `path` (`path`)
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
				'name' => __( 'Deposit', 'wp-ever-accounting' ),
				'type' => 'income',
			] );

			eaccounting_insert_category( [
				'name' => __( 'Other', 'wp-ever-accounting' ),
				'type' => 'expense',
			] );

			eaccounting_insert_category( [
				'name' => __( 'Sales', 'wp-ever-accounting' ),
				'type' => 'income',
			] );
		}

		//create transfer category
		if ( ! eaccounting_get_category( 'Transfer', 'name' ) ) {
			eaccounting_insert_category( [
				'name' => __( 'Transfer', 'wp-ever-accounting' ),
				'type' => 'other',
			] );
		}

		if ( ! eaccounting_get_accounts() ) {
			eaccounting_insert_account( [
				'name'            => __( 'Cash', 'wp-ever-accounting' ),
				'number'          => '',
				'opening_balance' => '0',
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
				'note'       => 'demo user',
				'types'      => [ 'vendor', 'customer' ],
			] );
		}

		if ( ! eaccounting_get_currencies() ) {
			eaccounting_insert_currency( array(
				'name'              => 'US Dollar',
				'code'              => 'USD',
				'rate'              => '1',
				'precision'         => 2,
				'symbol'            => '$',
				'position'          => 'before',
				'decimalSeparator'  => '.',
				'thousandSeparator' => ',',
			) );
			eaccounting_insert_currency( array(
				'name'              => 'Taka',
				'code'              => 'BDT',
				'rate'              => '84.89',
				'precision'         => 2,
				'symbol'            => '৳',
				'position'          => 'before',
				'decimalSeparator'  => '.',
				'thousandSeparator' => ',',
			) );
		}

	}
}
