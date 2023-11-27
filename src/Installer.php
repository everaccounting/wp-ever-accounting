<?php

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class Installer
 *
 * @since 1.1.6
 * @package EverAccounting
 */
class Installer extends Singleton {
	/**
	 * Update callbacks.
	 *
	 * @since 1.1.6
	 * @var array
	 */
	protected $updates = array(
		'1.1.6' => 'eac_update_1_1_6',
	);

	/**
	 * Installer constructor.
	 *
	 * @since 1.1.6
	 */
	protected function __construct() {
		add_action( 'init', array( $this, 'check_update' ), 0 );
	}

	/**
	 * Check the plugin version and run the updater if necessary.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 *
	 * @return void
	 * @since 1.0.5
	 */
	public function check_update() {
		$db_version      = EAC()->get_db_version();
		$current_version = EAC()->get_version();
		$requires_update = version_compare( $db_version, $current_version, '<' );
		$can_install     = ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && ! defined( 'IFRAME_REQUEST' );
		if ( $can_install && $requires_update ) {
			static::install();
			$update_versions = array_keys( $this->updates );
			usort( $update_versions, 'version_compare' );
			if ( ! is_null( $db_version ) && version_compare( $db_version, end( $update_versions ), '<' ) ) {
				$this->update();
			} else {
				EAC()->update_db_version( $current_version );
			}
		}
	}

	/**
	 * Update the plugin.
	 *
	 * @return void
	 * @since 1.0.5
	 */
	public function update() {
		$db_version = EAC()->get_db_version();
		foreach ( $this->updates as $version => $callbacks ) {
			$callbacks = (array) $callbacks;
			if ( version_compare( $db_version, $version, '<' ) ) {
				foreach ( $callbacks as $callback ) {
					require_once __DIR__ . '/Functions/Updates.php';
					// if the callback return false then we need to update the db version.
					$continue = call_user_func( $callback );
					if ( ! $continue ) {
						EAC()->update_db_version( $version );
					}
				}
			}
		}
	}

	/**
	 * Install the plugin.
	 *
	 * @return void
	 * @since 1.0.5
	 */
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}
		// Create the tables.
		self::create_tables();
		self::save_defaults();
		self::create_currencies();
		self::create_accounts();
		self::create_categories();
		self::create_roles();
		self::schedule_events();
		flush_rewrite_rules();
	}

	/**
	 * Create the tables.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();
		$index_length = 191;
		$collate      = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		// Order of the table columns is important.
		// Do not change the order of the columns.
		// Start with table specific columns and then common columns.
		// Common columns are used in all tables.
		// example: id, name, type, description, status, creator_id, date_updated, date_created.

		$tables = array(
			"CREATE TABLE {$wpdb->prefix}ea_accounts(
		    `id` bigINT(20) NOT NULL AUTO_INCREMENT,
			`name` VARCHAR(191) NOT NULL,
			`type` VARCHAR(50) NOT NULL,
			`number` VARCHAR(100) NOT NULL,
			`opening_balance` DOUBLE(15,4) NOT NULL DEFAULT '0.0000',
			`bank_name` VARCHAR(191) DEFAULT NULL,
			`bank_phone` VARCHAR(20) DEFAULT NULL,
			`bank_address` VARCHAR(191) DEFAULT NULL,
			`currency_code` varchar(3) NOT NULL DEFAULT 'USD',
			`status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
			`uuid` VARCHAR(36) NOT NULL,
			`creator_id` BIGINT(20) UNSIGNED DEFAULT NULL,
			`date_updated` DATETIME NULL DEFAULT NULL,
			`date_created` DATETIME NULL DEFAULT NULL,
			PRIMARY KEY (`id`),
			KEY `name` (`name`),
			KEY `type` (`type`),
			UNIQUE KEY (`number`),
			UNIQUE KEY (`uuid`),
			KEY `currency_code` (`currency_code`),
			KEY `status` (`status`)
		    ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_categories(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  		  	`name` VARCHAR(191) NOT NULL,
		  	`type` VARCHAR(50) NOT NULL,
		  	`description` TEXT NULL,
		  	`status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
			`date_updated` DATETIME NULL DEFAULT NULL,
			`date_created` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `name` (`name`),
		    KEY `type` (`type`),
		    KEY `status` (`status`),
		    UNIQUE KEY (`name`, `type`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_currencies(
			`id` bigINT(20) NOT NULL AUTO_INCREMENT,
		    `code` VARCHAR(191) NOT NULL COMMENT 'Currency Code',
		    `name` VARCHAR(191) NOT NULL COMMENT 'Currency Name',
		    `precision` INT(2) NOT NULL DEFAULT 0,
		    `symbol` VARCHAR(5) NOT NULL COMMENT 'Currency Symbol',
		    `position` ENUM('before','after') NOT NULL DEFAULT 'before',
		    `thousand_separator` VARCHAR(5) NOT NULL DEFAULT ',',
		    `decimal_separator` VARCHAR(5) NOT NULL DEFAULT '.',
		    `exchange_rate` DOUBLE(15,4) NOT NULL DEFAULT '1.0000',
		    `auto_update` TINYINT(1) NOT NULL DEFAULT 0,
		    `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
			`date_updated` DATETIME NULL DEFAULT NULL,
			`date_created` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `name` (`name`),
		    UNIQUE KEY `code` (`code`),
		    KEY `exchange_rate` (`exchange_rate`),
		    KEY `status` (`status`)
    		) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_contactmeta(
			`meta_id` bigINT(20) NOT NULL AUTO_INCREMENT,
			`ea_contact_id` bigint(20) unsigned NOT NULL default '0',
			`meta_key` varchar(255) default NULL,
			`meta_value` longtext,
			 PRIMARY KEY (`meta_id`),
		    KEY `ea_contact_id`(`ea_contact_id`),
			KEY `meta_key` (meta_key($index_length))
			) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_contacts(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
			`name` VARCHAR(191) NOT NULL,
            `type` VARCHAR(30) DEFAULT NULL default 'customer',
			`company` VARCHAR(191) NOT NULL,
			`email` VARCHAR(191) DEFAULT NULL,
			`phone` VARCHAR(50) DEFAULT NULL,
			`website` VARCHAR(191) DEFAULT NULL,
			`address_1` VARCHAR(191) DEFAULT NULL,
			`address_2` VARCHAR(191) DEFAULT NULL,
			`city` VARCHAR(50) DEFAULT NULL,
			`state` VARCHAR(50) DEFAULT NULL,
			`postcode` VARCHAR(20) DEFAULT NULL,
			`country` VARCHAR(3) DEFAULT NULL,
			`vat_number` VARCHAR(50) DEFAULT NULL,
			`vat_exempt` TINYINT(1) NOT NULL DEFAULT '0',
			`currency_code` varchar(3) NOT NULL DEFAULT 'USD',
			`thumbnail_id` BIGINT(20) UNSIGNED DEFAULT NULL,
			`user_id` BIGINT(20) UNSIGNED DEFAULT NULL,
			`status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
			`uuid` VARCHAR(36) DEFAULT NULL,
			`created_via` VARCHAR(100) DEFAULT 'manual',
			`creator_id` BIGINT(20) UNSIGNED DEFAULT NULL,
		   	`date_updated` DATETIME NULL DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `name`(`name`),
		    KEY `type`(`type`),
		    KEY `email`(`email`),
		    KEY `phone`(`phone`),
    		KEY `currency_code`(`currency_code`),
		    KEY `user_id`(`user_id`),
    		UNIQUE KEY (`uuid`),
		    KEY `status`(`status`)
			) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_document_items(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`name` VARCHAR(191) NOT NULL,
  			`type` VARCHAR(20) NOT NULL default 'standard',
  			`description` TEXT NULL,
  			`unit` VARCHAR(20) DEFAULT NULL,
  			`price` double(15,4) NOT NULL,
  			`quantity` double(7,2) NOT NULL DEFAULT 0.00,
  			`subtotal` double(15,4) NOT NULL DEFAULT 0.00,
  			`subtotal_tax` double(15,4) NOT NULL DEFAULT 0.00,
  			`discount` double(15,4) NOT NULL DEFAULT 0.00,
  			`discount_tax` double(15,4) NOT NULL DEFAULT 0.00,
  			`tax_total` double(15,4) NOT NULL DEFAULT 0.00,
  			`total` double(15,4) NOT NULL DEFAULT 0.00,
  			`taxable` TINYINT(1) NOT NULL DEFAULT 0,
  			`item_id` BIGINT(20) UNSIGNED DEFAULT NULL,
  			`document_id` BIGINT(20) UNSIGNED DEFAULT NULL,
		  	`date_updated` DATETIME NULL DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `name` (`name`),
		    KEY `type` (`type`),
		    KEY `unit` (`unit`),
		    KEY `price` (`price`),
		    KEY `quantity` (`quantity`),
		    KEY `subtotal` (`subtotal`),
		    KEY `discount` (`discount`),
		    KEY `total` (`total`),
		    KEY `tax_total` (`tax_total`),
		    KEY `taxable` (`taxable`),
		    KEY `item_id` (`item_id`),
		    KEY `document_id` (`document_id`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_document_item_taxes(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`name` VARCHAR(191) NOT NULL,
  			`rate` double(15,4) NOT NULL,
  			`is_compound` TINYINT(1) NOT NULL DEFAULT 0,
  			`amount` double(15,4) NOT NULL DEFAULT 0.00,
  			`item_id` BIGINT(20) UNSIGNED NOT NULL,
  			`tax_id` BIGINT(20) UNSIGNED NOT NULL,
  			`document_id` BIGINT(20) UNSIGNED NOT NULL,
		  	`date_updated` DATETIME NULL DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `item_id` (`item_id`),
		    KEY `tax_id` (`tax_id`),
		    KEY `document_id` (`document_id`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_documentmeta(
			`meta_id` bigINT(20) NOT NULL AUTO_INCREMENT,
			`ea_document_id` bigint(20) unsigned NOT NULL default '0',
			`meta_key` varchar(255) default NULL,
			`meta_value` longtext,
			 PRIMARY KEY (`meta_id`),
		    KEY `ea_document_id`(`ea_document_id`),
			KEY `meta_key` (meta_key($index_length))
			) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_documents(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `type` VARCHAR(20) NOT NULL DEFAULT 'invoice',
            `status` VARCHAR(20) DEFAULT NULL DEFAULT 'draft',
            `number` VARCHAR(30) NOT NULL,
            `contact_id` BIGINT(20) UNSIGNED NOT NULL,
            `items_total` DOUBLE(15,4) DEFAULT 0,
    		`discount_total` DOUBLE(15,4) DEFAULT 0,
    		`shipping_total` DOUBLE(15,4) DEFAULT 0,
    		`fees_total` DOUBLE(15,4) DEFAULT 0,
    		`tax_total` DOUBLE(15,4) DEFAULT 0,
    		`total` DOUBLE(15,4) DEFAULT 0,
    		`total_paid` DOUBLE(15,4) DEFAULT 0,
    		`balance` DOUBLE(15,4) DEFAULT 0,
    		`discount_amount` DOUBLE(15,4) DEFAULT 0,
    		`discount_type` VARCHAR(30) DEFAULT NULL,
    		`billing_data` TEXT DEFAULT NULL,
    		`reference` VARCHAR(30) DEFAULT NULL,
            `note` TEXT DEFAULT NULL,
			`tax_inclusive` TINYINT(1) NOT NULL DEFAULT 0,
            `vat_exempt` TINYINT(1) NOT NULL DEFAULT 0,
  			`issue_date` DATETIME NULL DEFAULT NULL,
            `due_date` DATETIME NULL DEFAULT NULL,
            `sent_date` DATETIME NULL DEFAULT NULL,
            `payment_date` DATETIME NULL DEFAULT NULL,
			`currency_code` varchar(3) NOT NULL DEFAULT 'USD',
			`exchange_rate` double(15,4) NOT NULL DEFAULT 1.00,
  			`parent_id` BIGINT(20) UNSIGNED NOT NULL,
    		`uuid` VARCHAR(36) DEFAULT NULL,
    		`created_via` VARCHAR(100) DEFAULT 'manual',
  			`creator_id` BIGINT(20) UNSIGNED NOT NULL,
		  	`date_updated` DATETIME NULL DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `number` (`number`),
            KEY `contact_id` (`contact_id`),
            KEY `type` (`type`),
            KEY `status` (`status`),
            KEY `tax_total` (`tax_total`),
            KEY `total` (`total`),
            KEY `total_paid` (`total_paid`),
            KEY `balance` (`balance`),
           	UNIQUE KEY `uuid` (`uuid`)
    		) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_items(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(191) NOT NULL,
            `type` VARCHAR(50) NOT NULL DEFAULT 'standard',
			`description` TEXT DEFAULT NULL,
			`unit` VARCHAR(50) DEFAULT NULL,
            `price` double(15,4) NOT NULL,
            `cost` double(15,4) NOT NULL,
  			`taxable` TINYINT(1) NOT NULL DEFAULT 0,
  			`tax_ids` VARCHAR(191) DEFAULT NULL,
			`category_id` int(11) DEFAULT NULL,
  			`thumbnail_id` BIGINT(20) UNSIGNED DEFAULT NULL,
			`status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
		  	`date_updated` DATETIME NULL DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `name` (`name`),
		    KEY `type` (`type`),
		    KEY `price` (`price`),
			KEY `cost` (`cost`),
		    KEY `status` (`status`),
		    KEY `unit` (`unit`),
		    KEY `category_id` (`category_id`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_notes(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`object_id`  BIGINT(20) UNSIGNED NOT NULL,
  			`object_type` VARCHAR(20) NOT NULL,
  			`content` TEXT DEFAULT NULL,
  			`note_metadata` longtext DEFAULT NULL,
  			`creator_id` BIGINT(20) UNSIGNED DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `object_id` (`object_id`),
		    KEY `object_type` (`object_type`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_taxes(
    		`id` bigINT(20) NOT NULL AUTO_INCREMENT,
    		`name` VARCHAR(191) NOT NULL,
    		`rate` double(15,4) NOT NULL,
    		`is_compound` TINYINT(1) NOT NULL DEFAULT 0,
    		`description` TEXT DEFAULT NULL ,
    		`status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
		  	`date_updated` DATETIME NULL DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL,
             PRIMARY KEY (`id`),
             KEY `name` (`name`),
             KEY `rate` (`rate`),
             KEY `is_compound` (`is_compound`),
             KEY `status` (`status`)
			 ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_transactionmeta(
    		`meta_id` bigINT(20) NOT NULL AUTO_INCREMENT,
    		`ea_transaction_id` bigint(20) unsigned NOT NULL default '0',
    		`meta_key` varchar(255) default NULL,
    		`meta_value` longtext,
    		PRIMARY KEY (`meta_id`),
    		KEY `ea_transaction_id`(`ea_transaction_id`),
    		KEY `meta_key` (meta_key($index_length))
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_transactions(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `type` VARCHAR(20) DEFAULT NULL,
            `number` VARCHAR(30) DEFAULT NULL,
		  	`date` DATE NOT NULL DEFAULT '0000-00-00',
		  	`amount` DOUBLE(15,4) NOT NULL,
		  	`currency_code` varchar(3) NOT NULL DEFAULT 'USD',
		  	`exchange_rate` double(15,8) NOT NULL DEFAULT 1,
		  	`reference` VARCHAR(191) DEFAULT NULL,
		  	`note` text DEFAULT NULL,
		  	`payment_method` VARCHAR(100) DEFAULT NULL,
            `account_id` BIGINT(20) UNSIGNED NOT NULL,
            `document_id` BIGINT(20) UNSIGNED DEFAULT NULL,
		  	`contact_id` BIGINT(20) UNSIGNED DEFAULT NULL,
		  	`category_id` BIGINT(20) UNSIGNED NOT NULL,
			`attachment_id` BIGINT(20) UNSIGNED DEFAULT NULL,
		  	`parent_id` BIGINT(20) UNSIGNED DEFAULT NULL,
		    `reconciled` tinyINT(1) NOT NULL DEFAULT '0',
		    `uuid` VARCHAR(36) DEFAULT NULL,
		    `created_via` VARCHAR(100) DEFAULT 'manual',
		    `creator_id` BIGINT(20) UNSIGNED DEFAULT NULL,
		  	`date_updated` DATETIME NULL DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `type` (`type`),
		    KEY `number` (`number`),
		    KEY `amount` (`amount`),
		    KEY `currency_code` (`currency_code`),
		    KEY `exchange_rate` (`exchange_rate`),
		    KEY `account_id` (`account_id`),
		    KEY `document_id` (`document_id`),
		    KEY `category_id` (`category_id`),
		    KEY `contact_id` (`contact_id`),
		    UNIQUE KEY `uuid` (`uuid`)
			) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_transfers(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`payment_id` BIGINT(20) UNSIGNED NOT NULL,
  			`expense_id` BIGINT(20) UNSIGNED NOT NULL,
  			`amount` DOUBLE(15,4) NOT NULL,
  			`uuid` VARCHAR(36) DEFAULT NULL,
  			`creator_id` BIGINT(20) UNSIGNED DEFAULT NULL,
		  	`date_updated` DATETIME NULL DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `payment_id` (`payment_id`),
		    KEY `expense_id` (`expense_id`),
		    KEY `amount` (`amount`),
		    UNIQUE KEY `uuid` (`uuid`)
			) $collate",
		);

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		foreach ( $tables as $table ) {
			dbDelta( $table );
		}
	}

	/**
	 * save default data.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function save_defaults() {
		// Save default currency.
		$tabs = Admin\Settings::get_tabs();
		foreach ( $tabs as $tab ) {
			if ( ! method_exists( $tab, 'get_settings_for_section' ) ) {
				continue;
			}
			$subsections = array_unique( array_merge( array( '' ), array_keys( $tab->get_sections() ) ) );
			foreach ( $subsections as $subsection ) {
				foreach ( $tab->get_settings_for_section( $subsection ) as $value ) {
					if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
						$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
						add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
					}
				}
			}
		}
	}

	/**
	 * Create currencies.
	 *
	 * @since 1.1.6
	 * @returns void
	 */
	public static function create_currencies() {
		// If there is no currency, create default currency.
		if ( empty( eac_get_currencies() ) ) {
//			$default_currency = eac_get_base_currency();
//			$currency         = array(
//				'code' => $default_currency,
//			);
//			eac_insert_currency( $currency );
		}
	}

	/**
	 * Create default accounts.
	 *
	 * @since 1.1.6
	 * @returns void
	 */
	public static function create_accounts() {
		if ( empty( eac_get_accounts() ) ) {
			$base_currency = eac_get_base_currency();
			// Create few dummy accounts.
			$accounts = array(
				array(
					'name'     => __( 'Cash', 'wp-ever-accounting' ),
					'number'   => '1000',
					'currency' => $base_currency,
					'status'   => 'active',
				),
				array(
					'name'     => __( 'Bank', 'wp-ever-accounting' ),
					'number'   => '1001',
					'type'     => 'bank',
					'currency' => $base_currency,
					'status'   => 'active',
				),
			);
			foreach ( $accounts as $account ) {
				Models\Account::insert( $account );
			}
		}
		$accounts = Models\Account::query();
		if ( ! empty( $accounts ) ) {
			$account = array_pop( $accounts );
			add_option( 'eac_default_sales_account', $account->get_id() );
			add_option( 'eac_default_purchases_account', $account->get_id() );
		}
	}

	/**
	 * Create default categories.
	 *
	 * @since 1.1.6
	 * @returns void
	 */
	public static function create_categories() {
		if ( empty( eac_get_categories() ) ) {
			$terms = array(
				array(
					'name'        => __( 'Deposit', 'wp-ever-accounting' ),
					'type'        => 'payment',
					'status'      => 'active',
					'created_via' => 'system',
				),
				array(
					'name'        => __( 'Sales', 'wp-ever-accounting' ),
					'type'        => 'payment',
					'status'      => 'active',
					'created_via' => 'system',
				),
				array(
					'name'        => __( 'Other', 'wp-ever-accounting' ),
					'type'        => 'payment',
					'status'      => 'active',
					'created_via' => 'system',
				),
				array(
					'name'        => __( 'Withdrawal', 'wp-ever-accounting' ),
					'type'        => 'expense',
					'status'      => 'active',
					'created_via' => 'system',
				),
				array(
					'name'        => __( 'Purchase', 'wp-ever-accounting' ),
					'type'        => 'expense',
					'status'      => 'active',
					'created_via' => 'system',
				),
				array(
					'name'        => __( 'Uncategorized', 'wp-ever-accounting' ),
					'type'        => 'item',
					'status'      => 'active',
					'created_via' => 'system',
				),
			);
			foreach ( $terms as $term ) {
				eac_insert_category( $term );
			}
		}
	}

	/**
	 * Create roles and capabilities.
	 *
	 * @return void
	 * @since 1.1.6
	 */
	public static function create_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new \WP_Roles();
		}

		// Dummy gettext calls to get strings in the catalog.
		_x( 'Accounting Manager', 'User role', 'wp-ever-accounting' );
		_x( 'Accountant', 'User role', 'wp-ever-accounting' );

		// Accountant role.
		add_role(
			'eac_accountant',
			'Accountant',
			array(
				'manage_accounting'   => true,
				'eac_manage_product'  => true,
				'eac_manage_customer' => true,
				'eac_manage_vendor'   => true,
				'eac_manage_account'  => true,
				'eac_manage_payment'  => true,
				'eac_manage_expense'  => true,
				'eac_manage_transfer' => true,
				'eac_manage_category' => true,
				'eac_manage_currency' => true,
				'eac_manage_item'     => true,
				'eac_manage_invoice'  => true,
				'eac_manage_bill'     => true,
				'eac_manage_tax'      => true,
				'read'                => true,
			)
		);

		// Accounting manager role.
		add_role(
			'eac_manager',
			'Accounting Manager',
			array(
				'manage_accounting'   => true,
				'eac_manage_report'   => true,
				'eac_manage_options'  => true,
				'eac_manage_product'  => true,
				'eac_manage_customer' => true,
				'eac_manage_vendor'   => true,
				'eac_manage_account'  => true,
				'eac_manage_payment'  => true,
				'eac_manage_expense'  => true,
				'eac_manage_transfer' => true,
				'eac_manage_category' => true,
				'eac_manage_currency' => true,
				'eac_manage_item'     => true,
				'eac_manage_invoice'  => true,
				'eac_manage_bill'     => true,
				'eac_manage_tax'      => true,
				'eac_manage_import'   => true,
				'eac_manage_export'   => true,
				'read'                => true,
			)
		);

		// add caps to admin.
		global $wp_roles;

		if ( is_object( $wp_roles ) ) {
			$wp_roles->add_cap( 'administrator', 'manage_accounting' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_report' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_options' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_product' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_customer' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_vendor' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_account' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_payment' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_expense' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_transfer' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_category' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_currency' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_item' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_invoice' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_bill' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_tax' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_import' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_export' );
		}
	}

	/**
	 * Create cron jobs (clear them first).
	 *
	 * @return void
	 * @since 1.0.2
	 */
	public static function schedule_events() {
		wp_clear_scheduled_hook( 'eac_twicedaily_scheduled_event' );
		wp_clear_scheduled_hook( 'eac_daily_scheduled_event' );
		wp_clear_scheduled_hook( 'eac_weekly_scheduled_event' );

		wp_schedule_event( time() + ( 6 * HOUR_IN_SECONDS ), 'twicedaily', 'eac_twicedaily_scheduled_event' );
		wp_schedule_event( time() + 10, 'daily', 'eac_daily_scheduled_event' );
		wp_schedule_event( time() + ( 3 * HOUR_IN_SECONDS ), 'weekly', 'eac_weekly_scheduled_event' );
	}

	/**
	 * Return a list of EverAccounting tables.
	 * Used to make sure all EverAccounting tables are dropped when uninstalling the plugin
	 * in a single site or multisite environment.
	 *
	 * @return array EverAccounting tables.
	 */
	public static function get_tables() {
		global $wpdb;

		$tables = array(
			"{$wpdb->prefix}ea_accounts",
			"{$wpdb->prefix}ea_categories",
			"{$wpdb->prefix}ea_categories",
			"{$wpdb->prefix}ea_contactmeta",
			"{$wpdb->prefix}ea_contacts",
			"{$wpdb->prefix}ea_currencies",
			"{$wpdb->prefix}ea_document_item_taxes",
			"{$wpdb->prefix}ea_document_items",
			"{$wpdb->prefix}ea_documentmeta",
			"{$wpdb->prefix}ea_documents",
			"{$wpdb->prefix}ea_items",
			"{$wpdb->prefix}ea_notes",
			"{$wpdb->prefix}ea_taxes",
			"{$wpdb->prefix}ea_transactionmeta",
			"{$wpdb->prefix}ea_transactions",
			"{$wpdb->prefix}ea_transfers",
		);

		$tables = apply_filters( 'ever_accounting_tables', $tables );

		return $tables;
	}
}
