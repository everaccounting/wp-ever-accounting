<?php
/**
 * Account Query class.
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
	 * SQL string used to perform database query.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $request;

	/**
	 * SQL query clauses.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	protected $sql_clauses = array(
		'fields'  => '',
		'from'    => '',
		'join'    => '',
		'where'   => '',
		'groupby' => '',
		'having'  => '',
		'orderby' => '',
		'limit'   => '',
	);

	/**
	 * Query vars set by the user.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $query_vars;

	/**
	 * Default values for query vars.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $query_var_defaults;

	/**
	 * List of items located by the query.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $results = [];

	/**
	 * The number of items found for the current query.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $total = 0;

	/**
	 * Table name without prefix.
	 * @since 1.2.1
	 * @var string
	 */
	const TABLE_NAME = 'ea_accounts';

	/**
	 * Table name with prefix.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $table;

	/**
	 * Constructor.
	 *
	 * Sets up the Item query, if parameter is not empty.
	 *
	 * @param string|array $query Query string or array of vars.
	 *
	 *
	 * @since 1.2.1
	 */
	public function __construct( $query = '' ) {
		$this->query_var_defaults = array(
			'include'        => array(),
			'exclude'        => array(),
			'search'         => '',
			'search_columns' => array(),
			'orderby'        => 'payment_date',
			'order'          => 'ASC',
			'offset'         => '',
			'number'         => 20,
			'paged'          => 1,
			'no_found_rows'  => false,
			'fields'         => 'all',
		);

		if ( ! empty( $query ) ) {
			$this->query( $query );
		}

	}

	/**
	 * Retrieve query variable.
	 *
	 * @param string $query_var Query variable key.
	 *
	 * @return mixed
	 * @since 1.2.1
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
	 * @since 1.2.1
	 *
	 */
	public function set( $query_var, $value ) {
		$this->query_vars[ $query_var ] = $value;
	}


	/**
	 * Parse arguments passed to the query with default query parameters.
	 *
	 * @param string|array $query Query arguments.
	 *
	 * @since 1.2.1
	 *
	 */
	public function parse_query( $query = '' ) {
		if ( empty( $query ) ) {
			$query = $this->query_vars;
		}

		$query = wp_parse_args( $query, $this->query_var_defaults );

		// Parse args.
		$query['number']        = absint( $query['number'] );
		$query['offset']        = absint( $query['offset'] );
		$query['no_found_rows'] = (bool) $query['no_found_rows'];

		$this->query_vars = $query;

		/**
		 * Fires after term query vars have been parsed.
		 *
		 * @param Account_Query $this Current instance of Query.
		 *
		 * @since 1.2.1
		 *
		 */
		do_action( 'eaccounting_parse_account_query', $this );
	}

	/**
	 * Prepare the query variables.
	 *
	 * @param string|array $query Array or string of Query parameters.
	 *
	 * @since 1.2.1
	 */
	public function prepare_query() {
		global $wpdb;
		$args = &$this->query_vars;

		/**
		 * Filters the query arguments.
		 *
		 * @param array $args An array of arguments.
		 *
		 * @since 1.2.1
		 *
		 */
		$qv = apply_filters( 'eaccounting_get_accounts_args', $args );

		// Setup table.
		$this->table = $wpdb->prefix . self::TABLE_NAME;

		// Fields setup.
		if ( is_array( $qv['fields'] ) ) {
			$qv['fields'] = array_unique( $qv['fields'] );

			$fields = array();
			foreach ( $qv['fields'] as $field ) {
				$field    = 'id' === $field ? 'id' : sanitize_key( $field );
				$fields[] = "$this->table.$field";
			}
			$this->sql_clauses['fields'] .= implode( ',', $fields );
		} elseif ( 'all' === $qv['fields'] ) {
			$this->sql_clauses['fields'] .= "$this->table.* ";
		} else {
			$this->sql_clauses['fields'] .= "$this->table.id";
		}

		if ( false === $args['no_found_rows'] ) {
			$this->sql_clauses['fields'] = 'SQL_CALC_FOUND_ROWS ' . $this->sql_clauses['fields'];
		}

		$this->sql_clauses['from']  .= "FROM $this->table";
		$this->sql_clauses['where'] .= 'WHERE 1=1';


		// Where
		if ( ! empty( $qv['include'] ) ) {
			// Sanitized earlier.
			$ids                        = implode( ',', wp_parse_id_list( $qv['include'] ) );
			$this->sql_clauses['where'] .= " AND $this->table.id IN ($ids)";
		} elseif ( ! empty( $qv['exclude'] ) ) {
			$ids                        = implode( ',', wp_parse_id_list( $qv['exclude'] ) );
			$this->sql_clauses['where'] .= " AND $this->table.id NOT IN ($ids)";
		}

		if ( $qv['orderby'] === 'balance' || ! empty( $qv['balance_min'] ) || empty( $qv['balance_max'] ) || ! empty( $qv['balance_between'] ) ) {
			$this->sql_clauses['join'] .= " LEFT OUTER JOIN (
				SELECT account_id, SUM(CASE WHEN type='income' then amount WHEN type='expense' then - amount END) balance, currency_code code
				FROM {$wpdb->prefix}ea_transactions
				group by account_id, currency_code
			) transactions ON ({$this->table}.id = transactions.account_id)
			";
		}
		if ( ! empty( $qv['balance_min'] ) ) {
			$this->sql_clauses['where'] .= $wpdb->prepare( " AND transactions >= (%f)", (float) $qv['balance_min'] );
		}

		if ( ! empty( $qv['balance_max'] ) ) {
			$this->sql_clauses['where'] .= $wpdb->prepare( " AND transactions <= (%f)", (float) $qv['balance_max'] );
		}

		if ( ! empty( $qv['balance_between'] ) && is_array( $qv['balance_between'] ) ) {
			$min                        = min( $qv['balance_between'] );
			$max                        = max( $qv['balance_between'] );
			$this->sql_clauses['where'] .= $wpdb->prepare( " AND transactions >= (%f) AND transactions <= (%f) ", (float) $min, (float) $max );
		}

		// Search
		$search         = '';
		$search_columns = array( 'name', 'number', 'bank_name', 'bank_phone', 'bank_address' );
		if ( ! empty( $args['search'] ) ) {
			$search = trim( $args['search'] );
		}
		if ( ! empty( $args['search_columns'] ) ) {
			$search_columns = array_intersect( $args['search_columns'], $search_columns );
		}
		if ( ! empty( $search ) ) {
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

			$this->sql_clauses['where'] .= $this->get_search_sql( $search, $search_columns, $wild );
		}


		// Order
		$order = $this->parse_order( $args['order'] );
		if ( is_array( $args['orderby'] ) ) {
			$ordersby = $args['orderby'];
		} else {
			// 'orderby' values may be a comma- or space-separated list.
			$ordersby = preg_split( '/[,\s]+/', $args['orderby'] );
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
			$orderby_array[] = "id $order";
		}
		$this->sql_clauses['orderby'] = 'ORDER BY ' . implode( ', ', $orderby_array );

		// Limit.
		if ( isset( $args['number'] ) && $args['number'] > 0 ) {
			if ( $args['offset'] ) {
				$this->sql_clauses['limit'] = $wpdb->prepare( 'LIMIT %d, %d', $args['offset'], $args['number'] );
			} else {
				$this->sql_clauses['limit'] = $wpdb->prepare( 'LIMIT %d, %d', $args['number'] * ( $args['paged'] - 1 ), $args['number'] );
			}
		}
	}

	/**
	 * Run the query and retrieves the results.
	 *
	 * @param string $query
	 *
	 * @return array|object|null
	 */
	public function query( $query = '' ) {
		global $wpdb;
		if ( ! empty( $query ) ) {
			$this->parse_query( $query );
		}

		$key          = md5( serialize( wp_array_slice_assoc( $this->query_vars, array_keys( $this->query_var_defaults ) ) ) );
		$last_changed = wp_cache_get_last_changed( 'ea_accounts' );
		$cache_key    = "ea_accounts:$key:$last_changed";
		$cache        = wp_cache_get( $cache_key, 'ea_accounts' );
	
		if ( false !== $cache ) {
			$this->results = $cache->results;
			$this->total   = $cache->total;

			return $this->results;
		}

		echo 'NO CACHW';
		// Prepare out query.
		$this->prepare_query();

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

		if ( empty( $this->results ) ) {
			$this->request = "SELECT {$this->sql_clauses['fields']} {$this->sql_clauses['from']} {$this->sql_clauses['join']} {$this->sql_clauses['where']} {$this->sql_clauses['groupby']} {$this->sql_clauses['having']} {$this->sql_clauses['orderby']} {$this->sql_clauses['limit']}";

			if ( is_array( $this->query_vars['fields'] ) || 'all' === $this->query_vars['fields'] ) {
				$results       = $wpdb->get_results( $this->request );
				$this->results = $results;
				//$this->results = ! empty( $results ) ? array_map( 'eaccounting_get_accounts', $results ) : [];
			} else {
				$this->results = $wpdb->get_col( $this->request );
			}

			if ( ! $this->query_vars['no_found_rows'] ) {
				/**
				 * Filters SELECT FOUND_ROWS() query for the current Account_Query instance.
				 *
				 * @param string $sql The SELECT FOUND_ROWS() query for the current Account_Query.
				 * @param Account_Query $query The current Account_Query instance.
				 *
				 * @global \wpdb $wpdb WordPress database abstraction object.
				 *
				 * @since 1.2.1
				 *
				 */
				$count_query = apply_filters( 'eaccounting_count_accounts_query', 'SELECT FOUND_ROWS()', $this );

				$this->total = (int) $wpdb->get_var( $count_query );
			}
		}

		$cache          = new \StdClass;
		$cache->results = $this->results;
		$cache->total   = $this->total;


		wp_cache_add( $cache_key, $cache, 'ea_accounts' );

		return $this->results;
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
			if ( 'id' === $col ) {
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
		$_orderby = '';
		if ( in_array( $orderby, array(
			'name',
			'number',
			'currency_code',
			'bank_name',
			'bank_phone',
			'bank_address',
			'balance',
		), true ) ) {
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
