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
		'eac_default_sales_payment_mode' => 'default_payment_method',
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
	$wpdb->query( "ALTER TABLE $table DROP `enabled`" );
	$wpdb->query( "ALTER TABLE $table DROP `currency_code`" );
	$wpdb->query( "ALTER TABLE $table DROP `date_created`" );

	// Categories.
	$table = $wpdb->prefix . 'ea_categories';
	$wpdb->query( "UPDATE $table SET type = 'payment' WHERE type = 'income'" );
	$wpdb->query( "UPDATE $table SET created_at = date_created" );
	$wpdb->query( "ALTER TABLE $table DROP `color`" );
	$wpdb->query( "ALTER TABLE $table DROP `enabled`" );
	$wpdb->query( "ALTER TABLE $table DROP `date_created`" );

	// Contacts.
	$table = $wpdb->prefix . 'ea_contactmeta';
	$wpdb->query( "UPDATE $table SET ea_contact_id = contact_id" );
	$wpdb->query( "ALTER TABLE $table DROP `contact_id`" );

	$table = $wpdb->prefix . 'ea_contacts';
	$wpdb->query( "UPDATE $table SET tax_number = vat_number" );
	$wpdb->query( "UPDATE $table SET zip = postcode" );
	$wpdb->query( "UPDATE $table SET currency = currency_code" );
	$wpdb->query( "UPDATE $table SET created_at = date_created" );
	$wpdb->query( "ALTER TABLE $table DROP `birth_date`" );
	$wpdb->query( "ALTER TABLE $table DROP `vat_number`" );
	$wpdb->query( "ALTER TABLE $table DROP `postcode`" );
	$wpdb->query( "ALTER TABLE $table DROP `currency_code`" );
	$wpdb->query( "ALTER TABLE $table DROP `date_created`" );
	$wpdb->query( "ALTER TABLE $table DROP `attachment`" );
	$wpdb->query( "ALTER TABLE $table DROP `enabled`" );

	// Items.
	$table = $wpdb->prefix . 'ea_items';
	$wpdb->query( "UPDATE $table SET price = sale_price" );
	$wpdb->query( "UPDATE $table SET cost = purchase_price" );
	$wpdb->query( "UPDATE $table SET created_at = date_created" );
	$wpdb->query( "ALTER TABLE $table DROP sale_price" );
	$wpdb->query( "ALTER TABLE $table DROP purchase_price" );
	$wpdb->query( "ALTER TABLE $table DROP sku" );
	$wpdb->query( "ALTER TABLE $table DROP quantity" );
	$wpdb->query( "ALTER TABLE $table DROP sales_tax" );
	$wpdb->query( "ALTER TABLE $table DROP purchase_tax" );
	$wpdb->query( "ALTER TABLE $table DROP enabled" );
	$wpdb->query( "ALTER TABLE $table DROP date_created" );

	// Notes.
	$table = $wpdb->prefix . 'ea_notes';
	$wpdb->query( "UPDATE $table SET parent_type = type" );
	$wpdb->query( "UPDATE $table SET content = note" );
	$wpdb->query( "UPDATE $table SET created_at = date_created" );
	$wpdb->query( "ALTER TABLE $table DROP `type`" );
	$wpdb->query( "ALTER TABLE $table DROP note" );
	$wpdb->query( "ALTER TABLE $table DROP note_metadata" );
	$wpdb->query( "ALTER TABLE $table DROP extra" );

	// Transfers.
	$table = $wpdb->prefix . 'ea_transfers';
	$wpdb->query( "UPDATE $table SET payment_id = income_id" );
	$wpdb->query( "UPDATE $table SET created_at = date_created" );
	$wpdb->query( "ALTER TABLE $table DROP income_id" );
	$wpdb->query( "ALTER TABLE $table DROP date_created" );

	// Transactions.
	$table = $wpdb->prefix . 'ea_transactions';
	$wpdb->query( "UPDATE $table SET type = 'payment' WHERE type = 'income'" );
	$wpdb->query( "UPDATE $table SET payment_mode = payment_method" );
	$wpdb->query( "UPDATE $table SET date = payment_date" );
	$wpdb->query( "UPDATE $table SET currency = currency_code" );
	$wpdb->query( "UPDATE $table SET note = description" );
	$wpdb->query( "UPDATE $table SET exchange_rate = currency_rate" );
	$wpdb->query( "UPDATE $table SET created_at = date_created" );
	$wpdb->query( "UPDATE $table SET uuid = UUID()" );
	$wpdb->query( "UPDATE $table JOIN (SELECT @rank := 0) r SET number=CONCAT('PAY-',LPAD(@rank:=@rank+1, 5, '0')) WHERE type='payment' AND number IS NULL OR number = ''" );
	$wpdb->query( "UPDATE $table JOIN (SELECT @rank := 0) r SET number=CONCAT('EXP-',LPAD(@rank:=@rank+1, 5, '0')) WHERE type='expense' AND number IS NULL OR number = ''" );
	$wpdb->query( "ALTER TABLE $table DROP payment_date" );
	$wpdb->query( "ALTER TABLE $table DROP currency_code" );
	$wpdb->query( "ALTER TABLE $table DROP currency_rate" );
	$wpdb->query( "ALTER TABLE $table DROP payment_method" );
	$wpdb->query( "ALTER TABLE $table DROP date_created" );
	$wpdb->query( "ALTER TABLE $table DROP description" );

	// Documents Items.
	$table = $wpdb->prefix . 'ea_document_items';
	$wpdb->query( "UPDATE $table SET name = item_name" );
	$wpdb->query( "UPDATE $table SET created_at = date_created" );
	$wpdb->query( "ALTER TABLE $table DROP `item_name`" );
	$wpdb->query( "ALTER TABLE $table DROP `date_created`" );
	// todo need to adjust tax rate and decide about storing currency.

	// Documents.
	$table = $wpdb->prefix . 'ea_documents';
	$wpdb->query( "UPDATE $table SET number = document_number" );
	$wpdb->query( "UPDATE $table SET reference = order_number" );
	$wpdb->query( "UPDATE $table SET currency = currency_code" );
	$wpdb->query( "UPDATE $table SET exchange_rate = currency_rate" );
	$wpdb->query( "UPDATE $table SET tax = total_tax" );
	$wpdb->query( "UPDATE $table SET discount = total_discount" );
	$wpdb->query( "UPDATE $table SET created_at = date_created" );
	$wpdb->query( "UPDATE $table SET uuid = UUID()" );

	$wpdb->query( "ALTER TABLE $table DROP `document_number`" );
	$wpdb->query( "ALTER TABLE $table DROP `order_number`" );
	$wpdb->query( "ALTER TABLE $table DROP `currency_code`" );
	$wpdb->query( "ALTER TABLE $table DROP `currency_rate`" );
	$wpdb->query( "ALTER TABLE $table DROP `total_tax`" );
	$wpdb->query( "ALTER TABLE $table DROP `total_discount`" );
	$wpdb->query( "ALTER TABLE $table DROP `total_shipping`" );
	$wpdb->query( "ALTER TABLE $table DROP `total_fees`" );
	$wpdb->query( "ALTER TABLE $table DROP `tax_inclusive`" );
	$wpdb->query( "ALTER TABLE $table DROP `date_created`" );
	$wpdb->query( "ALTER TABLE $table DROP `category_id`" );
	$wpdb->query( "ALTER TABLE $table DROP `key`" );
	$documents = $wpdb->get_results( "SELECT id, address FROM $table WHERE address IS NOT NULL AND address != ''" );
	foreach ( $documents as $document ) {
		$address = maybe_unserialize( $document->address );
		$address = wp_parse_args(
			$address,
			array(
				'name'       => '',
				'company'    => '',
				'street'     => '',
				'city'       => '',
				'state'      => '',
				'postcode'   => '',
				'country'    => '',
				'email'      => '',
				'phone'      => '',
				'vat_number' => '',
			)
		);
		$mapping = array(
			'contact_name'     => 'name',
			'contact_company'  => 'company',
			'contact_address'  => 'street',
			'contact_city'     => 'city',
			'contact_state'    => 'state',
			'contact_postcode' => 'postcode',
			'contact_country'  => 'country',
			'contact_email'    => 'email',
			'contact_phone'    => 'phone',
			'contact_tax'      => 'vat_number',
		);

		// map to new fields.
		$data = array();
		foreach ( $mapping as $new => $old ) {
			$data[ $new ] = $address[ $old ];
		}
		// take only keys which have values.
		$data = array_filter( $data );
		if ( empty( $data ) ) {
			continue;
		}
		// update the document.
		$wpdb->update( $table, $data, array( 'id' => $document->id ) );
	}
	$wpdb->query( "ALTER TABLE $table DROP `address`" );

	wp_cache_flush();
}
