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
	 * Updates and callbacks that need to be run per version.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	private static $updates = array(
		'1.0.2' => 'eaccounting_update_1_0_2',
	);

	/**
	 * Initialize all hooks.
	 *
	 * @since 1.0.2
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
		add_filter( 'plugin_action_links_' . EACCOUNTING_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
		add_filter( 'wpmu_drop_tables', array( __CLASS__, 'wpmu_drop_tables' ) );
		add_filter( 'cron_schedules', array( __CLASS__, 'cron_schedules' ) );
	}

	/**
	 * Check EverAccounting version and run the updater is required.
	 * This check is done on all requests and runs if the versions do not match.
	 *
	 * @since 1.0.2
	 *
	 * @return void
	 */
	public static function check_version() {
		//todo remove on later version.
		if ( false == get_option( 'eaccounting_version' ) && ! empty( get_option( 'eaccounting_localisation' ) ) ) {
			update_option( 'eaccounting_version', '1.0.1.1' );
		}

		if ( version_compare( get_option( 'eaccounting_version' ), eaccounting()->get_version(), '<' ) ) {
			self::maybe_update();
			do_action( 'eaccounting_updated' );
		}
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @param mixed $links Plugin Action links.
	 *
	 * @return array
	 */
	public static function plugin_action_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=ea-settings' ) . '" aria-label="' . esc_attr__( 'View settings', 'wp-ever-accounting' ) . '">' . esc_html__( 'Settings', 'wp-ever-accounting' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param mixed $links Plugin Row Meta.
	 * @param mixed $file  Plugin Base file.
	 *
	 * @return array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( EACCOUNTING_BASENAME !== $file ) {
			return $links;
		}

		$row_meta = array(
			'docs' => '<a href="' . esc_url( apply_filters( 'eaccounting_docs_url', 'https://wpeveraccounting.com/docs/' ) ) . '" aria-label="' . esc_attr__( 'View documentation', 'wp-ever-accounting' ) . '">' . esc_html__( 'Docs', 'wp-ever-accounting' ) . '</a>',
		);

		return array_merge( $links, $row_meta );
	}


	/**
	 * Uninstall tables when MU blog is deleted.
	 *
	 * @param array $tables List of tables that will be deleted by WP.
	 *
	 * @return string[]
	 */
	public static function wpmu_drop_tables( $tables ) {
		return array_merge( $tables, self::get_tables() );
	}

	/**
	 * Add more cron schedules.
	 *
	 * @param array $schedules List of WP scheduled cron jobs.
	 *
	 * @return array
	 */
	public static function cron_schedules( $schedules ) {
		$schedules['monthly']     = array(
			'interval' => 2635200,
			'display'  => __( 'Monthly', 'wp-ever-accounting' ),
		);
		$schedules['fifteendays'] = array(
			'interval' => 1296000,
			'display'  => __( 'Every 15 Days', 'wp-ever-accounting' ),
		);

		return $schedules;
	}

	/**
	 * Install EverAccounting.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}

		//Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'eaccounting_installing' ) ) {
			return;
		}

		//If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'eaccounting_installing', 'yes', MINUTE_IN_SECONDS * 10 );
		eaccounting_maybe_define_constant( 'EACCOUNTING_INSTALLING', true );
		self::remove_admin_notices();
		self::create_tables();
		self::verify_base_tables();
		self::create_options();
		self::create_categories();
		self::create_currencies();
		self::create_accounts();
		self::create_defaults();
		self::create_roles();
		self::create_cron_jobs();
		self::maybe_enable_setup_wizard();

		eaccounting_protect_files( true );
		flush_rewrite_rules();
		delete_transient( 'eaccounting_installing' );
		do_action( 'eaccounting_installed' );
	}

	/**
	 * Check if all the base tables are present.
	 *
	 * @return array.
	 */
	public static function verify_base_tables() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		global $wpdb;
		$missing_tables = array();
		$tables         = self::get_tables();
		foreach ( $tables as $table ) {
			if ( ! $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) ) {
				$missing_tables[] = $table;
			}
		}
		if ( 0 < count( $missing_tables ) ) {
			\EverAccounting\Admin\Admin_Notices::add_notice( 'base_tables_missing' );
			update_option( 'eaccounting_schema_missing_tables', $missing_tables );
		} else {
			delete_option( 'eaccounting_schema_missing_tables' );
		}

		return $missing_tables;
	}

	/**
	 * Create default options.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	private static function create_options() {
		$settings = new \EverAccounting\Admin\Settings();
		if ( empty( $settings->get( 'financial_year_start' ) ) ) {
			$settings->set( array( 'financial_year_start' => '01-01' ) );
		}

		if ( empty( $settings->get( 'default_payment_method' ) ) ) {
			$settings->set( array( 'default_payment_method' => 'cash' ) );
		}

		$settings->set( array(), true );

		$installation_time = get_option( 'eaccounting_install_date' );
		if ( empty( $installation_time ) ) {
			update_option( 'eaccounting_install_date', current_time( 'timestamp' ) );
		}
	}

	/**
	 * Create categories.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	private static function create_categories() {
		//If no categories then create default categories
		if ( ! \EverAccounting\Query_Category::init()->count() ) {
			eaccounting_insert_category(
				array(
					'name'    => __( 'Deposit', 'wp-ever-accounting' ),
					'type'    => 'income',
					'enabled' => '1',
				)
			);

			eaccounting_insert_category(
				array(
					'name'    => __( 'Other', 'wp-ever-accounting' ),
					'type'    => 'expense',
					'enabled' => '1',
				)
			);

			eaccounting_insert_category(
				array(
					'name'    => __( 'Sales', 'wp-ever-accounting' ),
					'type'    => 'income',
					'enabled' => '1',
				)
			);
		}

		//create transfer category
		if ( ! \EverAccounting\Query_Category::init()->where(
			array(
				'name' => 'Transfer',
				'type' => 'other',
			)
		)->count() ) {
			eaccounting_insert_category(
				array(
					'name'    => __( 'Transfer', 'wp-ever-accounting' ),
					'type'    => 'other',
					'enabled' => '1',
				)
			);
		}
	}

	/**
	 * Create currencies.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	private static function create_currencies() {
		//create currencies
		if ( ! \EverAccounting\Query_Currency::init()->count() ) {
			eaccounting_insert_currency(
				array(
					'code' => 'USD',
					'rate' => '1',
				)
			);
			eaccounting_insert_currency(
				array(
					'code' => 'EUR',
					'rate' => '1.25',
				)
			);
			eaccounting_insert_currency(
				array(
					'code' => 'GBP',
					'rate' => '1.6',
				)
			);
			eaccounting_insert_currency(
				array(
					'code' => 'CAD',
					'rate' => '1.31',
				)
			);
			eaccounting_insert_currency(
				array(
					'code' => 'JPY',
					'rate' => '106.22',
				)
			);
			eaccounting_insert_currency(
				array(
					'code' => 'BDT',
					'rate' => '84.81',
				)
			);
		}
	}

	/**
	 * Create accounts.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	private static function create_accounts() {
		if ( ! \EverAccounting\Query_Account::init()->count() ) {
			eaccounting_insert_account(
				array(
					'name'            => 'Cash',
					'currency_code'   => 'USD',
					'number'          => '001',
					'opening_balance' => '0',
					'enabled'         => '1',
				)
			);
		}
	}

	/**
	 * Create default data.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	private static function create_defaults() {
		$settings = new \EverAccounting\Admin\Settings();
		$account  = \EverAccounting\Query_Account::init()->find( 'Cash', 'name' );
		if ( ! empty( $account ) && empty( $settings->get( 'default_account' ) ) ) {
			$settings->set( array( 'default_account' => $account->id ) );
		}

		$currency = \EverAccounting\Query_Currency::init()->find( 'USD', 'code' );
		if ( ! empty( $currency ) && empty( $settings->get( 'default_currency' ) ) ) {
			$settings->set( array( 'default_currency' => $currency->code ) );
		}

		$settings->set( array(), true );
	}

	/**
	 * Reset any notices added to admin.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	private static function remove_admin_notices() {
		//include_once EACCOUNTING_ABSPATH . '/includes/admin/class-ea-admin-notices.php';
		//\EverAccounting\Admin\Admin_Notices::remove_all_notices();
	}


	/**
	 * Get Table schema.
	 *
	 * When adding or removing a table, make sure to update the list of tables in get_tables().
	 *
	 * @return void
	 */
	public static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$collate = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';

		$tables = array(
			"CREATE TABLE {$wpdb->prefix}ea_accounts(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
		    `name` VARCHAR(191) NOT NULL COMMENT 'Account Name',
		    `number` VARCHAR(191) NOT NULL COMMENT 'Account Number',
		    `opening_balance` DOUBLE(15,4) NOT NULL DEFAULT '0.0000',
		    `currency_code` varchar(3) NOT NULL DEFAULT 'USD',
		    `bank_name` VARCHAR(191) DEFAULT NULL,
		    `bank_phone` VARCHAR(20) DEFAULT NULL,
		    `bank_address` VARCHAR(191) DEFAULT NULL,
		   	`enabled` tinyint(1) NOT NULL DEFAULT '1',
		   	`creator_id` INT(11) DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY (`currency_code`),
		    UNIQUE KEY (`number`),
		    UNIQUE KEY (`name`, `number`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_categories(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  		  	`name` VARCHAR(191) NOT NULL,
		  	`type` VARCHAR(50) NOT NULL,
		  	`color` VARCHAR(20) NOT NULL,
		  	`enabled` tinyint(1) NOT NULL DEFAULT '1',
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `type` (`type`),
		    UNIQUE KEY (`name`, `type`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_currencies(
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
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_contacts(
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
			`attachment` TEXT DEFAULT NULL,
			`enabled` tinyint(1) NOT NULL DEFAULT '1',
			`creator_id` INT(11) DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `name`(`name`),
		    KEY `email`(`email`),
		    KEY `phone`(`phone`),
		    KEY `type`(`type`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_transactions(
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
			`attachment` TEXT DEFAULT NULL,
		  	`parent_id` INT(11) NOT NULL DEFAULT '0',
		    `reconciled` tinyINT(1) NOT NULL DEFAULT '0',
		    `creator_id` INT(11) DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `account_id` (`account_id`),
		    KEY `amount` (`amount`),
		    KEY `currency_code` (`currency_code`),
		    KEY `currency_rate` (`currency_rate`),
		    KEY `type` (`type`),
		    KEY `contact_id` (`contact_id`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_transfers(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`income_id` INT(11) NOT NULL,
  			`expense_id` INT(11) NOT NULL,
  			`creator_id` INT(11) DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `income_id` (`income_id`),
		    KEY `expense_id` (`expense_id`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_invoices(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `type` VARCHAR(191) NOT NULL,
            `invoice_number` VARCHAR(191) DEFAULT NULL,
            `order_number` VARCHAR(191) DEFAULT NULL,
            `status` VARCHAR(191) DEFAULT NULL,
            `invoiced_at` DATETIME NULL DEFAULT NULL,
            `due_at` DATETIME NULL DEFAULT NULL,
            `subtotal` DOUBLE(15,4) NOT NULL,
            `discount` DOUBLE(15,4) NOT NULL,
            `tax` DOUBLE(15,4) NOT NULL,
            `shipping` DOUBLE(15,4) NOT NULL,
            `total` DOUBLE(15,4) NOT NULL,
		  	`currency_code` varchar(3) NOT NULL DEFAULT 'USD',
		  	`currency_rate` double(15,8) NOT NULL DEFAULT 1,
  			`category_id` INT(11) NOT NULL,
  			`contact_id` INT(11) NOT NULL,
  			`contact_name` VARCHAR(191) DEFAULT NULL,
  			`contact_email` VARCHAR(191) DEFAULT NULL,
  			`contact_tax_number` VARCHAR(191) DEFAULT NULL,
  			`contact_phone` VARCHAR(191) DEFAULT NULL,
  			`contact_address` VARCHAR(191) DEFAULT NULL,
  			`note` TEXT DEFAULT NULL,
  			`footer` TEXT DEFAULT NULL,
  			`attachment` TEXT DEFAULT NULL,
  			`parent_id` INT(11) NOT NULL DEFAULT '0',
  			`creator_id` INT(11) DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `contact_id` (`contact_id`),
		    KEY `category_id` (`category_id`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_invoice_items(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`invoice_id` INT(11) NOT NULL,
  			`item_id` INT(11) NOT NULL,
  			`name` VARCHAR(191) NOT NULL,
  			`sku` VARCHAR(191) DEFAULT NULL,
  			`quantity` double(7,2) NOT NULL,
  			`price` double(15,4) NOT NULL,
  			`total` double(15,4) NOT NULL,
  			`tax_id` INT(11) NOT NULL,
  			`tax_name` VARCHAR(191) NOT NULL,
  			`tax` double(15,4) NOT NULL DEFAULT '0.0000',
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `invoice_id` (`invoice_id`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_invoice_histories(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`invoice_id` INT(11) NOT NULL,
  			`status` VARCHAR(191) DEFAULT NULL,
  			`notify` tinyint(1) NOT NULL,
  			`description` TEXT DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `invoice_id` (`invoice_id`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_items(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `name` varchar(191) NOT NULL,
  			`sku` varchar(100) NULL default '',
			`image_id` bigint(20) NULL default 0,
			`description` text COLLATE utf8mb4_unicode_ci,
  			`sale_price` double(15,4) NOT NULL,
  			`purchase_price` double(15,4) NOT NULL,
  			`quantity` int(11) NOT NULL DEFAULT '1',
  			`category_id` int(11) DEFAULT NULL,
  			`tax_id` int(11) DEFAULT NULL,
			`enabled` tinyint(1) NOT NULL DEFAULT '1',
			`creator_id` INT(11) DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `sale_price` (`sale_price`),
		    KEY `purchase_price` (`purchase_price`),
		    KEY `category_id` (`category_id`),
		    KEY `quantity` (`quantity`)
            ) $collate",
		);

		foreach ( $tables as $table ) {
			dbDelta( $table );
		}
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
			"{$wpdb->prefix}ea_currencies",
			"{$wpdb->prefix}ea_contacts",
			"{$wpdb->prefix}ea_transactions",
			"{$wpdb->prefix}ea_transfers",
		);

		$tables = apply_filters( 'eaccounting_install_get_tables', $tables );

		return $tables;
	}

	/**
	 * Drop EverAccounting tables.
	 *
	 * @return void
	 */
	public static function drop_tables() {
		global $wpdb;

		$tables = self::get_tables();

		foreach ( $tables as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
		}
	}

	/**
	 * Create roles and capabilities.
	 */
	public static function create_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		// Dummy gettext calls to get strings in the catalog.
		_x( 'Accounting Manager', 'User role', 'wp-ever-accounting' );
		_x( 'Accountant', 'User role', 'wp-ever-accounting' );

		// Accountant role.
		add_role(
			'ea_accountant',
			'Accountant',
			array(
				'manage_eaccounting' => true,
				'ea_manage_customer' => true,
				'ea_manage_vendor'   => true,
				'ea_manage_account'  => true,
				'ea_manage_payment'  => true,
				'ea_manage_revenue'  => true,
				'ea_manage_transfer' => true,
				'ea_manage_category' => true,
				'ea_manage_currency' => true,
				'read'               => true,
			)
		);

		// Shop manager role.
		add_role(
			'ea_manager',
			'Accounting Manager',
			array(
				'manage_eaccounting' => true,
				'ea_manage_report'   => true,
				'ea_manage_options'  => true,
				'ea_import'          => true,
				'ea_export'          => true,
				'ea_manage_customer' => true,
				'ea_manage_vendor'   => true,
				'ea_manage_account'  => true,
				'ea_manage_payment'  => true,
				'ea_manage_revenue'  => true,
				'ea_manage_transfer' => true,
				'ea_manage_category' => true,
				'ea_manage_currency' => true,
				'read'               => true,
			)
		);

		//add caps to admin
		global $wp_roles;

		if ( is_object( $wp_roles ) ) {
			$wp_roles->add_cap( 'administrator', 'manage_eaccounting' );
			$wp_roles->add_cap( 'administrator', 'ea_manage_report' );
			$wp_roles->add_cap( 'administrator', 'ea_manage_options' );
			$wp_roles->add_cap( 'administrator', 'ea_import' );
			$wp_roles->add_cap( 'administrator', 'ea_export' );
			$wp_roles->add_cap( 'administrator', 'ea_manage_customer' );
			$wp_roles->add_cap( 'administrator', 'ea_manage_vendor' );
			$wp_roles->add_cap( 'administrator', 'ea_manage_account' );
			$wp_roles->add_cap( 'administrator', 'ea_manage_payment' );
			$wp_roles->add_cap( 'administrator', 'ea_manage_revenue' );
			$wp_roles->add_cap( 'administrator', 'ea_manage_transfer' );
			$wp_roles->add_cap( 'administrator', 'ea_manage_category' );
			$wp_roles->add_cap( 'administrator', 'ea_manage_currency' );
		}
	}

	/**
	 * Remove EverAccounting roles.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function remove_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		remove_role( 'ea_accountant' );
		remove_role( 'ea_accountant' );
	}


	/**
	 * Create cron jobs (clear them first).
	 *
	 * @since 1.0.2
	 * @return void
	 */
	private static function create_cron_jobs() {
		wp_schedule_event( time() + ( 3 * HOUR_IN_SECONDS ), 'daily', 'eaccounting_cleanup_logs' );
	}

	/**
	 * See if we need the wizard or not.
	 *
	 * @since 1.0.2
	 */
	private static function maybe_enable_setup_wizard() {
		if ( apply_filters( 'eaccounting_enable_setup_wizard', true ) && self::is_new_install() ) {
			//\EverAccounting\Admin\Admin_Notices::add_notice( 'install', true );
			set_transient( '_eaccounting_activation_redirect', 1, 30 );
		}
	}

	/**
	 * Update version to current.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	private static function update_version() {
		delete_option( 'eaccounting_version' );
		add_option( 'eaccounting_version', EACCOUNTING_VERSION );
	}

	/**
	 * See if we need to show or run database updates during install.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	private static function maybe_update() {
		if ( self::needs_update() ) {
			self::update();
		} else {
			self::update_version();
		}
	}

	/**
	 * Is an update needed?
	 *
	 * @since  1.0.2
	 * @return boolean
	 */
	public static function needs_update() {
		$current_version = get_option( 'eaccounting_version', null );
		$updates         = self::$updates;
		$update_versions = array_keys( $updates );
		usort( $update_versions, 'version_compare' );

		return ! is_null( $current_version ) && version_compare( $current_version, end( $update_versions ), '<' );
	}

	/**
	 * Push all needed updates to the queue for processing.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	private static function update() {
		$current_version = get_option( 'eaccounting_version' );
		foreach ( self::$updates as $version => $update_callbacks ) {

			if ( version_compare( $current_version, $version, '<' ) ) {
				if ( is_array( $update_callbacks ) ) {
					array_map( array( __CLASS__, 'run_update_callback' ), $update_callbacks );
				} else {
					self::run_update_callback( $update_callbacks );
				}
				update_option( 'eaccounting_version', $version );
			}
		}
	}

	/**
	 * Run an update callback.
	 *
	 * @since 1.0.2
	 *
	 * @param string $callback Callback name.
	 */
	public static function run_update_callback( $callback ) {
		include_once EACCOUNTING_ABSPATH . '/includes/ea-update-functions.php';
		if ( is_callable( $callback ) ) {
			eaccounting_maybe_define_constant( 'EACCOUNTING_UPDATING', true );
			call_user_func( $callback );
		}
	}


	/**
	 * Is this a brand new install?
	 *
	 * A brand new install has no version yet. Also treat empty installs as 'new'.
	 *
	 * @since  1.0.2
	 * @return boolean
	 */
	public static function is_new_install() {
		$transaction_count = \EverAccounting\Query_Transaction::init()->count( 0 );

		return is_null( get_option( 'eaccounting_version', null ) ) || ( 0 === $transaction_count );
	}

}

EAccounting_Install::init();
