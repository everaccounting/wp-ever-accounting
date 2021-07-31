<?php

namespace EverAccounting;

class Contact_Query_BK {
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
	 * Date query container
	 *
	 * @since 1.2.1
	 * @var \WP_Date_Query A date query instance.
	 */
	public $date_query = false;

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
	 * Array of transactions objects or transactions ids.
	 *
	 * @since 1.2.1
	 * @var Transaction[]|int[]
	 */
	public $results = [];

	/**
	 * The amount of transactions for the current query.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $total = 0;

	/**
	 * Constructor.
	 *
	 * Sets up the WordPress query, if parameter is not empty.
	 *
	 * @param string|array $query URL query string or array of vars.
	 *
	 * @see Transaction_Query::parse_query() for all available arguments.
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
		$this->table = $wpdb->prefix . 'ea_transactions';

		if ( empty( $this->query_vars ) || ! empty( $query ) ) {
			$this->query_limit = null;
			$this->query_vars  = self::fill_query_vars( $query );
		}

		/**
		 * Fires before the Transaction_Query has been parsed.
		 *
		 * The passed Transaction_Query object contains the query variables,
		 * not yet passed into SQL.
		 *
		 * @param Transaction_Query $query Current instance of Transaction_Query (passed by reference).
		 *
		 * @since 4.0.0
		 *
		 */
		do_action_ref_array( 'eaccounting_pre_get_transactions', array( &$this ) );

		// Ensure that query vars are filled after 'pre_get_users'.
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
		if( $qv['orderby'] === 'amount' || !empty($qv['amount_min']) || empty( $qv['amount_max']) || !empty( $qv['amount_between'] )){
			$this->query_from .= " LEFT OUTER JOIN (
				SELECT id, ($this->table.amount/$this->table.currency_rate) as default_amount
				FROM $this->table
			) a ON ({$this->table}.id = a.id)
			";
		}

		// where

		if ( ! empty( $qv['type'] ) && $qv['type'] !== 'all' ) {
			$types             = implode( "','", wp_parse_list( $qv['type'] ) );
			$this->query_where .= " AND $this->table.`type` IN ('$types')";
		}

		if ( ! empty( $qv['currency_code'] ) ) {
			$currency_code     = implode( "','", wp_parse_list( $qv['currency_code'] ) );
			$this->query_where .= " AND $this->table.`currency_code` IN ('$currency_code')";
		}

		if ( ! empty( $qv['payment_method'] ) ) {
			$payment_method    = implode( "','", wp_parse_list( $qv['payment_method'] ) );
			$this->query_where .= " AND $this->table.`payment_method` IN ('$payment_method')";
		}

		if ( ! empty( $qv['account_id'] ) ) {
			$account_id        = implode( ',', wp_parse_id_list( $qv['account_id'] ) );
			$this->query_where .= " AND $this->table.`account_id` IN ($account_id)";
		}

		if ( ! empty( $qv['account__in'] ) ) {
			$account_in        = implode( ',', wp_parse_id_list( $qv['account__in'] ) );
			$this->query_where .= " AND $this->table.`account_id` IN ($account_in)";
		}

		if ( ! empty( $qv['account__not_in'] ) ) {
			$account_not_in    = implode( ',', wp_parse_id_list( $qv['account__not_in'] ) );
			$this->query_where .= " AND $this->table.`account_id` NOT IN ($account_not_in)";
		}

		if ( ! empty( $qv['document_id'] ) ) {
			$document_id       = implode( ',', wp_parse_id_list( $qv['document_id'] ) );
			$this->query_where .= " AND $this->table.`document_id` IN ($document_id)";
		}

		if ( ! empty( $qv['category_id'] ) ) {
			$category_in       = implode( ',', wp_parse_id_list( $qv['category_id'] ) );
			$this->query_where .= " AND $this->table.`category_id` IN ($category_in)";
		}

		if ( ! empty( $qv['category__in'] ) ) {
			$category_in       = implode( ',', wp_parse_id_list( $qv['category__in'] ) );
			$this->query_where .= " AND $this->table.`contact_id` IN ($category_in)";
		}

		if ( ! empty( $qv['category__not_in'] ) ) {
			$category_not_in   = implode( ',', wp_parse_id_list( $qv['category__not_in'] ) );
			$this->query_where .= " AND $this->table.`contact_id` NOT IN ($category_not_in)";
		}

		if ( ! empty( $qv['contact_id'] ) ) {
			$contact_id        = implode( ',', wp_parse_id_list( $qv['contact_id'] ) );
			$this->query_where .= " AND $this->table.`contact_id` IN ($contact_id)";
		}

		if ( ! empty( $qv['parent_id'] ) ) {
			$parent_id         = implode( ',', wp_parse_id_list( $qv['parent_id'] ) );
			$this->query_where .= " AND $this->table.`parent_id` IN ($parent_id)";
		}

		if ( ! empty( $qv['amount_min'] ) ) {
			$this->query_where .= $wpdb->prepare( " AND default_amount >= (%f)", (float) $qv['amount_min'] );
		}

		if ( ! empty( $qv['amount_max'] ) ) {
			$this->query_where .= $wpdb->prepare( " AND default_amount <= (%f)", (float) $qv['amount_max'] );
		}

		if ( ! empty( $qv['amount_between'] ) && is_array( $qv['amount_between'] ) ) {
			$min               = min( $qv['amount_between'] );
			$max               = max( $qv['amount_between'] );
			$this->query_where .= $wpdb->prepare( " AND default_amount >= (%f) AND default_amount <= (%f) ", (float) $min, (float) $max );
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
				$search_columns = array_intersect( $qv['search_columns'], array( 'reference', 'description' ) );
			}
			if ( ! $search_columns ) {
				$search_columns = array( 'reference', 'description' );
			}

			/**
			 * Filters the columns to search in a Transaction_Query search.
			 *
			 *
			 * @param string[] $search_columns Array of column names to be searched.
			 * @param string $search Text being searched.
			 * @param Transaction_Query $query The current Transaction_Query instance.
			 *
			 * @since 3.6.0
			 *
			 */
			$search_columns = apply_filters( 'eaccounting_transaction_search_columns', $search_columns, $search, $this );

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

		// Date queries are allowed for the user_registered field.


		/**
		 * Fires after the Transaction_Query has been parsed, and before
		 * the query is executed.
		 *
		 * The passed Transaction_Query object contains SQL parts formed
		 * from parsing the given query.
		 *
		 * @param Transaction_Query $query Current instance of Transaction_Query (passed by reference).
		 *
		 * @since 1.2.1
		 *
		 */
		do_action_ref_array( 'eaccounting_pre_transaction_query', array( &$this ) );

	}

	/**
	 * Execute the query, with the current variables.
	 *
	 * @since 1.2.1
	 *
	 * @global wpdb $wpdb WordPress database abstraction object.
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
		 * the `total_users` property of the Transaction_Query object, passed to the filter
		 * by reference. If Transaction_Query does not perform a database query, it will not
		 * have enough information to generate these values itself.
		 *
		 * @param array|null $results Return an array of user data to short-circuit WP's user query
		 *                               or null to allow WP to run its normal queries.
		 * @param Transaction_Query $query The Transaction_Query instance (passed by reference).
		 *
		 * @since 5.1.0
		 *
		 */
		$this->results = apply_filters_ref_array( 'eaccounting_transactions_pre_query', array( null, &$this ) );

		if ( null === $this->results ) {
			$this->request = "SELECT $this->query_fields $this->query_from $this->query_where $this->query_orderby $this->query_limit";

			if ( is_array( $qv['fields'] ) || 'all' === $qv['fields'] ) {
				$this->results = $wpdb->get_results( $this->request );
			} else {
				$this->results = $wpdb->get_col( $this->request );
			}

			if ( isset( $qv['count_total'] ) && $qv['count_total'] ) {
				/**
				 * Filters SELECT FOUND_ROWS() query for the current Transaction_Query instance.
				 *
				 * @param string $sql The SELECT FOUND_ROWS() query for the current Transaction_Query.
				 * @param Transaction_Query $query The current Transaction_Query instance.
				 *
				 * @global \wpdb $wpdb WordPress database abstraction object.
				 *
				 * @since 3.2.0
				 * @since 5.1.0 Added the `$this` parameter.
				 *
				 */
				$found_users_query = apply_filters( 'eaccounting_found_transactions_query', 'SELECT FOUND_ROWS()', $this );

				$this->total = (int) $wpdb->get_var( $found_users_query );
			}
		}

		if ( ! $this->results ) {
			return;
		}
		if ( 'all' === $qv['fields'] ) {
			foreach ( $this->results as $key => $transaction ) {
				eaccounting_set_cache( 'ea_transactions', $transaction );
				$this->results[ $key ] = new Transaction( $transaction );
			}
		}
	}

	/**
	 * Used internally to generate an SQL string for searching across multiple columns
	 *
	 * @param string $string
	 * @param array $cols
	 * @param bool $wild Whether to allow wildcard searches. Default is false for Network Admin, true for single site.
	 *                       Single site allows leading and trailing wildcards, Network Admin only trailing.
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
			'type',
			'payment_date',
			'currency_code',
			'currency_rate',
			'description',
			'payment_method',
			'reference',
			'reconciled',
			'date_created'
		), true ) ) {
			$_orderby = $orderby;
		} elseif ( $orderby === 'amount' ) {
			$_orderby = 'default_amount';
		} elseif ( 'account_id' === $orderby || 'account' === $orderby ) {
			$this->query_from .= " LEFT OUTER JOIN (
				SELECT id, name as account_name
				FROM {$wpdb->prefix}ea_accounts
			) accounts ON ({$this->table}.account_id = accounts.id)
			";
			$_orderby = 'account_name';
		} elseif ( 'category_id' === $orderby || 'category' === $orderby ) {
			$this->query_from .= " LEFT OUTER JOIN (
				SELECT id, name as category_name
				FROM {$wpdb->prefix}ea_categories
			) categories ON ({$this->table}.category_id = ea_categories.id)
			";
			$_orderby = 'category_name';
		}  elseif ( 'contact_id' === $orderby || 'contact' === $orderby ) {
			$this->query_from .= " LEFT OUTER JOIN (
				SELECT id, name as contact_name
				FROM {$wpdb->prefix}ea_contacts
			) contacts ON ({$this->table}.contact_id = ea_contacts.id)
			";
			$_orderby = 'contact_name';
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
	 * Return the list of users.
	 *
	 * @return array Array of results.
	 * @since 1.2.1
	 *
	 */
	public function get_results() {
		return $this->results;
	}

	/**
	 * Return the total number of users for the current query.
	 *
	 * @return int Number of total users.
	 * @since 1.2.1
	 *
	 */
	public function get_total() {
		return $this->total;
	}
}
