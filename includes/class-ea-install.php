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
//		self::create_default_data();
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
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
		    `name` VARCHAR(191) NOT NULL COMMENT 'Account Name',
		    `number` VARCHAR(191) NOT NULL COMMENT 'Account Number',
		    `opening_balance` DOUBLE(15,4) NOT NULL DEFAULT '0.0000',
		    `currency_code` varchar(3) NOT NULL DEFAULT 'USD',
		    `bank_name` VARCHAR(191) DEFAULT NULL,
		    `bank_phone` VARCHAR(20) DEFAULT NULL,
		    `bank_address` VARCHAR(191) DEFAULT NULL,
		   	`company_id` INT(11) DEFAULT 1,
		   	`enabled` tinyint(1) NOT NULL DEFAULT '1',
		   	`creator_id` INT(11) DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY (`currency_code`),
		    KEY `company_id` (`company_id`),
		    UNIQUE KEY (`number`),
		    UNIQUE KEY (`name`, `number`, `company_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_categories(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  		  	`name` VARCHAR(191) NOT NULL,
		  	`type` VARCHAR(50) NOT NULL,
		  	`color` VARCHAR(20) NOT NULL,
		  	`enabled` tinyint(1) NOT NULL DEFAULT '1',
		  	`company_id` INT(11) DEFAULT 1,
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `type` (`type`),
		    KEY `company_id` (`company_id`),
		    UNIQUE KEY (`name`, `type`, `company_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_currencies(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
			`name` varchar(100) NOT NULL,
			`code` varchar(3) NOT NULL,
			`rate` double(15,8) NOT NULL,
			`precision` varchar(2) DEFAULT NULL,
  			`symbol` varchar(5) DEFAULT NULL,
  			`position` ENUM ('before', 'after') DEFAULT 'before',
  			`decimal_separator` varchar(1) DEFAULT '.',
 			`thousand_separator` varchar(1) DEFAULT ',',
			`enabled` tinyint(1) NOT NULL DEFAULT '1',
	   		`date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `rate` (`rate`),
		    KEY `code` (`code`),
		    UNIQUE KEY (`name`, `code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",


			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_contacts(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `user_id` INT(11) DEFAULT NULL,
			`name` VARCHAR(191) NOT NULL,
			`email` VARCHAR(191) DEFAULT NULL,
			`phone` VARCHAR(50) DEFAULT NULL,
			`fax` VARCHAR(50) DEFAULT NULL,
			`birth_date` date DEFAULT NULL,
			`address` TEXT DEFAULT NULL,
			`country` VARCHAR(3) DEFAULT NULL,
			`website` VARCHAR(191) DEFAULT NULL,
			`tax_number` VARCHAR(50) DEFAULT NULL,
			`currency_code` varchar(3),
  			`type` VARCHAR(100) DEFAULT NULL COMMENT 'Customer or vendor',
			`note` TEXT DEFAULT NULL,
			`files` TEXT DEFAULT NULL,
			`company_id` INT(11) DEFAULT 1,
			`enabled` tinyint(1) NOT NULL DEFAULT '1',
			`creator_id` INT(11) DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `name`(`name`),
		    KEY `email`(`email`),
		    KEY `phone`(`phone`),
		    KEY `type`(`type`),
		    KEY `company_id`(`company_id`),
		    UNIQUE KEY (`name`, `email`, `phone`, `type`, `company_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_transactions(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `type` VARCHAR(100) DEFAULT NULL,
		  	`paid_at` datetime NOT NULL,
		  	`amount` DOUBLE(15,4) NOT NULL,
		  	`currency_code` varchar(3) NOT NULL DEFAULT 'USD',
		  	`currency_rate` double(15,8) NOT NULL DEFAULT 1,
            `account_id` INT(11) NOT NULL,
            `invoice_id` INT(11) DEFAULT NULL,
		  	`contact_id` INT(11) DEFAULT NULL,
		  	`category_id` INT(11) NOT NULL,
		  	`description` text,
	  		`payment_method` VARCHAR(100) DEFAULT NULL,
		  	`reference` VARCHAR(191) DEFAULT NULL,
			`files` TEXT DEFAULT NULL,
		  	`parent_id` INT(11) NOT NULL DEFAULT '0',
		    `reconciled` tinyINT(1) NOT NULL DEFAULT '0',
		    `creator_id` INT(11) DEFAULT NULL,
			`company_id` int(11) NOT NULL DEFAULT 1,
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `account_id` (`account_id`),
		    KEY `amount` (`amount`),
		    KEY `currency_code` (`currency_code`),
		    KEY `currency_rate` (`currency_rate`),
		    KEY `type` (`type`),
		    KEY `company_id` (`company_id`),
		    KEY `contact_id` (`contact_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_transfers(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`income_id` INT(11) NOT NULL,
  			`expense_id` INT(11) NOT NULL,
  			`creator_id` INT(11) DEFAULT NULL,
  			`company_id` int(11) NOT NULL DEFAULT 1,
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `income_id` (`income_id`),
		    KEY `expense_id` (`expense_id`),
		    KEY `company_id` (`company_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_reconciliations(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`account_id` INT(11) NOT NULL,
  			`date_started` DATETIME NULL DEFAULT NULL,
  			`date_ended` DATETIME NULL DEFAULT NULL,
  			`closing_balance` double(15,4) NOT NULL DEFAULT '0.0000',
  			`reconciled` tinyint(1) NOT NULL,
  			`creator_id` INT(11) DEFAULT NULL,
  			`company_id` int(11) NOT NULL DEFAULT 1,
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `account_id` (`account_id`),
		    KEY `date_started` (`date_started`),
		    KEY `date_ended` (`date_ended`),
		    KEY `reconciled` (`reconciled`),
		    KEY `company_id` (`company_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_files(
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
			`name` varchar(199) NOT NULL,
			`path` varchar(199) NOT NULL,
			`extension` varchar(28) NOT NULL,
			`mime_type` varchar(128) NOT NULL,
			`size` int(10) unsigned NOT NULL,
			`creator_id` INT(11) DEFAULT NULL,
			`company_id` int(11) NOT NULL DEFAULT 1,
	   		`created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `name` (`name`),
		    KEY `creator_id` (`creator_id`),
		    KEY `path` (`path`),
		    KEY `company_id` (`company_id`)
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
//		update_option( 'eaccounting_version', EACCOUNTING_VERSION );
//		update_option( 'eaccounting_install_date', date( 'timestamp' ) );
//		eaccounting_set_default_currency( 'USD' );
//
//		if ( empty( eaccounting_get_categories() ) ) {
//			eaccounting_insert_category( [
//				'name' => __( 'Deposit', 'wp-ever-accounting' ),
//				'type' => 'income',
//			] );
//
//			eaccounting_insert_category( [
//				'name' => __( 'Other', 'wp-ever-accounting' ),
//				'type' => 'expense',
//			] );
//
//			eaccounting_insert_category( [
//				'name' => __( 'Sales', 'wp-ever-accounting' ),
//				'type' => 'income',
//			] );
//		}
//
//		//create transfer category
//		if ( empty( eaccounting_get_category( 'Transfer', 'name' ) ) ) {
//			eaccounting_insert_category( [
//				'name' => __( 'Transfer', 'wp-ever-accounting' ),
//				'type' => 'other',
//			] );
//		}
//
//		if ( empty( eaccounting_get_accounts() ) ) {
//			$account_id = eaccounting_insert_account( [
//				'name'            => __( 'Cash', 'wp-ever-accounting' ),
//				'number'          => '0001',
//				'opening_balance' => '0',
//				'currency_code'   => 'USD',
//			] );
//
//			if ( ! is_wp_error( $account_id ) ) {
//				eaccounting_set_option( 'default_account_id', $account_id );
//			}
//		}
//
//		if ( empty( eaccounting_get_currencies() ) ) {
//			eaccounting_insert_currency( array(
//				'name' => 'US Dollar',
//				'code' => 'USD',
//				'rate' => 1,
//			) );
//
//			eaccounting_insert_currency( array(
//				'name' => 'British Pound',
//				'code' => 'GBP',
//				'rate' => 1.6,
//			) );
//			eaccounting_insert_currency( array(
//				'name' => 'Euro',
//				'code' => 'EUR',
//				'rate' => 1.25,
//			) );
//		}


	}
}
