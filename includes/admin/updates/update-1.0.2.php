<?php
defined( 'ABSPATH') || exit();

function eaccounting_update_1_0_2() {
	EAccounting_Install::install();
	error_log( "UPDATING");
	global $wpdb;
	$prefix          = $wpdb->prefix;
	$localization  = get_option( 'eaccounting_localisation', [] );
	$currency_code = array_key_exists( 'currency', $localization ) ? $localization['currency'] : 'USD';
	$currency_data = eaccounting_get_currency_config( $currency_code );
	$currency_id = eaccounting_insert_currency( [
		'name' => $currency_data['name'],
		'code' => $currency_data['code'],
		'rate' => 1,
	] );

	update_option( 'ea_default_payment_method', 'cash');
	update_option( 'ea_default_currency_id', intval( $currency_id));

	$account_id = $wpdb->get_row("SELECT id from $wpdb->ea_accounts order by id asc");
	update_option( 'ea_default_account_id', intval( $account_id));

	$current_user_id = eaccounting_get_creator_id();

	//transfer
	$wpdb->query( "ALTER TABLE {$prefix}ea_transfers DROP COLUMN `updated_at`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_transfers ADD `creator_id` INT(11) DEFAULT NULL AFTER `revenue_id`;" );
	$wpdb->query( $wpdb->prepare( "UPDATE {$prefix}ea_transfers SET creator_id=%d", $current_user_id ) );

	//revenues
	$wpdb->query( "ALTER TABLE {$prefix}ea_revenues DROP COLUMN `updated_at`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_revenues ADD `creator_id` INT(11) DEFAULT NULL AFTER `reconciled`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_revenues ADD `currency_rate` double(15,8) NOT NULL DEFAULT 1 AFTER `category_id`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_revenues ADD `currency_code` varchar(20) NOT NULL AFTER `category_id`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_revenues ADD `file_id` INT(11) DEFAULT NULL AFTER `reference`;" );
	$wpdb->query( $wpdb->prepare( "UPDATE {$prefix}ea_revenues SET creator_id=%d, currency_code=%s ", $current_user_id, $currency_code ) );
	$revenues = $wpdb->get_results( "SELECT id, attachment_url FROM {$prefix}ea_revenues" );
	foreach ( $revenues as $revenue ) {
		if ( ! empty( $revenue->attachment_url ) ) {
			$file_id = eaccounting_update_insert_file_1_0_2( $revenue->attachment_url, $current_user_id );
			$wpdb->query( $wpdb->prepare( "UPDATE {$prefix}ea_revenues SET file_id=%d WHERE id=%d", $file_id, $revenue->id ) );
		}
	}

	$wpdb->query( "ALTER TABLE {$prefix}ea_revenues DROP COLUMN `attachment_url`;" );

	//payments
	$wpdb->query( "ALTER TABLE {$prefix}ea_payments DROP COLUMN `updated_at`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_payments ADD `creator_id` INT(11) DEFAULT NULL AFTER `reconciled`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_payments ADD `currency_rate` double(15,8) NOT NULL DEFAULT 1 AFTER `category_id`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_payments ADD `currency_code` varchar(20) NOT NULL AFTER `category_id`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_payments ADD `file_id` INT(11) DEFAULT NULL AFTER `reference`;" );
	$wpdb->query( $wpdb->prepare( "UPDATE {$prefix}ea_payments SET creator_id=%d, currency_code=%s ", $current_user_id, $currency_code ) );
	$payments = $wpdb->get_results( "SELECT id, attachment_url FROM {$prefix}ea_payments" );
	foreach ( $payments as $payment ) {
		if ( ! empty( $payment->attachment_url ) ) {
			$file_id = eaccounting_update_insert_file_1_0_2( $payment->attachment_url, $current_user_id );
			$wpdb->query( $wpdb->prepare( "UPDATE {$prefix}ea_payments SET file_id=%d WHERE id=%d", $file_id, $payment->id ) );
		}
	}

	$wpdb->query( "ALTER TABLE {$prefix}ea_payments DROP COLUMN `attachment_url`;" );

	//accounts
	$wpdb->query( "ALTER TABLE {$prefix}ea_accounts DROP COLUMN `status`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_accounts DROP COLUMN `updated_at`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_accounts ADD `currency_code` varchar(3) NOT NULL AFTER `opening_balance`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_accounts ADD `creator_id` INT(11) DEFAULT NULL AFTER `bank_address`;" );
	$wpdb->query( $wpdb->prepare( "UPDATE {$prefix}ea_accounts SET creator_id=%d, currency_code=%s ", $current_user_id, $currency_code ) );

	//categories
	$wpdb->query( "ALTER TABLE {$prefix}ea_categories DROP COLUMN `status`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_categories DROP COLUMN `updated_at`;" );

	//contacts
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `status`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `updated_at`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `city`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `state`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `postcode`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `name` VARCHAR(191) NOT NULL AFTER `user_id`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `fax_number` VARCHAR(50) DEFAULT NULL AFTER `phone`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `birth_date` date DEFAULT NULL AFTER `phone`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `currency_code` varchar(3) NOT NULL AFTER `tax_number`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `type` VARCHAR(100) DEFAULT NULL AFTER `currency_code`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `file_id` INT(11) DEFAULT NULL AFTER `note`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `creator_id` INT(11) DEFAULT NULL AFTER `file_id`;" );

	$contacts = $wpdb->get_results( "SELECT * FROM {$prefix}ea_contacts" );
	foreach ( $contacts as $contact ) {
		$file_id = eaccounting_update_insert_file_1_0_2( $contact->avatar_url, $current_user_id );
		$name    = implode( " ", [ $contact->first_name, $contact->last_name ] );
		$wpdb->update( $wpdb->ea_contacts, [ 'name' => $name, 'type' => 'customer', 'file_id' => $file_id, 'creator_id' => $current_user_id ], ['id'=> $contact->id] );
		$payment_ids = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $wpdb->ea_payments WHERE contact_id=%d", $contact->id ) );
		if ( ! empty( $payment_ids ) ) {
			$data        = (array) $contact;
			$vendor_data = wp_parse_args( array(
				'id'         => null,
				'name'       => $name,
				'type'       => 'vendor',
				'file_id'    => $file_id,
				'creator_id' => $current_user_id,
			), $data );

			if ( false !== $wpdb->insert( $wpdb->ea_contacts, $vendor_data ) ) {
				$wpdb->update( $wpdb->ea_payments, [ 'contact_id' => $wpdb->insert_id ], [ 'contact_id' => $contact->id ] );
			}
		}
	}
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `types`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `avatar_url`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `last_name`;" );
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
