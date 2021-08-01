<?php
/**
 * EverAccounting account Functions.
 *
 * All account related function of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

use EverAccounting\Account;

defined( 'ABSPATH' ) || exit;

// Sanitization and escaping filters
add_filter( 'eaccounting_pre_account_opening_balance', 'eaccounting_format_decimal' );
add_filter( 'eaccounting_pre_account_enabled', 'eaccounting_bool_to_number', 10, 1 );
add_filter( 'eaccounting_edit_account_opening_balance', 'eaccounting_format_decimal' );
add_filter( 'eaccounting_edit_account_enabled', 'eaccounting_string_to_bool', 10, 1 );

/**
 * Retrieves account data given a account id or account object.
 *
 * @param int|object|Account $account account to retrieve
 * @param string             $output The required return type. One of OBJECT, ARRAY_A, or ARRAY_N.Default OBJECT.
 * @param string             $filter Type of filter to apply. Accepts 'raw', 'edit', 'db', or 'display'. Default 'raw'.
 *
 * @return Account|array|null
 * @since 1.1.0
 */
function eaccounting_get_account( $account, $output = OBJECT, $filter = 'raw' ) {
	if ( empty( $account ) ) {
		return null;
	}

	if ( $account instanceof Account ) {
		$_account = $account;
	} elseif ( is_object( $account ) ) {
		$_account = new Account( $account );
	} else {
		$_account = Account::get_instance( $account );
	}

	if ( ! $_account ) {
		return null;
	}

	$_account = $_account->filter( $filter );

	if ( ARRAY_A === $output ) {
		return $_account->to_array();
	}

	if ( ARRAY_N === $output ) {
		return array_values( $_account->to_array() );
	}

	return $_account->filter( $filter );
}

/**
 * Add or update a new account to the database.
 *
 * @param array|object|Account $account_data An array, object, or Account object of data arguments.
 *
 * @return Account|WP_Error The Account object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
/**
 *  Insert or update an account.
 *
 * @param array|object|Account $account_arr An array, object, or account object of data arguments.
 *
 * @return Account|WP_Error The account object or WP_Error otherwise.
 * @global wpdb $wpdb WordPress database abstraction object.
 * @since 1.1.0
 */
function eaccounting_insert_account( $account_arr ) {
	global $wpdb;
	$user_id = get_current_user_id();
	if ( $account_arr instanceof Account ) {
		$account_arr = $account_arr->to_array();
	} elseif ( $account_arr instanceof stdClass ) {
		$account_arr = get_object_vars( $account_arr );
	}

	$defaults = array(
		'currency_code'   => '',
		'name'            => '',
		'number'          => '',
		'opening_balance' => 0.00,
		'bank_name'       => '',
		'bank_phone'      => '',
		'bank_address'    => '',
		'thumbnail_id'    => '',
		'enabled'         => true,
		'creator_id'      => $user_id,
		'date_created'    => '',
	);

	// Are we updating or creating?
	$id          = null;
	$update      = false;
	$data_before = array();
	if ( ! empty( $item_data['id'] ) ) {
		$update      = true;
		$id          = absint( $item_data['id'] );
		$data_before = eaccounting_get_account( $id, ARRAY_A );

		if ( is_null( $data_before ) ) {
			return new WP_Error( 'invalid_account_id', __( 'Invalid account id to update.', 'wp-ever-accounting' ) );
		}

		// Merge old and new fields with new fields overwriting old ones.
		$account_arr = array_merge( $data_before, $account_arr );
		$data_before = $data_before->to_array();
	}

	$item_data = wp_parse_args( $account_arr, $defaults );
	$data_arr  = eaccounting_sanitize_account( $account_arr, 'db' );

	// Check required
	if ( empty( $data_arr['parent_id'] ) ) {
		return new WP_Error( 'invalid_account_currency_code', esc_html__( 'Account currency code is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['name'] ) ) {
		return new WP_Error( 'invalid_account_name', esc_html__( 'Account name is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['number'] ) ) {
		return new WP_Error( 'invalid_account_number', esc_html__( 'Account number is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['date_created'] ) || '0000-00-00 00:00:00' === $data_arr['date_created'] ) {
		$data_arr['date_created'] = current_time( 'mysql' );
	}

	$fields = array_keys( $defaults );
	$data   = wp_array_slice_assoc( $data_arr, $fields );

	/**
	 * Filters account data before it is inserted into the database.
	 *
	 * @param array $data Data to be inserted.
	 * @param array $data_arr Sanitized data.
	 *
	 * @since 1.2.1
	 */
	$data = apply_filters( 'eaccounting_insert_account', $data, $data_arr );

	$data  = wp_unslash( $data );
	$where = array( 'id' => $id );

	if ( $update ) {

		/**
		 * Fires immediately before an existing account is updated in the database.
		 *
		 * @param int $id Account id.
		 * @param array $data Account data to be inserted.
		 * @param array $changes Account data to be updated.
		 * @param array $data_arr Sanitized account data.
		 * @param array $data_before Account previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_update_account', $id, $data, $data_arr, $data_before );
		if ( false === $wpdb->update( $wpdb->prefix . 'ea_accounts', $data, $where, $data_before ) ) {
			new WP_Error( 'db_update_error', __( 'Could not update account in the database.' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing account is updated in the database.
		 *
		 * @param int $id Account id.
		 * @param array $data Account data to be inserted.
		 * @param array $changes Account data to be updated.
		 * @param array $data_arr Sanitized Account data.
		 * @param array $data_before Account previous data.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_update_account', $id, $data, $data_arr, $data_before );
	} else {

		/**
		 * Fires immediately before an existing account is inserted in the database.
		 *
		 * @param array $data Account data to be inserted.
		 * @param string $data_arr Sanitized account data.
		 * @param array $item_data Account data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_pre_insert_account', $data, $data_arr, $item_data );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_accounts', $data ) ) {
			new WP_Error( 'db_insert_error', __( 'Could not insert account into the database.', 'wp-ever-accounting' ), $wpdb->last_error );
		}

		$id = (int) $wpdb->insert_id;

		/**
		 * Fires immediately after an existing account is inserted in the database.
		 *
		 * @param int $id Account id.
		 * @param array $data Account has been inserted.
		 * @param array $data_arr Sanitized account data.
		 * @param array $item_data Account data as originally passed to the function.
		 *
		 * @since 1.2.1
		 */
		do_action( 'eaccounting_insert_account', $id, $data, $data_arr, $item_data );
	}

	// Clear cache.
	wp_cache_delete( $id, 'ea_accounts' );
	wp_cache_set( 'last_changed', microtime(), 'ea_accounts' );

	// Get new account object.
	$account = eaccounting_get_account( $id );

	/**
	 * Fires once a account has been saved.
	 *
	 * @param int $id Account id.
	 * @param Account $account Account object.
	 * @param bool $update Whether this is an existing account being updated.
	 *
	 * @since 1.2.1
	 */
	do_action( 'eaccounting_saved_account', $id, $account, $update, $data_arr, $data_before );

	return $account;
}


/**
 * Delete an account.
 *
 * @param int $account_id Account id.
 *
 * @return Account |false|null Account data on success, false or null on failure.
 * @since 1.1.0
 */
function eaccounting_delete_account( $account_id ) {
	global $wpdb;

	$account = eaccounting_get_account( $account_id );
	if ( ! $account || ! $account->exists() ) {
		return false;
	}

	/**
	 * Filters whether an account delete should take place.
	 *
	 * @param bool|null $delete Whether to go forward with deletion.
	 * @param Account $account contact object.
	 *
	 * @since 1.2.1
	 */
	$check = apply_filters( 'eaccounting_pre_delete_account', null, $account );
	if ( null !== $check ) {
		return $check;
	}

	/**
	 * Fires before an account is deleted.
	 *
	 * @param int $account_id Contact id.
	 * @param Account $account account object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_account()
	 */
	do_action( 'eaccounting_before_delete_account', $account_id, $account );

	$result = $wpdb->delete( $wpdb->prefix . 'ea_accounts', array( 'id' => $account_id ) );
	if ( ! $result ) {
		return false;
	}

	wp_cache_delete( $account_id, 'ea_accounts' );
	wp_cache_set( 'last_changed', microtime(), 'ea_accounts' );

	/**
	 * Fires after an account is deleted.
	 *
	 * @param int $account_id contact id.
	 * @param Account $account contact object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_account()
	 */
	do_action( 'eaccounting_delete_account', $account_id, $account );

	return $account;
}

/**
 * Retrieves an array of the accounts matching the given criteria.
 *
 * @param array $args Arguments to retrieve accounts.
 *
 * @return Account[]|int Array of account objects or account IDs.
 * @since 1.1.0
 */
function eaccounting_get_accounts( $args = array() ) {
	$defaults = array(
		'number'        => 20,
		'orderby'       => 'name',
		'order'         => 'DESC',
		'include'       => array(),
		'exclude'       => array(),
		'no_found_rows' => false,
		'count_total'   => false,
	);

	$parsed_args = wp_parse_args( $args, $defaults );
	$query       = new \EverAccounting\Account_Query( $parsed_args );
	if ( true === $parsed_args['count_total'] ) {
		return $query->get_total();
	}

	return $query->get_results();
}

/**
 * Sanitizes every account field.
 *
 * If the context is 'raw', then the account object or array will get minimal
 * sanitization of the integer fields.
 *
 * @param object|array $account Account object or array
 * @param string       $context Optional. How to sanitize post fields. Accepts 'raw', 'edit', 'db', 'display'. Default 'display'.
 *
 * @return object|Account|array The now sanitized account object or array
 * @see eaccounting_sanitize_account_field()
 *
 * @since 1.2.1
 */
function eaccounting_sanitize_account( $account, $context = 'raw' ) {
	if ( is_object( $account ) ) {
		// Check if post already filtered for this context.
		if ( isset( $account->filter ) && $context === $account->filter ) {
			return $account;
		}
		if ( ! isset( $account->id ) ) {
			$account->id = 0;
		}

		foreach ( array_keys( get_object_vars( $account ) ) as $field ) {
			$account->$field = eaccounting_sanitize_account_field( $field, $account->$field, $account->id, $context );
		}
		$account->filter = $context;
	} elseif ( is_array( $account ) ) {
		// Check if post already filtered for this context.
		if ( isset( $account['filter'] ) && $context === $account['filter'] ) {
			return $account;
		}
		if ( ! isset( $account['id'] ) ) {
			$account['id'] = 0;
		}
		foreach ( array_keys( $account ) as $field ) {
			$account[ $field ] = eaccounting_sanitize_account_field( $field, $account[ $field ], $account['id'], $context );
		}
		$account['filter'] = $context;
	}

	return $account;
}

/**
 * Sanitizes account field based on context.
 *
 * Possible context values are:  'raw', 'edit', 'db', 'display'.
 *
 * @param string $field The account Object field name.
 * @param mixed  $value The account Object value.
 * @param int    $account_id account id.
 * @param string $context Optional. How to sanitize the field. Possible values are 'raw', 'edit','db', 'display'. Default 'display'.
 *
 * @return mixed Sanitized value.
 * @since 1.2.1
 */
function eaccounting_sanitize_account_field( $field, $value, $account_id, $context ) {
	if ( false !== strpos( $field, '_id' ) || $field === 'id' ) { //phpcs:ignore
		$value = absint( $value );
	}

	$context = strtolower( $context );

	if ( 'raw' === $context ) {
		if ( $field === 'extra' ) { //phpcs:ignore
			$value = maybe_unserialize( $value );
		}

		return $value;
	}

	if ( 'edit' === $context ) {

		/**
		 * Filters account field to edit before it is sanitized.
		 *
		 * @param mixed $value Value of the account field.
		 * @param int $account_id Account id.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_edit_account_{$field}", $value, $account_id );

	} elseif ( 'db' === $context ) {

		/**
		 * Filters account field value before it is sanitized.
		 *
		 * @param mixed $value Value of the account field.
		 * @param int $account_id Account id.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_pre_account_{$field}", $value, $account_id );
	} else {
		// Use display filters by default.

		/**
		 * Filters the account field sanitized for display.
		 *
		 * @param mixed $value Value of the account field.
		 * @param int $account_id Account id.
		 * @param string $context Context to retrieve the account field value.
		 *
		 * @since 1.2.1
		 */
		$value = apply_filters( "eaccounting_account_{$field}", $value, $account_id, $context );
	}

	return $value;
}
