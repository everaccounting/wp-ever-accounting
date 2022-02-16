<?php
/**
 * Class Lifecycle
 *
 * @since       1.1.3
 * @package     wp-ever-accounting
 */

namespace Ever_Accounting;

defined( 'ABSPATH' ) || exit();

/**
 * Lifecycle class.
 */
class Lifecycle {

	/**
	 * Updates and callbacks that need to be run per version.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	private static $updates = array(
		'1.0.2' => array( __CLASS__, 'update_1_0_2' ),
		'1.1.0' => array(
			array( __CLASS__, 'update_1_1_0' ),
			array( __CLASS__, 'update_1_1_0_attachment' ),
		),
		'1.1.4' => array(
			array( __CLASS__, 'update_1_1_4' ),
		),
	);

	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'maybe_install' ) );
		add_action( 'init', array( __CLASS__, 'maybe_update' ) );
		add_action( 'init', array( __CLASS__, 'maybe_add_notices' ) );
		add_action( 'init', array( __CLASS__, 'define_tables' ) );
		add_action( 'switch_blog', array( __CLASS__, 'define_tables' ) );
		add_filter( 'wpmu_drop_tables', array( __CLASS__, 'wpmu_drop_tables' ) );
		add_filter( 'cron_schedules', array( __CLASS__, 'cron_schedules' ) );
	}

	/**
	 * Check version and run the installer if necessary.
	 *
	 * @since  1.1.3
	 */
	public static function maybe_install() {
		if ( ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && ! defined( 'IFRAME_REQUEST' ) && EVER_ACCOUNTING_VERSION !== self::get_db_version() ) {
			self::install();
		}
	}

	/**
	 * Perform all the necessary upgrade routines.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function maybe_update() {
		$current_db_version = self::get_db_version();
		$needs_update       = ! empty( $current_db_version ) && version_compare( $current_db_version, EVER_ACCOUNTING_VERSION, '<' );
		if ( $needs_update ) {
			$installed_version = self::get_db_version();
			foreach ( self::$updates as $version => $update_callbacks ) {
				if ( version_compare( $installed_version, $version, '<' ) ) {

					if ( is_callable( $update_callbacks ) ) {
						$update_callbacks = [ $update_callbacks ];
					}

					foreach ( $update_callbacks as $update_callback ) {
						Queue::instance()->add( $update_callback );
					}
				}
			}
			Queue::instance()->save()->dispatch();
			self::update_db_version();
		}
	}

	/**
	 * Conditionally add admin notices.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	public static function maybe_add_notices() {
		Admin\Admin_Notices::add_notice(
			array(
				'id'          => 'welcome_notice',
				'type'        => 'notice-success',
				'message'     => sprintf(
				/* translators: Placeholders: %1$s - plugin name, %2$s - <a> tag, %3$s - </a> tag */
					__( 'Thanks for installing %1$s! To get started, take a minute to %2$sread the documentation%3$s :)', 'wp-ever-accounting' ),
					'<strong>' . esc_html( ever_accounting()->name ) . '</strong>',
					'<a href="https://pluginever.com/docs" target="_blank">',
					'</a>'
				),
				'dismissible' => true,
				'capability'  => 'manage_options',
				'display_on'  => array(),
			)
		);

		// Review notice after 1 day of installation.
		$date = (int) get_option( 'wp_ever_accounting_install_date', current_time( 'timestamp' ) );
		if ( $date + ( DAY_IN_SECONDS * 1 ) < current_time( 'timestamp' ) ) {
			Admin\Admin_Notices::add_notice(
				array(
					'id'          => 'reviews_url',
					'type'        => 'notice-success',
					'message'     => sprintf(
					/* translators: Placeholders: %1$s - plugin name, %2$s - <a> tag, %3$s - </a> tag */
						__( 'We hope you\'re enjoying %1$s! Could you please do us a BIG favor and give it a 5-star rating to help us spread the word and boost our motivation? %2$s Sure! You deserve it! %3$s', 'wp-ever-accounting' ),
						'<strong>' . esc_html( ever_accounting()->name ) . '</strong>',
						'<a href="https://pluginever.com/reviews" target="_blank" style="text-decoration: none;"><span class="dashicons dashicons-external" style="margin-top: -2px;"></span>',
						'</a>'
					),
					'dismissible' => true,
					'capability'  => 'manage_options',
					'display_on'  => array(),
				)
			);
		}
	}


	/**
	 * Register custom tables within $wpdb object.
	 */
	public static function define_tables() {
		global $wpdb;

		// List of tables without prefixes.
		$tables = array(
			'contactmeta' => 'ea_contactmeta',
		);

		foreach ( $tables as $name => $table ) {
			$wpdb->$name    = $wpdb->prefix . $table;
			$wpdb->tables[] = $table;
		}
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
		$schedules['monthly'] = array(
			'interval' => 2635200,
			'display'  => __( 'Monthly', 'wp-ever-accounting' ),
		);

		$schedules['fifteendays'] = array(
			'interval' => 1296000,
			'display'  => __( 'Every 15 Days', 'wp-ever-accounting' ),
		);

		$schedules['weekly'] = array(
			'interval' => 604800,
			'display'  => __( 'Once Weekly', 'wp-ever-accounting' ),
		);

		return $schedules;
	}

	/**
	 * Return a list of Ever_Accounting tables.
	 * Used to make sure all Ever_Accounting tables are dropped when uninstalling the plugin
	 * in a single site or multi site environment.
	 *
	 * @return array Ever_Accounting tables.
	 */
	public static function get_tables() {
		global $wpdb;

		$tables = array(
			"{$wpdb->prefix}ea_accounts",
			"{$wpdb->prefix}ea_categories",
			"{$wpdb->prefix}ea_currencies",
			"{$wpdb->prefix}ea_contacts",
			"{$wpdb->prefix}ea_contactmeta",
			"{$wpdb->prefix}ea_transactions",
			"{$wpdb->prefix}ea_transactionmeta",
			"{$wpdb->prefix}ea_transfers",
			"{$wpdb->prefix}ea_invoices",
			"{$wpdb->prefix}ea_invoice_items",
			"{$wpdb->prefix}ea_invoicemeta",
			"{$wpdb->prefix}ea_notes",
			"{$wpdb->prefix}ea_items",
		);

		return apply_filters( 'ever_accounting_tables', $tables );
	}

	/**
	 * Gets the currently installed plugin database version.
	 *
	 * @since 1.1.3
	 * @return string
	 */
	protected static function get_db_version() {
		return get_option( 'ever_accounting_version', null );
	}

	/**
	 * Update the installed plugin database version.
	 *
	 * @param string $version version to set
	 *
	 * @since 1.1.3
	 */
	protected static function update_db_version( $version = null ) {
		update_option( 'ever_accounting_version', is_null( $version ) ? EVER_ACCOUNTING_VERSION : $version );
	}

	/**
	 * Performs any install tasks.
	 *
	 * @since 1.0.0
	 */
	public static function install() {
		$legacy_db_version = get_option( 'eaccounting_version', null );
		if ( ! is_null( $legacy_db_version ) ) {
			self::update_db_version( $legacy_db_version );
			delete_option( 'eaccounting_version' );
		}

		if ( ! self::get_db_version() ) {
			self::update_db_version();
		}

		self::remove_admin_notices();
		self::create_tables();
		self::create_roles();
	}

	/**
	 * Reset any notices added to admin.
	 *
	 * @since 1.0.2
	 * @return void
	 */
	private static function remove_admin_notices() {
		update_option( 'ever_accounting_notices', array() );
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
		$max_index_length = 191;
		$collate          = $wpdb->get_charset_collate();

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
		    `thumbnail_id` INT(11) DEFAULT NULL,
		   	`enabled` tinyint(1) NOT NULL DEFAULT '1',
		   	`creator_id` INT(11) DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `currency_code` (`currency_code`),
		    KEY `enabled` (`enabled`),
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
		    KEY `enabled` (`enabled`),
		    UNIQUE KEY (`name`, `type`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_currencies(
    		`id` bigINT(20) NOT NULL AUTO_INCREMENT,
			`code` varchar(3) NOT NULL,
			`name` varchar(100) NOT NULL,
			`rate` double(15,8) NOT NULL,
			`number` varchar(100) DEFAULT NULL,
			`precision` varchar(2) DEFAULT NULL,
			`subunit` varchar(100) DEFAULT NULL,
  			`symbol` varchar(5) DEFAULT NULL,
  			`position` ENUM ('before', 'after') DEFAULT 'before',
  			`decimal_separator` varchar(1) DEFAULT '.',
 			`thousand_separator` varchar(1) DEFAULT ',',
			`enabled` tinyint(1) NOT NULL DEFAULT '1',
	   		`date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `code` (`code`),
		    KEY `rate` (`rate`),
		    UNIQUE KEY (`code`)
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
			`thumbnail_id` INT(11) DEFAULT NULL,
			`enabled` tinyint(1) NOT NULL DEFAULT '1',
			`creator_id` INT(11) DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `user_id`(`user_id`),
		    KEY `name`(`name`),
		    KEY `email`(`email`),
		    KEY `phone`(`phone`),
		    KEY `enabled`(`enabled`),
		    KEY `type`(`type`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_contactmeta(
			`meta_id` bigINT(20) NOT NULL AUTO_INCREMENT,
			`ea_contact_id` bigint(20) unsigned NOT NULL default '0',
			`meta_key` varchar(255) default NULL,
			`meta_value` longtext,
			 PRIMARY KEY (`meta_id`),
		    KEY `ea_contact_id`(`ea_contact_id`),
			KEY `meta_key` (meta_key($max_index_length))
			) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_transactions(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `type` VARCHAR(100) DEFAULT NULL,
		  	`payment_date` date NOT NULL,
		  	`amount` DOUBLE(15,4) NOT NULL,
		  	`currency_code` varchar(3) NOT NULL DEFAULT 'USD',
		  	`currency_rate` double(15,8) NOT NULL DEFAULT 1,
            `account_id` INT(11) NOT NULL,
            `document_id` INT(11) DEFAULT NULL,
		  	`contact_id` INT(11) DEFAULT NULL,
		  	`category_id` INT(11) NOT NULL,
		  	`description` text,
	  		`payment_method` VARCHAR(100) DEFAULT NULL,
		  	`reference` VARCHAR(191) DEFAULT NULL,
			`attachment_id` INT(11) DEFAULT NULL,
		  	`parent_id` INT(11) DEFAULT NULL,
		    `reconciled` tinyINT(1) NOT NULL DEFAULT '0',
		    `creator_id` INT(11) DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `amount` (`amount`),
		    KEY `currency_code` (`currency_code`),
		    KEY `currency_rate` (`currency_rate`),
		    KEY `type` (`type`),
		    KEY `account_id` (`account_id`),
		    KEY `document_id` (`document_id`),
		    KEY `category_id` (`category_id`),
		    KEY `contact_id` (`contact_id`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_transactionmeta(
			`meta_id` bigINT(20) NOT NULL AUTO_INCREMENT,
			`ea_transaction_id` bigint(20) unsigned NOT NULL default '0',
			`meta_key` varchar(255) default NULL,
			`meta_value` longtext,
			 PRIMARY KEY (`meta_id`),
		    KEY `ea_transaction_id`(`ea_transaction_id`),
			KEY `meta_key` (meta_key($max_index_length))
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
            `document_number` VARCHAR(100) NOT NULL,
            `type` VARCHAR(60) NOT NULL,
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
  			`key` VARCHAR(30) DEFAULT NULL,
  			`parent_id` INT(11) DEFAULT NULL,
  			`creator_id` INT(11) DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
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

			"CREATE TABLE {$wpdb->prefix}ea_invoice_items(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`document_id` INT(11) DEFAULT NULL,
  			`item_id` INT(11) DEFAULT NULL,
  			`item_name` VARCHAR(191) NOT NULL,
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

			"CREATE TABLE {$wpdb->prefix}ea_invoicemeta(
			`meta_id` bigINT(20) NOT NULL AUTO_INCREMENT,
			`ea_invoice_id` bigint(20) unsigned NOT NULL default '0',
			`meta_key` varchar(255) default NULL,
			`meta_value` longtext,
			 PRIMARY KEY (`meta_id`),
		    KEY `ea_invoice_id`(`ea_invoice_id`),
			KEY `meta_key` (meta_key($max_index_length))
			) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_notes(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
  			`parent_id` INT(11) NOT NULL,
  			`type` VARCHAR(20) NOT NULL,
  			`note` TEXT DEFAULT NULL,
  			`extra` longtext DEFAULT NULL,
  			`creator_id` INT(11) DEFAULT NULL,
		    `date_created` DATETIME NULL DEFAULT NULL COMMENT 'Create Date',
		    PRIMARY KEY (`id`),
		    KEY `parent_id` (`parent_id`),
		    KEY `type` (`type`)
            ) $collate",

			"CREATE TABLE {$wpdb->prefix}ea_items(
            `id` bigINT(20) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(191) NOT NULL,
  			`sku` VARCHAR(100) NULL default '',
			`description` TEXT DEFAULT NULL ,
  			`sale_price` double(15,4) NOT NULL,
  			`purchase_price` double(15,4) NOT NULL,
  			`quantity` int(11) NOT NULL DEFAULT '1',
  			`category_id` int(11) DEFAULT NULL,
  			`sales_tax` double(15,4) DEFAULT NULL,
  			`purchase_tax` double(15,4) DEFAULT NULL,
  			`thumbnail_id` INT(11) DEFAULT NULL,
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
	 * Create roles and capabilities.
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
				'ea_manage_item'     => true,
				'ea_manage_invoice'  => true,
				'ea_manage_bill'     => true,
				'read'               => true,
			)
		);

		// Accounting manager role.
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
				'ea_manage_item'     => true,
				'ea_manage_invoice'  => true,
				'ea_manage_bill'     => true,
				'read'               => true,
			)
		);

		// add caps to admin
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
			$wp_roles->add_cap( 'administrator', 'ea_manage_item' );
			$wp_roles->add_cap( 'administrator', 'ea_manage_invoice' );
			$wp_roles->add_cap( 'administrator', 'ea_manage_bill' );
		}
	}

	/**
	 * Create cron jobs (clear them first).
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function schedule_events() {
		wp_clear_scheduled_hook( 'ever_accounting_twicedaily_event' );
		wp_clear_scheduled_hook( 'ever_accounting_event' );
		wp_clear_scheduled_hook( 'ever_accounting_weekly_event' );

		wp_schedule_event( time() + ( 6 * HOUR_IN_SECONDS ), 'twicedaily', 'ever_accounting_twicedaily_event' );
		wp_schedule_event( time() + 10, 'daily', 'ever_accounting_event' );
		wp_schedule_event( time() + ( 3 * HOUR_IN_SECONDS ), 'weekly', 'ever_accounting_weekly_event' );
	}

	/**
	 * Remove plugin related options.
	 *
	 * @since 1.1.3
	 * @return void
	 */
	public static function uninstall() {
		global $wpdb;

		// Roles.
		self::remove_roles();

		// Tables.
		self::drop_tables();

		// Delete options.
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'ever_accounting\_%';" );

		// Clear any cached data that has been removed.
		wp_cache_flush();
	}

	/**
	 * Drop Ever_Accounting tables.
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
	 * Remove Ever_Accounting roles.
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
			$wp_roles = new \WP_Roles();
		}

		remove_role( 'ea_accountant' );
		remove_role( 'ea_manager' );
	}

	/**
	 * Update the plugin from older version to 1.0.2
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function update_1_0_2() {
		Lifecycle::create_tables();
		Lifecycle::create_roles();

		global $wpdb;
		$prefix          = $wpdb->prefix;
		$current_user_id = eaccounting_get_current_user_id();

		$settings = array();
		delete_option( 'eaccounting_settings' );
		$localization  = get_option( 'eaccounting_localisation', array() );
		$currency_code = array_key_exists( 'currency', $localization ) ? $localization['currency'] : 'USD';
		$currency_code = empty( $currency_code ) ? 'USD' : sanitize_text_field( $currency_code );

		$currency = eaccounting_insert_currency(
			array(
				'code' => $currency_code,
				'rate' => 1,
			)
		);

		$settings['financial_year_start']   = '01-01';
		$settings['default_payment_method'] = 'cash';

		if ( ! is_wp_error( $currency ) ) {
			$settings['default_currency'] = $currency->get_code();
		}

		update_option( 'eaccounting_settings', $settings );

		// transfers
		$wpdb->query( "ALTER TABLE {$prefix}ea_transfers DROP COLUMN `updated_at`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_transfers ADD `creator_id` INT(11) DEFAULT NULL AFTER `revenue_id`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_transfers CHANGE `payment_id` `expense_id` INT(11) NOT NULL;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_transfers CHANGE `revenue_id` `income_id` INT(11) NOT NULL;" );
		$wpdb->query( $wpdb->prepare( "UPDATE {$prefix}ea_transfers SET creator_id=%d", $current_user_id ) );
		$wpdb->query( "ALTER TABLE {$prefix}ea_transfers CHANGE `created_at` `date_created` DATETIME NULL DEFAULT NULL;" );

		$transfers = $wpdb->get_results( "SELECT * FROM {$prefix}ea_transfers" );
		foreach ( $transfers as $transfer ) {
			$revenue = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}ea_revenues where id=%d", $transfer->income_id ) );
			$expense = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}ea_payments where id=%d", $transfer->expense_id ) );

			$wpdb->insert(
				$prefix . 'ea_transactions',
				array(
					'type'           => 'income',
					'payment_date'   => $revenue->payment_date,
					'amount'         => $revenue->amount,
					'currency_code'  => $currency_code,
					'currency_rate'  => 1, // protected
					'account_id'     => $revenue->account_id,
					'invoice_id'     => null,
					'contact_id'     => null,
					'category_id'    => $revenue->category_id,
					'description'    => $revenue->description,
					'payment_method' => $revenue->payment_method,
					'reference'      => $revenue->reference,
					'attachment'     => $revenue->attachment_url,
					'parent_id'      => 0,
					'reconciled'     => 0,
					'creator_id'     => $current_user_id,
					'date_created'   => $revenue->created_at,
				)
			);

			$income_id = $wpdb->insert_id;

			$wpdb->insert(
				$prefix . 'ea_transactions',
				array(
					'type'           => 'expense',
					'payment_date'   => $expense->payment_date,
					'amount'         => $expense->amount,
					'currency_code'  => $currency_code,
					'currency_rate'  => 1, // protected
					'account_id'     => $expense->account_id,
					'invoice_id'     => null,
					'contact_id'     => null,
					'category_id'    => $expense->category_id,
					'description'    => $expense->description,
					'payment_method' => $expense->payment_method,
					'reference'      => $expense->reference,
					'attachment'     => $expense->attachment_url,
					'parent_id'      => 0,
					'reconciled'     => 0,
					'creator_id'     => $current_user_id,
					'date_created'   => $expense->created_at,
				)
			);

			$expense_id = $wpdb->insert_id;

			$wpdb->update(
				$prefix . 'ea_transfers',
				array(
					'income_id'  => $income_id,
					'expense_id' => $expense_id,
				),
				array( 'id' => $transfer->id )
			);

			$wpdb->delete(
				$prefix . 'ea_revenues',
				array( 'id' => $revenue->id )
			);

			$wpdb->delete(
				$prefix . 'ea_payments',
				array( 'id' => $expense->id )
			);
		}

		$revenues = $wpdb->get_results( "SELECT * FROM {$prefix}ea_revenues order by id asc" );
		foreach ( $revenues as $revenue ) {
			$wpdb->insert(
				$prefix . 'ea_transactions',
				array(
					'type'           => 'income',
					'payment_date'   => $revenue->payment_date,
					'amount'         => $revenue->amount,
					'currency_code'  => $currency_code,
					'currency_rate'  => 1, // protected
					'account_id'     => $revenue->account_id,
					'invoice_id'     => null,
					'contact_id'     => $revenue->contact_id,
					'category_id'    => $revenue->category_id,
					'description'    => $revenue->description,
					'payment_method' => $revenue->payment_method,
					'reference'      => $revenue->reference,
					'attachment'     => $revenue->attachment_url,
					'parent_id'      => 0,
					'reconciled'     => 0,
					'creator_id'     => $current_user_id,
					'date_created'   => $revenue->created_at,
				)
			);

			// $wpdb->delete(
			// $prefix . 'ea_revenues',
			// array( 'id' => $revenue->id )
			// );
		}

		// expenses
		$expenses = $wpdb->get_results( "SELECT * FROM {$prefix}ea_payments order by id asc" );
		foreach ( $expenses as $expense ) {
			$wpdb->insert(
				$prefix . 'ea_transactions',
				array(
					'type'           => 'expense',
					'payment_date'   => $expense->payment_date,
					'amount'         => $expense->amount,
					'currency_code'  => $currency_code,
					'currency_rate'  => 1, // protected
					'account_id'     => $expense->account_id,
					'invoice_id'     => null,
					'contact_id'     => $expense->contact_id,
					'category_id'    => $expense->category_id,
					'description'    => $expense->description,
					'payment_method' => $expense->payment_method,
					'reference'      => $expense->reference,
					'attachment'     => $expense->attachment_url,
					'parent_id'      => 0,
					'reconciled'     => 0,
					'creator_id'     => $current_user_id,
					'date_created'   => $expense->created_at,
				)
			);

			// $wpdb->delete(
			// $prefix . 'ea_payments',
			// array( 'id' => $expense->id )
			// );
		}

		// accounts
		$wpdb->query( "ALTER TABLE {$prefix}ea_accounts DROP COLUMN `updated_at`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_accounts ADD `currency_code` varchar(3) NOT NULL AFTER `opening_balance`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_accounts ADD `creator_id` INT(11) DEFAULT NULL AFTER `bank_address`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_accounts ADD `enabled` tinyint(1) NOT NULL DEFAULT '1' AFTER `bank_address`;" );
		$wpdb->query( $wpdb->prepare( "UPDATE {$prefix}ea_accounts SET creator_id=%d, currency_code=%s ", $current_user_id, $currency_code ) );
		$wpdb->update( "{$prefix}ea_accounts", array( 'enabled' => '1' ), array( 'status' => 'active' ) );
		$wpdb->update( "{$prefix}ea_accounts", array( 'enabled' => '0' ), array( 'status' => 'inactive' ) );
		$wpdb->query( "ALTER TABLE {$prefix}ea_accounts DROP COLUMN `status`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_accounts CHANGE `created_at` `date_created` DATETIME NULL DEFAULT NULL;" );

		// categories
		$wpdb->query( "ALTER TABLE {$prefix}ea_categories DROP COLUMN `updated_at`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_categories ADD `enabled` tinyint(1) NOT NULL DEFAULT '1' AFTER `color`;" );
		$wpdb->update( "{$prefix}ea_categories", array( 'enabled' => '1' ), array( 'status' => 'active' ) );
		$wpdb->update( "{$prefix}ea_categories", array( 'enabled' => '0' ), array( 'status' => 'inactive' ) );
		$wpdb->query( "ALTER TABLE {$prefix}ea_categories DROP COLUMN `status`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_categories CHANGE `created_at` `date_created` DATETIME NULL DEFAULT NULL;" );

		// contacts
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `name` VARCHAR(191) NOT NULL AFTER `user_id`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `fax` VARCHAR(50) DEFAULT NULL AFTER `phone`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `birth_date` date DEFAULT NULL AFTER `phone`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `type` VARCHAR(100) DEFAULT NULL AFTER `note`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `enabled` tinyint(1) NOT NULL DEFAULT '1' AFTER `note`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `creator_id` INT(11) DEFAULT NULL AFTER `note`;" );
		$contacts = $wpdb->get_results( "SELECT * FROM {$prefix}ea_contacts" );

		foreach ( $contacts as $contact ) {
			$types = maybe_unserialize( $contact->types );
			if ( count( $types ) == 1 ) {
				$type = reset( $types );
				$wpdb->update(
					$wpdb->prefix . 'ea_contacts',
					array(
						'type' => $type,
					),
					array( 'id' => $contact->id )
				);
			} else {
				$wpdb->update(
					$wpdb->prefix . 'ea_contacts',
					array(
						'type' => 'customer',
					),
					array( 'id' => $contact->id )
				);

				$data         = (array) $contact;
				$data['type'] = 'vendor';
				unset( $data['types'] );
				unset( $data['id'] );
				$wpdb->insert( $wpdb->prefix . 'ea_contacts', $data );
				if ( ! empty( $wpdb->insert_id ) ) {
					$vendor_id = $wpdb->insert_id;

					$wpdb->update(
						$wpdb->prefix . 'ea_transactions',
						array(
							'contact_id' => $vendor_id,
						),
						array(
							'contact_id' => $contact->id,
							'type'       => 'expense',
						)
					);
				}
			}
		}

		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `currency_code` varchar(3) NOT NULL AFTER `tax_number`;" );

		foreach ( $contacts as $contact ) {
			$name = implode( ' ', array( $contact->first_name, $contact->last_name ) );
			$wpdb->update(
				$wpdb->prefix . 'ea_contacts',
				array(
					'currency_code' => $currency_code,
					'enabled'       => $contact->status === 'active' ? 1 : 0,
					'name'          => $name,
					'creator_id'    => $current_user_id,
				),
				array( 'id' => $contact->id )
			);
		}
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `avatar_url`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `updated_at`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `city`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `state`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `postcode`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `status`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `types`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts CHANGE `created_at` `date_created` DATETIME NULL DEFAULT NULL;" );

		delete_option( 'eaccounting_localisation' );
	}

	/**
	 * Update the plugin from older version to 1.1.0
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public static function update_1_1_0(){
		global $wpdb;
		$prefix = $wpdb->prefix;

		//todo update attachment files
		$wpdb->query( "ALTER TABLE {$prefix}ea_accounts ADD `thumbnail_id` INT(11) DEFAULT NULL AFTER `bank_address`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_categories ADD INDEX enabled (`enabled`);" );

		//$wpdb->query( "ALTER TABLE {$prefix}ea_contacts CHANGE `attachment` `avatar_id` INT(11) DEFAULT NULL;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts CHANGE `tax_number` `vat_number` VARCHAR(50) DEFAULT NULL;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `fax`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `note`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `company` VARCHAR(191) NOT NULL AFTER `name`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `website` VARCHAR(191) NOT NULL AFTER `phone`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `street` VARCHAR(191) NOT NULL AFTER `vat_number`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `city` VARCHAR(191) NOT NULL AFTER `street`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `state` VARCHAR(191) NOT NULL AFTER `city`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `postcode` VARCHAR(191) NOT NULL AFTER `state`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `thumbnail_id` INT(11) DEFAULT NULL AFTER `type`;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD INDEX enabled (`enabled`);" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD INDEX user_id (`user_id`);" );

		$wpdb->query( "ALTER TABLE {$prefix}ea_transactions CHANGE `paid_at` `payment_date` date NOT NULL;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_transactions CHANGE `invoice_id` `document_id` INT(11) DEFAULT NULL;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_transactions CHANGE `parent_id` `parent_id` INT(11) DEFAULT NULL;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_transactions ADD `attachment_id` INT(11) DEFAULT NULL AFTER `reference`;" );
		//$wpdb->query( "ALTER TABLE {$prefix}ea_transactions CHANGE `attachment` `attachment_id` INT(11) DEFAULT NULL;" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_transactions ADD INDEX document_id (`document_id`);" );
		$wpdb->query( "ALTER TABLE {$prefix}ea_transactions ADD INDEX category_id (`category_id`);" );

		//update currency table to options
		$currencies = $wpdb->get_results( "SELECT * FROM {$prefix}ea_currencies order by id asc" );

		if ( is_array( $currencies ) && count( $currencies ) ) {
			foreach ( $currencies as $currency ) {
				eaccounting_insert_currency(
					array(
						'name'               => $currency->name,
						'code'               => $currency->code,
						'rate'               => $currency->rate,
						'precision'          => $currency->precision,
						'symbol'             => $currency->symbol,
						'position'           => $currency->position,
						'decimal_separator'  => $currency->decimal_separator,
						'thousand_separator' => $currency->thousand_separator,
						'date_created'       => $currency->date_created,
					)
				);
			}
		}

		//update permissions
		global $wp_roles;

		if ( is_object( $wp_roles ) ) {
			$wp_roles->add_cap( 'ea_manager', 'ea_manage_item' );
			$wp_roles->add_cap( 'ea_manager', 'ea_manage_invoice' );
			$wp_roles->add_cap( 'ea_manager', 'ea_manage_bill' );
			$wp_roles->add_cap( 'ea_accountant', 'ea_manage_item' );
			$wp_roles->add_cap( 'ea_accountant', 'ea_manage_invoice' );
			$wp_roles->add_cap( 'ea_accountant', 'ea_manage_bill' );
			$wp_roles->add_cap( 'administrator', 'ea_manage_item' );
			$wp_roles->add_cap( 'administrator', 'ea_manage_invoice' );
			$wp_roles->add_cap( 'administrator', 'ea_manage_bill' );
		}

		\Ever_Accounting\Lifecycle::install();

		//todo upload transaction files as attachment then update transaction table and delete attachment column
		flush_rewrite_rules();
	}

	/**
	 * Update attachments
	 *
	 * @since 1.1.0
	 */
	public static function update_1_1_0_attachment() {
		global $wpdb;
		$prefix      = $wpdb->prefix;
		$attachments = $wpdb->get_results( "SELECT id, attachment url from {$wpdb->prefix}ea_transactions WHERE attachment_id IS NULL AND attachment !='' limit 5" );
		if ( empty( $attachments ) ) {
			$wpdb->query( "ALTER TABLE {$prefix}ea_transactions DROP COLUMN `attachment`;" );
			return false;
		}

		$dir = wp_get_upload_dir();

		foreach ( $attachments as $attachment ) {
			$path       = $attachment->url;
			$site_url   = parse_url( $dir['url'] );
			$image_path = parse_url( $path );

			// Force the protocols to match if needed.
			if ( isset( $image_path['scheme'] ) && ( $image_path['scheme'] !== $site_url['scheme'] ) ) {
				$path = str_replace( $image_path['scheme'], $site_url['scheme'], $path );
			}

			if ( 0 === strpos( $path, $dir['baseurl'] . '/' ) ) {
				$path = substr( $path, strlen( $dir['baseurl'] . '/' ) );
			}

			$path      = str_replace( 'axis.byteever.com', 'axis.test', $path );
			$path      = str_replace( $dir['baseurl'], '', $path );
			$full_path = untrailingslashit( $dir['basedir'] ) . '/' . ltrim( $path, '/' );

			if ( ! file_exists( $full_path ) ) {
				continue;
			}
			$attachment_id = eaccounting_file_to_attachment( $full_path );
			if ( $attachment_id && is_numeric( $attachment_id ) ) {
				$wpdb->update( "{$wpdb->prefix}ea_transactions", array( 'attachment_id' => $attachment_id ), array( 'id' => $attachment->id ) );
			}
		}

		return true;
	}

	/**
	 * Update the plugin from older version to 1.1.3
	 *
	 * @since 1.0.2
	 * @return void
	 */
	public static function update_1_1_3(){
		global $wpdb;
		$wpdb->query( "UPDATE {$wpdb->options} SET option_name = REPLACE(option_name, 'eaccounting_', 'ever_accounting') WHERE option_name LIKE 'eaccounting_%'" );
		$wpdb->query( "ALTER TABLE {$wpdb->prefix}ea_contact_meta CHANGE `contact_id` `ea_contact_id` bigint(20) unsigned NOT NULL DEFAULT '0';" );
		$wpdb->query( "ALTER TABLE {$wpdb->prefix}ea_contact_meta ADD INDEX `ea_contact_id` (`ea_contact_id`);" );
		//drop old table
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ea_contact_meta;" );

		$currencies = get_option( 'eaccounting_currencies' );
		foreach ( $currencies as $currency ) {
			unset( $currency['id'] );
			Currencies::insert_currency( $currency );
		}
	}
}

return new Lifecycle();
