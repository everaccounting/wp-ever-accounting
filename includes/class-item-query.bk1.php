<?php
/**
 * Item Query class.
 * @since   1.2.1
 * @package   EverAccounting
 */

namespace EverAccounting;

/**
 * Class Item_Query
 * @package EverAccounting
 */
class Item_Query_BK_BK {
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
	const TABLE_NAME = 'ea_items';

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
	public function __construct( $query = null ) {
		$this->query_var_defaults = array(
			'category_id'            => array(),
			'category__in'           => array(),
			'category__not_in'       => array(),
			'sale_price_min'         => '',
			'sale_price_max'         => '',
			'sale_price_between'     => '',
			'purchase_price_min'     => '',
			'purchase_price_max'     => '',
			'purchase_price_between' => '',
			'include'                => array(),
			'exclude'                => array(),
			'search'                 => '',
			'search_columns'         => array(),
			'orderby'                => 'name',
			'order'                  => 'ASC',
			'offset'                 => '',
			'number'                 => 20,
			'paged'                  => 1,
			'no_found_rows'          => false,
			'fields'                 => 'all',
		);

		if ( ! is_null( $query ) ) {
			$this->parse_query( $query );
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
	public function parse_query( $query = null ) {
		global $wpdb;
		if ( is_null( $query ) ) {
			$query = $this->query_vars;
		}
		// Setup table.
		$this->table = $wpdb->prefix . self::TABLE_NAME;
		$query       = (array) wp_parse_args( $query, $this->query_var_defaults );

		// Parse args.
		$query['number']        = absint( $query['number'] );
		$query['offset']        = absint( $query['offset'] );
		$query['no_found_rows'] = (bool) $query['no_found_rows'];

		if ( ! empty( $query['fields'] ) && 'all' !== $query['fields'] ) {
			$query['fields'] = array_unique( wp_parse_list( $query['fields'] ) );
		}

		/**
		 * Filters the query arguments.
		 *
		 * @param array $args An array of arguments.
		 *
		 * @since 1.2.1
		 *
		 */
		$this->query_vars = apply_filters( 'eaccounting_get_items_args', $query );
	}

	/**
	 * Run the query and retrieves the results.
	 *
	 * @return array|object|null
	 */
	public function query() {
		global $wpdb;
		$key          = md5( serialize( wp_array_slice_assoc( $this->query_vars, array_keys( $this->query_var_defaults ) ) ) );
		$last_changed = wp_cache_get_last_changed( 'ea_items' );
		$cache_key    = "ea_items:$key:$last_changed";
		$cache        = wp_cache_get( $cache_key, 'ea_items' );

		if ( false !== $cache ) {
			$this->results = $cache->results;
			$this->total   = $cache->total;

			return $this->results;
		}

		// Prepare query fields.
		$this->prepare_query_fields();

		// Prepare query where.
		$this->prepare_query_where();

		// Prepare query order.
		$this->prepare_query_order();


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
				 * Filters SELECT FOUND_ROWS() query for the current query instance.
				 *
				 * @param string $sql The SELECT FOUND_ROWS() query for the current query.
				 * @param Transaction_Query $query The current query instance.
				 *
				 * @global \wpdb $wpdb WordPress database abstraction object.
				 *
				 * @since 1.2.1
				 *
				 */
				$count_query = apply_filters( 'eaccounting_count_items_query', 'SELECT FOUND_ROWS()', $this );

				$this->total = (int) $wpdb->get_var( $count_query );
			}
		}

		$cache          = new \StdClass;
		$cache->results = $this->results;
		$cache->total   = $this->total;


		wp_cache_add( $cache_key, $cache, 'ea_items' );

		return $this->results;
	}

	/**
	 * Prepares the query fields.
	 *
	 * @since 1.2.1
	 *
	 */
	protected function prepare_query_fields() {
		$qv = &$this->query_vars;
		// Fields setup.
		if ( is_array( $qv['fields'] ) ) {
			$this->sql_clauses['fields'] .= implode( ',', $qv['fields'] );
		} elseif ( 'all' === $qv['fields'] ) {
			$this->sql_clauses['fields'] .= "$this->table.* ";
		} else {
			$this->sql_clauses['fields'] .= "$this->table.id";
		}

		if ( false === $qv['no_found_rows'] ) {
			$this->sql_clauses['fields'] = 'SQL_CALC_FOUND_ROWS ' . $this->sql_clauses['fields'];
		}
	}

	/**
	 * Prepares the query where.
	 *
	 * @since 1.2.1
	 *
	 */
	protected function prepare_query_where() {

	}

	/**
	 * Prepares the query order.
	 *
	 * @since 1.2.1
	 *
	 */
	protected function prepare_query_order() {

	}


}
