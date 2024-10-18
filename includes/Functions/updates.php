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
	error_log( __METHOD__ );
}

/**
 * Update Transactions to 1.2.0
 */
function eac_update_120_transactions() {
	global $wpdb;
	$table = $wpdb->prefix . 'ea_transactions';
	$wpdb->query( "UPDATE $table SET type = 'payment' WHERE type = 'income'" );
	$wpdb->query( "UPDATE $table SET currency = currency_code, note = description, exchange_rate = currency_rate, uuid = UUID(), author_id = creator_id" );
	$wpdb->query( "UPDATE $table SET author_id = creator_id" );
	$wpdb->query( "UPDATE $table JOIN (SELECT @rank := 0) r SET number=CONCAT('PAY-',LPAD(@rank:=@rank+1, 5, '0')) WHERE type='payment' AND number = ''" );
	$wpdb->query( "UPDATE $table JOIN (SELECT @rank := 0) r SET number=CONCAT('EXP-',LPAD(@rank:=@rank+1, 5, '0')) WHERE type='expense' AND number = ''" );

	$wpdb->query( "ALTER TABLE $table DROP currency_code" );
	$wpdb->query( "ALTER TABLE $table DROP currency_rate" );
	$wpdb->query( "ALTER TABLE $table DROP description" );
	$wpdb->query( "ALTER TABLE $table DROP reconciled" );
	$wpdb->query( "ALTER TABLE $table DROP creator_id" );

	$wpdb->query( "ALTER TABLE $table MODIFY status VARCHAR(20) NOT NULL DEFAULT 'completed' AFTER type" );
	$wpdb->query( "ALTER TABLE $table MODIFY number VARCHAR(30) NOT NULL AFTER status" );
	$wpdb->query( "ALTER TABLE $table MODIFY currency VARCHAR(3) NOT NULL DEFAULT 'USD' AFTER amount" );
	$wpdb->query( "ALTER TABLE $table MODIFY exchange_rate DOUBLE(15, 8) NOT NULL DEFAULT 1.0 AFTER currency" );
	$wpdb->query( "ALTER TABLE $table MODIFY reference VARCHAR(191) DEFAULT NULL AFTER exchange_rate" );
	$wpdb->query( "ALTER TABLE $table MODIFY note TEXT DEFAULT NULL AFTER reference" );
	$wpdb->query( "ALTER TABLE $table MODIFY payment_method VARCHAR(100) DEFAULT NULL AFTER note" );
	$wpdb->query( "ALTER TABLE $table MODIFY date_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER uuid" );

	// select transaction where document id is not null and update transaction_id on document table.
	// set all the transactions that have document id as pending.
	$wpdb->query( "UPDATE $table JOIN {$wpdb->prefix}ea_documents AS document ON document.id = $table.document_id SET $table.status = 'pending'" );
	$wpdb->query( "UPDATE {$wpdb->prefix}ea_documents AS document JOIN $table AS t ON t.document_id = document.id SET document.transaction_id = t.id" );
	$wpdb->query( "UPDATE $table SET editable = 0, status = 'completed' WHERE document_id IS NOT NULL" );
	$wpdb->query( "ALTER TABLE $table DROP document_id" );

	$table = $wpdb->prefix . 'ea_transfers';
	$wpdb->query( "UPDATE $table SET payment_id = income_id" );
	$wpdb->query( "ALTER TABLE $table DROP income_id" );
	$wpdb->query( "ALTER TABLE $table DROP creator_id" );
	$wpdb->query( "ALTER TABLE $table MODIFY date_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER note" );
	$wpdb->query(
		"UPDATE $table
	JOIN {$wpdb->prefix }ea_transactions AS payment ON payment.id = $table.payment_id
	JOIN {$wpdb->prefix }ea_transactions AS expense ON expense.id = $table.expense_id
	SET $table.transfer_date = expense.payment_date,
	$table.amount = expense.amount,
	$table.currency = expense.currency,
	$table.payment_method = expense.payment_method,
	$table.reference = expense.reference,
	$table.note = expense.note"
	);

	// Now any transaction that id is either in expense_id or payment_id set editable to 0.
	$table = $wpdb->prefix . 'ea_transactions';
	$wpdb->query(
		"UPDATE $table JOIN {$wpdb->prefix }ea_transfers AS transfer ON transfer.expense_id = $table.id OR transfer.payment_id = $table.id SET $table.editable = 0"
	);
}

/**
 * Update Documents to 1.2.0
 */
function eac_update_120_documents() {
	global $wpdb;
	$table = $wpdb->prefix . 'ea_documents';
	$wpdb->query( "UPDATE $table SET number = document_number" );
	$wpdb->query( "UPDATE $table SET reference = order_number" );
	$wpdb->query( "UPDATE $table SET currency = currency_code" );
	$wpdb->query( "UPDATE $table SET exchange_rate = currency_rate" );
	$wpdb->query( "UPDATE $table SET tax = total_tax" );
	$wpdb->query( "UPDATE $table SET discount_value = discount" );
	$wpdb->query( "UPDATE $table SET discount = total_discount" );
	$wpdb->query( "UPDATE $table SET author_id = creator_id" );
	$wpdb->query( "UPDATE $table SET uuid = UUID()" );

	$wpdb->query( "ALTER TABLE $table DROP COLUMN document_number" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN order_number" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN currency_code" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN currency_rate" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN total_tax" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN total_discount" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN total_shipping" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN total_fees" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN tax_inclusive" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN category_id" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN `key`" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN creator_id" );

	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN number VARCHAR(30) NOT NULL AFTER status" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN reference VARCHAR(191) DEFAULT NULL AFTER number" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN sent_date DATETIME DEFAULT NULL AFTER due_date" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN discount DOUBLE(15, 4) DEFAULT 0 AFTER subtotal" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN tax DOUBLE(15, 4) DEFAULT 0 AFTER discount" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN discount_value DOUBLE(15, 4) DEFAULT 0 AFTER payment_date" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN date_created DATETIME  NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER uuid" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN attachment_id BIGINT(20) UNSIGNED DEFAULT NULL AFTER transaction_id" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN note TEXT DEFAULT NULL AFTER contact_tax_number" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN terms TEXT DEFAULT NULL AFTER note" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN contact_id BIGINT(20) UNSIGNED NOT NULL AFTER attachment_id" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN  parent_id BIGINT(20) UNSIGNED DEFAULT NULL AFTER author_id" );

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
			'contact_name'       => 'name',
			'contact_company'    => 'company',
			'contact_address'    => 'street',
			'contact_city'       => 'city',
			'contact_state'      => 'state',
			'contact_postcode'   => 'postcode',
			'contact_country'    => 'country',
			'contact_email'      => 'email',
			'contact_phone'      => 'phone',
			'contact_tax_number' => 'vat_number',
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
	$wpdb->query( "ALTER TABLE $table DROP COLUMN address" );

	// Documents Items.
	$table = $wpdb->prefix . 'ea_document_items';
	$wpdb->query( "UPDATE $table SET name = item_name" );
	$wpdb->query( "UPDATE $table SET currency = currency_code" );

	$wpdb->query( "ALTER TABLE $table DROP COLUMN item_name" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN extra" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN tax_rate" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN currency_code" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN date_created" );

	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN unit VARCHAR(20) DEFAULT NULL AFTER item_id" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN description VARCHAR(160) DEFAULT NULL AFTER item_id" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN name VARCHAR(191) NOT NULL AFTER item_id" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN type VARCHAR(20) NOT NULL DEFAULT 'standard' AFTER item_id" );
}

/**
 * Update items to 1.2.0
 */
function eac_update_120_accounts() {
	global $wpdb;
	$table = $wpdb->prefix . 'ea_accounts';
	$wpdb->query( "UPDATE $table SET currency = currency_code" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN creator_id" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN currency_code" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN enabled" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN thumbnail_id" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN bank_name" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN bank_phone" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN bank_address" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN type VARCHAR(50) NOT NULL DEFAULT 'account' AFTER id" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN date_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER currency" );

	// accounts having opening balance insert as payments.
	$accounts = $wpdb->get_results( "SELECT id, opening_balance, currency, date_created FROM {$wpdb->prefix}ea_accounts WHERE opening_balance != ''" );
	foreach ( $accounts as $account ) {
		$payment = new \EverAccounting\Models\Payment();
		$payment->fill(
			array(
				'account_id'    => $account->id,
				'amount'        => $account->opening_balance,
				'currency'      => $account->currency,
				'exchange_rate' => EAC()->currencies->get_rate( $account->currency ),
				'payment_date'  => ! empty( $account->date_created ) ? $account->date_created : current_time( 'mysql' ),
				'note'          => __( 'Opening Balance', 'wp-ever-accounting' ),
			)
		);
		$payment->save();
	}

	// now drop the opening balance column.
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}ea_accounts DROP COLUMN opening_balance" );

	// now query all the accounts and update the balance.
	$accounts = EAC()->accounts->query( array( 'limit' => -1 ) );
	foreach ( $accounts as $account ) {
		$account->update_balance();
	}

	wp_cache_flush();
}

/**
 * Update Categories to 1.2.0
 */
function eac_update_120_categories() {
	global $wpdb;
	$table = $wpdb->prefix . 'ea_categories';
	$wpdb->query( "UPDATE $table SET type = 'payment' WHERE type = 'income'" );
	$wpdb->query( "DELETE FROM $table WHERE type = 'other'" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN type VARCHAR(50) NOT NULL AFTER id" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN color" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN enabled" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN date_created" );
}

/**
 * Update Contacts to 1.2.0
 */
function eac_update_120_contacts() {
	global $wpdb;
	$table = $wpdb->prefix . 'ea_contacts';
	$wpdb->query( "UPDATE $table SET tax_number = vat_number" );
	$wpdb->query( "UPDATE $table SET currency = currency_code" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN type VARCHAR(30) DEFAULT 'customer' AFTER id" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN website VARCHAR(191) DEFAULT NULL AFTER phone" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN country VARCHAR(3) DEFAULT NULL AFTER postcode" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN vat_number" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN currency_code" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN attachment" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN enabled" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN creator_id" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN thumbnail_id" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN user_id" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN street" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN birth_date" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN date_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER created_via" );

	// Contacts.
	$table = $wpdb->prefix . 'ea_contactmeta';
	$wpdb->query( "UPDATE $table SET ea_contact_id = contact_id" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN contact_id" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN ea_contact_id INT(11) NOT NULL AFTER meta_id" );
	error_log( __METHOD__ );
}


/**
 * Update Items to 1.2.0
 */
function eac_update_120_items() {
	global $wpdb;
	$table = $wpdb->prefix . 'ea_items';
	$wpdb->query( "UPDATE $table SET price = sale_price, cost = purchase_price" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN sale_price" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN purchase_price" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN sku" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN quantity" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN sales_tax" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN purchase_tax" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN enabled" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN creator_id" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN thumbnail_id" );

	$wpdb->query( "ALTER TABLE $table MODIFY type VARCHAR(50) NOT NULL AFTER id" );
	$wpdb->query( "ALTER TABLE $table MODIFY unit VARCHAR(50) DEFAULT NULL AFTER description" );
	$wpdb->query( "ALTER TABLE $table MODIFY price DECIMAL(10,2) DEFAULT NULL AFTER unit" );
	$wpdb->query( "ALTER TABLE $table MODIFY cost DOUBLE(15, 4) NOT NULL AFTER price" );
	$wpdb->query( "ALTER TABLE $table MODIFY tax_ids VARCHAR(191) DEFAULT NULL AFTER cost" );
}

/**
 * Update Notes to 1.2.0
 */
function eac_update_120_notes() {
	global $wpdb;
	$table = $wpdb->prefix . 'ea_notes';
	$wpdb->query( "UPDATE $table SET author_id = creator_id" );
	$wpdb->query( "UPDATE $table SET parent_type = type" );
	$wpdb->query( "UPDATE $table SET content = note" );

	$wpdb->query( "ALTER TABLE $table DROP COLUMN `type`" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN note" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN extra" );
	$wpdb->query( "ALTER TABLE $table DROP COLUMN creator_id" );
	$wpdb->query( "ALTER TABLE $table MODIFY date_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER author_id" );
}

/**
 * Update Payments to 1.2.0
 */
function eac_update_120_misc() {
	global $wpdb;
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ea_currencies" );
	// if any document has partial status but no payment then set status to draft.
	$wpdb->query( "UPDATE {$wpdb->prefix}ea_documents SET status = 'draft' WHERE transaction_id IS NULL" );
	// if any document has partial status and has transaction then set status to paid.
	$wpdb->query( "UPDATE {$wpdb->prefix}ea_documents SET status = 'paid' WHERE transaction_id IS NOT NULL" );
}
