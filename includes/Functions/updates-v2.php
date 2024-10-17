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
 * eac_update_120_migration.
 *
 * @param string $table Table name.
 * @param array  $updates Array of updates.
 * @param array  $deletes Array of deletes.
 *
 * @return void
 */
function eac_update_120_migration( $table, $updates, $deletes ) {
	global $wpdb;
	$wpdb->hide_errors();
	$columns = wp_list_pluck( $wpdb->get_results( "DESCRIBE `$table`" ), 'Field' );

	foreach ( $updates as $new => $old ) {
		if ( in_array( $old, $columns, true ) ) {
			$wpdb->query( "UPDATE $table SET `$new` = `$old`" );
		}
	}

	foreach ( $deletes as $column ) {
		if ( in_array( $column, $columns, true ) ) {
			$wpdb->query( "ALTER TABLE $table DROP COLUMN `$column`" );
		}
	}
}

/**
 * Update Accounts to 1.2.0
 */
function eac_update_120_accounts() {
	global $wpdb;
	$table = $wpdb->prefix . 'ea_accounts';
	eac_update_120_migration(
		$table,
		array(
			'created_at' => 'date_created',
			'currency'   => 'currency_code',
			'balance'    => 'current_balance',
		),
		array(
			'currency_code',
			'date_created',
			'current_balance',
			'enabled',
			'creator_id',
			'bank_name',
			'bank_phone',
			'bank_address',
		)
	);
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN type VARCHAR(50) NOT NULL DEFAULT 'account' AFTER id" );
}

/**
 * Update Categories to 1.2.0
 */
function eac_update_120_categories() {
	global $wpdb;
	$table = $wpdb->prefix . 'ea_categories';
	eac_update_120_migration(
		$table,
		array(
			'created_at' => 'date_created',
		),
		array(
			'color',
			'enabled',
			'date_created',
			'creator_id',
		)
	);
	$wpdb->query( "UPDATE $table SET type = 'payment' WHERE type = 'income'" );
	$wpdb->query( "DELETE FROM $table WHERE type = 'other'" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN type VARCHAR(50) NOT NULL AFTER id" );
}

/**
 * Update Contacts to 1.2.0
 */
function eac_update_120_contacts() {
	global $wpdb;
	$table = $wpdb->prefix . 'ea_contacts';

	eac_update_120_migration(
		$table,
		array(
			'created_at' => 'date_created',
			'currency'   => 'currency_code',
			'vat_number' => 'tax_number',
		),
		array(
			'vat_number',
			'currency_code',
			'date_created',
			'attachment',
			'enabled',
			'creator_id',
			'thumbnail_id',
			'user_id',
			'street',
			'birth_date',
		)
	);
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN type VARCHAR(30) DEFAULT 'customer' AFTER id" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN website VARCHAR(191) DEFAULT NULL AFTER phone" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN country VARCHAR(3) DEFAULT NULL AFTER postcode" );

	// Contacts.
	$table = $wpdb->prefix . 'ea_contactmeta';
	eac_update_120_migration(
		$table,
		array(
			'ea_contact_id' => 'contact_id',
		),
		array(
			'contact_id',
		)
	);
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN ea_contact_id INT(11) NOT NULL AFTER meta_id" );
	error_log( __METHOD__ );
}

/**
 * Update Documents to 1.2.0
 */
function eac_update_120_documents() {
	global $wpdb;
	$table = $wpdb->prefix . 'ea_documents';
	eac_update_120_migration(
		$table,
		array(
			'number'         => 'document_number',
			'reference'      => 'order_number',
			'currency'       => 'currency_code',
			'exchange_rate'  => 'currency_rate',
			'tax'            => 'total_tax',
			'discount_value' => 'discount',
			'discount'       => 'total_discount',
			'issued_at'      => 'issue_date',
			'due_at'         => 'due_date',
			'paid_at'        => 'payment_date',
			'created_at'     => 'date_created',
		),
		array(
			'document_number',
			'order_number',
			'currency_code',
			'currency_rate',
			'total_tax',
			'total_discount',
			'total_shipping',
			'total_fees',
			'tax_inclusive',
			'date_created',
			'category_id',
			'issue_date',
			'due_date',
			'payment_date',
			'key',
			'creator_id',
			'parent_id'
		)
	);
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN number VARCHAR(30) NOT NULL AFTER status" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN reference VARCHAR(191) DEFAULT NULL AFTER number" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN discount DOUBLE(15, 4) DEFAULT 0 AFTER subtotal" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN tax DOUBLE(15, 4) DEFAULT 0 AFTER discount" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN balance DOUBLE(15, 4) DEFAULT 0 AFTER total" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN discount_type ENUM('fixed', 'percentage') DEFAULT 'fixed' AFTER balance" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN discount_value DOUBLE(15, 4) DEFAULT 0 AFTER balance" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN contact_id BIGINT(20) UNSIGNED NOT NULL AFTER terms" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN attachment_id BIGINT(20) UNSIGNED DEFAULT NULL AFTER exchange_rate" );

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
	eac_update_120_migration(
		$table,
		array( 'name' => 'item_name', 'currency' => 'currency_code', 'created_at' => 'date_created' ),
		array(
			'item_name',
			'extra',
			'tax_rate',
			'currency_code',
			'date_created'
		)
	);
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN unit VARCHAR(20) DEFAULT NULL AFTER item_id" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN description VARCHAR(160) DEFAULT NULL AFTER item_id" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN name VARCHAR(191) NOT NULL AFTER item_id" );
	$wpdb->query( "ALTER TABLE $table MODIFY COLUMN type VARCHAR(20) NOT NULL DEFAULT 'standard' AFTER item_id" );
	error_log( __METHOD__ );
}

/**
 * Update Transactions to 1.2.0
 */
function eac_update_120_transactions() {
	global $wpdb;
	$wpdb->hide_errors();
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table = $wpdb->prefix . 'ea_transactions';
	eac_update_120_migration(
		$table,
		array( 'paid_at' => 'payment_date', 'currency' => 'currency_code', 'note' => 'description', 'exchange_rate' => 'currency_rate', 'created_at' => 'date_created', 'author_id' => 'creator_id' ),
		array( 'payment_date', 'currency_code', 'currency_rate', 'date_created', 'description', 'creator_id' )
	);
	$wpdb->query( "UPDATE $table SET uuid = UUID()" );
	$wpdb->query( "UPDATE $table SET type = 'payment' WHERE type = 'income'" );
	$wpdb->query( "UPDATE $table JOIN (SELECT @rank := 0) r SET number=CONCAT('PAY-',LPAD(@rank:=@rank+1, 5, '0')) WHERE type='payment' AND number = ''" );
	$wpdb->query( "UPDATE $table JOIN (SELECT @rank := 0) r SET number=CONCAT('EXP-',LPAD(@rank:=@rank+1, 5, '0')) WHERE type='expense' AND number = ''" );
	$wpdb->query( "ALTER TABLE $table MODIFY status VARCHAR(20) NOT NULL DEFAULT 'completed' AFTER type" );
	$wpdb->query( "ALTER TABLE $table MODIFY number VARCHAR(30) NOT NULL AFTER status" );
	$wpdb->query( "ALTER TABLE $table MODIFY paid_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER number" );
	$wpdb->query( "ALTER TABLE $table MODIFY currency VARCHAR(3) NOT NULL DEFAULT 'USD' AFTER amount" );
	$wpdb->query( "ALTER TABLE $table MODIFY exchange_rate DOUBLE(15, 8) NOT NULL DEFAULT 1.0 AFTER currency" );
	$wpdb->query( "ALTER TABLE $table MODIFY reference VARCHAR(191) DEFAULT NULL AFTER exchange_rate" );
	$wpdb->query( "ALTER TABLE $table MODIFY note TEXT DEFAULT NULL AFTER reference" );
	$wpdb->query( "ALTER TABLE $table MODIFY payment_method VARCHAR(100) DEFAULT NULL AFTER note" );
	$wpdb->query( "ALTER TABLE $table MODIFY author_id BIGINT(20) UNSIGNED DEFAULT NULL AFTER attachment_id" );


	$table = $wpdb->prefix . 'ea_transfers';
	eac_update_120_migration(
		$table,
		array( 'created_at' => 'date_created', 'payment_id' => 'income_id' ),
		array( 'date_created', 'creator_id', 'income_id' )
	);
	$wpdb->query(
		"UPDATE $table
	JOIN {$wpdb->prefix }ea_transactions AS payment ON payment.id = $table.payment_id
	JOIN {$wpdb->prefix }ea_transactions AS expense ON expense.id = $table.expense_id
	SET $table.from_account_id = expense.account_id,
	$table.to_account_id = payment.account_id,
	$table.amount = expense.amount,
	$table.currency = expense.currency,
	$table.payment_method = payment.payment_method,
	$table.reference = payment.reference,
	$table.note = payment.note"
	);

	error_log( __METHOD__ );
}

/**
 * Update Items to 1.2.0
 */
function eac_update_120_items() {
	global $wpdb;
	$wpdb->hide_errors();
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table = $wpdb->prefix . 'ea_items';
	eac_update_120_migration(
		$table,
		array( 'created_at' => 'date_created', 'price' => 'sale_price', 'cost' => 'purchase_price' ),
		array(
			'sale_price',
			'purchase_price',
			'sku',
			'quantity',
			'sales_tax',
			'purchase_tax',
			'enabled',
			'date_created',
			'creator_id'
		)
	);
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
	eac_update_120_migration(
		$table,
		array( 'author_id' => 'creator_id', 'parent_type' => 'type', 'content' => 'note', 'created_at' => 'date_created' ),
		array(
			'type',
			'note',
			'extra',
			'creator_id',
			'date_created'
		)
	);
}

/**
 * Update items to 1.2.0
 */
function eac_update_120_misc() {
	global $wpdb;
	$wpdb->hide_errors();
	error_log( __METHOD__ );
	// accounts having opening balance insert as payments.
	$accounts = $wpdb->get_results( "SELECT id, opening_balance, currency, created_at FROM {$wpdb->prefix}ea_accounts WHERE opening_balance > 0" );
	foreach ( $accounts as $account ) {
		$payment = new \EverAccounting\Models\Payment();
		$payment->fill( array(
			'account_id'    => $account->id,
			'amount'        => $account->opening_balance,
			'currency'      => $account->currency,
			'exchange_rate' => EAC()->currencies->get_rate( $account->currency ),
			'paid_at'       => wp_date( 'Y-m-d H:i:s' ),
			'note'          => __( 'Opening Balance', 'wp-ever-accounting' ),
		) );
		$payment->save();
		$account = EAC()->accounts->get( $account->id );
		if ( $account ) {
			$account->update_balance();
		}
	}

	// now drop the opening balance column.
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}ea_accounts DROP COLUMN opening_balance" );
	wp_cache_flush();
}
