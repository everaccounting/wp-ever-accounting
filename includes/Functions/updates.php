<?php
/**
 * Updates functions.
 *
 * @since 1.0.0
 * @package EverAccounting\Functions
 */

defined( 'ABSPATH' ) || exit;

/**
 * Update to 1.2.0
 */
function eac_update_120() {
	error_log('eac_update_120');
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$settings     = get_option( 'eaccounting_settings', array() );
	$settings_map = array(
		'eac_business_name'              => 'company_name',
		'eac_business_email'             => 'company_email',
		'eac_business_phone'             => 'company_phone',
		'eac_business_logo'              => 'company_logo',
		'eac_business_tax_number'        => 'company_vat_number',
		'eac_base_currency'              => 'default_currency',
		'eac_year_start_date'            => 'financial_year_start',
		'eac_business_address'           => 'company_address',
		'eac_business_city'              => 'company_city',
		'eac_business_state'             => 'company_state',
		'eac_business_postcode'          => 'company_postcode',
		'eac_business_country'           => 'company_country',
		'eac_tax_enabled'                => 'tax_enabled',
		'eac_tax_subtotal_rounding'      => 'tax_subtotal_rounding',
		'eac_tax_display_totals'         => 'tax_display_totals',
		'eac_default_sales_account_id'   => 'default_account',
		'eac_default_sales_payment_method' => 'default_payment_method',
		'eac_invoice_prefix'             => 'invoice_prefix',
		'eac_invoice_digits'             => 'invoice_digit',
		'eac_invoice_due'                => 'invoice_due',
		'eac_invoice_note'               => 'invoice_notes',
		'eac_invoice_item_label'         => 'invoice_item_label',
		'eac_invoice_price_label'        => 'invoice_price_label',
		'eac_invoice_quantity_label'     => 'invoice_quantity_label',
		'eac_bill_prefix'                => 'bill_prefix',
		'eac_bill_digits'                => 'bill_digit',
		'eac_bill_note'                  => 'bill_notes',
		'eac_bill_due'                   => 'bill_due',
		'eac_bill_item_label'            => 'bill_item_label',
		'eac_bill_price_label'           => 'bill_price_label',
		'eac_bill_quantity_label'        => 'bill_quantity_label',
	);

	foreach ( $settings_map as $new_key => $old_key ) {
		if ( isset( $settings[ $old_key ] ) ) {
			update_option( $new_key, $settings[ $old_key ] );
		}
	}

	$currencies   = get_option( 'eac_currencies', array() );
	$o_currencies = get_option( 'eaccounting_currencies', array() );
	if ( is_array( $o_currencies ) && ! empty( $o_currencies ) ) {
		$o_currencies = wp_list_pluck( $o_currencies, 'rate', 'code' );
		foreach ( $o_currencies as $code => $rate ) {
			if ( ! empty( $code ) && ! empty( $rate ) && eac_base_currency() !== $code ) {
				$currencies[ $code ] = array(
					'rate' => $rate,
				);
			}
		}
	}
	update_option( 'eac_currencies', $currencies );

	// Accounts.
	$table = $wpdb->prefix . 'ea_accounts';
	$wpdb->query( "UPDATE $table SET created_at = date_created" );
	$wpdb->query( "UPDATE $table SET currency = currency_code" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN type VARCHAR(50) NOT NULL DEFAULT 'account' AFTER id" );
	$drops = array( 'enabled', 'currency_code', 'date_created', 'thumbnail_id', 'creator_id', 'bank_name', 'bank_phone', 'bank_address' );
	foreach ( $drops as $drop ) {
		$wpdb->query( "ALTER TABLE $table DROP COLUMN $drop" );
	}

	// Categories.
	$table = $wpdb->prefix . 'ea_categories';
	$wpdb->query( "UPDATE $table SET type = 'payment' WHERE type = 'income'" );
	$wpdb->query( "UPDATE $table SET created_at = date_created" );
	$wpdb->query( "DELETE FROM $table WHERE type = 'other'" );
	$drops = array( 'color', 'enabled', 'date_created' );
	foreach ( $drops as $drop ) {
		$wpdb->query( "ALTER TABLE $table DROP COLUMN $drop" );
	}

	// Contacts.
	$table = $wpdb->prefix . 'ea_contactmeta';
	$wpdb->query( "UPDATE $table SET ea_contact_id = contact_id" );
	$wpdb->query( "ALTER TABLE $table DROP `contact_id`" );

	$table = $wpdb->prefix . 'ea_contacts';
	$wpdb->query( "UPDATE $table SET tax_number = vat_number" );
	$wpdb->query( "UPDATE $table SET currency = currency_code" );
	$wpdb->query( "UPDATE $table SET created_at = date_created" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN type VARCHAR(30) DEFAULT 'customer' AFTER id" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN website VARCHAR(191) DEFAULT NULL AFTER phone" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN country VARCHAR(3) DEFAULT NULL AFTER zip" );
	$drops = array( 'birth_date', 'vat_number', 'currency_code', 'date_created', 'attachment', 'enabled', 'creator_id', 'thumbnail_id', 'user_id', 'street' );
	foreach ( $drops as $drop ) {
		$wpdb->query( "ALTER TABLE $table DROP COLUMN $drop" );
	}

	// Documents Items.
	$table = $wpdb->prefix . 'ea_document_items';
	$wpdb->query( "UPDATE $table SET name = item_name" );
	$wpdb->query( "UPDATE $table SET currency = currency_code" );
	$wpdb->query( "UPDATE $table SET created_at = date_created" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN unit VARCHAR(20) DEFAULT NULL AFTER item_id" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN description VARCHAR(160) DEFAULT NULL AFTER item_id" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN name VARCHAR(191) NOT NULL AFTER item_id" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN type VARCHAR(20) NOT NULL DEFAULT 'standard' AFTER item_id" );
	$drops = array( 'item_name', 'extra', 'tax_rate', 'currency_code', 'date_created' );
	foreach ( $drops as $drop ) {
		$wpdb->query( "ALTER TABLE $table DROP `$drop`" );
	}
}
