<?php
/**
 * EverAccounting Updates
 *
 * Functions for updating data.
 *
 * @package EverAccounting/Functions
 * @version 1.0.2
 */

defined( 'ABSPATH' ) || exit;

function eaccounting_update_1_0_2() {
	EAccounting_Install::create_tables();
	EAccounting_Install::create_roles();

	global $wpdb;
	$prefix          = $wpdb->prefix;
	$current_user_id = eaccounting_get_current_user_id();

	$settings = array();
	delete_option( 'eaccounting_settings' );
	$localization  = get_option( 'eaccounting_localisation', [] );
	$currency_code = array_key_exists( 'currency', $localization ) ? $localization['currency'] : 'USD';
	$currency_code = empty( $currency_code ) ? 'USD' : sanitize_text_field( $currency_code );

	$currency = eaccounting_insert_currency( [
		'code' => $currency_code,
		'rate' => 1,
	] );

	$settings['financial_year_start']   = '01-01';
	$settings['default_payment_method'] = 'cash';


	if ( ! is_wp_error( $currency ) ) {
		$settings['default_currency'] = $currency->get_code();
	}

	update_option( 'eaccounting_settings', $settings );

	//transfers
	$wpdb->query( "ALTER TABLE {$prefix}ea_transfers DROP COLUMN `updated_at`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_transfers ADD `creator_id` INT(11) DEFAULT NULL AFTER `revenue_id`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_transfers CHANGE `payment_id` `expense_id` INT(11) NOT NULL;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_transfers CHANGE `revenue_id` `income_id` INT(11) NOT NULL;" );
	$wpdb->query( $wpdb->prepare( "UPDATE {$prefix}ea_transfers SET creator_id=%d", $current_user_id ) );
	$wpdb->query( "ALTER TABLE {$prefix}ea_transfers CHANGE `created_at` `date_created` DATETIME NULL DEFAULT NULL;" );

	$transfers = $wpdb->get_results( "SELECT * FROM {$prefix}ea_transfers" );
	foreach ( $transfers as $transfer ) {
		$revenue = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}ea_revenues where id=%d", $transfer->income_id ) );
		$expense = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}ea_payments where id=%d", $transfer->expense_id ) );

		$wpdb->insert( $prefix . 'ea_transactions',
			array(
				'type'           => 'income',
				'paid_at'        => $revenue->paid_at,
				'amount'         => $revenue->amount,
				'currency_code'  => $currency_code,
				'currency_rate'  => 1, //protected
				'account_id'     => $revenue->account_id,
				'invoice_id'     => null,
				'contact_id'     => null,
				'category_id'    => $revenue->category_id,
				'description'    => $revenue->description,
				'payment_method' => $revenue->payment_method,
				'reference'      => $revenue->reference,
				'attachment'     => $revenue->attachment_url,
				'parent_id'      => 0,
				'reconciled'     => 0,
				'creator_id'     => $current_user_id,
				'date_created'   => $revenue->created_at,
			)
		);

		$income_id = $wpdb->insert_id;

		$wpdb->insert( $prefix . 'ea_transactions',
			array(
				'type'           => 'expense',
				'paid_at'        => $expense->paid_at,
				'amount'         => $expense->amount,
				'currency_code'  => $currency_code,
				'currency_rate'  => 1, //protected
				'account_id'     => $expense->account_id,
				'invoice_id'     => null,
				'contact_id'     => null,
				'category_id'    => $expense->category_id,
				'description'    => $expense->description,
				'payment_method' => $expense->payment_method,
				'reference'      => $expense->reference,
				'attachment'     => $expense->attachment_url,
				'parent_id'      => 0,
				'reconciled'     => 0,
				'creator_id'     => $current_user_id,
				'date_created'   => $expense->created_at,
			)
		);

		$expense_id = $wpdb->insert_id;

		$wpdb->update(
			$prefix . 'ea_transfers',
			array(
				'income_id'  => $income_id,
				'expense_id' => $expense_id,
			),
			array( 'id' => $transfer->id )
		);

		$wpdb->delete(
			$prefix . 'ea_revenues',
			array( 'id' => $revenue->id )
		);

		$wpdb->delete(
			$prefix . 'ea_payments',
			array( 'id' => $expense->id )
		);
	}

	//
	$revenues = $wpdb->get_results( "SELECT * FROM {$prefix}ea_revenues order by id asc" );
	foreach ( $revenues as $revenue ) {
		$wpdb->insert( $prefix . 'ea_transactions',
			array(
				'type'           => 'income',
				'paid_at'        => $revenue->paid_at,
				'amount'         => $revenue->amount,
				'currency_code'  => $currency_code,
				'currency_rate'  => 1, //protected
				'account_id'     => $revenue->account_id,
				'invoice_id'     => null,
				'contact_id'     => $revenue->contact_id,
				'category_id'    => $revenue->category_id,
				'description'    => $revenue->description,
				'payment_method' => $revenue->payment_method,
				'reference'      => $revenue->reference,
				'attachment'     => $revenue->attachment_url,
				'parent_id'      => 0,
				'reconciled'     => 0,
				'creator_id'     => $current_user_id,
				'date_created'   => $revenue->created_at,
			)
		);

//		$wpdb->delete(
//			$prefix . 'ea_revenues',
//			array( 'id' => $revenue->id )
//		);
	}

	//expenses
	$expenses = $wpdb->get_results( "SELECT * FROM {$prefix}ea_payments order by id asc" );
	foreach ( $expenses as $expense ) {
		$wpdb->insert( $prefix . 'ea_transactions',
			array(
				'type'           => 'expense',
				'paid_at'        => $expense->paid_at,
				'amount'         => $expense->amount,
				'currency_code'  => $currency_code,
				'currency_rate'  => 1, //protected
				'account_id'     => $expense->account_id,
				'invoice_id'     => null,
				'contact_id'     => $expense->contact_id,
				'category_id'    => $expense->category_id,
				'description'    => $expense->description,
				'payment_method' => $expense->payment_method,
				'reference'      => $expense->reference,
				'attachment'     => $expense->attachment_url,
				'parent_id'      => 0,
				'reconciled'     => 0,
				'creator_id'     => $current_user_id,
				'date_created'   => $expense->created_at,
			)
		);

//		$wpdb->delete(
//			$prefix . 'ea_payments',
//			array( 'id' => $expense->id )
//		);
	}

	//accounts
	$wpdb->query( "ALTER TABLE {$prefix}ea_accounts DROP COLUMN `updated_at`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_accounts ADD `currency_code` varchar(3) NOT NULL AFTER `opening_balance`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_accounts ADD `creator_id` INT(11) DEFAULT NULL AFTER `bank_address`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_accounts ADD `enabled` tinyint(1) NOT NULL DEFAULT '1' AFTER `bank_address`;" );
	$wpdb->query( $wpdb->prepare( "UPDATE {$prefix}ea_accounts SET creator_id=%d, currency_code=%s ", $current_user_id, $currency_code ) );
	$wpdb->update( "{$prefix}ea_accounts", array( 'enabled' => '1' ), array( 'status' => 'active' ) );
	$wpdb->update( "{$prefix}ea_accounts", array( 'enabled' => '0' ), array( 'status' => 'inactive' ) );
	$wpdb->query( "ALTER TABLE {$prefix}ea_accounts DROP COLUMN `status`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_accounts CHANGE `created_at` `date_created` DATETIME NULL DEFAULT NULL;" );

	//categories
	$wpdb->query( "ALTER TABLE {$prefix}ea_categories DROP COLUMN `updated_at`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_categories ADD `enabled` tinyint(1) NOT NULL DEFAULT '1' AFTER `color`;" );
	$wpdb->update( "{$prefix}ea_categories", array( 'enabled' => '1' ), array( 'status' => 'active' ) );
	$wpdb->update( "{$prefix}ea_categories", array( 'enabled' => '0' ), array( 'status' => 'inactive' ) );
	$wpdb->query( "ALTER TABLE {$prefix}ea_categories DROP COLUMN `status`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_categories CHANGE `created_at` `date_created` DATETIME NULL DEFAULT NULL;" );

	//contacts
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `name` VARCHAR(191) NOT NULL AFTER `user_id`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `fax` VARCHAR(50) DEFAULT NULL AFTER `phone`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `birth_date` date DEFAULT NULL AFTER `phone`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `type` VARCHAR(100) DEFAULT NULL AFTER `note`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `enabled` tinyint(1) NOT NULL DEFAULT '1' AFTER `note`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `creator_id` INT(11) DEFAULT NULL AFTER `note`;" );
	$contacts = $wpdb->get_results( "SELECT * FROM {$prefix}ea_contacts" );

	foreach ( $contacts as $contact ) {
		$types = maybe_unserialize( $contact->types );
		if ( count( $types ) == 1 ) {
			$type = reset( $types );
			$wpdb->update( $wpdb->prefix . 'ea_contacts', [
				'type' => $type,
			], [ 'id' => $contact->id ] );
		} else {
			$wpdb->update( $wpdb->prefix . 'ea_contacts', [
				'type' => 'customer'
			], [ 'id' => $contact->id ] );

			$data         = (array) $contact;
			$data['type'] = 'vendor';
			unset( $data['types'] );
			unset( $data['id'] );
			$wpdb->insert( $wpdb->prefix . 'ea_contacts', $data );
			if ( ! empty( $wpdb->insert_id ) ) {
				$vendor_id = $wpdb->insert_id;

				$wpdb->update( $wpdb->prefix . 'ea_transactions', [
					'contact_id' => $vendor_id,
				], [ 'contact_id' => $contact->id, 'type' => 'expense' ] );
			}
		}
	}

	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts ADD `currency_code` varchar(3) NOT NULL AFTER `tax_number`;" );

	foreach ( $contacts as $contact ) {
		$name = implode( " ", [ $contact->first_name, $contact->last_name ] );
		$wpdb->update( $wpdb->prefix . 'ea_contacts', [
			'currency_code' => $currency_code,
			'enabled'       => $contact->status == 'active' ? 1 : 0,
			'name'          => $name,
			'creator_id'    => $current_user_id
		], [ 'id' => $contact->id ] );
	}
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `avatar_url`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `updated_at`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `city`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `state`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `postcode`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `status`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts DROP COLUMN `types`;" );
	$wpdb->query( "ALTER TABLE {$prefix}ea_contacts CHANGE `created_at` `date_created` DATETIME NULL DEFAULT NULL;" );
}