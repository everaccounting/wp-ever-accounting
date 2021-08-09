<?php
/**
 * Contact Query class.
 * @since   1.2.1
 * @package   EverAccounting
 */

namespace EverAccounting;

/**
 * Class Contact_Query
 * @package EverAccounting
 */
class Contact_Query {
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
	 * List of contacts located by the query.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $results = [];

	/**
	 * The number of contacts found for the current query.
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
	const TABLE_NAME = 'ea_contacts';

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
	 * Sets up the Category query, if parameter is not empty.
	 *
	 * @param string|array $query Query string or array of vars.
	 *
	 *
	 * @since 1.2.1
	 */
	public function __construct( $query = null ) {
		$this->query_var_defaults = array(
			'include'        => array(),
			'exclude'        => array(),
			'search'         => '',
			'search_columns' => array(),
			'orderby'        => 'date_created',
			'order'          => 'ASC',
			'offset'         => '',
			'number'         => 20,
			'paged'          => 1,
			'no_found_rows'  => false,
			'fields'         => 'all',
		);

		if ( ! is_null( $query ) ) {
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
	 * Prepare the query variables.
	 *
	 * @param string|array $query Array or string of Query parameters.
	 *
	 * @since 1.2.1
	 */
	public function prepare_query( $query = null ) {
		global $wpdb;
		if ( empty( $this->query_vars ) || ! empty( $query ) ) {
			$this->query_vars = (array) wp_parse_args( $query, $this->query_var_defaults );
		}

		$this->table = $wpdb->prefix . self::TABLE_NAME;
		$qv          =& $this->query_vars;

		// Parse args.
		if ( ! empty( $qv['fields'] ) && 'all' !== $qv['fields'] ) {
			$qv['fields'] = array_unique( wp_parse_list( $qv['fields'] ) );
		}
		$qv['number']        = absint( $qv['number'] );
		$qv['offset']        = absint( $qv['offset'] );
		$qv['paged']         = absint( $qv['paged'] );
		$qv['no_found_rows'] = (bool) $qv['no_found_rows'];


		/**
		 * Fires after the main query vars have been parsed.
		 *
		 * @param self $query The query instance (passed by reference).
		 *
		 * @since 1.2.1
		 *
		 */
		do_action_ref_array( 'eaccounting_parse_contact_query', array( &$this ) );


		/**
		 * Filters the query arguments.
		 *
		 * @param array $args An array of arguments.
		 *
		 * @since 1.2.1
		 *
		 */
		$qv = apply_filters( 'eaccounting_get_contacts_args', $qv );

		// Alias.
		$query_fields  = &$this->sql_clauses['fields'];
		$query_from    = &$this->sql_clauses['from'];
		$query_where   = &$this->sql_clauses['where'];
		$query_join    = &$this->sql_clauses['join'];
		$query_orderby = &$this->sql_clauses['orderby'];
		$query_limit   = &$this->sql_clauses['limit'];
		// Fields setup.
		if ( is_array( $qv['fields'] ) ) {
			$query_fields .= implode( ',', $qv['fields'] );
		} elseif ( 'all' === $qv['fields'] ) {
			$query_fields .= "$this->table.* ";
		} else {
			$query_fields .= "$this->table.id";
		}

		if ( false === $qv['no_found_rows'] ) {
			$query_fields = 'SQL_CALC_FOUND_ROWS ' . $query_fields;
		}

		// Query from.
		$query_from = "FROM $this->table";

		// Query where.
		$query_where = 'WHERE 1=1';
		if ( ! empty( $qv['include'] ) ) {
			// Sanitized earlier.
			$ids         = implode( ',', wp_parse_id_list( $qv['include'] ) );
			$query_where .= " AND $this->table.id IN ($ids)";
		} elseif ( ! empty( $qv['exclude'] ) ) {
			$ids         = implode( ',', wp_parse_id_list( $qv['exclude'] ) );
			$query_where .= " AND $this->table.id NOT IN ($ids)";
		}
		if ( ! empty( $qv['type'] ) && $qv['type'] !== 'all' ) {
			$types       = implode( "','", wp_parse_list( $qv['type'] ) );
			$query_where .= " AND $this->table.`type` IN ('$types')";
		}
		if ( ! empty( $qv['country'] ) ) {
			$country     = implode( "','", wp_parse_list( $qv['country'] ) );
			$query_where .= " AND $this->table.`country` IN ('$country')";
		}


		// Search
		$search         = '';
		$search_columns = array( 'name', 'company', 'email', 'phone' );
		if ( ! empty( $qv['search'] ) ) {
			$search = trim( $qv['search'] );
		}
		if ( ! empty( $qv['search_columns'] ) ) {
			$search_columns = array_intersect( $qv['search_columns'], $search_columns );
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
			 * Filters the columns to search in a Contact_Query search.
			 *
			 *
			 * @param string[] $search_columns Array of column names to be searched.
			 * @param string $search Text being searched.
			 * @param Contact_Query $query The current Contact_Query instance.
			 *
			 * @since 1.2.1
			 *
			 */
			$search_columns = apply_filters( 'eaccounting_contact_search_columns', $search_columns, $search, $this );

			$query_where .= $this->get_search_sql( $search, $search_columns, $wild );
		}

		// Order
		$order = $this->parse_order( $qv['order'] );
		if ( is_array( $qv['orderby'] ) ) {
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

		// If no valid clauses were found, order by name.
		if ( empty( $orderby_array ) ) {
			$orderby_array[] = "id $order";
		}

		$query_orderby .= 'ORDER BY ' . implode( ', ', $orderby_array );


		// Limit.
		if ( isset( $qv['number'] ) && $qv['number'] > 0 ) {
			if ( $qv['offset'] ) {
				$query_limit .= $wpdb->prepare( 'LIMIT %d, %d', $qv['offset'], $qv['number'] );
			} else {
				$query_limit .= $wpdb->prepare( 'LIMIT %d, %d', $qv['number'] * ( $qv['paged'] - 1 ), $qv['number'] );
			}
		}
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
		 * Filters all query clauses at once, for convenience.
		 *
		 * Covers the WHERE, GROUP BY, JOIN, ORDER BY,
		 * fields (SELECT), and LIMITS clauses.
		 *
		 * @param string[] $clauses Associative array of the clauses for the query.
		 * @param Contact_Query $query The Contact_Query instance (passed by reference).
		 *
		 * @since 1.2.1
		 *
		 */
		$clauses = (array) apply_filters_ref_array( 'eaccounting_contact_query_clauses', array( $this->sql_clauses, &$this ) );

		$key          = md5( serialize( wp_array_slice_assoc( $this->query_vars, array_keys( $this->query_var_defaults ) ) ) . $this->request );
		$last_changed = wp_cache_get_last_changed( 'ea_contacts' );
		$cache_key    = "ea_contacts:$key:$last_changed";
		$cache        = wp_cache_get( $cache_key, 'ea_contacts' );

		if ( false !== $cache ) {
			$this->results = $cache->results;
			$this->total   = $cache->total;

			return $this->results;
		}

		/**
		 * Filters the query array before the query takes place.
		 *
		 * Return a non-null value to bypass WordPress' default user queries.
		 *
		 * @param array|null $results Return an array of user data to short-circuit the query
		 *                               or null to allow its normal queries.
		 * @param Contact_Query $query The Contact_Query instance (passed by reference).
		 *
		 * @since 1.2.1
		 *
		 */
		$this->results = apply_filters_ref_array( 'eaccounting_pre_contact_query', array( null, &$this ) );

		if ( null === $this->results ) {
			$this->request = "SELECT {$clauses['fields']} {$clauses['from']} {$clauses['join']} {$clauses['where']} {$clauses['groupby']} {$clauses['having']} {$clauses['orderby']} {$clauses['limit']}";

			if ( is_array( $qv['fields'] ) || 'all' === $qv['fields'] ) {
				$this->results = $wpdb->get_results( $this->request );
			} else {
				$this->results = $wpdb->get_col( $this->request );
			}

			if ( ! $this->query_vars['no_found_rows'] ) {
				/**
				 * Filters SELECT FOUND_ROWS() query for the current Contact_Query instance.
				 *
				 * @param string $sql The SELECT FOUND_ROWS() query for the current Contact_Query.
				 * @param Contact_Query $query The current Contact_Query instance.
				 *
				 * @global \wpdb $wpdb WordPress database abstraction object.
				 *
				 * @since 1.2.1
				 *
				 */
				$count_query = apply_filters( 'eaccounting_count_contacts_query', 'SELECT FOUND_ROWS()', $this );
				$this->total = (int) $wpdb->get_var( $count_query );
			}

			/**
			 * Filters the raw contact results array.
			 *
			 * @param Category[] $contacts Array of contacts objects.
			 * @param Contact_Query $query The Contact_Query instance (passed by reference).
			 *
			 * @since 1.2.1
			 *
			 */
			$this->results = apply_filters_ref_array( 'eaccounting_contacts_results', array( $this->results, &$this ) );

			if ( 'all' === $qv['fields'] ) {
				foreach ( $this->results as $key => $row ) {
					wp_cache_add( $row->id, $row, 'ea_contacts' );
					$contact = new Contact;
					$contact->set_props( $row );
					$contact->set_object_read( true );
					$this->results[ $key ] = $contact;
				}
			}
		}

		$cache          = new \StdClass;
		$cache->results = $this->results;
		$cache->total   = $this->total;


		wp_cache_add( $cache_key, $cache, 'ea_contacts' );

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
		if ( in_array( $orderby, array( 'id', 'user_id', 'name', 'company', 'email', 'phone', 'birth_date', 'date_created' ) ) ) {
			$_orderby = $orderby;
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
	 * Return the list of contacts.
	 *
	 * @return array Array of results.
	 * @since 1.2.1
	 *
	 */
	public function get_results() {
		return $this->results;
	}

	/**
	 * Return the total number of contacts for the current query.
	 *
	 * @return int Number of total contacts.
	 * @since 1.2.1
	 *
	 */
	public function get_total() {
		return $this->total;
	}
}
