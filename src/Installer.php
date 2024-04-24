<?php

namespace EverAccounting;

use EverAccounting\Utilities\I18n;

defined( 'ABSPATH' ) || exit;

/**
 * Class Installer.
 *
 * @since   1.0.0
 * @package WooCommerceKeyManager
 */
class Installer {
	/**
	 * Update callbacks.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $updates = array(
		'1.2.1.4' => array(
			'eac_update_1214',
			'eac_update_1214_another',
		),
		'1.2.1.5' => array(
			'eac_update_1215',
			'eac_update_1215_another',
		),
		'1.2.1.7' => 'eac_update_1217'
	);

	/**
	 * Construct and initialize the plugin aware trait.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'check_update' ), 5 );
		add_action( 'eac_run_update_callback', array( $this, 'run_update_callback' ), 10, 2 );
		add_action( 'eac_update_db_version', array( $this, 'update_db_version' ) );
	}

	/**
	 * Check the plugin version and run the updater if necessary.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 *
	 * @since 1.2.1
	 * @return void
	 */
	public function check_update() {
		$db_version      = EAC()->get_db_version();
		$current_version = EAC()->get_version();
		$requires_update = version_compare( $db_version, $current_version, '<' );
		$can_install     = ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) && ! defined( 'IFRAME_REQUEST' );
		if ( $can_install && $requires_update && ! EAC()->queue()->get_next( 'eac_run_update_callback' ) ) {
			error_log( 'Updating Ever Accounting' );
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
	 * @since 1.0.0
	 * @return void
	 */
	public function update() {
		$db_version = EAC()->get_db_version();
		$loop       = 0;
		foreach ( $this->updates as $version => $callbacks ) {
			$callbacks = (array) $callbacks;
			if ( version_compare( $db_version, $version, '<' ) ) {
				foreach ( $callbacks as $callback ) {
					EAC()->queue()->schedule_single(
						time() + $loop,
						'eac_run_update_callback',
						array( 'callback' => $callback, 'version' => $version )
					);

					error_log( sprintf( 'Scheduling update callback %s of version %s', $callback, $version ) );

					$loop ++;
				}
			}
			$loop ++;
		}

		if ( version_compare( EAC()->get_db_version(), EAC()->get_version(), '<' ) &&
		     ! EAC()->queue()->get_next( 'eac_update_db_version' ) ) {
			EAC()->queue()->schedule_single(
				time() + $loop,
				'eac_update_db_version',
				array(
					'version' => EAC()->get_version(),
				)
			);
			error_log( "Finally scheduling db udpate" );
		}
	}

	/**
	 * Run the update callback.
	 *
	 * @param string $callback The callback to run.
	 * @param string $version The version of the callback.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function run_update_callback( $callback, $version ) {
		require_once __DIR__ . '/Functions/updates.php';
		if ( is_callable( $callback ) ) {
			$result = (bool) call_user_func( $callback );
			if ( $result ) {
				EAC()->queue()->add(
					'eac_run_update_callback',
					array(
						'callback' => $callback,
						'version'  => $version,
					)
				);
			}
		}
	}

	/**
	 * Update the plugin version.
	 *
	 * @param string $version The version to update to.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function update_db_version( $version ) {
		error_log( "Updating db version to $version" );
		EAC()->update_db_version( $version );
	}

	/**
	 * Install the plugin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}
		self::create_tables();
		self::create_currencies();
		EAC()->add_db_version();
	}

	/**
	 * Create tables.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function create_tables() {
		global $wpdb;
		$collate      = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';
		$index_length = 191;

		// drop old ea_currencies table if exists.
		$table_name = $wpdb->prefix . 'ea_currencies';
		// if the table does not have exchange_rate column, then drop the table and create it again.
		if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'exchange_rate'" ) != 'exchange_rate' ) {
			$wpdb->query( "DROP TABLE $table_name" );
		}


		$tables = "
CREATE TABLE {$wpdb->prefix}ea_accounts(
`id` bigINT(20) NOT NULL AUTO_INCREMENT,
`type` VARCHAR(50) NOT NULL,
`name` VARCHAR(191) NOT NULL,
`number` VARCHAR(100) NOT NULL,
`opening_balance` DOUBLE(15,4) NOT NULL DEFAULT '0.0000',
`bank_name` VARCHAR(191) DEFAULT NULL,
`bank_phone` VARCHAR(20) DEFAULT NULL,
`bank_address` VARCHAR(191) DEFAULT NULL,
`currency_code` varchar(3) NOT NULL DEFAULT 'USD',
`author_id` BIGINT(20) UNSIGNED DEFAULT NULL,
`thumbnail_id` BIGINT(20) UNSIGNED DEFAULT NULL,
`status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
`uuid` VARCHAR(36) NOT NULL,
`date_updated` DATETIME NULL DEFAULT NULL,
`date_created` DATETIME NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `name` (`name`),
KEY `type` (`type`),
UNIQUE KEY (`number`),
UNIQUE KEY (`uuid`),
KEY `currency_code` (`currency_code`),
KEY `status` (`status`)
) $collate;
CREATE TABLE {$wpdb->prefix}ea_categories(
`id` bigINT(20) NOT NULL AUTO_INCREMENT,
`type` VARCHAR(50) NOT NULL,
`name` VARCHAR(191) NOT NULL,
`description` TEXT NULL,
`status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
`date_updated` DATETIME NULL DEFAULT NULL,
`date_created` DATETIME NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `name` (`name`),
KEY `type` (`type`),
KEY `status` (`status`),
UNIQUE KEY (`name`, `type`)
) $collate;
CREATE TABLE {$wpdb->prefix}ea_currencies (
`id` bigINT(20) NOT NULL AUTO_INCREMENT,
`code` VARCHAR(191) NOT NULL,
`name` VARCHAR(191) NOT NULL,
`exchange_rate` DOUBLE(15,4) NOT NULL DEFAULT '1.0000',
`precision` INT(2) NOT NULL DEFAULT 0,
`symbol` VARCHAR(5) NOT NULL,
`subunit` INT(3) NOT NULL DEFAULT 100,
`position` ENUM('before','after') NOT NULL DEFAULT 'before',
`thousand_separator` VARCHAR(5) NOT NULL DEFAULT ',',
`decimal_separator` VARCHAR(5) NOT NULL DEFAULT '.',
`status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
`date_updated` DATETIME NULL DEFAULT NULL,
`date_created` DATETIME NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `name` (`name`),
UNIQUE KEY `code` (`code`),
KEY `exchange_rate` (`exchange_rate`),
KEY `status` (`status`)
) $collate;
CREATE TABLE {$wpdb->prefix}ea_contactmeta(
`meta_id` bigINT(20) NOT NULL AUTO_INCREMENT,
`ea_contact_id` bigint(20) unsigned NOT NULL default '0',
`meta_key` varchar(255) default NULL,
`meta_value` longtext,
 PRIMARY KEY (`meta_id`),
KEY `ea_contact_id`(`ea_contact_id`),
KEY `meta_key` (meta_key($index_length))
) $collate;
CREATE TABLE {$wpdb->prefix}ea_contacts(
`id` bigINT(20) NOT NULL AUTO_INCREMENT,
`type` VARCHAR(30) DEFAULT NULL default 'customer',
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
`vat_exempt` TINYINT(1) NOT NULL DEFAULT '0',
`currency_code` varchar(3) NOT NULL DEFAULT 'USD',
`thumbnail_id` BIGINT(20) UNSIGNED DEFAULT NULL,
`user_id` BIGINT(20) UNSIGNED DEFAULT NULL,
`status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
`created_via` VARCHAR(100) DEFAULT 'manual',
`author_id` BIGINT(20) UNSIGNED DEFAULT NULL,
`uuid` VARCHAR(36) DEFAULT NULL,
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
) $collate;
CREATE TABLE {$wpdb->prefix}ea_document_items(
`id` bigINT(20) NOT NULL AUTO_INCREMENT,
`type` VARCHAR(20) NOT NULL default 'standard',
`name` VARCHAR(191) NOT NULL,
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
) $collate;
CREATE TABLE {$wpdb->prefix}ea_document_item_taxes(
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
) $collate;
CREATE TABLE {$wpdb->prefix}ea_documentmeta(
`meta_id` bigINT(20) NOT NULL AUTO_INCREMENT,
`ea_document_id` bigint(20) unsigned NOT NULL default '0',
`meta_key` varchar(255) default NULL,
`meta_value` longtext,
 PRIMARY KEY (`meta_id`),
KEY `ea_document_id`(`ea_document_id`),
KEY `meta_key` (meta_key($index_length))
) $collate;
CREATE TABLE {$wpdb->prefix}ea_documents(
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
`created_via` VARCHAR(100) DEFAULT 'manual',
`author_id` BIGINT(20) UNSIGNED NOT NULL,
`uuid` VARCHAR(36) DEFAULT NULL,
`date_created` DATETIME NULL DEFAULT NULL,
`date_updated` DATETIME NULL DEFAULT NULL,
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
) $collate;
CREATE TABLE {$wpdb->prefix}ea_items(
`id` bigINT(20) NOT NULL AUTO_INCREMENT,
`type` VARCHAR(50) NOT NULL DEFAULT 'standard',
`name` VARCHAR(191) NOT NULL,
`description` TEXT DEFAULT NULL,
`unit` VARCHAR(50) DEFAULT NULL,
`price` double(15,4) NOT NULL,
`cost` double(15,4) NOT NULL,
`taxable` TINYINT(1) NOT NULL DEFAULT 0,
`tax_ids` VARCHAR(191) DEFAULT NULL,
`category_id` int(11) DEFAULT NULL,
`thumbnail_id` BIGINT(20) UNSIGNED DEFAULT NULL,
`status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
`date_created` DATETIME NULL DEFAULT NULL,
`date_updated` DATETIME NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `name` (`name`),
KEY `type` (`type`),
KEY `price` (`price`),
KEY `cost` (`cost`),
KEY `status` (`status`),
KEY `unit` (`unit`),
KEY `category_id` (`category_id`)
) $collate;
CREATE TABLE {$wpdb->prefix}ea_notes(
`id` bigINT(20) NOT NULL AUTO_INCREMENT,
`object_id`  BIGINT(20) UNSIGNED NOT NULL,
`object_type` VARCHAR(20) NOT NULL,
`content` TEXT DEFAULT NULL,
`note_metadata` longtext DEFAULT NULL,
`author_id` BIGINT(20) UNSIGNED DEFAULT NULL,
`date_created` DATETIME NULL DEFAULT NULL,
`date_updated` DATETIME NULL DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `object_id` (`object_id`),
KEY `object_type` (`object_type`)
) $collate;
CREATE TABLE {$wpdb->prefix}ea_taxes(
`id` bigINT(20) NOT NULL AUTO_INCREMENT,
`name` VARCHAR(191) NOT NULL,
`rate` double(15,4) NOT NULL,
`is_compound` TINYINT(1) NOT NULL DEFAULT 0,
`description` TEXT DEFAULT NULL ,
`status` ENUM('active','inactive') NOT NULL DEFAULT 'active',
`date_created` DATETIME NULL DEFAULT NULL,
`date_updated` DATETIME NULL DEFAULT NULL,
 PRIMARY KEY (`id`),
 KEY `name` (`name`),
 KEY `rate` (`rate`),
 KEY `is_compound` (`is_compound`),
 KEY `status` (`status`)
 ) $collate;
 CREATE TABLE {$wpdb->prefix}ea_transactionmeta(
`meta_id` bigINT(20) NOT NULL AUTO_INCREMENT,
`ea_transaction_id` bigint(20) unsigned NOT NULL default '0',
`meta_key` varchar(255) default NULL,
`meta_value` longtext,
PRIMARY KEY (`meta_id`),
KEY `ea_transaction_id`(`ea_transaction_id`),
KEY `meta_key` (meta_key($index_length))
) $collate;
CREATE TABLE {$wpdb->prefix}ea_transactions(
`type` VARCHAR(20) DEFAULT NULL,
`id` bigINT(20) NOT NULL AUTO_INCREMENT,
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
`created_via` VARCHAR(100) DEFAULT 'manual',
`author_id` BIGINT(20) UNSIGNED DEFAULT NULL,
`uuid` VARCHAR(36) DEFAULT NULL,
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
) $collate;
CREATE TABLE {$wpdb->prefix}ea_transfers(
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
) $collate;
";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $tables );
	}

	/**
	 * Create currencies.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function create_currencies() {
		$options = get_option( 'eaccounting_currencies', array() );
		if ( $options ) {
			foreach ( $options as $option ) {
				eac_insert_currency( $option );
			}
			delete_option( 'eaccounting_currencies' );
		}
		$currencies = eac_get_currencies( [ 'limit' => - 1 ] );
		$codes      = wp_list_pluck( $currencies, 'code' );
		foreach ( I18n::get_currencies() as $code => $currency ) {
			if ( ! in_array( $code, $codes, true ) ) {
				$currency['status'] = 'inactive';
				eac_insert_currency( $currency );
			}
		}
	}
}
