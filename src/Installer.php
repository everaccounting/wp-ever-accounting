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
		self::create_categories();
		self::create_roles();
		self::schedule_events();
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
		    `currency_code` varchar(3) NOT NULL DEFAULT 'USD',
		    `bank_name` VARCHAR(191) DEFAULT NULL,
		    `bank_phone` VARCHAR(20) DEFAULT NULL,
		    `bank_address` VARCHAR(191) DEFAULT NULL,
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

			"CREATE TABLE {$wpdb->prefix}ea_categories(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  		  	`name` VARCHAR(191) NOT NULL,
		  	`type` VARCHAR(50) NOT NULL,
		  	`description` TEXT NULL,
		  	`color` VARCHAR(20) NOT NULL,
		  	`status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
		   	`updated_at` DATETIME NULL DEFAULT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `type` (`type`),
		    KEY `status` (`status`),
		    UNIQUE KEY (`name`, `type`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_contacts(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `user_id` INT(11) DEFAULT NULL,
			`name` VARCHAR(191) NOT NULL,
			`company` VARCHAR(191) NOT NULL,
			`email` VARCHAR(191) DEFAULT NULL,
			`phone` VARCHAR(50) DEFAULT NULL,
			`website` VARCHAR(191) DEFAULT NULL,
			`birth_date` date DEFAULT NULL,
			`vat_number` VARCHAR(50) DEFAULT NULL,
			`street` VARCHAR(191) DEFAULT NULL,
			`city` VARCHAR(191) DEFAULT NULL,
			`state` VARCHAR(191) DEFAULT NULL,
			`postcode` VARCHAR(20) DEFAULT NULL,
			`country` VARCHAR(3) DEFAULT NULL,
			`currency_code` varchar(3),
  			`type` VARCHAR(100) DEFAULT NULL COMMENT 'Customer or vendor',
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

			"CREATE TABLE {$wpdb->prefix}ea_contactmeta(
			`meta_id` bigINT(20) NOT NULL AUTO_INCREMENT,
			`contact_id` bigint(20) unsigned NOT NULL default '0',
			`meta_key` varchar(255) default NULL,
			`meta_value` longtext,
			 PRIMARY KEY (`meta_id`),
		    KEY `contact_id`(`contact_id`),
			KEY `meta_key` (meta_key($index_length))
			) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_currencies(
			`id` bigINT(20) NOT NULL AUTO_INCREMENT,
		    `code` VARCHAR(191) NOT NULL COMMENT 'Currency Code',
		    `name` VARCHAR(191) NOT NULL COMMENT 'Currency Name',
		    `rate` DOUBLE(15,4) NOT NULL DEFAULT '0.0000',
		    `precision` INT(2) NOT NULL DEFAULT 0,
		    `symbol` VARCHAR(5) NOT NULL COMMENT 'Currency Symbol',
		    `position` ENUM('before','after') NOT NULL DEFAULT 'before',
		    `thousand_sep` VARCHAR(5) NOT NULL DEFAULT ',',
		    `decimal_sep` VARCHAR(5) NOT NULL DEFAULT '.',
		    `status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
		  	`updated_at` DATETIME NULL DEFAULT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `rate` (`rate`),
		    KEY `status` (`status`),
		    UNIQUE KEY `code` (`code`)
    		) $collate;",

			"CREATE TABLE {$wpdb->prefix}ea_document_items(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`document_id` INT(11) DEFAULT NULL,
  			`item_id` INT(11) DEFAULT NULL,
  			`name` VARCHAR(191) NOT NULL,
  			`price` double(15,4) NOT NULL,
  			`quantity` double(7,2) NOT NULL DEFAULT 0.00,
  			`subtotal` double(15,4) NOT NULL DEFAULT 0.00,
  			`tax_rate` double(15,4) NOT NULL DEFAULT 0.00,
  			`discount` double(15,4) NOT NULL DEFAULT 0.00,
  			`tax` double(15,4) NOT NULL DEFAULT 0.00,
  			`total` double(15,4) NOT NULL DEFAULT 0.00,
  			`currency_code` varchar(3) NOT NULL DEFAULT 'USD',
  			`extra` longtext DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `document_id` (`document_id`),
		    KEY `item_id` (`item_id`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_documents(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `type` VARCHAR(60) NOT NULL,
            `document_number` VARCHAR(100) NOT NULL,
            `order_number` VARCHAR(100) DEFAULT NULL,
            `status` VARCHAR(100) DEFAULT NULL,
            `issue_date` DATETIME NULL DEFAULT NULL,
            `due_date` DATETIME NULL DEFAULT NULL,
            `payment_date` DATETIME NULL DEFAULT NULL,
            `category_id` INT(11) NOT NULL,
  			`contact_id` INT(11) NOT NULL,
  			`address` longtext DEFAULT NULL,
            `discount` DOUBLE(15,4) DEFAULT 0,
            `discount_type`  ENUM('percentage', 'fixed') DEFAULT 'percentage',
            `subtotal` DOUBLE(15,4) DEFAULT 0,
            `total_tax` DOUBLE(15,4) DEFAULT 0,
            `total_discount` DOUBLE(15,4) DEFAULT 0,
            `total_fees` DOUBLE(15,4) DEFAULT 0,
            `total_shipping` DOUBLE(15,4) DEFAULT 0,
            `total` DOUBLE(15,4) DEFAULT 0,
            `tax_inclusive` tinyINT(1) NOT NULL DEFAULT '0',
  			`note` TEXT DEFAULT NULL,
  			`terms` TEXT DEFAULT NULL,
			`attachment_id` INT(11) DEFAULT NULL,
		  	`currency_code` varchar(3) NOT NULL DEFAULT 'USD',
		  	`currency_rate` double(15,8) NOT NULL DEFAULT 1,
  			`token` VARCHAR(100) DEFAULT NULL,
  			`parent_id` INT(11) DEFAULT NULL,
  			`creator_id` INT(11) DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `type` (`type`),
		    KEY `status` (`status`),
		    KEY `issue_date` (`issue_date`),
		    KEY `contact_id` (`contact_id`),
		    KEY `category_id` (`category_id`),
		    KEY `total` (`total`),
		    KEY `currency_code` (`currency_code`),
		    KEY `currency_rate` (`currency_rate`),
		    UNIQUE KEY (`document_number`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_documentmeta(
			`meta_id` bigINT(20) NOT NULL AUTO_INCREMENT,
			`document_id` bigint(20) unsigned NOT NULL default '0',
			`meta_key` varchar(255) default NULL,
			`meta_value` longtext,
			 PRIMARY KEY (`meta_id`),
		    KEY `document_id`(`document_id`),
			KEY `meta_key` (meta_key($index_length))
			) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_items(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `type` VARCHAR(50) NOT NULL COMMENT 'Item Type',
            `name` VARCHAR(191) NOT NULL,
  			`sku` VARCHAR(100) NULL default '',
			`description` TEXT DEFAULT NULL ,
  			`price` double(15,4) NOT NULL,
  			`unit` VARCHAR(50) DEFAULT NULL,
  			`quantity` int(11) NOT NULL DEFAULT '1',
  			`category_id` int(11) DEFAULT NULL,
  			`sales_tax` double(15,4) DEFAULT NULL,
  			`purchase_tax` double(15,4) DEFAULT NULL,
			`status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
			`creator_id` INT(11) DEFAULT NULL,
		  	`updated_at` DATETIME NULL DEFAULT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `type` (`type`),
		    KEY `price` (`price`),
		    KEY `unit` (`unit`),
		    KEY `category_id` (`category_id`),
		    KEY `quantity` (`quantity`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_media(
    		`id` bigINT(20) NOT NULL AUTO_INCREMENT,
			`name` VARCHAR(191) NOT NULL,
			`path` VARCHAR(191) NOT NULL,
			`type` VARCHAR(50) NOT NULL,
  			`size` int(11) NOT NULL DEFAULT '0',
  			`description` TEXT DEFAULT NULL ,
  			`ext` VARCHAR(50) NOT NULL,
  			`mime` VARCHAR(50) NOT NULL,
  			`creator_id` INT(11) DEFAULT NULL,
		  	`updated_at` DATETIME NULL DEFAULT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `type` (`type`),
		    KEY `name` (`name`),
		    KEY `path` (`path`),
		    KEY `ext` (`ext`),
		    KEY `mime` (`mime`)
			) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_notes(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`parent_id` INT(11) NOT NULL,
  			`type` VARCHAR(20) NOT NULL,
  			`note` TEXT DEFAULT NULL,
  			`extra` longtext DEFAULT NULL,
  			`creator_id` INT(11) DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `parent_id` (`parent_id`),
		    KEY `type` (`type`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_transactions(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `type` VARCHAR(100) DEFAULT NULL,
            `transaction_number` VARCHAR(100) DEFAULT NULL,
		  	`payment_date` date NOT NULL,
		  	`amount` DOUBLE(15,4) NOT NULL,
		  	`currency_code` varchar(3) NOT NULL DEFAULT 'USD',
		  	`currency_rate` double(15,8) NOT NULL DEFAULT 1,
            `account_id` INT(11) NOT NULL,
            `document_id` INT(11) DEFAULT NULL,
		  	`contact_id` INT(11) DEFAULT NULL,
		  	`category_id` INT(11) NOT NULL,
		  	`note` text DEFAULT NULL,
	  		`payment_method` VARCHAR(100) DEFAULT NULL,
		  	`reference` VARCHAR(191) DEFAULT NULL,
			`attachment_id` INT(11) DEFAULT NULL,
		  	`parent_id` INT(11) DEFAULT NULL,
		    `reconciled` tinyINT(1) NOT NULL DEFAULT '0',
		    `token` VARCHAR(100) DEFAULT NULL,
		    `creator_id` INT(11) DEFAULT NULL,
		    `updated_at` DATETIME NULL DEFAULT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `prefix` (`prefix`),
		    KEY `number` (`number`),
		    KEY `amount` (`amount`),
		    KEY `currency_code` (`currency_code`),
		    KEY `currency_rate` (`currency_rate`),
		    KEY `type` (`type`),
		    KEY `account_id` (`account_id`),
		    KEY `document_id` (`document_id`),
		    KEY `category_id` (`category_id`),
		    KEY `token` (`token`),
		    KEY `contact_id` (`contact_id`),
		    UNIQUE KEY `transaction_number` (`transaction_number`)
            ) $collate",
			"CREATE TABLE {$wpdb->prefix}ea_transactionmeta(
    		`meta_id` bigINT(20) NOT NULL AUTO_INCREMENT,
    		`transaction_id` bigint(20) unsigned NOT NULL default '0',
    		`meta_key` varchar(255) default NULL,
    		`meta_value` longtext,
    		PRIMARY KEY (`meta_id`),
    		KEY `transaction_id`(`transaction_id`),
    		KEY `meta_key` (meta_key($index_length))
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_transfers(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`payment_id` INT(11) NOT NULL,
  			`expense_id` INT(11) NOT NULL,
  			`creator_id` INT(11) DEFAULT NULL,
		    `updated_at` DATETIME NULL DEFAULT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `payment_id` (`payment_id`),
		    KEY `expense_id` (`expense_id`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_relationships(
			`id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`id1` INT(11) NOT NULL,
  			`id2` INT(11) NOT NULL,
  			`type1` VARCHAR(100) NOT NULL,
  			`type2` VARCHAR(100) NOT NULL,
  			`order` INT(11) NOT NULL DEFAULT 0,
		    `updated_at` DATETIME NULL DEFAULT NULL,
		    `created_at` DATETIME NULL DEFAULT NULL,
		    PRIMARY KEY (`id`),
		    KEY `id1` (`id1`),
		    KEY `id2` (`id2`),
		    KEY `type1` (`type1`),
		    KEY `type2` (`type2`),
		    UNIQUE KEY id1_id2_type1_type2 (`id1`,`id2`,`type1`,`type2`)
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
		global $wpdb;
		$default_currency      = eac_get_default_currency();
		$currencies            = include ever_accounting()->get_path( 'i18n/currencies.php' );
		$default_currency_rate = (float) ( isset( $currencies[ $default_currency ]['rate'] ) ? $currencies[ $default_currency ]['rate'] : 1 );
		// This is the part of migration, we use to save the currencies in options table before.
		if ( ! empty( get_option( 'eaccounting_currencies' ) ) ) {
			foreach ( get_option( 'eaccounting_currencies', array() ) as $currency ) {
				$code = $currency['code'];
				if ( empty( $code ) ) {
					continue;
				}
				$data = array(
					'code'         => $currency['code'],
					'name'         => $currency['name'],
					'rate'         => $currency['rate'],
					'precision'    => $currency['precision'],
					'symbol'       => $currency['symbol'],
					'position'     => $currency['position'],
					'thousand_sep' => $currency['thousand_separator'],
					'decimal_sep'  => $currency['decimal_separator'],
					'status'       => 'active',
				);
				if ( isset( $currencies[ $code ] ) ) {
					$currencies[ $code ] = wp_parse_args( array_filter( $data ), $currencies[ $code ] );
				}
			}

			// rename the option.
			delete_option( 'eaccounting_currencies' );
		}
		$db_currencies = $wpdb->get_col( "SELECT code FROM {$wpdb->prefix}ea_currencies" );

		foreach ( $db_currencies as $db_currency ) {
			if ( isset( $currencies[ $db_currency ] ) ) {
				unset( $currencies[ $db_currency ] );
			}
		}
		foreach ( $currencies as $code => $currency ) {
			if ( $code === $default_currency ) {
				$status = 'active';
			} elseif ( ! empty( $currency['status'] ) ) {
				$status = $currency['status'];
			} else {
				$status = 'inactive';
			}
			$data = array(
				'code'         => $code,
				'name'         => $currency['name'],
				'rate'         => $code === $default_currency ? 1 : $currency['rate'],
				'precision'    => $currency['precision'],
				'symbol'       => $currency['symbol'],
				'position'     => $currency['position'],
				'thousand_sep' => $currency['thousand_sep'],
				'decimal_sep'  => $currency['decimal_sep'],
				'status'       => $status,
				'created_at'   => current_time( 'mysql' ),
			);

			// Default currency is not USD then we have to change the conversion rate as we have conversion rate based on USD.
			if ( 'USD' !== $default_currency && $code !== $default_currency ) {
				// Old conversion rate is based on USD. So we have to convert it to the default currency.
				$data['rate'] = $data['rate'] / $default_currency_rate;
			}

			$wpdb->insert( "{$wpdb->prefix}ea_currencies", $data );
		}
	}

	/**
	 * Create default accounts.
	 *
	 * @since 1.1.6
	 * @returns void
	 */
	public static function create_accounts() {
		$default_currency = eac_get_default_currency();
		global $wpdb;
		$accounts = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}ea_accounts" );
		if ( empty( $accounts ) ) {
			// Create few dummy accounts.
			$accounts = array(
				array(
					'name'       => __( 'Cash', 'ever-accounting' ),
					'number'     => '1000',
					'currency'   => $default_currency,
					'balance'    => 0,
					'status'     => 'active',
					'created_at' => current_time( 'mysql' ),
				),
				array(
					'name'       => __( 'Bank', 'ever-accounting' ),
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
	public static function create_categories() {
		global $wpdb;
		$categories = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}ea_categories" );
		$transfer   = esc_html__( 'Transfer', 'wp-ever-accounting' );
		if ( empty( $categories ) ) {
			$categories = array(
				array(
					'name'       => __( 'Deposit', 'ever-accounting' ),
					'type'       => 'payment',
					'status'     => 'active',
					'created_at' => current_time( 'mysql' ),
				),
				array(
					'name'       => __( 'Sales', 'ever-accounting' ),
					'type'       => 'payment',
					'status'     => 'active',
					'created_at' => current_time( 'mysql' ),
				),
				array(
					'name'       => __( 'Other', 'ever-accounting' ),
					'type'       => 'payment',
					'status'     => 'active',
					'created_at' => current_time( 'mysql' ),
				),
				array(
					'name'       => __( 'Withdrawal', 'ever-accounting' ),
					'type'       => 'expense',
					'status'     => 'active',
					'created_at' => current_time( 'mysql' ),
				),
				array(
					'name'       => __( 'Purchase', 'ever-accounting' ),
					'type'       => 'expense',
					'status'     => 'active',
					'created_at' => current_time( 'mysql' ),
				),
				array(
					'name'       => __( 'Other', 'ever-accounting' ),
					'type'       => 'expense',
					'status'     => 'active',
					'created_at' => current_time( 'mysql' ),
				),
			);
			foreach ( $categories as $category ) {
				Models\Category::insert( $category );
			}
		}
		if ( empty( Models\Category::get( $transfer, 'name' ) ) ) {
			Models\Category::insert(
				array(
					'name'       => $transfer,
					'type'       => 'other',
					'status'     => 'active',
					'created_at' => current_time( 'mysql' ),
				)
			);
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
	 * in a single site or multi site environment.
	 *
	 * @return array EverAccounting tables.
	 */
	public static function get_tables() {
		global $wpdb;

		$tables = array(
			"{$wpdb->prefix}ea_accounts",
			"{$wpdb->prefix}ea_categories",
			"{$wpdb->prefix}ea_contacts",
			"{$wpdb->prefix}ea_contactmeta",
			"{$wpdb->prefix}ea_transactions",
			"{$wpdb->prefix}ea_transactionmeta",
			"{$wpdb->prefix}ea_transfers",
			"{$wpdb->prefix}ea_documents",
			"{$wpdb->prefix}ea_document_items",
			"{$wpdb->prefix}ea_documentmeta",
			"{$wpdb->prefix}ea_notes",
			"{$wpdb->prefix}ea_items",
		);

		$tables = apply_filters( 'ever_accounting_tables', $tables );

		return $tables;
	}
}
