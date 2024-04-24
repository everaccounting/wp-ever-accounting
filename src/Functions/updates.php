<?php
/**
 * Updates functions.
 *
 * @since 1.0.0
 * @package EverAccounting\Functions
 */

use EverAccounting\Utilities\I18n;

defined( 'ABSPATH' ) || exit;

function eac_update_121() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'ea_accounts';
	$wpdb->query( "ALTER TABLE $table_name ADD `type` VARCHAR(50) NOT NULL DEFAULT 'bank' AFTER `id`" );
	$wpdb->query( "ALTER TABLE $table_name ADD KEY `type` (`type`)" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `currency_code` `currency_code` varchar(3) NOT NULL DEFAULT 'USD' AFTER `bank_address`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `creator_id` `author_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'" );
	$wpdb->query( "ALTER TABLE $table_name ADD `date_updated` DATETIME NULL DEFAULT NULL AFTER `date_created`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `uuid` VARCHAR(36) NOT NULL AFTER `author_id`" );
	$wpdb->query( "UPDATE $table_name SET uuid = UUID()" );
	$wpdb->query( "ALTER TABLE $table_name ADD UNIQUE KEY `uuid` (`uuid`)" );
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'enabled'" ) == 'enabled' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER `author_id`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `status` (`status`)" );
		$wpdb->query( "UPDATE $table_name SET status = 'active' WHERE enabled = 1" );
		$wpdb->query( "UPDATE $table_name SET status = 'inactive' WHERE enabled = 0" );
		$wpdb->query( "ALTER TABLE $table_name DROP `enabled`" );
	}

	$table_name = $wpdb->prefix . 'ea_categories';
	$wpdb->query( "ALTER TABLE $table_name ADD `description` TEXT DEFAULT NULL AFTER `type`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `color`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `date_updated` DATETIME NULL DEFAULT NULL AFTER `date_created`" );
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'enabled'" ) == 'enabled' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER `description`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `status` (`status`)" );
		$wpdb->query( "UPDATE $table_name SET status = 'active' WHERE enabled = 1" );
		$wpdb->query( "UPDATE $table_name SET status = 'inactive' WHERE enabled = 0" );
		$wpdb->query( "ALTER TABLE $table_name DROP `enabled`" );
	}

	$table_name = $wpdb->prefix . 'ea_contactmeta';
	$wpdb->query( "ALTER TABLE $table_name CHANGE `contact_id` `ea_contact_id` bigint(20) unsigned NOT NULL default '0'" );

	$table_name = $wpdb->prefix . 'ea_contacts';
	$wpdb->query( "ALTER TABLE $table_name CHANGE `type` `type` VARCHAR(30) DEFAULT NULL default 'customer' after `id`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `user_id` `user_id` BIGINT(20) UNSIGNED DEFAULT NULL after `thumbnail_id`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `city` `city` VARCHAR(50) DEFAULT NULL" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `state` `state` VARCHAR(50) DEFAULT NULL" );
	$wpdb->query( "ALTER TABLE $table_name DROP `birth_date`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `website` `website` VARCHAR(191) DEFAULT NULL AFTER `phone`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `address` `address_1` VARCHAR(191) DEFAULT NULL" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `street` `address_2` VARCHAR(191) DEFAULT NULL AFTER `address_1`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `postcode` `postcode` VARCHAR(20) DEFAULT NULL" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `country` `country` VARCHAR(3) DEFAULT NULL AFTER `postcode`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `vat_number` `vat_number` VARCHAR(50) DEFAULT NULL AFTER `country`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `attachment`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `vat_number` `vat_number` VARCHAR(50) DEFAULT NULL AFTER `country`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `vat_exempt` TINYINT(1) NOT NULL DEFAULT '0' AFTER `vat_number`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `date_updated` DATETIME NULL DEFAULT NULL AFTER `date_created`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `creator_id` `author_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'" );
	$wpdb->query( "ALTER TABLE $table_name ADD `uuid` VARCHAR(36) NOT NULL AFTER `author_id`" );
	$wpdb->query( "UPDATE $table_name SET uuid = UUID()" );
	$wpdb->query( "ALTER TABLE $table_name ADD UNIQUE KEY `uuid` (`uuid`)" );

	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'enabled'" ) == 'enabled' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER `enabled`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `status` (`status`)" );
		$wpdb->query( "UPDATE $table_name SET status = 'active' WHERE enabled = 1" );
		$wpdb->query( "UPDATE $table_name SET status = 'inactive' WHERE enabled = 0" );
		$wpdb->query( "ALTER TABLE $table_name DROP `enabled`" );
	}

	$table_name = $wpdb->prefix . 'ea_items';
	$wpdb->query( "ALTER TABLE $table_name ADD `type` VARCHAR(50) NOT NULL DEFAULT 'standard' AFTER `id`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `unit` VARCHAR(20) DEFAULT NULL AFTER `description`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `sku`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `sale_price` `price` double(15,4) NOT NULL DEFAULT 0.00" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `purchase_price` `cost` double(15,4) NOT NULL DEFAULT 0.00" );
	$wpdb->query( "ALTER TABLE $table_name DROP `quantity`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `sales_tax`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `purchase_tax`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `taxable` TINYINT(1) NOT NULL DEFAULT 0 AFTER `cost`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `tax_ids` TEXT DEFAULT NULL AFTER `taxable`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `date_updated` DATETIME NULL DEFAULT NULL AFTER `date_created`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `creator_id` `author_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'" );
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'enabled'" ) == 'enabled' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER `thumbnail_id`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `status` (`status`)" );
		$wpdb->query( "UPDATE $table_name SET status = 'active' WHERE enabled = 1" );
		$wpdb->query( "UPDATE $table_name SET status = 'inactive' WHERE enabled = 0" );
		$wpdb->query( "ALTER TABLE $table_name DROP `enabled`" );
	}

	$table_name = $wpdb->prefix . 'ea_notes';
	$wpdb->query( "ALTER TABLE $table_name CHANGE `parent_id` `object_id`  BIGINT(20) UNSIGNED NOT NULL" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `type` `object_type`   VARCHAR(20) NOT NULL" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `note` `content` TEXT DEFAULT NULL" );
	$wpdb->query( "ALTER TABLE $table_name ADD `note_metadata` longtext DEFAULT NULL AFTER `content`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `creator_id` `author_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'" );
	$wpdb->query( "ALTER TABLE $table_name DROP `extra`" );

	$table_name = $wpdb->prefix . 'ea_transfers';
	$wpdb->query( "ALTER TABLE $table_name CHANGE `creator_id` `author_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'" );
	$wpdb->query( "ALTER TABLE $table_name ADD `date_updated` DATETIME NULL DEFAULT NULL AFTER `date_created`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `uuid` VARCHAR(36) DEFAULT NULL AFTER `author_id`" );
	$wpdb->query( "ALTER TABLE $table_name ADD UNIQUE KEY `uuid` (`uuid`)" );
	$wpdb->query( "UPDATE $table_name SET uuid = UUID()" );

	$table_name = $wpdb->prefix . 'ea_transactions';
	$wpdb->query( "ALTER TABLE $table_name ADD `number` VARCHAR(30) DEFAULT NULL AFTER `type`" );
	$wpdb->query( "ALTER TABLE $table_name ADD KEY `number` (`number`)" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `payment_date` `date` DATE DEFAULT NULL" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `description` `note` TEXT DEFAULT NULL " );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `currency_rate` `exchange_rate` double(15,4) NOT NULL DEFAULT 1.00" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `reference` `reference` VARCHAR(30) DEFAULT NULL AFTER `exchange_rate` " );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `note` `note` TEXT DEFAULT NULL AFTER `reference` " );
	$wpdb->query( "ALTER TABLE $table_name ADD `created_via` VARCHAR(100) DEFAULT 'manual' AFTER `uuid`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `date_updated` DATETIME NULL DEFAULT NULL AFTER `date_created`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `creator_id` `author_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'" );
	$wpdb->query( "UPDATE $table_name SET type = 'payment' WHERE type = 'income'" );
	$wpdb->query( "UPDATE $table_name JOIN (SELECT @rank := 0) r SET number=CONCAT('PAY-',LPAD(@rank:=@rank+1, 5, '0')) WHERE type='payment' AND number IS NULL OR number = ''" );
	$wpdb->query( "UPDATE $table_name JOIN (SELECT @rank := 0) r SET number=CONCAT('EXP-',LPAD(@rank:=@rank+1, 5, '0')) WHERE type='expense' AND number IS NULL OR number = ''" );
	$wpdb->query( "ALTER TABLE $table_name ADD `uuid` VARCHAR(36) DEFAULT NULL AFTER `author_id`" );
	$wpdb->query( "UPDATE $table_name SET uuid = UUID()" );
	$wpdb->query( "ALTER TABLE $table_name ADD UNIQUE KEY `uuid` (`uuid`)" );

	$table_name = $wpdb->prefix . 'ea_documents';
	$wpdb->query( "ALTER TABLE $table_name CHANGE `type` `type` VARCHAR(20) NOT NULL DEFAULT 'invoice' AFTER `id`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `status` `status` VARCHAR(20) NOT NULL DEFAULT 'draft' AFTER `type`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `document_number` `number` VARCHAR(30) NOT NULL AFTER `status`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `subtotal` `items_total` DOUBLE(15,4) DEFAULT 0 AFTER `number`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `total_discount` `discount_total` DOUBLE(15,4) DEFAULT 0 AFTER `items_total`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `total_shipping` `shipping_total` DOUBLE(15,4) DEFAULT 0 AFTER `discount_total`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `total_fees` `fees_total` DOUBLE(15,4) DEFAULT 0 AFTER `shipping_total`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `total_tax` `tax_total` DOUBLE(15,4) DEFAULT 0 AFTER `fees_total`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `total` `total` DOUBLE(15,4) DEFAULT 0 AFTER `tax_total`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `total_paid` DOUBLE(15,4) DEFAULT 0 AFTER `total`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `balance` DOUBLE(15,4) DEFAULT 0 AFTER `total_paid`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `discount` `discount_amount` DOUBLE(15,4) DEFAULT 0 AFTER `balance`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `discount_type` `discount_type` VARCHAR(20) NOT NULL DEFAULT 'fixed' AFTER `discount_amount`" );
	$wpdb->query( "UPDATE $table_name SET discount_type = 'percent' WHERE discount_type = 'percentage'" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `discount_type` `discount_type` ENUM('fixed','percent') NOT NULL DEFAULT 'fixed'" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `address` `billing_data` longtext NULL AFTER `discount_type`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `order_number` `reference` VARCHAR(30) DEFAULT NULL AFTER `billing_data` " );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `note` `note` TEXT DEFAULT NULL AFTER `reference` " );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `tax_inclusive` `tax_inclusive` TINYINT(1) NOT NULL DEFAULT 0 AFTER `note`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `vat_exempt` TINYINT(1) NOT NULL DEFAULT 0 AFTER `tax_inclusive`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `sent_date` DATETIME NULL DEFAULT NULL AFTER `due_date`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `currency_code` `currency_code` varchar(3) NOT NULL DEFAULT 'USD' AFTER `payment_date`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `currency_rate` `exchange_rate` double(15,4) NOT NULL DEFAULT 1.00 AFTER `currency_code`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `contact_id` `contact_id` BIGINT(20) UNSIGNED NOT NULL AFTER `number`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `terms`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `category_id`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `key`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `created_via` VARCHAR(100) DEFAULT 'manual' AFTER `uuid`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `date_updated` DATETIME NULL DEFAULT NULL AFTER `date_created`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `creator_id` `author_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0'" );
	$wpdb->query( "ALTER TABLE $table_name ADD `uuid` VARCHAR(36) DEFAULT NULL AFTER `author_id`" );
	$wpdb->query( "UPDATE $table_name SET uuid = UUID()" );
	$wpdb->query( "ALTER TABLE $table_name ADD UNIQUE KEY `uuid` (`uuid`)" );

	wp_cache_flush();

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
			$currency['enabled'] = 0;
			eac_insert_currency( $currency );
		}
	}

	wp_cache_flush();
}
