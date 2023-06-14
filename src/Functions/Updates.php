<?php

defined( 'ABSPATH' ) || exit;

/**
 * Update the plugin to version 1.1.6.
 *
 * @since 1.1.6
 * @return void
 */
function eac_update_1_1_6() {
	$settings     = get_option( 'eaccounting_settings', array() );
	$settings_map = array(
		'company_name'                 => 'eac_business_name',
		'company_email'                => 'eac_business_email',
		'company_phone'                => 'eac_business_phone',
		'company_address'              => 'eac_business_street',
		'company_city'                 => 'eac_business_city',
		'company_state'                => 'eac_business_state',
		'company_postcode'             => 'eac_business_postcode',
		'company_country'              => 'eac_business_country',
		'company_logo'                 => 'eac_business_logo',
		'financial_year_start'         => 'eac_financial_year_start',
		'tax_enabled'                  => 'eac_tax_enabled',
		'dashboard_transactions_limit' => 'eac_dashboard_transactions_limit',
		'default_account'              => 'eac_default_account',
		'default_currency'             => 'eac_default_currency',
		'default_payment_method'       => 'eac_default_payment_method',
		'tax_subtotal_rounding'        => 'eac_tax_subtotal_rounding',
		'prices_include_tax'           => 'eac_prices_include_tax',
		'tax_display_totals'           => 'eac_tax_display_totals',
		'invoice_prefix'               => 'eac_invoice_prefix',
		'invoice_digits'               => 'eac_invoice_digits',
		'invoice_terms'                => 'eac_invoice_notes',
		'invoice_notes'                => 'eac_invoice_footer',
		'invoice_due_after'            => 'eac_invoice_due_after',
		'invoice_item_label'           => 'eac_invoice_item_label',
		'invoice_price_label'          => 'eac_invoice_price_label',
		'invoice_quantity_label'       => 'eac_invoice_quantity_label',
		'bill_prefix'                  => 'eac_bill_prefix',
		'bill_digits'                  => 'eac_bill_digits',
		'bill_terms'                   => 'eac_bill_notes',
		'bill_notes'                   => 'eac_bill_footer',
		'bill_due_after'               => 'eac_bill_due_after',
		'bill_item_label'              => 'eac_bill_item_label',
		'bill_price_label'             => 'eac_bill_price_label',
		'bill_quantity_label'          => 'eac_bill_quantity_label',
	);

	foreach ( $settings_map as $old_key => $new_key ) {
		if ( isset( $settings[ $old_key ] ) ) {
			update_option( $new_key, $settings[ $old_key ] );
		}
	}

	global $wpdb;
	$wpdb->hide_errors();
	$index_length = 191;

	// now call installer to create new tables.
	\EverAccounting\Installer::install();

	$table_name = $wpdb->prefix . 'ea_accounts';
	$wpdb->query( "ALTER TABLE $table_name ADD `type` VARCHAR(50) NOT NULL DEFAULT 'bank' AFTER `id`" );
	$wpdb->query( "ALTER TABLE $table_name ADD KEY `type` (`type`)" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `currency_code` `currency_code` varchar(3) NOT NULL DEFAULT 'USD' AFTER `bank_address`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `thumbnail_id`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `updated_at` DATETIME NULL DEFAULT NULL AFTER `creator_id`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `date_created` `created_at` DATETIME NULL DEFAULT NULL" );

	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'enabled'" ) == 'enabled' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER `currency_code`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `status` (`status`)" );
		$wpdb->query( "UPDATE $table_name SET status = 'active' WHERE enabled = 1" );
		$wpdb->query( "UPDATE $table_name SET status = 'inactive' WHERE enabled = 0" );
		$wpdb->query( "ALTER TABLE $table_name DROP `enabled`" );
	}

	$table_name = $wpdb->prefix . 'ea_categories';
	$wpdb->query( "ALTER TABLE $table_name ADD `type` VARCHAR(50) NOT NULL DEFAULT 'expense' AFTER `name`" );
	$wpdb->query( "ALTER TABLE $table_name ADD KEY `type` (`type`)" );
	$wpdb->query( "ALTER TABLE $table_name ADD `description` `description` TEXT NULL DEFAULT NULL AFTER `type`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `color`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `updated_at` DATETIME NULL DEFAULT NULL AFTER `enabled`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `date_created` `created_at` DATETIME NULL DEFAULT NULL" );
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'enabled'" ) == 'enabled' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER `type`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `status` (`status`)" );
		$wpdb->query( "UPDATE $table_name SET status = 'active' WHERE enabled = 1" );
		$wpdb->query( "UPDATE $table_name SET status = 'inactive' WHERE enabled = 0" );
		$wpdb->query( "ALTER TABLE $table_name DROP `enabled`" );
	}
	// remove any categories that type is other.
	$wpdb->query( "DELETE FROM $table_name WHERE type = 'other'" );
	$wpdb->query( "UPDATE $table_name SET type = 'payment' WHERE type = 'income'" );

	$table_name = $wpdb->prefix . 'ea_contacts';
	$wpdb->query( "ALTER TABLE $table_name CHANGE `city` `city` VARCHAR(50) DEFAULT NULL" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `state` `state` VARCHAR(50) DEFAULT NULL" );
	$wpdb->query( "ALTER TABLE $table_name DROP `birth_date`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `website` `website` VARCHAR(191) DEFAULT NULL AFTER `phone`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `address` `address_1` VARCHAR(191) DEFAULT NULL" );
	$wpdb->query( "ALTER TABLE $table_name ADD `address_2` VARCHAR(191) NULL DEFAULT NULL AFTER `address_1`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `street`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `postcode` `postcode` VARCHAR(20) DEFAULT NULL" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `country` `country` VARCHAR(3) DEFAULT NULL AFTER `postcode`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `vat_number` `vat_number` VARCHAR(50) DEFAULT NULL AFTER `country`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `attachment`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `vat_number` `vat_number` VARCHAR(50) DEFAULT NULL AFTER `country`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `type` `type` VARCHAR(30) DEFAULT NULL default 'customer'" );
	$wpdb->query( "ALTER TABLE $table_name ADD `updated_at` DATETIME NULL DEFAULT NULL AFTER `creator_id`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `date_created` `created_at` DATETIME NULL DEFAULT NULL" );

	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'enabled'" ) == 'enabled' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER `type`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `status` (`status`)" );
		$wpdb->query( "UPDATE $table_name SET status = 'active' WHERE enabled = 1" );
		$wpdb->query( "UPDATE $table_name SET status = 'inactive' WHERE enabled = 0" );
		$wpdb->query( "ALTER TABLE $table_name DROP `enabled`" );
	}

	//update income type to payment.
	$wpdb->query( "UPDATE $table_name SET type = 'payment' WHERE type = 'income'" );

	$table_name = $wpdb->prefix . 'ea_document_items';
	$wpdb->query( "ALTER TABLE $table_name CHANGE `item_id` `product_id` INT(11) DEFAULT NULL" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `item_name` `name` VARCHAR(191) NOT NULL" );
	$wpdb->query( "ALTER TABLE $table_name ADD `description` TEXT NULL AFTER `name`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `unit` VARCHAR(20) NOT NULL AFTER `description`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `subtotal_tax` double(15,4) NOT NULL DEFAULT 0.00 AFTER `subtotal`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `discount_tax` double(15,4) NOT NULL DEFAULT 0.00 AFTER `discount`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `shipping` double(15,4) NOT NULL DEFAULT 0.00 AFTER `discount_tax`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `shipping_tax` double(15,4) NOT NULL DEFAULT 0.00 AFTER `shipping`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `fee` double(15,4) NOT NULL DEFAULT 0.00 AFTER `shipping_tax`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `fee_tax` double(15,4) NOT NULL DEFAULT 0.00 AFTER `fee`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `tax` `tax_total` double(15,4) NOT NULL DEFAULT 0.00" );
	$wpdb->query( "ALTER TABLE $table_name ADD `taxable` ENUM('yes','no') NOT NULL DEFAULT 'yes' AFTER `total`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `taxable_shipping` ENUM('yes','no') NOT NULL DEFAULT 'yes' AFTER `taxable`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `taxable_fee` ENUM('yes','no') NOT NULL DEFAULT 'yes' AFTER `taxable_shipping`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `updated_at` DATETIME NULL DEFAULT NULL AFTER `status`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `updated_at` DATETIME NULL DEFAULT NULL AFTER `currency_code`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `date_created` `created_at` DATETIME NULL DEFAULT NULL" );
	$wpdb->query( "ALTER TABLE $table_name DROP `tax_rate`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `extra`" );

	$table_name = $wpdb->prefix . 'ea_documents';
	$wpdb->query( "ALTER TABLE $table_name CHANGE `type` `type` VARCHAR(20) NOT NULL DEFAULT 'invoice' AFTER `id`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `status` `status` VARCHAR(20) NOT NULL DEFAULT 'draft' AFTER `type`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `number` `number` VARCHAR(30) NOT NULL AFTER `status`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `contact_id` `contact_id` BIGINT(20) UNSIGNED NOT NULL AFTER `number`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `subtotal` `subtotal` DOUBLE(15,4) DEFAULT 0 AFTER `contact_id`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `total_discount` `discount_total` DOUBLE(15,4) DEFAULT 0 AFTER `subtotal`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `total_shipping` `shipping_total` DOUBLE(15,4) DEFAULT 0 AFTER `discount_total`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `total_fees` `fees_total` DOUBLE(15,4) DEFAULT 0 AFTER `shipping_total`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `total_tax` `tax_total` DOUBLE(15,4) DEFAULT 0 AFTER `fees_total`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `total` `total` DOUBLE(15,4) DEFAULT 0 AFTER `tax_total`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `total_paid` DOUBLE(15,4) DEFAULT 0 AFTER `total`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `total_refunded` DOUBLE(15,4) DEFAULT 0 AFTER `total_paid`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `discount` `discount_amount` DOUBLE(15,4) DEFAULT 0 AFTER `total_refunded`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `discount_type` `discount_type` VARCHAR(20) NOT NULL DEFAULT 'fixed' AFTER `discount_amount`" );
	$wpdb->query( "UPDATE $table_name SET discount_type = 'percent' WHERE discount_type = 'percentage'" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `discount_type` `discount_type` ENUM('fixed','percent') NOT NULL DEFAULT 'fixed'" );
	$wpdb->query( "ALTER TABLE $table_name ADD `shipping_amount` DOUBLE(15,4) DEFAULT 0 AFTER `discount_type`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `fees_amount` DOUBLE(15,4) DEFAULT 0 AFTER `shipping_amount`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `address` `billing_data` longtext NULL AFTER `fees_amount`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `shipping_data` longtext NULL AFTER `billing_data`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `note` `document_note` TEXT DEFAULT NULL AFTER `reference` " );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `tax_inclusive` `tax_inclusive` VARCHAR(20) DEFAULT NULL AFTER `document_note`" );
	$wpdb->query( "UPDATE $table_name SET tax_inclusive = 'yes' WHERE tax_inclusive = 1" );
	$wpdb->query( "UPDATE $table_name SET tax_inclusive = 'no' WHERE tax_inclusive = 0" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `tax_inclusive` `tax_inclusive` ENUM('yes','no') NOT NULL DEFAULT 'no'" );
	$wpdb->query( "ALTER TABLE $table_name ADD `vat_exempt` ENUM('yes','no') NOT NULL DEFAULT 'no' AFTER `tax_inclusive`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `issue_date` `issued_at` DATETIME NULL DEFAULT NULL AFTER `vat_exempt`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `due_date` `due_at` DATETIME NULL DEFAULT NULL AFTER `issued_at`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `sent_at` DATETIME NULL DEFAULT NULL AFTER `due_at`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `viewed_at` DATETIME NULL DEFAULT NULL AFTER `sent_at`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `payment_date` `paid_at` DATETIME NULL DEFAULT NULL AFTER `viewed_at`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `created_via` VARCHAR(100) DEFAULT NULL AFTER `paid_at`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `terms`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `attachment_id`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `category_id`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `key` `uuid` VARCHAR(32) DEFAULT NULL AFTER `creator_id`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `updated_at` DATETIME NULL DEFAULT NULL AFTER `uuid`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `date_created` `created_at` DATETIME NULL DEFAULT NULL" );

	$table_name = $wpdb->prefix . 'ea_items';
	$wpdb->query( "ALTER TABLE $table_name ADD `unit` VARCHAR(20) NOT NULL AFTER `description`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `sku`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `sale_price` `price` double(15,4) NOT NULL DEFAULT 0.00" );
	$wpdb->query( "ALTER TABLE $table_name DROP `purchase_price`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `quantity`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `sales_tax`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `purchase_tax`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `taxable` ENUM('yes', 'no') DEFAULT 'yes' AFTER `category_id`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `tax_ids` TEXT DEFAULT NULL AFTER `taxable`" );
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'enabled'" ) == 'enabled' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER `thumbnail_id`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `status` (`status`)" );
		$wpdb->query( "UPDATE $table_name SET status = 'active' WHERE enabled = 1" );
		$wpdb->query( "UPDATE $table_name SET status = 'inactive' WHERE enabled = 0" );
		$wpdb->query( "ALTER TABLE $table_name DROP `enabled`" );
	}
	$wpdb->query( "ALTER TABLE $table_name ADD `updated_at` DATETIME NULL DEFAULT NULL AFTER `creator_id`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `date_created` `created_at` DATETIME NULL DEFAULT NULL" );
	$wpdb->query( "ALTER TABLE $table_name DROP `creator_id`" );

	$table_name = $wpdb->prefix . 'ea_transfers';
	$wpdb->query( "ALTER TABLE $table_name CHANGE `income_id` `payment_id` BIGINT(20) UNSIGNED DEFAULT NULL" );
	$wpdb->query( "ALTER TABLE $table_name ADD `updated_at` DATETIME NULL DEFAULT NULL AFTER `creator_id`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `date_created` `created_at` DATETIME NULL DEFAULT NULL" );

	$table_name = $wpdb->prefix . 'ea_transactions';
	$wpdb->query( "ALTER TABLE $table_name ADD `number` VARCHAR(30) DEFAULT NULL AFTER `type`" );
	$wpdb->query( "ALTER TABLE $table_name ADD KEY `number` (`number`)" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `description` `payment_note` TEXT DEFAULT NULL " );
	$wpdb->query( "ALTER TABLE $table_name ADD `uuid` VARCHAR(32) DEFAULT NULL AFTER `creator_id`" );
	$wpdb->query( "ALTER TABLE $table_name ADD `updated_at` DATETIME NULL DEFAULT NULL AFTER `uuid`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `date_created` `created_at` DATETIME NULL DEFAULT NULL" );
	$wpdb->query( "UPDATE $table_name SET type = 'payment' WHERE type = 'income'" );

	$wpdb->query( "UPDATE $table_name JOIN (SELECT @rank := 0) r SET number=CONCAT('PAY-',LPAD(@rank:=@rank+1, 5, '0')) WHERE type='payment' AND number IS NULL OR number = ''" );
	$wpdb->query( "UPDATE $table_name JOIN (SELECT @rank := 0) r SET number=CONCAT('EXP-',LPAD(@rank:=@rank+1, 5, '0')) WHERE type='expense' AND number IS NULL OR number = ''" );
	$wpdb->query( "UPDATE $table_name SET uuid=MD5(id) WHERE uuid IS NULL OR uuid = ''" );

	$table_name = $wpdb->prefix . 'ea_notes';
	$wpdb->query( "ALTER TABLE $table_name CHANGE `parent_id` `object_id`  BIGINT(20) UNSIGNED NOT NULL" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `type` `object_type`   VARCHAR(20) NOT NULL" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `note` `content` TEXT DEFAULT NULL" );
	$wpdb->query( "ALTER TABLE $table_name ADD `updated_at` DATETIME NULL DEFAULT NULL AFTER `creator_id`" );
	$wpdb->query( "ALTER TABLE $table_name CHANGE `date_created` `created_at` DATETIME NULL DEFAULT NULL" );

	$table_name = $wpdb->prefix . 'ea_contactmeta';
	$wpdb->query( "ALTER TABLE $table_name CHANGE `contact_id` `ea_contact_id` bigint(20) unsigned NOT NULL default '0'" );

	$table_name = $wpdb->prefix . 'ea_currencies';

	if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
		$wpdb->query( "DROP TABLE $table_name" );
	}
}
