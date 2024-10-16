<?php
/**
 * Updates functions.
 *
 * @since 1.0.0
 * @package EverAccounting\Functions
 */

defined( 'ABSPATH' ) || exit;

/**
 * Update settings to 1.2.0
 */
function eac_update_120_settings() {
	$settings     = get_option( 'eaccounting_settings', array() );
	$settings_map = array(
		'eac_business_name'                => 'company_name',
		'eac_business_email'               => 'company_email',
		'eac_business_phone'               => 'company_phone',
		'eac_business_logo'                => 'company_logo',
		'eac_business_tax_number'          => 'company_vat_number',
		'eac_base_currency'                => 'default_currency',
		'eac_year_start_date'              => 'financial_year_start',
		'eac_business_address'             => 'company_address',
		'eac_business_city'                => 'company_city',
		'eac_business_state'               => 'company_state',
		'eac_business_postcode'            => 'company_postcode',
		'eac_business_country'             => 'company_country',
		'eac_tax_enabled'                  => 'tax_enabled',
		'eac_tax_subtotal_rounding'        => 'tax_subtotal_rounding',
		'eac_tax_display_totals'           => 'tax_display_totals',
		'eac_default_sales_account_id'     => 'default_account',
		'eac_default_sales_payment_method' => 'default_payment_method',
		'eac_invoice_prefix'               => 'invoice_prefix',
		'eac_invoice_digits'               => 'invoice_digit',
		'eac_invoice_due'                  => 'invoice_due',
		'eac_invoice_note'                 => 'invoice_notes',
		'eac_invoice_item_label'           => 'invoice_item_label',
		'eac_invoice_price_label'          => 'invoice_price_label',
		'eac_invoice_quantity_label'       => 'invoice_quantity_label',
		'eac_bill_prefix'                  => 'bill_prefix',
		'eac_bill_digits'                  => 'bill_digit',
		'eac_bill_note'                    => 'bill_notes',
		'eac_bill_due'                     => 'bill_due',
		'eac_bill_item_label'              => 'bill_item_label',
		'eac_bill_price_label'             => 'bill_price_label',
		'eac_bill_quantity_label'          => 'bill_quantity_label',
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
}

/**
 * Update Accounts to 1.2.0
 */
function eac_update_120_accounts() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table = $wpdb->prefix . 'ea_accounts';
	$wpdb->query( "UPDATE $table SET created_at = date_created, currency = currency_code" );
	$wpdb->query(
		"ALTER TABLE $table
	DROP COLUMN date_created,
	DROP COLUMN currency_code,
	DROP COLUMN enabled,
	DROP COLUMN thumbnail_id,
	DROP COLUMN creator_id,
	DROP COLUMN bank_name,
	DROP COLUMN bank_phone,
	DROP COLUMN bank_address,
	MODIFY COLUMN type VARCHAR(50) NOT NULL DEFAULT 'account' AFTER id"
	);
}

/**
 * Update Categories to 1.2.0
 */
function eac_update_120_categories() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table = $wpdb->prefix . 'ea_categories';
	$wpdb->query( "UPDATE $table SET type = 'payment' WHERE type = 'income'" );
	$wpdb->query( "UPDATE $table SET created_at = date_created" );
	$wpdb->query( "DELETE FROM $table WHERE type = 'other'" );
	$wpdb->query(
		"ALTER TABLE $table DROP COLUMN color,
	DROP COLUMN enabled,
	DROP COLUMN date_created,
	MODIFY COLUMN type VARCHAR(50) NOT NULL AFTER id"
	);
}

/**
 * Update Contacts to 1.2.0
 */
function eac_update_120_contacts() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table = $wpdb->prefix . 'ea_contacts';
	$wpdb->query( "UPDATE $table SET tax_number = vat_number" );
	$wpdb->query( "UPDATE $table SET currency = currency_code" );
	$wpdb->query( "UPDATE $table SET created_at = date_created" );
	$wpdb->query(
		"ALTER TABLE $table
    DROP COLUMN vat_number,
    DROP COLUMN currency_code,
    DROP COLUMN date_created,
    DROP COLUMN attachment,
    DROP COLUMN enabled,
    DROP COLUMN creator_id,
    DROP COLUMN thumbnail_id,
    DROP COLUMN user_id,
    DROP COLUMN street,
    DROP COLUMN birth_date,
    MODIFY COLUMN type VARCHAR(30) DEFAULT 'customer' AFTER id,
    MODIFY COLUMN website VARCHAR(191) DEFAULT NULL AFTER phone,
    MODIFY COLUMN country VARCHAR(3) DEFAULT NULL AFTER postcode"
	);

	// Contacts.
	$table = $wpdb->prefix . 'ea_contactmeta';
	$wpdb->query( "UPDATE $table SET ea_contact_id = contact_id" );
	$wpdb->query(
		"ALTER TABLE $table
    DROP COLUMN contact_id,
    MODIFY COLUMN ea_contact_id INT(11) NOT NULL AFTER meta_id"
	);
}

/**
 * Update Documents to 1.2.0
 */
function eac_update_120_documents() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table = $wpdb->prefix . 'ea_documents';
	$wpdb->query(
		"UPDATE $table
    SET number = document_number,
        reference = order_number,
        currency = currency_code,
        exchange_rate = currency_rate,
        tax = total_tax,
        discount_value = discount,
        discount = total_discount,
        issued_at = issue_date,
        due_at = due_date,
        paid_at = payment_date,
        created_at = date_created,
        uuid = UUID()"
	);
	$wpdb->query(
		"ALTER TABLE $table
    DROP COLUMN document_number,
    DROP COLUMN order_number,
    DROP COLUMN currency_code,
    DROP COLUMN currency_rate,
    DROP COLUMN total_tax,
    DROP COLUMN total_discount,
    DROP COLUMN total_shipping,
    DROP COLUMN total_fees,
    DROP COLUMN tax_inclusive,
    DROP COLUMN date_created,
    DROP COLUMN category_id,
    DROP COLUMN issue_date,
    DROP COLUMN due_date,
    DROP COLUMN payment_date,
    DROP COLUMN `key`,
    DROP COLUMN creator_id,
    DROP COLUMN parent_id,
    MODIFY COLUMN number VARCHAR(30) NOT NULL AFTER status,
    MODIFY COLUMN reference VARCHAR(191) DEFAULT NULL AFTER number,
    MODIFY COLUMN discount DOUBLE(15, 4) DEFAULT 0 AFTER subtotal,
    MODIFY COLUMN tax DOUBLE(15, 4) DEFAULT 0 AFTER discount,
    MODIFY COLUMN balance DOUBLE(15, 4) DEFAULT 0 AFTER total,
    MODIFY COLUMN discount_type ENUM('fixed', 'percentage') DEFAULT 'fixed' AFTER balance,
    MODIFY COLUMN discount_value DOUBLE(15, 4) DEFAULT 0 AFTER balance,
    MODIFY COLUMN contact_id BIGINT(20) UNSIGNED NOT NULL AFTER terms,
    MODIFY COLUMN attachment_id BIGINT(20) UNSIGNED DEFAULT NULL AFTER exchange_rate"
	);
	$documents = $wpdb->get_results( "SELECT id, billing_address address FROM $table WHERE billing_address IS NOT NULL AND billing_address != ''" );
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
			'name'       => 'name',
			'company'    => 'company',
			'address'    => 'street',
			'city'       => 'city',
			'state'      => 'state',
			'postcode'   => 'postcode',
			'country'    => 'country',
			'email'      => 'email',
			'phone'      => 'phone',
			'tax_number' => 'vat_number',
		);

		$data = array();
		foreach ( $mapping as $new => $old ) {
			$data[ $new ] = $address[ $old ];
		}
		$data = array_filter( $data );
		if ( empty( $data ) ) {
			continue;
		}
		$wpdb->update( $table, $data, array( 'id' => $document->id ) );
	}

	// drop the billing_address column.
	$wpdb->query( "ALTER TABLE $table DROP COLUMN billing_address" );

	// Documents Items.
	$table = $wpdb->prefix . 'ea_document_items';
	$wpdb->query(
		"UPDATE $table
    	SET
    	    name = item_name,
        	currency = currency_code,
        	created_at = date_created"
	);
	$wpdb->query(
		"ALTER TABLE $table
    DROP COLUMN item_name,
    DROP COLUMN extra,
    DROP COLUMN tax_rate,
    DROP COLUMN currency_code,
    DROP COLUMN date_created,
    MODIFY COLUMN unit VARCHAR(20) DEFAULT NULL AFTER item_id,
    MODIFY COLUMN description VARCHAR(160) DEFAULT NULL AFTER item_id,
    MODIFY COLUMN name VARCHAR(191) NOT NULL AFTER item_id,
    MODIFY COLUMN type VARCHAR(20) NOT NULL DEFAULT 'standard' AFTER item_id"
	);
}

/**
 * Update Transactions to 1.2.0
 */
function eac_update_120_transactions() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table = $wpdb->prefix . 'ea_transactions';
	$wpdb->query( "UPDATE $table SET type = 'payment' WHERE type = 'income'" );
	$wpdb->query( "UPDATE $table SET paid_at = payment_date, currency = currency_code, note = description, exchange_rate = currency_rate, created_at = date_created, uuid = UUID()" );
	$wpdb->query( "UPDATE $table JOIN (SELECT @rank := 0) r SET number=CONCAT('PAY-',LPAD(@rank:=@rank+1, 5, '0')) WHERE type='payment' AND number = ''" );
	$wpdb->query( "UPDATE $table JOIN (SELECT @rank := 0) r SET number=CONCAT('EXP-',LPAD(@rank:=@rank+1, 5, '0')) WHERE type='expense' AND number = ''" );
	$wpdb->query(
		"ALTER TABLE $table
    DROP payment_date,
    DROP currency_code,
    DROP currency_rate,
    DROP date_created,
    DROP description,
    DROP creator_id,
	MODIFY status VARCHAR(20) NOT NULL DEFAULT 'completed' AFTER type,
	MODIFY number VARCHAR(30) NOT NULL AFTER status,
	MODIFY paid_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER number,
	MODIFY currency VARCHAR(3) NOT NULL DEFAULT 'USD' AFTER amount,
	MODIFY exchange_rate DOUBLE(15, 8) NOT NULL DEFAULT 1.0 AFTER currency,
	MODIFY reference VARCHAR(191) DEFAULT NULL AFTER exchange_rate,
	MODIFY note TEXT DEFAULT NULL AFTER reference,
	MODIFY payment_method VARCHAR(100) DEFAULT NULL AFTER note,
	MODIFY author_id BIGINT(20) UNSIGNED DEFAULT NULL AFTER attachment_id"
	);

	$table = $wpdb->prefix . 'ea_transfers';
	$wpdb->query( "UPDATE $table SET payment_id = income_id, created_at = date_created" );
	$wpdb->query( "ALTER TABLE $table DROP date_created, DROP income_id, DROP date_created" );
	$wpdb->query(
		"UPDATE $table
	JOIN {$wpdb->prefix }ea_transactions AS payment ON payment.id = $table.payment_id
	JOIN {$wpdb->prefix }ea_transactions AS expense ON expense.id = $table.expense_id
	SET $table.from_account_id = payment.account_id,
	$table.to_account_id = expense.account_id,
	$table.from_exchange_rate = expense.exchange_rate,
	$table.to_exchange_rate = payment.exchange_rate,
	$table.amount = payment.amount,
	$table.currency = payment.currency,
	$table.payment_method = payment.payment_method,
	$table.reference = payment.reference,
	$table.note = payment.note"
	);
}

/**
 * Update items to 1.2.0
 */
function eac_update_120_misc() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table = $wpdb->prefix . 'ea_items';
	$wpdb->query( "UPDATE $table SET created_at = date_created" );
	$wpdb->query(
		"UPDATE $table
    SET
        price = sale_price,
        cost = purchase_price,
        created_at = date_created"
	);
	$wpdb->query(
		"ALTER TABLE $table
    DROP COLUMN sale_price,
    DROP COLUMN purchase_price,
    DROP COLUMN sku,
    DROP COLUMN quantity,
    DROP COLUMN sales_tax,
    DROP COLUMN purchase_tax,
    DROP COLUMN enabled,
    DROP COLUMN date_created"
	);

	// Notes.
	$table = $wpdb->prefix . 'ea_notes';
	$wpdb->query(
		"UPDATE $table
    SET
        parent_type = type,
        content = note,
        created_at = date_created"
	);
	$wpdb->query(
		"ALTER TABLE $table
    DROP COLUMN `type`,
    DROP COLUMN note,
    DROP COLUMN note_metadata,
    DROP COLUMN extra"
	);

	// accounts having opening balance insert as payments.
	$accounts = $wpdb->get_results( "SELECT id, opening_balance, currency, created_at FROM {$wpdb->prefix}ea_accounts WHERE opening_balance > 0" );
	foreach ( $accounts as $account ) {
		$payment = new \EverAccounting\Models\Payment(
			array(
				'account_id'    => $account->id,
				'amount'        => $account->opening_balance,
				'currency'      => $account->currency,
				'exchange_rate' => EAC()->currencies->get_rate( $account->currency ),
				'paid_at'       => wp_date( 'Y-m-d H:i:s' ),
				'note'          => __( 'Opening Balance', 'wp-ever-accounting' ),
			)
		);

		$payment->save();
	}

	// now drop the opening balance column.
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}ea_accounts DROP COLUMN opening_balance" );

	wp_cache_flush();
}
