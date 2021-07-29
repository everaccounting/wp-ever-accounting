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
 * @param int|array|object|Account $account account to retrieve
 * @param string $filter Optional. Type of filter to apply. Accepts 'raw', 'edit', 'db', or 'display'. Default 'raw'.
 *
 * @return array|Account|null
 * @since 1.1.0
 */
function eaccounting_get_account( $account, $filter = 'raw' ) {
	if ( empty( $account ) ) {
		return null;
	}

	$account = new Account( $account );
	if ( ! $account->exists() ) {
		return null;
	}

	return $account->filter( $filter );
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
function eaccounting_insert_account( $account_data ) {
	global $wpdb;
	$user_id = get_current_user_id();
	if ( $account_data instanceof Account ) {
		$account_data = $account_data->to_array();
	} elseif ( $account_data instanceof stdClass ) {
		$account_data = get_object_vars( $account_data );
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
	$id      = null;
	$update  = false;
	$changes = $account_data;
	if ( ! empty( $account_data['id'] ) ) {
		$update = true;
		$id     = absint( $account_data['id'] );
		$before = eaccounting_get_account( $id );

		if ( is_null( $before ) ) {
			return new WP_Error( 'invalid_account_id', __( 'Invalid account id to update.' ) );
		}
		// Store changes value.
		$changes = array_diff_assoc( $account_data, $before->to_array() );

		// Merge old and new fields with new fields overwriting old ones.
		$account_data = array_merge( $before->to_array(), $account_data );
	}


	$data_arr = wp_parse_args( $account_data, $defaults );
	$data_arr = eaccounting_sanitize_account( $data_arr, 'db' );

	if ( empty( $data_arr['currency_code'] ) ) {
		return new WP_Error( 'invalid_account_currency_code', esc_html__( 'Account currency code is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['name'] ) ) {
		return new WP_Error( 'invalid_account_name', esc_html__( 'Account name is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['currency_code'] ) ) {
		return new WP_Error( 'invalid_account_currency_code', esc_html__( 'Currency code is required', 'wp-ever-accounting' ) );
	}

	if ( empty( $data_arr['date_created'] ) || '0000-00-00 00:00:00' === $data_arr['date_created'] ) {
		$data_arr['date_created'] = current_time( 'mysql' );
	}

	// Compute fields.
	$currency_code   = $data_arr['currency_code'];
	$name            = $data_arr['name'];
	$number          = $data_arr['number'];
	$opening_balance = $data_arr['opening_balance'];
	$bank_name       = $data_arr['bank_name'];
	$bank_phone      = $data_arr['bank_phone'];
	$bank_address    = $data_arr['bank_address'];
	$thumbnail_id    = (int) $data_arr['thumbnail_id'];
	$enabled         = (int) $data_arr['enabled'];
	$creator_id      = (int) $data_arr['creator_id'];
	$date_created    = $data_arr['date_created'];
	$data            = compact( 'currency_code', 'name', 'number', 'opening_balance', 'bank_name', 'bank_phone', 'bank_address', 'thumbnail_id', 'enabled', 'creator_id', 'date_created' );

	/**
	 * Filters account data before it is inserted into the database.
	 *
	 * @param array $data Account data to be inserted.
	 * @param array $data_arr Sanitized account data.
	 * @param array $account_data Account data as originally passed to the function.
	 *
	 * @since 1.2.1
	 *
	 */
	$data = apply_filters( 'eaccounting_insert_account_data', $data, $data_arr, $account_data );

	$data  = wp_unslash( $data );
	$where = array( 'id' => $id );

	if ( $update ) {

		/**
		 * Fires immediately before an existing account is updated in the database.
		 *
		 * @param int     $id Account id.
		 * @param array $data Account data to be inserted.
		 * @param array $changes Account data to be updated.
		 * @param array $data_arr Sanitized account data.
		 *
		 * @since 1.2.1
		 *
		 */
		do_action( 'eaccounting_pre_update_account', $id, $data, $changes, $data_arr );
		if ( false === $wpdb->update( $wpdb->prefix . 'ea_accounts', $data, $where ) ) {
			new WP_Error( 'db_update_error', __( 'Could not update account in the database.' ), $wpdb->last_error );
		}

		/**
		 * Fires immediately after an existing account is updated in the database.
		 *
		 * @param int $id Account id.
		 * @param array $data Account data to be inserted.
		 * @param array $changes Account data to be updated.
		 * @param array $data_arr Sanitized account data.
		 *
		 * @since 1.2.1
		 *
		 */
		do_action( 'eaccounting_update_account', $id, $data, $changes, $data_arr  );
	} else {

		/**
		 * Fires immediately before an existing account is inserted in the database.
		 *
		 * @param array $data Account data to be inserted.
		 * @param string $data_arr Sanitized account data.
		 * @param array $account_data Account data as originally passed to the function.
		 *
		 * @since 1.2.1
		 *
		 */
		do_action( 'eaccounting_pre_insert_account', $data, $data_arr, $account_data );

		if ( false === $wpdb->insert( $wpdb->prefix . 'ea_accounts', $data ) ) {
			new WP_Error( 'db_insert_error', __( 'Could not insert account into the database.' ), $wpdb->last_error );
		}

		$id = (int) $wpdb->insert_id;

		/**
		 * Fires immediately after an existing account is inserted in the database.
		 *
		 * @param int $id Account id.
		 * @param array $data Account has been inserted.
		 * @param array $data_arr Sanitized account data.
		 * @param array $account_data Account data as originally passed to the function.
		 *
		 * @since 1.2.1
		 *
		 */
		do_action( 'eaccounting_insert_account', $id, $data, $data_arr, $account_data );
	}

	// Clear cache.
	eaccounting_delete_cache( 'ea_accounts', $id );

	// Get new account object.
	$account = eaccounting_get_account( $id );

	/**
	 * Fires once an account has been saved.
	 *
	 * @param int $id Account id.
	 * @param Account $account Account object.
	 * @param bool $update Whether this is an existing account being updated.
	 *
	 * @since 1.2.1
	 *
	 */
	do_action( 'eaccounting_saved_account', $id, $account, $update );

	return $account;
}


/**
 * Delete an account.
 *
 * @param int $account_id Note id.
 *
 * @return Account |false|null Note data on success, false or null on failure.
 * @since 1.1.0
 *
 */
function eaccounting_delete_account( $account_id ) {
	global $wpdb;

	$account = eaccounting_get_account( $account_id );
	if ( ! $account->exists() ) {
		return false;
	}

	/**
	 * Filters whether an account delete should take place.
	 *
	 * @param bool|null $delete Whether to go forward with deletion.
	 * @param Account $account account object.
	 *
	 * @since 1.2.1
	 *
	 */
	$check = apply_filters( 'eaccounting_pre_delete_account', null, $account );
	if ( null !== $check ) {
		return $check;
	}

	/**
	 * Fires before an account is deleted.
	 *
	 * @param int $account_id Account id.
	 * @param Account $account Account object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_account()
	 *
	 */
	do_action( 'eaccounting_before_delete_account', $account_id, $account );

	$result = $wpdb->delete( $wpdb->prefix . 'ea_accounts', array( 'id' => $account_id ) );
	if ( ! $result ) {
		return false;
	}

	eaccounting_delete_cache( 'ea_accounts', $account_id );

	/**
	 * Fires after an account is deleted.
	 *
	 * @param int $account_id account id.
	 * @param Account $account account object.
	 *
	 * @since 1.2.1
	 *
	 * @see eaccounting_delete_account()
	 *
	 */
	do_action( 'eaccounting_delete_account', $account_id, $account );

	return $account;
}

/**
 * Sanitizes every account field.
 *
 * If the context is 'raw', then the account object or array will get minimal
 * sanitization of the integer fields.
 *
 * @param object|array $account The account object or array
 * @param string $context Optional. How to sanitize post fields. Accepts 'raw', 'edit', 'db', 'display'. Default 'display'.
 *
 * @return object|Account|array The now sanitized account object or array
 * @see eaccounting_sanitize_account_field()
 *
 * @since 1.2.1
 *
 */
function eaccounting_sanitize_account( $account, $context = 'raw' ) {
	if ( is_object( $account ) ) {
		// Check if post already filtered for this context.
		if ( isset( $account->filter ) && $context == $account->filter ) {
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
		if ( isset( $account['filter'] ) && $context == $account['filter'] ) {
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
 * Sanitizes a account field based on context.
 *
 * Possible context values are:  'raw', 'edit', 'db', 'display'.
 *
 * @param string $field The account Object field name.
 * @param mixed $value The account Object value.
 * @param int $account_id Account id.
 * @param string $context Optional. How to sanitize the field. Possible values are 'raw', 'edit','db', 'display'. Default 'display'.
 *
 * @return mixed Sanitized value.
 * @since 1.2.1
 *
 */
function eaccounting_sanitize_account_field( $field, $value, $account_id, $context ) {
	if ( false !== strpos( $field, '_id' ) || $field === 'id' ) {
		$value = absint( $value );
	}

	$context = strtolower( $context );

	if ( 'raw' === $context ) {
		return $value;
	}

	if ( 'edit' === $context ) {

		/**
		 * Filters an account field to edit before it is sanitized.
		 *
		 * @param mixed $value Value of the account field.
		 * @param int $account_id Account id.
		 *
		 * @since 1.2.1
		 *
		 */
		$value = apply_filters( "eaccounting_edit_account_{$field}", $value, $account_id );

	} elseif ( 'db' === $context ) {

		/**
		 * Filters a account field value before it is sanitized.
		 *
		 * @param mixed $value Value of the account field.
		 * @param int $account_id Account id.
		 *
		 * @since 1.2.1
		 *
		 */
		$value = apply_filters( "eaccounting_pre_account_{$field}", $value, $account_id );

	} else {
		// Use display filters by default.

		/**
		 * Filters the account field sanitized for display.
		 *
		 * The dynamic portion of the filter name, `$field`, refers to the account field name.
		 *
		 * @param mixed $value Value of the account field.
		 * @param int $account_id account id.
		 * @param string $context Context to retrieve the account field value.
		 *
		 * @since 1.2.1
		 *
		 */
		$value = apply_filters( "eaccounting_account_{$field}", $value, $account_id, $context );
	}

	return $value;
}


/**
 * Get account items.
 *
 * @param array $args {
 *                               Optional. Arguments to retrieve accounts.
 *
 * @type string $name The name of the account .
 *
 * @type string $number The number of account.
 *
 * @type string $currency_code The currency_code for the account.
 *
 * @type double $opening_balance The opening balance of the account.
 *
 * @type string $bank_name The bank name for the account.
 *
 * @type string $bank_phone The phone number of the bank on which the account is opened.
 *
 * @type string $bank_address The address of the bank.
 *
 * @type int $enabled The status of the account.
 *
 * @type int $creator_id The creator id for the account.
 *
 * @type string $date_created The date when the account is created.
 *
 *
 * }
 *
 * @return array|int
 * @since 1.1.0
 *
 */
function eaccounting_get_accounts( $args = array() ) {
	global $wpdb;
	// Prepare args.
	$args = wp_parse_args(
		$args,
		array(
			'status'      => 'all',
			'include'     => '',
			'search'      => '',
			'balance'     => false,
			'fields'      => '*',
			'orderby'     => 'id',
			'order'       => 'ASC',
			'number'      => 20,
			'offset'      => 0,
			'paged'       => 1,
			'return'      => 'objects',
			'count_total' => false,
		)
	);

	$qv           = apply_filters( 'eaccounting_get_accounts_args', $args );
	$table        = \EverAccounting\Repositories\Accounts::TABLE;
	$columns      = \EverAccounting\Repositories\Accounts::get_columns();
	$qv['fields'] = wp_parse_list( $qv['fields'] );
	foreach ( $qv['fields'] as $index => $field ) {
		if ( ! in_array( $field, $columns, true ) ) {
			unset( $qv['fields'][ $index ] );
		}
	}
	$fields = is_array( $qv['fields'] ) && ! empty( $qv['fields'] ) ? implode( ',', $qv['fields'] ) : '*';
	$where  = 'WHERE 1=1';

	if ( ! empty( $qv['include'] ) ) {
		$include = implode( ',', wp_parse_id_list( $qv['include'] ) );
		$where   .= " AND $table.`id` IN ($include)";
	} elseif ( ! empty( $qv['exclude'] ) ) {
		$exclude = implode( ',', wp_parse_id_list( $qv['exclude'] ) );
		$where   .= " AND $table.`id` NOT IN ($exclude)";
	}

	if ( ! empty( $qv['status'] ) && ! in_array( $qv['status'], array( 'all', 'any' ), true ) ) {
		$status = eaccounting_string_to_bool( $qv['status'] );
		$status = eaccounting_bool_to_number( $status );
		$where  .= " AND $table.`enabled` = ('$status')";
	}

	$join = '';
	if ( true === $qv['balance'] && ! $qv['count_total'] ) {
		$sub_query = "
		SELECT account_id, SUM(CASE WHEN ea_transactions.type='income' then amount WHEN ea_transactions.type='expense' then - amount END) as total from
		{$wpdb->prefix}ea_transactions as ea_transactions LEFT JOIN {$wpdb->prefix}$table ea_accounts ON ea_accounts.id=ea_transactions.account_id GROUP BY account_id";
		$join      .= " LEFT JOIN ($sub_query) as calculated ON calculated.account_id = {$table}.id";
		$fields    .= " , ( {$table}.opening_balance + IFNULL( calculated.total, 0) ) as balance ";
	}

	// search.
	$search_cols = array( 'name', 'number', 'currency_code', 'bank_name', 'bank_phone', 'bank_address' );
	if ( ! empty( $qv['search'] ) ) {
		$searches = array();
		$where    .= ' AND (';
		foreach ( $search_cols as $col ) {
			$searches[] = $wpdb->prepare( $col . ' LIKE %s', '%' . $wpdb->esc_like( $qv['search'] ) . '%' );
		}
		$where .= implode( ' OR ', $searches );
		$where .= ')';
	}

	if ( ! empty( $qv['due_date'] ) && is_array( $qv['due_date'] ) ) {
		$date_created_query = new \WP_Date_Query( $qv['due_date'], "{$table}.due_date" );
		$where              .= $date_created_query->get_sql();
	}

	$order   = isset( $qv['order'] ) ? strtoupper( $qv['order'] ) : 'ASC';
	$orderby = isset( $qv['orderby'] ) && in_array( $qv['orderby'], $columns, true ) ? eaccounting_clean( $qv['orderby'] ) : "{$table}.id";

	$limit = '';
	if ( isset( $qv['number'] ) && $qv['number'] > 0 ) {
		if ( $qv['offset'] ) {
			$limit = $wpdb->prepare( 'LIMIT %d, %d', $qv['offset'], $qv['number'] );
		} else {
			$limit = $wpdb->prepare( 'LIMIT %d, %d', $qv['number'] * ( $qv['paged'] - 1 ), $qv['number'] );
		}
	}

	$select      = "SELECT {$fields}";
	$from        = "FROM {$wpdb->prefix}$table $table";
	$orderby     = "ORDER BY {$orderby} {$order}";
	$count_total = true === $qv['count_total'];
	$clauses     = compact( 'select', 'from', 'join', 'where', 'orderby', 'limit' );
	$cache_key   = 'query:' . md5( maybe_serialize( $qv ) ) . ':' . wp_cache_get_last_changed( 'ea_accounts' );
	$results     = wp_cache_get( $cache_key, 'ea_accounts' );
	if ( false === $results ) {
		if ( $count_total ) {
			$results = (int) $wpdb->get_var( "SELECT COUNT(id) $from $where" );
			wp_cache_set( $cache_key, $results, 'ea_accounts' );
		} else {
			$results = $wpdb->get_results( implode( ' ', $clauses ) );
			if ( in_array( $fields, array( 'all', '*' ), true ) ) {
				foreach ( $results as $key => $item ) {
					wp_cache_set( $item->id, $item, 'ea_accounts' );
					if ( true === $qv['balance'] ) {
						wp_cache_set( 'balance-' . $item->id, $item->balance, 'ea_accounts' );
					}
				}
			}
			wp_cache_set( $cache_key, $results, 'ea_accounts' );
		}
	}

	if ( 'objects' === $qv['return'] && true !== $qv['count_total'] ) {
		$results = array_map( 'eaccounting_get_account', $results );
	}

	return $results;
}

