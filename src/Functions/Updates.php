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

	$table_name = $wpdb->prefix . 'ea_accounts';
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'type'" ) !== 'type' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `type` VARCHAR(50) NOT NULL DEFAULT 'bank' COMMENT 'Account Type' AFTER `id`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `type` (`type`)" );
	}

	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'status'" ) !== 'status' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER `thumbnail_id`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `status` (`status`)" );
		$wpdb->query( "UPDATE $table_name SET status = 'active' WHERE enabled = 1" );
		$wpdb->query( "UPDATE $table_name SET status = 'inactive' WHERE enabled = 0" );
		$wpdb->query( "ALTER TABLE $table_name DROP `enabled`" );
	}
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'updated_at'" ) !== 'updated_at' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date' AFTER `status`" );
		$wpdb->query( "ALTER TABLE $table_name CHANGE `date_created` `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date'" );
	}
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'created_at'" ) !== 'created_at' ) {
		$wpdb->query( "ALTER TABLE $table_name CHANGE `date_created` `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date'" );
	}

	$table_name = $wpdb->prefix . 'ea_categories';
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'status'" ) !== 'status' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER `color`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `status` (`status`)" );
		$wpdb->query( "UPDATE $table_name SET status = 'active' WHERE enabled = 1" );
		$wpdb->query( "UPDATE $table_name SET status = 'inactive' WHERE enabled = 0" );
		$wpdb->query( "ALTER TABLE $table_name DROP `enabled`" );
	}
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'updated_at'" ) !== 'updated_at' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date' AFTER `status`" );
		$wpdb->query( "ALTER TABLE $table_name CHANGE `date_created` `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date'" );
	}
	// Add description column.
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'description'" ) !== 'description' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `description` TEXT NULL DEFAULT NULL COMMENT 'Description' AFTER `type`" );
	}
	// change type income to payment.
	$wpdb->query( "UPDATE $table_name SET type = 'payment' WHERE type = 'income'" );

	$table_name = $wpdb->prefix . 'ea_contacts';
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'status'" ) !== 'status' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER `thumbnail_id`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `status` (`status`)" );
		$wpdb->query( "UPDATE $table_name SET status = 'active' WHERE enabled = 1" );
		$wpdb->query( "UPDATE $table_name SET status = 'inactive' WHERE enabled = 0" );
		$wpdb->query( "ALTER TABLE $table_name DROP `enabled`" );
		// $wpdb->query( "ALTER TABLE $table_name DROP `thumbnail_id`" );
	}
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'updated_at'" ) !== 'updated_at' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date' AFTER `creator_id`" );
		$wpdb->query( "ALTER TABLE $table_name CHANGE `date_created` `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date'" );
	}
	// drop attachments column.
	$wpdb->query( "ALTER TABLE $table_name DROP `attachment`" );

	// ea_documents_items table.
	$table_name = $wpdb->prefix . 'ea_document_items';
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'updated_at'" ) !== 'updated_at' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date' AFTER `date_created`" );
		$wpdb->query( "ALTER TABLE $table_name CHANGE `date_created` `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date'" );
	}
	// rename item_name to name.
	$wpdb->query( "ALTER TABLE $table_name CHANGE `item_name` `name` VARCHAR(255) NULL DEFAULT NULL" );

	// ea_documents table.
	$table_name = $wpdb->prefix . 'ea_documents';
	// add prefix column.
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'prefix'" ) !== 'prefix' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `prefix` VARCHAR(100) NULL DEFAULT NULL AFTER `id`" );
	}
	// if token column does not exist rename key to token.
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'token'" ) !== 'token' ) {
		$wpdb->query( "ALTER TABLE $table_name CHANGE `key` `token` VARCHAR(100) NULL DEFAULT NULL" );
	}
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'updated_at'" ) !== 'updated_at' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date' AFTER `creator_id`" );
		$wpdb->query( "ALTER TABLE $table_name CHANGE `date_created` `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date'" );
	}
	// If footer does not exist rename terms to footer.
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'footer'" ) !== 'footer' ) {
		$wpdb->query( "ALTER TABLE $table_name CHANGE `terms` `footer` TEXT NULL DEFAULT NULL" );
	}
	// change type income to payment.
	$wpdb->query( "UPDATE $table_name SET type = 'payment' WHERE type = 'income'" );

	// ea_items table.
	$table_name = $wpdb->prefix . 'ea_items';
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'status'" ) !== 'status' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' AFTER `thumbnail_id`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `status` (`status`)" );
		$wpdb->query( "UPDATE $table_name SET status = 'active' WHERE enabled = 1" );
		$wpdb->query( "UPDATE $table_name SET status = 'inactive' WHERE enabled = 0" );
		$wpdb->query( "ALTER TABLE $table_name DROP `enabled`" );
	}
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'type'" ) !== 'type' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `type` VARCHAR(191) NOT NULL DEFAULT 'product' COMMENT 'Item Type' AFTER `id`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `type` (`type`)" );
	}
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'unit'" ) !== 'unit' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `unit` VARCHAR(50) DEFAULT NULL AFTER `purchase_price`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `unit` (`unit`)" );
	}
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'price'" ) !== 'price' ) {
		$wpdb->query( "ALTER TABLE $table_name CHANGE `sale_price` `price` DECIMAL(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Price'" );
	}
	// drop purchase_price column.
	$wpdb->query( "ALTER TABLE $table_name DROP `purchase_price`" );
	$wpdb->query( "ALTER TABLE $table_name DROP `sku`" );
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'updated_at'" ) !== 'updated_at' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date' AFTER `status`" );
		$wpdb->query( "ALTER TABLE $table_name CHANGE `date_created` `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date'" );
	}

	// notes table.
	$table_name = $wpdb->prefix . 'ea_notes';
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'updated_at'" ) !== 'updated_at' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date' AFTER `creator_id`" );
		$wpdb->query( "ALTER TABLE $table_name CHANGE `date_created` `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date'" );
	}

	$table_name = $wpdb->prefix . 'ea_transactions';
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'transaction_number'" ) !== 'transaction_number' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `transaction_number` VARCHAR(100) NULL DEFAULT NULL AFTER `type`" );
		$wpdb->query( "ALTER TABLE $table_name ADD UNIQUE KEY `transaction_number` (`transaction_number`)" );
	}
	// set unique key for prefix and number.
	// if token column does not exist add token column.
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'token'" ) !== 'token' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `token` VARCHAR(100) NULL DEFAULT NULL AFTER `reconciled`" );
		$wpdb->query( "ALTER TABLE $table_name ADD KEY `token` (`token`)" );
	}

	// if not column does not exist rename description to note.
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'note'" ) !== 'note' ) {
		$wpdb->query( "ALTER TABLE $table_name CHANGE `description` `note` TEXT NULL DEFAULT NULL" );
	}
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'updated_at'" ) !== 'updated_at' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date' AFTER `creator_id`" );
		$wpdb->query( "ALTER TABLE $table_name CHANGE `date_created` `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date'" );
	}

	$wpdb->query( "UPDATE $table_name SET type = 'payment' WHERE type = 'income'" );
	$wpdb->query( "UPDATE $table_name JOIN (SELECT @rank := 0) r SET transaction_number=CONCAT('PAY-',LPAD(@rank:=@rank+1, 5, '0')) WHERE type='payment' AND transaction_number IS NULL OR transaction_number = ''" );
	$wpdb->query( "UPDATE $table_name JOIN (SELECT @rank := 0) r SET transaction_number=CONCAT('EXP-',LPAD(@rank:=@rank+1, 5, '0')) WHERE type='expense' AND transaction_number IS NULL OR transaction_number = ''" );
	$wpdb->query( "UPDATE $table_name SET token=MD5(id) WHERE token IS NULL OR token = ''" );

	$table_name = $wpdb->prefix . 'ea_transfers';
	// if payment_id column does not exist rename income_id to payment_id.
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'payment_id'" ) !== 'payment_id' ) {
		$wpdb->query( "ALTER TABLE $table_name CHANGE `income_id` `payment_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL" );
	}
	if ( $wpdb->get_var( "SHOW COLUMNS FROM $table_name LIKE 'updated_at'" ) !== 'updated_at' ) {
		$wpdb->query( "ALTER TABLE $table_name ADD `updated_at` DATETIME NULL DEFAULT NULL COMMENT 'Update Date' AFTER `creator_id`" );
		$wpdb->query( "ALTER TABLE $table_name CHANGE `date_created` `created_at` DATETIME NULL DEFAULT NULL COMMENT 'Create Date'" );
	}
}
