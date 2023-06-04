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
		'1.1.5.1' => 'eac_update_1_1_5_1',
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
	 * @since 1.0.5
	 * @return void
	 */
	public function check_update() {
		$db_version      = ever_accounting()->get_db_version();
		$current_version = ever_accounting()->get_version();
		$requires_update = version_compare( $db_version, $current_version, '<' );
		$can_install     = ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && ! defined( 'IFRAME_REQUEST' );
		if ( $can_install && $requires_update ) {
			static::install();
			$update_versions = array_keys( $this->updates );
			usort( $update_versions, 'version_compare' );
			if ( ! is_null( $db_version ) && version_compare( $db_version, end( $update_versions ), '<' ) ) {
				$this->update();
			} else {
				ever_accounting()->update_db_version( $current_version );
			}
		}
	}

	/**
	 * Update the plugin.
	 *
	 * @since 1.0.5
	 * @return void
	 */
	public function update() {
		$db_version = ever_accounting()->get_db_version();
		foreach ( $this->updates as $version => $callbacks ) {
			$callbacks = (array) $callbacks;
			if ( version_compare( $db_version, $version, '<' ) ) {
				foreach ( $callbacks as $callback ) {
					require_once __DIR__ . '/Functions/Updates.php';
					// if the callback return false then we need to update the db version.
					$continue = call_user_func( $callback );
					if ( ! $continue ) {
						ever_accounting()->update_db_version( $version );
					}
				}
			}
		}
	}

	/**
	 * Install the plugin.
	 *
	 * @since 1.0.5
	 * @return void
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
		self::create_terms();
		self::create_roles();
		self::schedule_events();
		flush_rewrite_rules();
	}

	/**
	 * Create the tables.
	 *
	 * @since 1.1.6
	 * @return void
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

		// If version is 1.1.5 drop currency table.
		// Somehow the table was created before this version.
		if ( version_compare( ever_accounting()->get_db_version(), '1.1.5', '=' ) ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ea_currencies" );
		}

		$tables = array(
			"CREATE TABLE {$wpdb->prefix}ea_accounts(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `type` VARCHAR(50) NOT NULL,
		    `name` VARCHAR(191) NOT NULL,
		    `number` VARCHAR(100) NOT NULL,
		    `opening_balance` DOUBLE(15,4) NOT NULL DEFAULT '0.0000',
		    `bank_name` VARCHAR(191) DEFAULT NULL,
		    `bank_phone` VARCHAR(20) DEFAULT NULL,
		    `bank_address` VARCHAR(191) DEFAULT NULL,
		    `currency_code` varchar(3) NOT NULL DEFAULT 'USD',
		   	`status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
		   	`creator_id` INT(11) DEFAULT NULL,
		   	`updated_at` DATETIME NULL DEFAULT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `type` (`type`),
		    KEY `currency_code` (`currency_code`),
		    KEY `status` (`status`),
		    UNIQUE KEY (`number`)
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
            `user_id` INT(11) DEFAULT NULL,
			`name` VARCHAR(191) NOT NULL,
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
			`currency_code` varchar(3) NOT NULL DEFAULT 'USD',
  			`type` VARCHAR(30) DEFAULT NULL default 'customer',
			`status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
			`thumbnail_id` BIGINT(20) UNSIGNED DEFAULT NULL,
			`creator_id` INT(11) DEFAULT NULL,
		   	`updated_at` DATETIME NULL DEFAULT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `user_id`(`user_id`),
		    KEY `name`(`name`),
		    KEY `email`(`email`),
		    KEY `phone`(`phone`),
		    KEY `status`(`status`),
		    KEY `type`(`type`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_document_items(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`document_id` INT(11) DEFAULT NULL,
  			`product_id` INT(11) DEFAULT NULL,
  			`name` VARCHAR(191) NOT NULL,
  			`description` TEXT NULL,
  			`unit` VARCHAR(20) NOT NULL DEFAULT 'unit',
  			`price` double(15,4) NOT NULL,
  			`quantity` double(7,2) NOT NULL DEFAULT 0.00,
  			`subtotal` double(15,4) NOT NULL DEFAULT 0.00,
  			`subtotal_tax` double(15,4) NOT NULL DEFAULT 0.00,
  			`discount` double(15,4) NOT NULL DEFAULT 0.00,
  			`discount_tax` double(15,4) NOT NULL DEFAULT 0.00,
  			`shipping` double(15,4) NOT NULL DEFAULT 0.00,
  			`shipping_tax` double(15,4) NOT NULL DEFAULT 0.00,
  			`fee` double(15,4) NOT NULL DEFAULT 0.00,
  			`fee_tax` double(15,4) NOT NULL DEFAULT 0.00,
  			`tax_total` double(15,4) NOT NULL DEFAULT 0.00,
  			`total` double(15,4) NOT NULL DEFAULT 0.00,
  			`taxable` ENUM('yes','no') NOT NULL DEFAULT 'yes',
  			`taxable_shipping` ENUM('yes','no') NOT NULL DEFAULT 'yes',
  			`taxable_fee` ENUM('yes','no') NOT NULL DEFAULT 'yes',
  			`currency_code` varchar(3) NOT NULL DEFAULT 'USD',
		  	`updated_at` DATETIME NULL DEFAULT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `document_id` (`document_id`),
		    KEY `product_id` (`product_id`),
		    KEY `name` (`name`),
		    KEY `unit` (`unit`),
		    KEY `price` (`price`),
		    KEY `quantity` (`quantity`),
		    KEY `subtotal` (`subtotal`),
		    KEY `discount` (`discount`),
		    KEY `shipping` (`shipping`),
		    KEY `fee` (`fee`),
		    KEY `total` (`total`),
		    KEY `tax_total` (`tax_total`),
		    KEY `taxable` (`taxable`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_document_taxes(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`item_id` BIGINT(20) UNSIGNED NOT NULL,
  			`tax_id` BIGINT(20) UNSIGNED NOT NULL,
  			`document_id` BIGINT(20) UNSIGNED NOT NULL,
  			`name` VARCHAR(191) NOT NULL,
  			`rate` double(15,4) NOT NULL,
  			`is_compound` ENUM('yes','no') NOT NULL DEFAULT 'no',
  			`amount` double(15,4) NOT NULL DEFAULT 0.00,
  			`currency_code` varchar(3) NOT NULL DEFAULT 'USD',
		  	`updated_at` DATETIME NULL DEFAULT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL,
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
            `document_number` VARCHAR(30) NOT NULL,
            `contact_id` BIGINT(20) UNSIGNED NOT NULL,
            `subtotal` DOUBLE(15,4) DEFAULT 0,
    		`discount_total` DOUBLE(15,4) DEFAULT 0,
    		`shipping_total` DOUBLE(15,4) DEFAULT 0,
    		`fees_total` DOUBLE(15,4) DEFAULT 0,
    		`tax_total` DOUBLE(15,4) DEFAULT 0,
    		`total` DOUBLE(15,4) DEFAULT 0,
    		`total_paid` DOUBLE(15,4) DEFAULT 0,
    		`total_refunded` DOUBLE(15,4) DEFAULT 0,
    		`discount_amount` DOUBLE(15,4) DEFAULT 0,
    		`discount_type` VARCHAR(30) DEFAULT NULL,
    		`shipping_amount` DOUBLE(15,4) DEFAULT 0,
    		`fees_amount` DOUBLE(15,4) DEFAULT 0,
    		`billing_data` TEXT DEFAULT NULL,
    		`shipping_data` TEXT DEFAULT NULL,
    		`order_number` VARCHAR(30) DEFAULT NULL,
            `document_note` TEXT DEFAULT NULL,
			`tax_inclusive` ENUM('yes', 'no') DEFAULT 'no',
            `vat_exempt` ENUM('yes', 'no') DEFAULT 'no',
  			`issued_at` DATETIME NULL DEFAULT NULL,
            `due_at` DATETIME NULL DEFAULT NULL,
            `sent_at` DATETIME NULL DEFAULT NULL,
            `viewed_at` DATETIME NULL DEFAULT NULL,
            `paid_at` DATETIME NULL DEFAULT NULL,
    		`created_via` VARCHAR(100) DEFAULT NULL,
			`currency_code` varchar(3) NOT NULL DEFAULT 'USD',
			`currency_rate` double(15,4) NOT NULL DEFAULT 1.00,
  			`parent_id` BIGINT(20) UNSIGNED NOT NULL,
  			`creator_id` BIGINT(20) UNSIGNED NOT NULL,
    		`uuid_key` VARCHAR(32) DEFAULT NULL,
		  	`updated_at` DATETIME NULL DEFAULT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `document_number` (`document_number`),
            KEY `contact_id` (`contact_id`),
            KEY `type` (`type`),
            KEY `status` (`status`),
            KEY `tax_total` (`tax_total`),
            KEY `total` (`total`),
            KEY `total_paid` (`total_paid`),
            KEY `total_refunded` (`total_refunded`),
            KEY `issued_at` (`issued_at`),
            KEY `due_at` (`due_at`),
            KEY `sent_at` (`sent_at`),
            KEY `viewed_at` (`viewed_at`),
            KEY `uuid_key` (`uuid_key`)
    		) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_notes(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`object_id`  BIGINT(20) UNSIGNED NOT NULL,
  			`object_type` VARCHAR(20) NOT NULL,
  			`content` TEXT DEFAULT NULL,
  			`extra` longtext DEFAULT NULL,
  			`creator_id` INT(11) DEFAULT NULL,
		  	`updated_at` DATETIME NULL DEFAULT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `object_id` (`object_id`),
		    KEY `object_type` (`object_type`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_products(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(191) NOT NULL,
			`description` TEXT DEFAULT NULL ,
			`unit` VARCHAR(50) NOT NULL DEFAULT 'unit',
            `price` double(15,4) NOT NULL,
			`category_id` int(11) DEFAULT NULL,
  			`taxable` ENUM('yes', 'no') DEFAULT 'yes',
  			`tax_ids` TEXT DEFAULT NULL,
  			`thumbnail_id` BIGINT(20) UNSIGNED DEFAULT NULL,
			`status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
		  	`updated_at` DATETIME NULL DEFAULT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `price` (`price`),
		    KEY `unit` (`unit`),
		    KEY `category_id` (`category_id`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_termmeta(
			`meta_id` bigINT(20) NOT NULL AUTO_INCREMENT,
			`ea_term_id` bigint(20) unsigned NOT NULL default '0',
			`meta_key` varchar(255) default NULL,
			`meta_value` longtext,
			PRIMARY KEY (`meta_id`),
			KEY `ea_term_id`(`ea_term_id`),
			KEY `meta_key` (meta_key($index_length))
			) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_terms(
			`id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`name` VARCHAR(191) NOT NULL,
  			`description` TEXT DEFAULT NULL ,
  			`group` VARCHAR(20) NOT NULL COMMENT 'category, tag, etc',
  			`parent_id` INT(11) DEFAULT NULL,
  			`status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
		    PRIMARY KEY (`id`),
		    KEY `name` (`name`),
		    KEY `group` (`group`),
		    KEY `parent_id` (`parent_id`),
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
            `type` VARCHAR(100) DEFAULT NULL,
            `voucher_number` VARCHAR(30) DEFAULT NULL,
		  	`payment_date` date NOT NULL,
		  	`amount` DOUBLE(15,4) NOT NULL,
		  	`currency_code` varchar(3) NOT NULL DEFAULT 'USD',
		  	`currency_rate` double(15,8) NOT NULL DEFAULT 1,
            `account_id` INT(11) NOT NULL,
            `document_id` INT(11) DEFAULT NULL,
		  	`contact_id` INT(11) DEFAULT NULL,
		  	`category_id` INT(11) NOT NULL,
		  	`payment_note` text DEFAULT NULL,
	  		`payment_method` VARCHAR(100) DEFAULT NULL,
		  	`reference` VARCHAR(191) DEFAULT NULL,
			`attachment_id` INT(11) DEFAULT NULL,
		  	`parent_id` INT(11) DEFAULT NULL,
		    `reconciled` tinyINT(1) NOT NULL DEFAULT '0',
		    `creator_id` INT(11) DEFAULT NULL,
		    `uuid_key` VARCHAR(32) DEFAULT NULL,
		    `updated_at` DATETIME NULL DEFAULT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `voucher_number` (`voucher_number`),
		    KEY `amount` (`amount`),
		    KEY `currency_code` (`currency_code`),
		    KEY `currency_rate` (`currency_rate`),
		    KEY `type` (`type`),
		    KEY `account_id` (`account_id`),
		    KEY `document_id` (`document_id`),
		    KEY `category_id` (`category_id`),
		    KEY `uuid_key` (`uuid_key`),
		    KEY `contact_id` (`contact_id`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_transfers(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`income_id` INT(11) NOT NULL,
  			`expense_id` INT(11) NOT NULL,
  			`creator_id` INT(11) DEFAULT NULL,
		    `updated_at` DATETIME NULL DEFAULT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `income_id` (`income_id`),
		    KEY `expense_id` (`expense_id`)
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
	 * @since 1.1.6
	 * @return void
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
		// migrate currency.
		$currencies        = include ever_accounting()->get_dir_path( 'i18n/currencies.php' );
		$active_currencies = array( 'BDT', 'USD', 'EUR', 'GBP', 'INR', 'AUD', 'CAD', 'SGD', 'CHF', 'MYR', 'JPY', 'CNY' );
		$legacy_settings   = get_option( 'eaccounting_settings', array() );
		$is_new            = empty( get_option( 'eac_currencies' ) );
		if ( ! empty( $legacy_settings ) && $is_new ) {
			$legacy_base = isset( $legacy_settings['base_currency'] ) ? $legacy_settings['base_currency'] : 'USD';
			update_option( 'eac_base_currency', $legacy_base );
			$legacy_currencies = get_option( 'eaccounting_currencies', array() );
			$legacy_currencies = wp_list_pluck( $legacy_currencies, 'code' );
			if ( ! empty( $legacy_currencies ) ) {
				$active_currencies = $legacy_currencies;
			}
		}

		// This part must go after the above migration.
		$base_currency = eac_get_base_currency();
		// Make sure base currency is active.
		if ( ! in_array( $base_currency, $active_currencies, true ) ) {
			$active_currencies[] = $base_currency;
		}

		if ( 'USD' !== $base_currency && isset( $currencies[ $base_currency ] ) ) {
			$new_rate = $currencies[ $base_currency ]['rate'];
			foreach ( $currencies as $code => $currency ) {
				if ( isset( $currency['rate'] ) ) {
					$currencies[ $code ]['rate'] = $currency['rate'] / $new_rate;
				}
			}
		}
		$currencies[ $base_currency ]['rate'] = 1;
		// Set all currency status as inactive.
		foreach ( $currencies as $code => &$currency ) {
			$status             = in_array( $code, $active_currencies, true ) ? 'active' : 'inactive';
			$currency['status'] = $status;
			// If base currency the set a flag.
			if ( $base_currency === $code ) {
				$currency['base'] = 'yes';
			}
		}
		// sort by name.
		uasort(
			$currencies,
			function ( $a, $b ) {
				return strcasecmp( $a['name'], $b['name'] );
			}
		);
		add_option( 'eac_currencies', $currencies );
	}

	/**
	 * Create default accounts.
	 *
	 * @since 1.1.6
	 * @returns void
	 */
	public static function create_accounts() {
		$default_currency = eac_get_base_currency();
		global $wpdb;
		$accounts = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}ea_accounts" );
		if ( empty( $accounts ) ) {
			// Create few dummy accounts.
			$accounts = array(
				array(
					'name'       => __( 'Cash', 'wp-ever-accounting' ),
					'number'     => '1000',
					'currency'   => $default_currency,
					'balance'    => 0,
					'status'     => 'active',
					'created_at' => current_time( 'mysql' ),
				),
				array(
					'name'       => __( 'Bank', 'wp-ever-accounting' ),
					'number'     => '1001',
					'type'       => 'bank',
					'currency'   => $default_currency,
					'balance'    => 0,
					'status'     => 'active',
					'created_at' => current_time( 'mysql' ),
				),
			);
			foreach ( $accounts as $account ) {
				Models\Account::insert( $account );
			}
		}
		$accounts = Models\Account::query();
		if ( ! empty( $accounts ) ) {
			$account = array_pop( $accounts );
			add_option( 'eac_default_account', $account->get_id() );
		}
	}

	/**
	 * Create default categories.
	 *
	 * @since 1.1.6
	 * @returns void
	 */
	public static function create_terms() {
		$terms = get_terms();
		if ( empty( $terms ) ) {
			$terms = array(
				array(
					'name'   => __( 'Deposit', 'wp-ever-accounting' ),
					'type'   => 'income_cat',
					'status' => 'active',
				),
				array(
					'name'   => __( 'Sales', 'wp-ever-accounting' ),
					'type'   => 'income_cat',
					'status' => 'active',
				),
				array(
					'name'   => __( 'Other', 'wp-ever-accounting' ),
					'type'   => 'income_cat',
					'status' => 'active',
				),
				array(
					'name'   => __( 'Withdrawal', 'wp-ever-accounting' ),
					'type'   => 'expense_cat',
					'status' => 'active',
				),
				array(
					'name'   => __( 'Purchase', 'wp-ever-accounting' ),
					'type'   => 'expense',
					'status' => 'expense_cat',
				),
				array(
					'name'   => __( 'Other', 'wp-ever-accounting' ),
					'type'   => 'expense',
					'status' => 'expense_cat',
				),
			);
			foreach ( $terms as $term ) {
				eac_insert_term( $term );
			}
		}
	}

	/**
	 * Create roles and capabilities.
	 *
	 * @since 1.1.6
	 * @return void
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
				'eac_manage_income'   => true,
				'eac_manage_expense'  => true,
				'eac_manage_transfer' => true,
				'eac_manage_category' => true,
				'eac_manage_currency' => true,
				'eac_manage_item'     => true,
				'eac_manage_invoice'  => true,
				'eac_manage_bill'     => true,
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
				'eac_manage_income'   => true,
				'eac_manage_expense'  => true,
				'eac_manage_transfer' => true,
				'eac_manage_category' => true,
				'eac_manage_currency' => true,
				'eac_manage_item'     => true,
				'eac_manage_invoice'  => true,
				'eac_manage_bill'     => true,
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
			$wp_roles->add_cap( 'administrator', 'eac_manage_income' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_expense' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_transfer' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_category' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_currency' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_item' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_invoice' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_bill' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_import' );
			$wp_roles->add_cap( 'administrator', 'eac_manage_export' );
		}
	}

	/**
	 * Create cron jobs (clear them first).
	 *
	 * @since 1.0.2
	 * @return void
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
	 * in a single site or multi site environment.
	 *
	 * @return array EverAccounting tables.
	 */
	public static function get_tables() {
		global $wpdb;

		$tables = array(
			"{$wpdb->prefix}ea_accounts",
			"{$wpdb->prefix}ea_contactmeta",
			"{$wpdb->prefix}ea_contacts",
			"{$wpdb->prefix}ea_document_items",
			"{$wpdb->prefix}ea_document_taxes",
			"{$wpdb->prefix}ea_documentmeta",
			"{$wpdb->prefix}ea_documents",
			"{$wpdb->prefix}ea_notes",
			"{$wpdb->prefix}ea_products",
			"{$wpdb->prefix}ea_termmeta",
			"{$wpdb->prefix}ea_terms",
			"{$wpdb->prefix}ea_transactionmeta",
			"{$wpdb->prefix}ea_transactions",
			"{$wpdb->prefix}ea_transfers",
		);

		$tables = apply_filters( 'ever_accounting_tables', $tables );

		return $tables;
	}
}
