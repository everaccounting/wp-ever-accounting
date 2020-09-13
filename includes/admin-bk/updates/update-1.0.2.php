<?php
defined( 'ABSPATH' ) || exit();

function eaccounting_update_1_0_2() {
	EAccounting_Install::create_tables();
	global $wpdb;
	$prefix        = $wpdb->prefix;
	$localization  = get_option( 'eaccounting_localisation', [] );
	$currency_code = array_key_exists( 'currency', $localization ) ? $localization['currency'] : 'USD';
	$currency_code = empty( $currency_code ) ? 'USD' : sanitize_text_field( $currency_code );
	$currency_data = eaccounting_get_currency_config( $currency_code );

	if ( empty( eaccounting_get_currency( $currency_code, 'code' ) ) ) {
		eaccounting_insert_currency( [
			'name' => $currency_data['name'],
			'code' => $currency_data['code'],
			'rate' => 1,
		] );
	}

	update_option( 'ea_default_payment_method', 'cash' );
	update_option( 'ea_default_currency_code', $currency_data['code'] );

	$current_user_id = eaccounting_get_creator_id();


	//transfer
	$wpdb->query( "ALTER TABLE {$prefix}ea_transfers DROP COLUMN `updated_at`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_transfers ADD `creator_id` INT(11) DEFAULT NULL AFTER `revenue_id`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_transfers CHANGE `payment_id` `expense_id` INT(11) NOT NULL;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_transfers CHANGE `revenue_id` `income_id` INT(11) NOT NULL;" );
	$wpdb->query( $wpdb->prepare( "UPDATE {$prefix}ea_transfers SET creator_id=%d", $current_user_id ) );

	//revenues
	$revenues = $wpdb->get_results( "SELECT * FROM {$prefix}ea_revenues order by id asc" );
	foreach ( $revenues as $revenue ) {
		$wpdb->insert( $prefix . 'ea_transactions', array(
			'type'           => 'income',
			'paid_at'        => $revenue->paid_at,
			'amount'         => $revenue->amount,
			'currency_code'  => $currency_code,
			'currency_rate'  => 1,
			'account_id'     => $revenue->account_id,
			'contact_id'     => $revenue->contact_id,
			'category_id'    => $revenue->category_id,
			'description'    => $revenue->description,
			'payment_method' => $revenue->payment_method,
			'file_id'        => eaccounting_update_insert_file_1_0_2( $revenue->attachment_url, $current_user_id ),
			'reference'      => $revenue->reference,
			'creator_id'     => $current_user_id,
			'created_at'     => $revenue->created_at,
		) );
	}


	//revenues
	$payments = $wpdb->get_results( "SELECT * FROM {$prefix}ea_payments order by id asc" );
	foreach ( $payments as $payment ) {
		$wpdb->insert( $prefix . 'ea_transactions', array(
			'type'           => 'expense',
			'paid_at'        => $payment->paid_at,
			'amount'         => $payment->amount,
			'currency_code'  => $currency_code,
			'currency_rate'  => 1,
			'account_id'     => $payment->account_id,
			'contact_id'     => $payment->contact_id,
			'category_id'    => $payment->category_id,
			'description'    => $payment->description,
			'payment_method' => $payment->payment_method,
			'file_id'        => eaccounting_update_insert_file_1_0_2( $payment->attachment_url, $current_user_id ),
			'reference'      => $payment->reference,
			'creator_id'     => $current_user_id,
			'created_at'     => $payment->created_at,
		) );
	}

	//accounts
	$wpdb->query( "ALTER TABLE {$prefix}ea_accounts DROP COLUMN `status`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_accounts DROP COLUMN `updated_at`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_accounts ADD `currency_code` varchar(3) NOT NULL AFTER `opening_balance`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_accounts ADD `creator_id` INT(11) DEFAULT NULL AFTER `bank_address`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_accounts ADD `company_id`  int(11) NOT NULL DEFAULT 1 AFTER `bank_address`;" );
	$wpdb->query( $wpdb->prepare( "UPDATE {$prefix}ea_accounts SET creator_id=%d, currency_code=%s ", $current_user_id, $currency_code ) );

	//categories
	$wpdb->query( "ALTER TABLE {$prefix}ea_categories DROP COLUMN `status`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_categories DROP COLUMN `updated_at`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_categories ADD `company_id`  int(11) NOT NULL DEFAULT 1 AFTER `color`;" );

	//contacts
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `status`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `updated_at`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `city`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `state`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `postcode`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `name` VARCHAR(191) NOT NULL AFTER `user_id`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `fax` VARCHAR(50) DEFAULT NULL AFTER `phone`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `birth_date` date DEFAULT NULL AFTER `phone`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `currency_code` varchar(3) NOT NULL AFTER `tax_number`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `type` VARCHAR(100) DEFAULT NULL AFTER `currency_code`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `file_id` INT(11) DEFAULT NULL AFTER `note`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `creator_id` INT(11) DEFAULT NULL AFTER `file_id`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `company_id`  int(11) NOT NULL DEFAULT 1 AFTER `file_id`;" );

	$contacts = $wpdb->get_results( "SELECT * FROM {$prefix}ea_contacts" );
	foreach ( $contacts as $contact ) {
		$file_id = eaccounting_update_insert_file_1_0_2( $contact->avatar_url, $current_user_id );
		$name    = implode( " ", [ $contact->first_name, $contact->last_name ] );
		$wpdb->update( $wpdb->ea_contacts, [
			'currency_code' => $currency_data['code'],
			'name'          => $name,
			'type'          => 'customer',
			'file_id'       => $file_id,
			'creator_id'    => $current_user_id
		], [ 'id' => $contact->id ] );
		$payment_ids = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $wpdb->ea_transactions WHERE type='expense' AND contact_id=%d", $contact->id ) );
		if ( ! empty( $payment_ids ) ) {
			$data        = (array) $contact;
			$vendor_data = wp_parse_args( array(
				'id'            => null,
				'name'          => $name,
				'type'          => 'vendor',
				'currency_code' => $currency_data['code'],
				'file_id'       => $file_id,
				'creator_id'    => $current_user_id,
			), $data );

			if ( false !== $wpdb->insert( $wpdb->ea_contacts, $vendor_data ) ) {
				$wpdb->update( $wpdb->ea_transactions, [ 'contact_id' => $wpdb->insert_id ], [ 'contact_id' => $contact->id ] );
			}
		}
	}
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `types`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `avatar_url`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `first_name`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `last_name`;" );


	$account_id = $wpdb->get_var( "SELECT id from $wpdb->ea_accounts order by id asc" );
	if ( empty( $account_id ) ) {
		$account_id = eaccounting_insert_account( [
			'name'            => __( 'Cash', 'wp-ever-accounting' ),
			'number'          => '',
			'opening_balance' => '0',
		] );
	}
	update_option( 'ea_default_account_id', intval( $account_id ) );
}

function eaccounting_update_insert_file_1_0_2( $url, $user_id = 1 ) {
	//todo remove this before going live
	$url = str_replace( 'http://axis.byteever.com', 'http://eaccounting.test', $url );
	if ( empty( $url ) ) {
		return 0;
	}
	$paths     = eaccounting_get_upload_dir();
	$base_url  = $paths['baseurl'];
	$base_dir  = $paths['basedir'];
	$file_path = str_replace( $base_url, $base_dir, $url );
	$file_name = basename( $url );

	if ( ! file_exists( $file_path ) ) {
		return 0;
	}

	$ext        = pathinfo( $file_name, PATHINFO_EXTENSION );
	$mime_type  = @mime_content_type( $file_path );
	$filesize   = @filesize( $file_path );
	$filetime   = @fileatime( $file_path );
	$created_at = date( 'Y-m-d', $filetime );
	$path       = str_replace( $file_name, '', $url );
	$path       = rtrim( str_replace( $base_url, '', $path ), '/' );
	global $wpdb;
	$data = array(
		'name'       => $file_name,
		'path'       => $path,
		'extension'  => $ext,
		'mime_type'  => $mime_type,
		'size'       => $filesize,
		'creator_id' => $user_id,
		'created_at' => $created_at,
	);

	if ( false !== $wpdb->insert( $wpdb->ea_files, $data ) ) {
		return $wpdb->insert_id;
	}

	return 0;
}


eaccounting_update_1_0_2();
