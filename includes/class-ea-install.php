<?php
/**
 * Main Plugin Install Class.
 *
 * @since       1.0.2
 * @package     EverAccounting
 */
defined( 'ABSPATH' ) || exit();

class EAccounting_Install {
	/**
	 * Everything need to be done
	 *
	 * @since 1.0.2
	 */
	public static function install() {
		self::create_tables();
		//setup plugin storage
		eaccounting_protect_files( true );

		// Add Upgraded From Option
		update_option( 'eaccounting_version', eaccounting()->get_version() );

		$installation_time = get_option( 'eaccounting_install_date' );
		if ( empty( $installation_time ) ) {
			update_option( 'eaccounting_install_date', current_time( 'timestamp' ) );
		}

		//If no categories then create default categories
		if ( ! \EverAccounting\Query_Category::init()->count() ) {
			eaccounting_insert_category( [
				'name'    => __( 'Deposit', 'wp-ever-accounting' ),
				'type'    => 'income',
				'enabled' => '1',
			] );

			eaccounting_insert_category( [
				'name'    => __( 'Other', 'wp-ever-accounting' ),
				'type'    => 'expense',
				'enabled' => '1',
			] );

			eaccounting_insert_category( [
				'name'    => __( 'Sales', 'wp-ever-accounting' ),
				'type'    => 'income',
				'enabled' => '1',
			] );
		}

		//create transfer category
		if ( ! ! \EverAccounting\Query_Category::init()->where( array( 'name' => 'Transfer', 'type' => 'other' ) )->count() ) {
			eaccounting_insert_category( [
				'name'    => __( 'Transfer', 'wp-ever-accounting' ),
				'type'    => 'other',
				'enabled' => '1',
			] );
		}

		//create currencies
		if ( ! \EverAccounting\Query_Currency::init()->count() ) {
			eaccounting_insert_currency( [
				'code' => 'USD',
				'rate' => '1',
			] );
			eaccounting_insert_currency( [
				'code' => 'EUR',
				'rate' => '1.25',
			] );
			eaccounting_insert_currency( [
				'code' => 'GBP',
				'rate' => '1.6',
			] );
			eaccounting_insert_currency( [
				'code' => 'CAD',
				'rate' => '1.31',
			] );
			eaccounting_insert_currency( [
				'code' => 'JPY',
				'rate' => '106.22',
			] );
			eaccounting_insert_currency( [
				'code' => 'BDT',
				'rate' => '84.81',
			] );
		}

		//create default account
		if ( ! \EverAccounting\Query_Account::init()->count() ) {
			eaccounting_insert_account( [
				'name'            => 'Cash',
				'currency_code'   => 'USD',
				'number'          => '001',
				'opening_balance' => '0',
				'enabled'         => '1',
			] );
		}

		$settings = new \EverAccounting\Admin\Settings();

		if ( empty( $settings->get( 'financial_year_start' ) ) ) {
			$settings->set( [ 'financial_year_start' => '01-01' ]);
		}

		if ( empty( $settings->get( 'default_payment_method' ) ) ) {
			$settings->set( [ 'default_payment_method' => 'cash' ]);
		}

		$account = \EverAccounting\Query_Account::init()->find( 'Cash', 'name' );
		if ( ! empty( $account ) && empty( $settings->get( 'default_account' ) ) ) {
			$settings->set( [ 'default_account' => $account->id ]);
		}

		$currency = \EverAccounting\Query_Currency::init()->find( 'USD', 'code' );
		if ( ! empty( $currency ) && empty( $settings->get( 'default_currency' ) ) ) {
			$settings->set( [ 'default_currency' => $currency->code ]);
		}

		$settings->set(array(), true);

		$capabilities = new \EAccounting\Roles();
		$capabilities->add_roles();

		// Add a temporary option
		set_transient( '_eaccounting_installed', true, 60 );

		if ( apply_filters( 'eaccounting_enable_setup_wizard', true ) && get_option('ea_run_setup_wizard') == false ) {
			set_transient( '_ea_activation_redirect', 1, 30 );
		}
	}

	/**
	 * Delete all data
	 *
	 * @since 1.0.2
	 */
	public static function uninstall() {

	}

	/**
	 * Creat tables
	 *
	 * @since 1.0.2
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
		    KEY `company_id`(`company_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8",

			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ea_transactions(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `type` VARCHAR(100) DEFAULT NULL,
		  	`paid_at` date NOT NULL,
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
}
