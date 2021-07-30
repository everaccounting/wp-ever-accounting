<?php
/**
 * Account Query class.
 *
 *
 * @since   1.2.1
 * @package   EverAccounting
 */

namespace EverAccounting;

/**
 * Class Account_Query
 * @package EverAccounting
 */
class Account_Query {
	/**
	 * Query vars set by the user
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $query;

	/**
	 * Query vars, after parsing
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $query_vars = array();

	/**
	 * SQL fields clauses
	 * @since 1.2.1
	 * @var array
	 */
	public $query_fields;

	/**
	 * SQL from clauses
	 * @since 1.2.1
	 * @var string
	 */
	public $query_from;

	/**
	 * SQL where clauses
	 * @since 1.2.1
	 * @var string
	 */
	public $query_where;

	/**
	 * SQL orderby clauses
	 * @since 1.2.1
	 * @var string
	 */
	public $query_orderby;

	/**
	 * SQL limit clauses
	 * @since 1.2.1
	 * @var string
	 */
	public $query_limit;

	/**
	 * Table name
	 * @since 1.2.1
	 * @var string
	 */
	public $table;

	/**
	 * SQL for the database query.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $request;

	/**
	 * Array of accounts objects or accounts ids.
	 *
	 * @since 1.2.1
	 * @var Account[]|int[]
	 */
	public $results = [];

	/**
	 * The number of accounts found for the current query.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $total = 0;


	/**
	 * Sets up the accounts query, based on the query vars passed.
	 *
	 * @param string|array $query Array or query string of account query parameters. Default empty.
	 *
	 * @since 1.2.1
	 *
	 */
	public function __construct( $query = '' ) {
		if ( ! empty( $query ) ) {
			$this->prepare_query( $query );
			$this->query();
		}
	}

	/**
	 * Retrieve query variable.
	 *
	 * @param string $query_var Query variable key.
	 *
	 * @return mixed
	 * @since 3.5.0
	 *
	 */
	public function get( $query_var ) {
		if ( isset( $this->query_vars[ $query_var ] ) ) {
			return $this->query_vars[ $query_var ];
		}

		return null;
	}

	/**
	 * Set query variable.
	 *
	 * @param string $query_var Query variable key.
	 * @param mixed $value Query variable value.
	 *
	 * @since 3.5.0
	 *
	 */
	public function set( $query_var, $value ) {
		$this->query_vars[ $query_var ] = $value;
	}

	/**
	 * Fills in missing query variables with default values.
	 *
	 * @param array $args Query vars, as passed to class.
	 *
	 * @return array Complete query variables with undefined ones filled in with defaults.
	 * @since 1.2.1
	 *
	 */
	public static function fill_query_vars( $args ) {
		$defaults = array(
			'type'           => '',
			'include'        => array(),
			'exclude'        => array(),
			'search'         => '',
			'search_columns' => array(),
			'orderby'        => 'payment_date',
			'order'          => 'ASC',
			'offset'         => '',
			'number'         => 20,
			'paged'          => 1,
			'count_total'    => true,
			'fields'         => 'all',
		);

		return wp_parse_args( $args, $defaults );
	}

	/**
	 * Prepare the query variables.
	 *
	 * @param string|array $query Array or string of Query parameters.
	 *
	 * @since 1.2.1
	 */
	public function prepare_query( $query = array() ) {
		global $wpdb;
		$this->table = $wpdb->prefix . 'ea_accounts';

		if ( empty( $this->query_vars ) || ! empty( $query ) ) {
			$this->query_limit = null;
			$this->query_vars  = self::fill_query_vars( $query );
		}

		/**
		 * Fires before the Account_Query has been parsed.
		 *
		 * The passed Account_Query object contains the query variables,
		 * not yet passed into SQL.
		 *
		 * @param Account_Query $query Current instance of Account_Query (passed by reference).
		 *
		 * @since 4.0.0
		 *
		 */
		do_action_ref_array( 'eaccounting_pre_get_accounts', array( &$this ) );

		// Ensure that query vars are filled after 'eaccounting_pre_get_accounts'.
		$qv =& $this->query_vars;
		$qv = self::fill_query_vars( $qv );

		if ( is_array( $qv['fields'] ) ) {
			$qv['fields'] = array_unique( $qv['fields'] );

			$this->query_fields = array();
			foreach ( $qv['fields'] as $field ) {
				$field                = 'id' === $field ? 'id' : sanitize_key( $field );
				$this->query_fields[] = "$this->table.$field";
			}
			$this->query_fields = implode( ',', $this->query_fields );
		} elseif ( 'all' === $qv['fields'] ) {
			$this->query_fields = "$this->table.* ";
		} else {
			$this->query_fields = "$this->table.id";
		}

		if ( isset( $qv['count_total'] ) && $qv['count_total'] ) {
			$this->query_fields = 'SQL_CALC_FOUND_ROWS ' . $this->query_fields;
		}

		$this->query_from  = "FROM $this->table";
		$this->query_where = 'WHERE 1=1';

		// Parse and sanitize 'include', for use by 'orderby' as well as 'include' below.
		if ( ! empty( $qv['include'] ) ) {
			$include = wp_parse_id_list( $qv['include'] );
		} else {
			$include = false;
		}

		// fields
		if ( $qv['orderby'] === 'balance' || ! empty( $qv['balance_min'] ) || empty( $qv['balance_max'] ) || ! empty( $qv['balance_between'] ) ) {
			$this->query_from .= " LEFT OUTER JOIN (
				SELECT account_id, SUM(CASE WHEN type='income' then amount WHEN type='expense' then - amount END) balance, currency_code code
				FROM {$wpdb->prefix}ea_transactions
				group by account_id, currency_code
			) transactions ON ({$this->table}.id = transactions.account_id)
			";
		}

		// where

		if ( ! empty( $qv['balance_min'] ) ) {
			$this->query_where .= $wpdb->prepare( " AND transactions >= (%f)", (float) $qv['balance_min'] );
		}

		if ( ! empty( $qv['balance_max'] ) ) {
			$this->query_where .= $wpdb->prepare( " AND transactions <= (%f)", (float) $qv['balance_max'] );
		}

		if ( ! empty( $qv['balance_between'] ) && is_array( $qv['balance_between'] ) ) {
			$min               = min( $qv['balance_between'] );
			$max               = max( $qv['balance_between'] );
			$this->query_where .= $wpdb->prepare( " AND transactions >= (%f) AND transactions <= (%f) ", (float) $min, (float) $max );
		}

		// Sorting.
		$qv['order'] = isset( $qv['order'] ) ? strtoupper( $qv['order'] ) : '';
		$order       = $this->parse_order( $qv['order'] );

		if ( empty( $qv['orderby'] ) ) {
			// Default order is by 'payment_date'.
			$ordersby = array( 'payment_date' => $order );
		} elseif ( is_array( $qv['orderby'] ) ) {
			$ordersby = $qv['orderby'];
		} else {
			// 'orderby' values may be a comma- or space-separated list.
			$ordersby = preg_split( '/[,\s]+/', $qv['orderby'] );
		}

		$orderby_array = array();
		foreach ( $ordersby as $_key => $_value ) {
			if ( ! $_value ) {
				continue;
			}

			if ( is_int( $_key ) ) {
				// Integer key means this is a flat array of 'orderby' fields.
				$_orderby = $_value;
				$_order   = $order;
			} else {
				// Non-integer key means this the key is the field and the value is ASC/DESC.
				$_orderby = $_key;
				$_order   = $_value;
			}

			$parsed = $this->parse_orderby( $_orderby );

			if ( ! $parsed ) {
				continue;
			}

			$orderby_array[] = $parsed . ' ' . $this->parse_order( $_order );
		}

		// If no valid clauses were found, order by payment_date.
		if ( empty( $orderby_array ) ) {
			$orderby_array[] = "payment_date $order";
		}

		$this->query_orderby = 'ORDER BY ' . implode( ', ', $orderby_array );

		// Limit.
		if ( isset( $qv['number'] ) && $qv['number'] > 0 ) {
			if ( $qv['offset'] ) {
				$this->query_limit = $wpdb->prepare( 'LIMIT %d, %d', $qv['offset'], $qv['number'] );
			} else {
				$this->query_limit = $wpdb->prepare( 'LIMIT %d, %d', $qv['number'] * ( $qv['paged'] - 1 ), $qv['number'] );
			}
		}

		$search = '';
		if ( isset( $qv['search'] ) ) {
			$search = trim( $qv['search'] );
		}

		if ( $search ) {
			$leading_wild  = ( ltrim( $search, '*' ) != $search );
			$trailing_wild = ( rtrim( $search, '*' ) != $search );
			if ( $leading_wild && $trailing_wild ) {
				$wild = 'both';
			} elseif ( $leading_wild ) {
				$wild = 'leading';
			} elseif ( $trailing_wild ) {
				$wild = 'trailing';
			} else {
				$wild = false;
			}
			if ( $wild ) {
				$search = trim( $search, '*' );
			}

			$search_columns = array();
			if ( $qv['search_columns'] ) {
				$search_columns = array_intersect( $qv['search_columns'], array(
					'name',
					'number',
					'bank_name',
					'bank_phone',
					'bank_address'
				) );
			}
			if ( ! $search_columns ) {
				$search_columns = array( 'name', 'number', 'bank_name', 'bank_phone', 'bank_address' );
			}

			/**
			 * Filters the columns to search in a Account_Query search.
			 *
			 *
			 * @param string[] $search_columns Array of column names to be searched.
			 * @param string $search Text being searched.
			 * @param Account_Query $query The current Account_Query instance.
			 *
			 * @since 1.2.1
			 *
			 */
			$search_columns = apply_filters( 'eaccounting_account_search_columns', $search_columns, $search, $this );

			$this->query_where .= $this->get_search_sql( $search, $search_columns, $wild );
		}

		if ( ! empty( $include ) ) {
			// Sanitized earlier.
			$ids               = implode( ',', $include );
			$this->query_where .= " AND $this->table.id IN ($ids)";
		} elseif ( ! empty( $qv['exclude'] ) ) {
			$ids               = implode( ',', wp_parse_id_list( $qv['exclude'] ) );
			$this->query_where .= " AND $this->table.id NOT IN ($ids)";
		}

		/**
		 * Fires after the Account_Query has been parsed, and before
		 * the query is executed.
		 *
		 * The passed Account_Query object contains SQL parts formed
		 * from parsing the given query.
		 *
		 * @param Account_Query $query Current instance of Account_Query (passed by reference).
		 *
		 * @since 1.2.1
		 *
		 */
		do_action_ref_array( 'eaccounting_pre_account_query', array( &$this ) );

	}

	/**
	 * Execute the query, with the current variables.
	 *
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	public function query() {
		global $wpdb;

		$qv =& $this->query_vars;

		/**
		 * Filters the users array before the query takes place.
		 *
		 * Return a non-null value to bypass WordPress' default user queries.
		 *
		 * Filtering functions that require pagination information are encouraged to set
		 * the `total_users` property of the Account_Query object, passed to the filter
		 * by reference. If Account_Query does not perform a database query, it will not
		 * have enough information to generate these values itself.
		 *
		 * @param array|null $results Return an array of user data to short-circuit WP's user query
		 *                               or null to allow WP to run its normal queries.
		 * @param Account_Query $query The Account_Query instance (passed by reference).
		 *
		 * @since 5.1.0
		 *
		 */
		$this->results = apply_filters_ref_array( 'eaccounting_accounts_pre_query', array( null, &$this ) );

		if ( null === $this->results ) {
			$this->request = "SELECT $this->query_fields $this->query_from $this->query_where $this->query_orderby $this->query_limit";

			if ( is_array( $qv['fields'] ) || 'all' === $qv['fields'] ) {
				$this->results = $wpdb->get_results( $this->request );
			} else {
				$this->results = $wpdb->get_col( $this->request );
			}

			if ( isset( $qv['count_total'] ) && $qv['count_total'] ) {
				/**
				 * Filters SELECT FOUND_ROWS() query for the current Account_Query instance.
				 *
				 * @param string $sql The SELECT FOUND_ROWS() query for the current Account_Query.
				 * @param Account_Query $query The current Account_Query instance.
				 *
				 * @global \wpdb $wpdb WordPress database abstraction object.
				 *
				 * @since 3.2.0
				 * @since 5.1.0 Added the `$this` parameter.
				 *
				 */
				$found_users_query = apply_filters( 'eaccounting_found_accounts_query', 'SELECT FOUND_ROWS()', $this );

				$this->total = (int) $wpdb->get_var( $found_users_query );
			}
		}

		if ( ! $this->results ) {
			return;
		}

		if ( 'all' === $qv['fields'] ) {
			foreach ( $this->results as $key => $data ) {
				eaccounting_set_cache( 'ea_accounts', $data );
				$account = new Account( null );
				$account->init( $data );
				$this->results[ $key ] = $account;
			}
		}
	}

	/**
	 * Used internally to generate an SQL string for searching across multiple columns
	 *
	 * @param string $string
	 * @param array $cols
	 * @param bool $wild Whether to allow wildcard searches.
	 *
	 * @return string
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 *
	 */
	protected function get_search_sql( $string, $cols, $wild = false ) {
		global $wpdb;

		$searches      = array();
		$leading_wild  = ( 'leading' === $wild || 'both' === $wild ) ? '%' : '';
		$trailing_wild = ( 'trailing' === $wild || 'both' === $wild ) ? '%' : '';
		$like          = $leading_wild . $wpdb->esc_like( $string ) . $trailing_wild;

		foreach ( $cols as $col ) {
			if ( 'ID' === $col ) {
				$searches[] = $wpdb->prepare( "$col = %s", $string );
			} else {
				$searches[] = $wpdb->prepare( "$col LIKE %s", $like );
			}
		}

		return ' AND (' . implode( ' OR ', $searches ) . ')';
	}


	/**
	 * Parse and sanitize 'orderby' keys passed to the query.
	 *
	 * @param string $orderby Alias for the field to order by.
	 *
	 * @return string Value to used in the ORDER clause, if `$orderby` is valid.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 *
	 */
	protected function parse_orderby( $orderby ) {
		global $wpdb;

		$_orderby = '';
		if ( in_array( $orderby, array(
			'name',
			'number',
			'currency_code',
			'bank_name',
			'bank_phone',
			'bank_address'
		), true ) ) {
			$_orderby = $orderby;
		} elseif ( $orderby === 'opening_balance' ) {
			$_orderby = $orderby;
		} elseif ( 'id' === $orderby ) {
			$_orderby = 'id';
		} elseif ( 'include' === $orderby && ! empty( $this->query_vars['include'] ) ) {
			$include     = wp_parse_id_list( $this->query_vars['include'] );
			$include_sql = implode( ',', $include );
			$_orderby    = "FIELD( $this->table.id, $include_sql )";
		}

		return $_orderby;
	}

	/**
	 * Parse an 'order' query variable and cast it to ASC or DESC as necessary.
	 *
	 * @param string $order The 'order' query variable.
	 *
	 * @return string The sanitized 'order' query variable.
	 * @since 1.2.1
	 *
	 */
	protected function parse_order( $order ) {
		if ( ! is_string( $order ) || empty( $order ) ) {
			return 'DESC';
		}

		if ( 'ASC' === strtoupper( $order ) ) {
			return 'ASC';
		}

		return 'DESC';
	}

	/**
	 * Return the list of accounts.
	 *
	 * @return array Array of results.
	 * @since 1.2.1
	 *
	 */
	public function get_results() {
		return $this->results;
	}

	/**
	 * Return the total number of items for the current query.
	 *
	 * @return int Number of total accounts.
	 * @since 1.2.1
	 *
	 */
	public function get_total() {
		return $this->total;
	}
}
