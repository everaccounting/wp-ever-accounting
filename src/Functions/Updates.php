<?php

defined( 'ABSPATH' ) || exit;

/**
 * Update the plugin to version 1.1.6.
 *
 * @return void
 * @since 1.1.6
 */
function eac_update_1_1_6() {
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$settings     = get_option( 'eaccounting_settings', array() );
	$currencies   = get_option( 'eaccounting_currencies', array() );
	$settings_map = array(
		'company_name'           => 'eac_company_name',
		'company_email'          => 'eac_company_email',
		'company_phone'          => 'eac_company_phone',
		'company_logo'           => 'eac_company_logo',
		'company_vat_number'     => 'eac_company_vat_number',
		'financial_year_start'   => 'eac_year_start_date',
		'company_address'        => 'eac_company_address_1',
		'company_city'           => 'eac_company_city',
		'company_state'          => 'eac_company_state',
		'company_postcode'       => 'eac_company_postcode',
		'company_country'        => 'eac_company_country',
		'tax_enabled'            => 'eac_tax_enabled',
		'tax_subtotal_rounding'  => 'eac_tax_subtotal_rounding',
		'tax_display_totals'     => 'eac_tax_display_totals',
		'default_account'        => 'eac_default_sales_account_id',
		'default_payment_method' => 'eac_default_sales_payment_method',
		'prices_include_tax'     => 'eac_prices_include_tax',
		'invoice_prefix'         => 'eac_invoice_prefix',
		'invoice_digits'         => 'eac_invoice_digits',
		'invoice_due_after'      => 'eac_invoice_due_date',
		'invoice_notes'          => 'eac_invoice_note',
		'invoice_item_label'     => 'eac_invoice_item_label',
		'invoice_price_label'    => 'eac_invoice_price_label',
		'invoice_quantity_label' => 'eac_invoice_quantity_label',
		'bill_prefix'            => 'eac_bill_prefix',
		'bill_digits'            => 'eac_bill_digits',
		'bill_notes'             => 'eac_bill_note',
		'bill_due_after'         => 'eac_bill_due_after',
		'bill_item_label'        => 'eac_bill_item_label',
		'bill_price_label'       => 'eac_bill_price_label',
		'bill_quantity_label'    => 'eac_bill_quantity_label',
	);

	foreach ( $settings_map as $old_key => $new_key ) {
		if ( isset( $settings[ $old_key ] ) ) {
			update_option( $new_key, $settings[ $old_key ] );
		}
	}

	global $wpdb;
	$wpdb->hide_errors();
	$collate = '';
	if ( $wpdb->has_cap( 'collation' ) ) {
		if ( ! empty( $wpdb->charset ) ) {
			$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$collate .= " COLLATE $wpdb->collate";
		}
	}
	$base_currency = ! empty( $settings['default_currency'] ) ? $settings['default_currency'] : eac_get_base_currency();
	$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->options SET option_value = %s WHERE option_name = 'eac_base_currency'", $base_currency ) );
// Base currency update.

	$table_name = $wpdb->prefix . 'ea_accounts';
	$wpdb->query( "ALTER TABLE $table_name ADD `type` VARCHAR(50) NOT NULL DEFAULT 'bank' AFTER `name`" );
	$wpdb->query( "ALTER TABLE $table_name ADD KEY `type` (`type`)" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `currency_code` `currency_code` varchar(3) NOT NULL DEFAULT 'USD' AFTER `bank_address`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `thumbnail_id`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `date_updated` DATETIME NULL DEFAULT NULL AFTER `creator_id`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `uuid` VARCHAR(36) NOT NULL AFTER `currency_code`" );
	$wpdb->query( "UPDATE $table_name SET uuid = UUID()" );
	$wpdb->query( "ALTER TABLE $table_name ADD UNIQUE KEY `uuid` (`uuid`)" );

	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'enabled'" ) == 'enabled' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER `currency_code`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `status` (`status`)" );
		$wpdb->query( "UPDATE $table_name SET status = 'active' WHERE enabled = 1" );
		$wpdb->query( "UPDATE $table_name SET status = 'inactive' WHERE enabled = 0" );
		$wpdb->query( "ALTER TABLE $table_name DROP `enabled`" );
	}

	$table_name = $wpdb->prefix . 'ea_categories';
	$wpdb->query( "ALTER TABLE $table_name CHANGE `type` `type` VARCHAR(50) NOT NULL DEFAULT 'expense' AFTER `name`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `description` TEXT DEFAULT NULL AFTER `type`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `color`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `date_updated` DATETIME NULL DEFAULT NULL AFTER `enabled`" );
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'enabled'" ) == 'enabled' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER `description`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `status` (`status`)" );
		$wpdb->query( "UPDATE $table_name SET status = 'active' WHERE enabled = 1" );
		$wpdb->query( "UPDATE $table_name SET status = 'inactive' WHERE enabled = 0" );
		$wpdb->query( "ALTER TABLE $table_name DROP `enabled`" );
	}
// remove any categories that type is other.
	$wpdb->query( "DELETE FROM $table_name WHERE type = 'other'" );
	$wpdb->query( "UPDATE $table_name SET type = 'payment' WHERE type = 'income'" );


	$table_name = $wpdb->prefix . 'ea_contactmeta';
	$wpdb->query( "ALTER TABLE $table_name CHANGE `contact_id` `ea_contact_id` bigint(20) unsigned NOT NULL default '0'" );

	$table_name = $wpdb->prefix . 'ea_contacts';
	$wpdb->query( "ALTER TABLE $table_name CHANGE `type` `type` VARCHAR(30) DEFAULT NULL default 'customer' after `name`" );
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
	$wpdb->query( "ALTER TABLE $table_name ADD `date_updated` DATETIME NULL DEFAULT NULL AFTER `creator_id`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `uuid` VARCHAR(36) NOT NULL AFTER `enabled`" );
	$wpdb->query( "UPDATE $table_name SET uuid = UUID()" );
	$wpdb->query( "ALTER TABLE $table_name ADD UNIQUE KEY `uuid` (`uuid`)" );

	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'enabled'" ) == 'enabled' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER `enabled`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `status` (`status`)" );
		$wpdb->query( "UPDATE $table_name SET status = 'active' WHERE enabled = 1" );
		$wpdb->query( "UPDATE $table_name SET status = 'inactive' WHERE enabled = 0" );
		$wpdb->query( "ALTER TABLE $table_name DROP `enabled`" );
	}

	$table_name = $wpdb->prefix . 'ea_document_items';
	$wpdb->query( "ALTER TABLE $table_name CHANGE `item_name` `name` VARCHAR(191) NOT NULL AFTER `id`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `type` VARCHAR(20) NOT NULL DEFAULT 'standard' AFTER `name`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `description` TEXT NULL AFTER `type`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `unit` VARCHAR(20) DEFAULT NULL AFTER `description`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `taxable` TINYINT(1) NOT NULL DEFAULT 0 AFTER `total`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `item_id` `item_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `taxable`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `document_id` `document_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL AFTER `item_id`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `subtotal_tax` double(15,4) NOT NULL DEFAULT 0.00 AFTER `subtotal`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `discount_tax` double(15,4) NOT NULL DEFAULT 0.00 AFTER `discount`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `tax` `tax_total` double(15,4) NOT NULL DEFAULT 0.00" );
	$wpdb->query( "ALTER TABLE $table_name ADD `date_updated` DATETIME NULL DEFAULT NULL AFTER `currency_code`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `tax_rate`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `extra`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `currency_code`" );


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
	$wpdb->query( "ALTER TABLE $table_name DROP `attachment_id`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `category_id`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `key`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `uuid` VARCHAR(36) DEFAULT NULL AFTER `parent_id`" );
	$wpdb->query( "UPDATE $table_name SET uuid = UUID()" );
	$wpdb->query( "ALTER TABLE $table_name ADD UNIQUE KEY `uuid` (`uuid`)" );
	$wpdb->query( "ALTER TABLE $table_name ADD `created_via` VARCHAR(100) DEFAULT 'manual' AFTER `uuid`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `date_updated` DATETIME NULL DEFAULT NULL AFTER `creator_id`" );


	$table_name = $wpdb->prefix . 'ea_items';
	$wpdb->query( "ALTER TABLE $table_name ADD `type` VARCHAR(50) NOT NULL DEFAULT 'standard' AFTER `name`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `unit` VARCHAR(20) DEFAULT NULL AFTER `description`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `sku`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `sale_price` `price` double(15,4) NOT NULL DEFAULT 0.00" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `purchase_price` `cost` double(15,4) NOT NULL DEFAULT 0.00" );
	$wpdb->query( "ALTER TABLE $table_name DROP `quantity`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `sales_tax`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `purchase_tax`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `taxable` TINYINT(1) NOT NULL DEFAULT 0 AFTER `cost`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `tax_ids` TEXT DEFAULT NULL AFTER `taxable`" );
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'enabled'" ) == 'enabled' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER `thumbnail_id`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `status` (`status`)" );
		$wpdb->query( "UPDATE $table_name SET status = 'active' WHERE enabled = 1" );
		$wpdb->query( "UPDATE $table_name SET status = 'inactive' WHERE enabled = 0" );
		$wpdb->query( "ALTER TABLE $table_name DROP `enabled`" );
	}
	$wpdb->query( "ALTER TABLE $table_name ADD `date_updated` DATETIME NULL DEFAULT NULL AFTER `creator_id`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `creator_id`" );

	$table_name = $wpdb->prefix . 'ea_notes';
	$wpdb->query( "ALTER TABLE $table_name CHANGE `parent_id` `object_id`  BIGINT(20) UNSIGNED NOT NULL" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `type` `object_type`   VARCHAR(20) NOT NULL" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `note` `content` TEXT DEFAULT NULL" );
	$wpdb->query( "ALTER TABLE $table_name ADD `note_metadata` longtext DEFAULT NULL AFTER `content`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `extra`" );

	$table_name = $wpdb->prefix . 'ea_transfers';
	$wpdb->query( "ALTER TABLE $table_name CHANGE `income_id` `payment_id` BIGINT(20) UNSIGNED DEFAULT NULL" );
	$wpdb->query( "ALTER TABLE $table_name ADD `date_updated` DATETIME NULL DEFAULT NULL AFTER `creator_id`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `uuid` VARCHAR(36) DEFAULT NULL AFTER `expense_id`" );
	$wpdb->query( "UPDATE $table_name SET uuid = UUID()" );
	$wpdb->query( "ALTER TABLE $table_name ADD UNIQUE KEY `uuid` (`uuid`)" );

	$table_name = $wpdb->prefix . 'ea_transactions';
	$wpdb->query( "ALTER TABLE $table_name ADD `number` VARCHAR(30) DEFAULT NULL AFTER `type`" );
	$wpdb->query( "ALTER TABLE $table_name ADD KEY `number` (`number`)" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `payment_date` `date` DATE DEFAULT NULL" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `description` `note` TEXT DEFAULT NULL " );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `currency_rate` `exchange_rate` double(15,4) NOT NULL DEFAULT 1.00" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `reference` `reference` VARCHAR(30) DEFAULT NULL AFTER `exchange_rate` " );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `note` `note` TEXT DEFAULT NULL AFTER `reference` " );
	$wpdb->query( "ALTER TABLE $table_name ADD `uuid` VARCHAR(36) DEFAULT NULL AFTER `reconciled`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `created_via` VARCHAR(100) DEFAULT 'manual' AFTER `uuid`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `date_updated` DATETIME NULL DEFAULT NULL AFTER `creator_id`" );
	$wpdb->query( "UPDATE $table_name SET type = 'payment' WHERE type = 'income'" );
	$wpdb->query( "UPDATE $table_name JOIN (SELECT @rank := 0) r SET number=CONCAT('PAY-',LPAD(@rank:=@rank+1, 5, '0')) WHERE type='payment' AND number IS NULL OR number = ''" );
	$wpdb->query( "UPDATE $table_name JOIN (SELECT @rank := 0) r SET number=CONCAT('EXP-',LPAD(@rank:=@rank+1, 5, '0')) WHERE type='expense' AND number IS NULL OR number = ''" );
	$wpdb->query( "UPDATE $table_name SET uuid = UUID()" );
	$wpdb->query( "ALTER TABLE $table_name ADD UNIQUE KEY `uuid` (`uuid`)" );

	$table_name = $wpdb->prefix . 'ea_currencies';
// if the table does not have exchange_rate column, then drop the table and create it again.
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'exchange_rate'" ) != 'exchange_rate' ) {
		$wpdb->query( "DROP TABLE $table_name" );
	}
	wp_cache_flush();
	dbDelta(
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
    		) $collate;"
	);

	foreach ( $currencies as $currency ) {
		eac_insert_currency(
			array(
				'code'          => $currency['code'],
				'exchange_rate' => $currency['rate'],
			)
		);
	}
}
