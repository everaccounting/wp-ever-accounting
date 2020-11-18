<?php
/**
 * The main query class that will be used by inherited classes.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit();

class Query {
	/**
	 * Primary could of the table.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $primary_column = 'id';

	/**
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $table = '';

	/**
	 * @since 1.0.2
	 * @var string
	 */
	protected $cache_group = 'eaccounting';

	/**
	 * @var array
	 */
	protected $select = array();

	/**
	 * @var string
	 */
	protected $from = null;

	/**
	 * @var array
	 */
	protected $join = array();

	/**
	 * @var array
	 */
	protected $where = array();

	/**
	 * @var array
	 */
	protected $order = array();

	/**
	 * @var array
	 */
	protected $group = array();

	/**
	 * @var string
	 */
	protected $having = null;

	/**
	 * @var int
	 */
	protected $limit = null;

	/**
	 * @var int
	 */
	protected $offset = 0;

	/**
	 * Query constructor.
	 *
	 * @param array $query
	 */
	public function __construct( $query = array() ) {
		$this->parse( wp_parse_args( $query, $this->get_query_vars() ) );
	}

	/**
	 * @since 1.0.2
	 * @return \EverAccounting\Query
	 */
	public static function init( $query = array() ) {
		return new self( $query );
	}

	/**
	 * Get the default allowed query vars.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	protected function get_query_vars() {
		return array(
			'primary_column' => 'id',
			'table'          => '',
			'include'        => array(),
			'exclude'        => array(),
			'page'           => 1,
			'per_page'       => 20,
			'order'          => 'DESC',
			'orderby'        => '',
			'search'         => '',
			'search_columns' => array(),
			'fields'         => array(),
		);
	}

	/**
	 * @since 1.0.0
	 *
	 * @param $vars
	 *
	 * @return \EverAccounting\Query
	 */
	public function parse( $vars ) {
		// set primary could
		if ( ! empty( $vars['primary_column'] ) ) {
			$this->primary_column = sanitize_key( $vars['primary_column'] );
		}

		// set table
		if ( ! empty( $vars['table'] ) ) {
			$this->from( $vars['table'] . ' as `' . $vars['table'] . '`' );
			$this->table = $vars['table'];
		}

		// include
		$include = wp_parse_id_list( $vars['include'] );
		if ( ! empty( $include ) ) {
			$this->where_in( $this->primary_column, $include );
		}

		// exclude
		$exclude = wp_parse_id_list( $vars['exclude'] );
		if ( ! empty( $exclude ) ) {
			$this->where_not_in( $this->primary_column, $exclude );
		}

		// search
		if ( is_string( $vars['search_columns'] ) ) {
			$vars['search_columns'] = array_unique( array_filter( explode( ',', $vars['search_columns'] ) ) );
		}
		if ( ! empty( $vars['search'] ) || ! empty( $vars['search_columns'] ) ) {
			$this->search( $vars['search'], $vars['search_columns'] );
		}

		// order
		if ( ! empty( $vars['orderby'] ) || ! empty( $vars['order'] ) ) {
			$this->order_by( $vars['orderby'], $vars['order'] );
		}

		// pagination
		if ( - 1 !== $vars['per_page'] && ( ! empty( $vars['page'] ) || ! empty( $vars['per_page'] ) ) ) {
			$this->page( $vars['page'], $vars['per_page'] );
		}

		// select
		if ( ! empty( $vars['fields'] ) && is_array( $vars['fields'] ) ) {
			$vars['fields'] = array_unique( $vars['fields'] );
			foreach ( $vars['fields'] as $field ) {
				$field = 'id' === $field ? 'id' : sanitize_key( $field );
				$this->select( $field );
			}
		}

		// status
		if ( ! empty( $vars['status'] ) && in_array( $vars['status'], array( 'active', 'inactive' ), true ) ) {
			$status = 'active' === $vars['status'] && ! is_numeric( $vars['status'] ) ? 1 : 0;
			$this->where( 'enabled', $status );
		}
		$this->parse_extra( $vars );

		return $this;
	}

	/**
	 * Prepare additional query vars in subclass.
	 *
	 * @since 1.1.0
	 *
	 * @param $vars
	 */
	protected function parse_extra( $vars ) {
		// todo overwrite in subclass
	}

	/**
	 * Adds select statement.
	 *
	 * @since       1.0.2
	 *
	 * @param array|string $statement
	 *
	 * @param bool         $reset
	 *
	 * @return $this
	 */
	public function select( $statement, $reset = false ) {
		if ( $reset ) {
			$this->select = $statement;

			return $this;
		}
		$this->select[] = $statement;

		return $this;
	}

	/**
	 * Adds from statement.
	 *
	 * @since       1.1.0
	 *
	 * @global object $wpdb
	 *
	 * @param string $from
	 * @param bool   $add_prefix Should DB prefix be added.
	 *
	 * @return \EverAccounting\Query
	 */
	public function from( $from, $add_prefix = true ) {
		global $wpdb;
		$this->from = $this->from . ' ' . ( $add_prefix ? $wpdb->prefix : '' ) . $from;

		return $this;
	}


	/**
	 * Adds from statement.
	 *
	 * @since       1.0.2
	 *
	 * @param bool   $add_prefix
	 *
	 * @param string $name
	 */
	public static function table( $name, $add_prefix = true ) {
		global $wpdb;
		$builder       = new self();
		$table         = ( $add_prefix ? $wpdb->prefix : '' ) . $name;
		$builder->from = $table;

		return $builder;
	}

	/**
	 * Adds search statement.
	 *
	 * @since       1.0.2
	 *
	 * @param string $search
	 * @param array  $columns
	 * @param string $joint
	 *
	 * @return self
	 */
	public function search( $search, $columns = array(), $joint = 'AND' ) {
		global $wpdb;
		$searches = array();
		$words    = array_unique( array_filter( explode( ' ', $search ) ) );
		if ( empty( $words ) || empty( $columns ) ) {
			return $this;
		}
		foreach ( $words as $word ) {
			$like = '%' . $wpdb->esc_like( $word ) . '%';
			foreach ( $columns as $column ) {
				$searches[] = $wpdb->prepare( "$column LIKE %s", $like );
			}
		}

		if ( ! empty( $searches ) ) {
			$this->where[] = array(
				'joint'     => $joint,
				'condition' => '(' . implode( ' OR ', $searches ) . ')',
			);
		}

		return $this;
	}

	/**
	 * Create a where statement.
	 *
	 *     ->where('name', 'sultan')
	 *     ->where('age', '>', 18)
	 *     ->where('name', 'in', array('ayaan', 'ayaash', 'anaan'))
	 *     ->where(function($q){
	 *       $q->where('ID', '>', 21);
	 * })
	 *
	 * @param string|array $column The SQL column
	 * @param mixed        $param1 Operator or value depending if $param2 isset.
	 * @param mixed        $param2 The value if $param1 is an operator.
	 * @param string       $joint  the where type ( and, or )
	 *
	 * @return \EverAccounting\Query
	 */
	public function where( $column, $param1 = null, $param2 = null, $joint = 'and' ) {
		global $wpdb;

		if ( ! in_array( strtolower( $joint ), array( 'and', 'or', 'where' ), true ) ) {
			_doing_it_wrong( __METHOD__, 'Invalid where type "' . $joint . '"', '1.1.0' );

			return $this;
		}

		// when column is an array we assume to make a bulk and where.
		if ( is_array( $column ) ) {
			// create new query object
			$subquery = new Query();
			$column   = array_filter( $column );
			foreach ( $column as $key => $val ) {
				$subquery->where( $key, $val, null, $joint );
			}

			$this->where = array_merge( $this->where, $subquery->where );

			return $this;
		}

		if ( is_object( $column ) && ( $column instanceof \Closure ) ) {
			// create new query object
			$subquery = new Query();

			// run the closure callback on the sub query
			call_user_func_array( $column, array( &$subquery ) );
			$condition = '';
			for ( $i = 0, $max = count( $subquery->where ); $i < $max; ++ $i ) {
				$condition .= ( 0 === $i ? ' ' : ' ' . $subquery->where[ $i ]['joint'] . ' ' ) . $subquery->where[ $i ]['condition'];
			}

			$this->where = array_merge(
				$this->where,
				array(
					array(
						'joint'     => $joint,
						'condition' => "($condition)",
					),
				)
			);

			return $this;
		}

		// when param2 is null we replace param2 with param one as the
		// value holder and make param1 to the = operator.
		if ( is_null( $param2 ) ) {
			$param2 = $param1;
			$param1 = '=';
		}

		// if the param2 is an array we filter it. when param2 is an array we probably
		// have an "in" or "between" statement which has no need for duplicates.
		if ( is_array( $param2 ) ) {
			$param2 = array_unique( $param2 );
		}

		// Not Between?
		if ( is_array( $param2 ) && strpos( $param1, 'NOT BETWEEN' ) !== false ) {
			$min = isset( $param2[0] ) ? $param2[0] : false;
			$max = isset( $param2[1] ) ? $param2[1] : false;
			if ( ! $min || ! $max ) {
				_doing_it_wrong( __METHOD__, 'NOT BETWEEN min or max is missing', '1.1.0' );
			}

			$min = $wpdb->prepare( is_numeric( $min ) ? '%d' : '%s', $min );
			$max = $wpdb->prepare( is_numeric( $max ) ? '%d' : '%s', $max );

			$this->where[] = array(
				'joint'     => $joint,
				'condition' => "($column NOT BETWEEN $min AND $max)",
			);

			return $this;
		}

		// Between?
		if ( is_array( $param2 ) && strpos( $param1, 'BETWEEN' ) !== false ) {
			$min = isset( $param2[0] ) ? $param2[0] : false;
			$max = isset( $param2[1] ) ? $param2[1] : $min;
			if ( ! $min || ! $max ) {
				_doing_it_wrong( __METHOD__, 'BETWEEN min or max is missing', '1.1.0' );
			}

			$min = $wpdb->prepare( is_numeric( $min ) ? '%d' : '%s', $min );
			$max = $wpdb->prepare( is_numeric( $max ) ? '%d' : '%s', $max );

			$this->where[] = array(
				'joint'     => $joint,
				'condition' => "($column BETWEEN $min AND $max)",
			);

			return $this;
		}

		// first check if is array if so then make a string out of array
		// if not array but null then set value as null
		// if not null does it contains . it could be column so dont parse as string
		// If not column then use wpdb prepare
		// if contains $prefix
		$contain_join = preg_replace( '/^(\s?AND ?|\s?OR ?)|\s$/i', '', $param2 );

		$param2 = is_array( $param2 )
			? ( '("' . implode( '","', $param2 ) . '")' )
			: ( $param2 === null
				? 'null'
				: ( strpos( $param2, $wpdb->prefix ) !== false ? $param2 : $wpdb->prepare( is_numeric( $param2 ) ? '%d' : '%s', $param2 ) )
			);

		$this->where[] = array(
			'joint'     => $joint,
			'condition' => implode( ' ', array( $column, $param1, $param2 ) ),
		);

		return $this;
	}

	/**
	 * Create an or where statement
	 *
	 * This is the same as the normal where just with a fixed type
	 *
	 * @since       1.0.2
	 *
	 * @param mixed  $param1
	 * @param mixed  $param2
	 *
	 * @param string $column The SQL column
	 *
	 * @return Query The current query builder.
	 */
	public function or_where( $column, $param1 = null, $param2 = null ) {
		return $this->where( $column, $param1, $param2, 'or' );
	}

	/**
	 * Create an and where statement
	 *
	 * This is the same as the normal where just with a fixed type
	 *
	 * @since       1.0.2
	 *
	 * @param mixed  $param1
	 * @param mixed  $param2
	 *
	 * @param string $column The SQL column
	 *
	 * @return Query The current query builder
	 */
	public function and_where( $column, $param1 = null, $param2 = null ) {
		return $this->where( $column, $param1, $param2, 'and' );
	}

	/**
	 * Creates a where in statement
	 *
	 *     ->whereIn('id', [42, 38, 12])
	 *
	 * @since       1.0.2
	 *
	 * @param array  $options
	 *
	 * @param string $column
	 *
	 * @return Query The current query builder.
	 */
	public function where_in( $column, array $options = array() ) {
		// when the options are empty we skip
		if ( empty( $options ) ) {
			return $this;
		}

		return $this->where( $column, 'in', array_filter( $options ) );
	}

	/**
	 * Creates a where not in statement
	 *
	 *     ->whereNotIn('id', [42, 38, 12])
	 *
	 * @since       1.0.2
	 *
	 * @param array  $options
	 *
	 * @param string $column
	 *
	 * @return Query The current query builder.
	 */
	public function where_not_in( $column, array $options = array() ) {
		// when the options are empty we skip
		if ( empty( $options ) ) {
			return $this;
		}

		return $this->where( $column, 'not in', $options );
	}

	/**
	 * Creates a where something is null statement
	 *
	 *     ->whereNull('modified_at')
	 *
	 * @since       1.0.2
	 *
	 * @param string $column
	 *
	 * @return Query The current query builder.
	 */
	public function where_null( $column ) {
		return $this->where( $column, 'is', null );
	}

	/**
	 * Creates a where something is not null statement
	 *
	 *     ->whereNotNull('created_at')
	 *
	 * @since       1.0.2
	 *
	 * @param string $column
	 *
	 * @return Query The current query builder.
	 */
	public function where_not_null( $column ) {
		return $this->where( $column, 'is not', null );
	}

	/**
	 * Creates a or where something is null statement
	 *
	 *     ->orWhereNull('modified_at')
	 *
	 * @since       1.0.2
	 *
	 * @param string $column
	 *
	 * @return Query The current query builder.
	 */
	public function or_where_null( $column ) {
		return $this->or_where( $column, 'is', null );
	}

	/**
	 * Creates a or where something is not null statement
	 *
	 *     ->orWhereNotNull('modified_at')
	 *
	 * @since       1.0.2
	 *
	 * @param string $column
	 *
	 * @return Query The current query builder.
	 */
	public function or_where_not_null( $column ) {
		return $this->or_where( $column, 'is not', null );
	}


	/**
	 * Creates a where between statement
	 *
	 *     ->whereBetween('user_id', 1, 2000)
	 *
	 * @since       1.0.2
	 *
	 * @param string $column
	 *
	 * @return Query The current query builder.
	 */
	public function where_between( $column, $min, $max ) {
		return $this->where( $column, 'BETWEEN', array( $min, $max ) );
	}

	/**
	 * Creates a where not between statement
	 *
	 *     ->whereNotBetween('user_id', 1, 2000)
	 *
	 * @since       1.0.2
	 *
	 * @param string $column
	 *
	 * @return Query The current query builder.
	 */
	public function where_not_between( $column, $min, $max ) {
		return $this->where( $column, 'NOT BETWEEN', array( $min, $max ) );
	}

	/**
	 * Creates a where date between statement
	 *
	 *     ->where_date_between('date', '2014-02-01', '2014-02-28')
	 *
	 * @since       1.0.2
	 *
	 * @param string $column
	 *
	 * @return Query The current query builder.
	 */
	public function where_date_between( $column, $start = null, $end = null ) {
		global $wpdb;
		if ( empty( $start ) || empty( $end ) ) {
			return $this;
		}
		$stat_date = $wpdb->get_var( $wpdb->prepare( 'SELECT CAST(%s as DATE)', $start ) );
		$end_date  = $wpdb->get_var( $wpdb->prepare( 'SELECT CAST(%s as DATE)', $end ) );

		return $this->where( $column, 'BETWEEN', array( $stat_date, $end_date ) );
	}

	/**
	 *
	 * @since 1.0.2
	 *
	 * @param string $joint
	 *
	 * @param        $query
	 */
	public function where_raw( $query, $joint = 'AND' ) {
		$this->where[] = array(
			'joint'     => $joint,
			'condition' => $query,
		);

		return $this;
	}

	/**
	 * Add a join statement to the current query
	 *
	 *     ->join('avatars', 'users.id', '=', 'avatars.user_id')
	 *
	 * @since       1.0.2
	 *
	 * @param string       $local_key
	 * @param string       $operator   The operator (=, !=, <, > etc.)
	 * @param string       $reference_key
	 * @param string       $type       The join type (inner, left, right, outer)
	 * @param string       $joint      The join AND or Or
	 * @param bool         $add_prefix Add table prefix or not
	 *
	 * @param array|string $table      The table to join. (can contain an alias definition.)
	 *
	 * @return Query The current query builder.
	 */
	public function join( $table, $local_key, $operator = null, $reference_key = null, $type = 'left', $joint = 'AND', $add_prefix = true ) {
		global $wpdb;
		$type = is_string( $type ) ? strtoupper( trim( $type ) ) : ( $type ? 'LEFT' : '' );
		if ( ! in_array( $type, array( '', 'LEFT', 'RIGHT', 'INNER', 'CROSS', 'LEFT OUTER', 'RIGHT OUTER' ), true ) ) {
			_doing_it_wrong( __METHOD__, 'Invalid join type.', '1.1.0' );
		}

		$join = array(
			'table' => ( $add_prefix ? $wpdb->prefix : '' ) . $table . ' as `' . $table . '`',
			'type'  => $type,
			'on'    => array(),
		);

		// to make nested joins possible you can pass an closure
		// which will create a new query where you can add your nested where
		if ( is_object( $local_key ) && ( $local_key instanceof \Closure ) ) {
			// create new query object
			$subquery = new Query();
			// run the closure callback on the sub query
			call_user_func_array( $local_key, array( &$subquery ) );

			$join['on'] = array_merge( $join['on'], $subquery->where );
			$this->join = array_merge( $this->join, array( $join ) );

			return $this;
		}

		// when $reference_key is null we replace $operator with $reference_key one as the
		// value holder and make $operator to the = operator.
		if ( is_null( $reference_key ) ) {
			$reference_key = $operator;
			$operator      = '=';
		}

		$reference_key = is_array( $reference_key )
			? ( '(\'' . implode( '\',\'', $reference_key ) . '\')' )
			: ( $reference_key === null
				? 'null'
				: ( strpos( $reference_key, '.' ) !== false || strpos( $reference_key, $wpdb->prefix ) !== false ? $reference_key : $wpdb->prepare( is_numeric( $reference_key ) ? '%d' : '%s', $reference_key ) )
			);

		$join['on'][] = array(
			'joint'     => $joint,
			'condition' => implode( ' ', array( $local_key, $operator, $reference_key ) ),
		);

		$this->join[] = $join;

		return $this;
	}

	/**
	 * Left join same as join with special type
	 *
	 * @since       1.0.2
	 *
	 * @param string       $local_key
	 * @param string       $operator The operator (=, !=, <, > etc.)
	 * @param string       $reference_key
	 *
	 * @param array|string $table    The table to join. (can contain an alias definition.)
	 *
	 * @return Query The current query builder.
	 */
	public function left_join( $table, $local_key, $operator = null, $reference_key = null ) {
		return $this->join( $table, $local_key, $operator, $reference_key, 'left' );
	}

	/**
	 * Alias of the `join` method with join type right.
	 *
	 * @since       1.0.2
	 *
	 * @param string       $local_key
	 * @param string       $operator The operator (=, !=, <, > etc.)
	 * @param string       $reference_key
	 *
	 * @param array|string $table    The table to join. (can contain an alias definition.)
	 *
	 * @return Query The current query builder.
	 */
	public function right_join( $table, $local_key, $operator = null, $reference_key = null ) {
		return $this->join( $table, $local_key, $operator, $reference_key, 'right' );
	}

	/**
	 * Alias of the `join` method with join type inner.
	 *
	 * @since       1.0.2
	 *
	 * @param string       $local_key
	 * @param string       $operator The operator (=, !=, <, > etc.)
	 * @param string       $reference_key
	 *
	 * @param array|string $table    The table to join. (can contain an alias definition.)
	 *
	 * @return Query The current query builder.
	 */
	public function inner_join( $table, $local_key, $operator = null, $reference_key = null ) {
		return $this->join( $table, $local_key, $operator, $reference_key, 'inner' );
	}

	/**
	 * Alias of the `join` method with join type outer.
	 *
	 * @since       1.0.2
	 *
	 * @param string       $local_key
	 * @param string       $operator The operator (=, !=, <, > etc.)
	 * @param string       $reference_key
	 *
	 * @param array|string $table    The table to join. (can contain an alias definition.)
	 *
	 * @return Query The current query builder.
	 */
	public function outer_join( $table, $local_key, $operator = null, $reference_key = null ) {
		return $this->join( $table, $local_key, $operator, $reference_key, 'outer' );
	}


	/**
	 *
	 * @since 1.0.2
	 *
	 * @param string $joint
	 *
	 * @param        $query
	 */
	public function joinRaw( $query, $joint = 'AND' ) {
		$this->join['on'][] = array(
			'joint'     => $joint,
			'condition' => $query,
		);

		return $this;
	}

	/**
	 * Adds group by statement.
	 *     ->groupBy('category')
	 *     ->gorupBy(['category', 'price'])
	 *
	 * @since       1.0.2
	 *
	 * @param string $field
	 *
	 * @return Query this for chaining.
	 */
	public function group_by( $field ) {
		if ( empty( $field ) ) {
			return $this;
		}

		if ( is_array( $field ) ) {
			foreach ( $field as $groupby ) {
				$this->group[] = $groupby;
			}
		} else {
			$this->group[] = $field;
		}

		return $this;
	}

	/**
	 * Adds having statement.
	 *
	 *  ->group_by('user.id')
	 *  ->having('count(user.id)>1')
	 *
	 * @since       1.0.2
	 *
	 * @param string $statement
	 *
	 * @return Query this for chaining.
	 */
	public function having( $statement ) {
		if ( ! empty( $statement ) ) {
			$this->having = $statement;
		}

		return $this;
	}

	/**
	 * Adds order by statement.
	 *
	 *     ->orderBy('created_at')
	 *     ->orderBy('modified_at', 'desc')
	 *
	 * @since       1.0.2
	 *
	 * @param string $direction
	 *
	 * @param string $key
	 *
	 * @return Query this for chaining.
	 */
	public function order_by( $key, $direction = 'ASC' ) {
		$direction = trim( strtoupper( $direction ) );
		if ( $direction !== 'ASC' && $direction !== 'DESC' ) {
			_doing_it_wrong( __METHOD__, 'Invalid direction value.', '1.1.0' );
		}
		if ( ! empty( $key ) ) {
			$this->order[] = $key . ' ' . $direction;
		}

		return $this;
	}

	/**
	 * Set the query limit
	 *
	 *     // limit(<limit>)
	 *     ->limit(20)
	 *
	 *     // limit(<offset>, <limit>)
	 *     ->limit(60, 20)
	 *
	 * @since       1.0.2
	 *
	 * @param int $limit2
	 *
	 * @param int $limit
	 *
	 * @return Query The current query builder.
	 */
	public function limit( $limit, $limit2 = null ) {
		if ( ! is_null( $limit2 ) ) {
			$this->offset = (int) $limit;
			$this->limit  = (int) $limit2;
		} else {
			$this->limit = (int) $limit;
		}

		return $this;
	}

	/**
	 * Adds offset statement.
	 *
	 * ->offset(20)
	 *
	 * @since       1.0.2
	 *
	 * @param int $offset
	 *
	 * @return Query this for chaining.
	 */
	public function offset( $offset ) {
		$this->offset = $offset;

		return $this;
	}

	/**
	 * Create a query limit based on a page and a page size
	 *
	 * //page(<page>, <size>)
	 *  ->page(2, 20)
	 *
	 * @since       1.0.2
	 *
	 * @param int $size
	 *
	 * @param int $page
	 *
	 * @return Query The current query builder.
	 */
	public function page( $page, $size = 20 ) {
		if ( ( (int) $page ) <= 1 ) {
			$page = 0;
		}

		$this->limit = (int) $size;
		if ( $page ) {
			$this->offset = (int) ( ( $page - 1 ) * $size );
		}

		return $this;
	}

	/**
	 * Sanitize value.
	 *
	 * @since 1.0.2
	 *
	 * @param mixed       $value
	 *
	 * @param string|bool $callback Sanitize callback.
	 *
	 * @return mixed
	 */
	private function sanitize_value( $callback, $value ) {
		if ( $callback === true ) {
			$callback = ( is_numeric( $value ) && strpos( $value, '.' ) !== false )
				? 'floatval'
				: ( is_numeric( $value )
					? 'intval'
					: ( is_string( $value )
						? 'sanitize_text_field'
						: null
					)
				);
		}
		if ( strpos( $callback, '_builder' ) !== false ) {
			$callback = array( &$this, $callback );
		}
		if ( is_array( $value ) ) {
			for ( $i = count( $value ) - 1; $i >= 0; -- $i ) {
				$value[ $i ] = $this->sanitize_value( true, $value[ $i ] );
			}
		}

		return $callback && is_callable( $callback ) ? call_user_func_array( $callback, array( $value ) ) : $value;
	}


	/**
	 * Builds query's select statement.
	 *
	 * @since 1.0.2
	 *
	 * @param bool   $calc_rows
	 *
	 * @param string &$query
	 */
	private function _query_select( &$query, $calc_rows = false ) {
		$query = 'SELECT ' . ( $calc_rows ? 'SQL_CALC_FOUND_ROWS ' : '' ) . (
			is_array( $this->select ) && count( $this->select )
				? implode( ',', $this->select )
				: '*'
			);
	}

	/**
	 * Builds query's from statement.
	 *
	 * @since 1.0.2
	 *
	 * @param string &$query
	 */
	private function _query_from( &$query ) {
		$query .= ' FROM ' . $this->from;
	}

	/**
	 * Builds query's join statement.
	 *
	 * @since 1.0.2
	 *
	 * @param string &$query
	 */
	private function _query_join( &$query ) {
		foreach ( $this->join as $join ) {
			$query .= ( ! empty( $join['type'] ) ? ' ' . $join['type'] . ' JOIN ' : ' JOIN ' ) . $join['table'];
			for ( $i = 0, $max = count( $join['on'] ); $i < $max; ++ $i ) {
				$query .= ( 0 === $i ? ' ON ' : ' ' . $join['on'][ $i ]['joint'] . ' ' ) . $join['on'][ $i ]['condition'];
			}
		}
	}

	/**
	 * Builds query's where statement.
	 *
	 * @since 1.0.2
	 *
	 * @param string &$query
	 */
	public function _query_where( &$query ) {
		for ( $i = 0, $max = count( $this->where ); $i < $max; ++ $i ) {
			$query .= ( $i === 0 ? ' WHERE ' : ' ' . $this->where[ $i ]['joint'] . ' ' ) . $this->where[ $i ]['condition'];
		}
	}

	/**
	 * Builds query's group by statement.
	 *
	 * @since 1.0.2
	 *
	 * @param string &$query
	 */
	private function _query_group( &$query ) {
		if ( count( $this->group ) ) {
			$query .= ' GROUP BY ' . implode( ',', $this->group );
		}
	}

	/**
	 * Builds query's having statement.
	 *
	 * @since 1.0.2
	 *
	 * @param string &$query
	 */
	private function _query_having( &$query ) {
		if ( $this->having ) {
			$query .= ' HAVING ' . $this->having;
		}
	}

	/**
	 * Builds query's order by statement.
	 *
	 * @since 1.0.2
	 *
	 * @param string &$query
	 */
	private function _query_order( &$query ) {
		if ( count( $this->order ) ) {
			$query .= ' ORDER BY ' . implode( ',', $this->order );
		}
	}

	/**
	 * Builds query's limit statement.
	 *
	 * @since 1.0.2
	 *
	 * @global object $wpdb
	 *
	 * @param string &$query
	 */
	private function _query_limit( &$query ) {
		global $wpdb;
		if ( $this->limit ) {
			$query .= $wpdb->prepare( ' LIMIT %d', absint( $this->limit ) );
		}
	}

	/**
	 * Builds query's offset statement.
	 *
	 * @since 1.0.2
	 *
	 * @global object $wpdb
	 *
	 * @param string &$query
	 */
	private function _query_offset( &$query ) {
		global $wpdb;
		if ( $this->offset ) {
			$query .= $wpdb->prepare( ' OFFSET %d', absint( $this->offset ) );
		}
	}

	/**
	 * Builds query's delete statement.
	 *
	 * @since 1.0.2
	 *
	 * @param string &$query
	 */
	private function _query_delete( &$query ) {
		$query .= trim(
			'DELETE ' . ( count( $this->join )
				? preg_replace( '/\s[aA][sS][\s\S]+.*?/', '', $this->from )
				: ''
			)
		);
	}

	/**
	 * Return a cloned object from current builder.
	 *
	 * @since 1.0.2
	 * @return Query
	 */
	public function copy() {
		return clone( $this );
	}


	/**
	 * Find something, means select one item by key
	 *
	 * ->find('manikdrmc@gmail.com', 'email')
	 *
	 * @since       1.0.2
	 *
	 * @param string $key
	 *
	 * @param int    $id
	 *
	 * @return mixed
	 */
	public function find( $id, $key = 'id' ) {
		return $this->where( $key, $id )->one();
	}

	/**
	 * Get the first result ordered by the given key.
	 *
	 * @since       1.0.2
	 *
	 * @param string $key By what should the first item be selected? Default is: 'id'
	 *
	 * @return mixed The first result.
	 */
	public function first( $key = 'id' ) {
		return $this->order_by( $key, 'asc' )->one();
	}

	/**
	 * Get the last result by key
	 *
	 * @since       1.0.2
	 *
	 * @param string $key
	 *
	 * @return mixed the last result.
	 */
	public function last( $key = 'id' ) {
		return $this->order_by( $key, 'desc' )->one();
	}

	/**
	 * Pluck item.
	 * ->find('post_title')
	 *
	 * @since       1.0.2
	 * @return array
	 */
	public function pluck() {
		$selects      = func_get_args();
		$this->select = $selects;

		return $this->get_results();
	}


	/**
	 * Sets the limit to 1, executes and returns the first result using get.
	 *
	 * @since       1.0.2
	 *
	 * @param string $output
	 *
	 * @return mixed The single result.
	 */
	public function one( $output = OBJECT ) {
		global $wpdb;
		do_action( 'eaccounting_query_pre_one', $this );
		do_action( 'eaccounting_query_pre_one_' . $this->table, $this );

		$this->_query_select( $query );
		$this->_query_from( $query );
		$this->_query_join( $query );
		$this->_query_where( $query );
		$this->_query_group( $query );
		$this->_query_having( $query );
		$this->_query_order( $query );
		$query .= ' LIMIT 1';
		$this->_query_offset( $query );

		return $wpdb->get_row( $query, $output );
	}

	/**
	 * Returns results from builder statements.
	 *
	 * @since       1.0.2
	 *
	 * @global object  $wpdb
	 *
	 * @param callable $row_map   Function callable to filter or map results to.
	 *
	 * @param bool     $calc_rows Flag that indicates to SQL if rows should be calculated or not.
	 *
	 * @param string   $output    WPDB output type.
	 *
	 * @return array
	 */
	public function get_results( $output = OBJECT, $row_map = null, $calc_rows = false ) {
		global $wpdb;
		do_action( 'eaccounting_query_pre_get', $this );
		do_action( 'eaccounting_query_pre_get_' . $this->table, $this );

		$query = '';
		$this->_query_select( $query, $calc_rows );
		$this->_query_from( $query );
		$this->_query_join( $query );
		$this->_query_where( $query );
		$this->_query_group( $query );
		$this->_query_having( $query );
		$this->_query_order( $query );
		$this->_query_limit( $query );
		$this->_query_offset( $query );

		$results = $wpdb->get_results( $query, $output );

		if ( $row_map ) {
			$results = array_map(
				function ( $row ) use ( &$row_map ) {
					return call_user_func_array( $row_map, array( $row ) );
				},
				$results
			);
		}

		return $results;
	}

	/**
	 * @since      1.1.0
	 * @deprecated 1.1.0
	 *
	 * @param bool   $calc_rows
	 * @param string $output
	 * @param null   $row_map
	 *
	 * @return array
	 */
	public function get( $output = OBJECT, $row_map = null, $calc_rows = false ) {
		return $this->get_results( $output, $row_map, $calc_rows );
	}

	/**
	 * Just return the number of results
	 *
	 * @since       1.0.2
	 *
	 * @param string|int $column
	 *
	 * @return int
	 */
	public function count( $column = 1 ) {
		global $wpdb;
		do_action( 'eaccounting_pre_count', $this );
		do_action( 'eaccounting_pre_count_' . $this->table, $this );

		$query = 'SELECT count(' . $column . ') as `count`';
		$this->_query_from( $query );
		$this->_query_join( $query );
		$this->_query_where( $query );
		$this->_query_group( $query );
		$this->_query_having( $query );

		return (int) $wpdb->get_var( $query );

	}

	/**
	 * Just get a single value from the result
	 *
	 * @since       1.0.2
	 *
	 * @param bool   $calc_rows Flag that indicates to SQL if rows should be calculated or not.
	 *
	 * @param string $column    The index of the column.
	 *
	 * @return mixed The columns value
	 */
	public function get_column( $column = 0, $calc_rows = false ) {
		global $wpdb;
		do_action( 'eaccounting_pre_column', $this );
		do_action( 'eaccounting_pre_column_' . $this->table, $this );

		$query = '';
		$this->_query_select( $query, $calc_rows );
		$this->_query_from( $query );
		$this->_query_join( $query );
		$this->_query_where( $query );
		$this->_query_group( $query );
		$this->_query_having( $query );
		$this->_query_order( $query );
		$this->_query_limit( $query );
		$this->_query_offset( $query );

		return $wpdb->get_col( $query, $column );
	}

	/**
	 * Returns a value.
	 *
	 * @since       1.0.2
	 *
	 * @global object $wpdb
	 *
	 * @param int $x Column of value to return. Indexed from 0.
	 * @param int $y Row of value to return. Indexed from 0.
	 *
	 * @return mixed
	 */
	public function get_var( $x = 0, $y = 0 ) {
		global $wpdb;
		do_action( 'eaccounting_pre_value', $this );
		do_action( 'eaccounting_pre_value_' . $this->table, $this );

		// Build
		// Query
		$query = '';
		$this->_query_select( $query );
		$this->_query_from( $query );
		$this->_query_join( $query );
		$this->_query_where( $query );
		$this->_query_group( $query );
		$this->_query_having( $query );
		$this->_query_order( $query );
		$this->_query_limit( $query );
		$this->_query_offset( $query );

		return $wpdb->get_var( $query, $x, $y );
	}

	/**
	 * @since      1.1.0
	 * @deprecated 1.1.0
	 *
	 * @param int $x
	 * @param int $y
	 *
	 * @return mixed
	 */
	public function value( $x = 0, $y = 0 ) {
		return $this->get_var( $x, $y );
	}

	/**
	 * Get max value.
	 *
	 * @since       1.0.2
	 *
	 * @param $column
	 *
	 * @return int
	 */
	public function get_max( $column ) {
		global $wpdb;
		$query = 'SELECT MAX(' . $column . ')';
		$this->_query_from( $query );
		$this->_query_join( $query );
		$this->_query_where( $query );
		$this->_query_group( $query );
		$this->_query_having( $query );

		return (int) $wpdb->get_var( $query );
	}

	/**
	 * Get min value.
	 *
	 * @since       1.0.2
	 *
	 * @param $column
	 *
	 * @return int
	 */
	public function get_min( $column ) {
		global $wpdb;
		$query = 'SELECT MIN(' . $column . ')';
		$this->_query_from( $query );
		$this->_query_join( $query );
		$this->_query_where( $query );
		$this->_query_group( $query );
		$this->_query_having( $query );

		return (int) $wpdb->get_var( $query );
	}

	/**
	 * Get avg value.
	 *
	 * @since       1.0.2
	 *
	 * @param $column
	 *
	 * @return int
	 */
	public function get_avg( $column ) {
		global $wpdb;
		$query = 'SELECT AVG(' . $column . ')';
		$this->_query_from( $query );
		$this->_query_join( $query );
		$this->_query_where( $query );
		$this->_query_group( $query );
		$this->_query_having( $query );

		return (int) $wpdb->get_var( $query );
	}

	/**
	 * Get sum value.
	 *
	 * @since 1.0.2
	 *
	 * @param $column
	 *
	 * @return int
	 */
	public function get_sum( $column ) {
		global $wpdb;
		$query = 'SELECT SUM(' . $column . ')';
		$this->_query_from( $query );
		$this->_query_join( $query );
		$this->_query_where( $query );
		$this->_query_group( $query );
		$this->_query_having( $query );

		return (int) $wpdb->get_var( $query );
	}

	/**
	 * Returns flag indicating if query has been executed.
	 *
	 * @since 1.0.2
	 *
	 * @global object $wpdb
	 *
	 * @param string $sql
	 *
	 * @return bool
	 */
	public function query( $sql = '' ) {
		global $wpdb;
		$query = $sql;
		if ( empty( $query ) ) {
			$this->_query_select( $query, false );
			$this->_query_from( $query );
			$this->_query_join( $query );
			$this->_query_where( $query );
			$this->_query_group( $query );
			$this->_query_having( $query );
			$this->_query_order( $query );
			$this->_query_limit( $query );
			$this->_query_offset( $query );
		}

		return $wpdb->query( $query );
	}

	/**
	 * Returns found rows in last query, if SQL_CALC_FOUND_ROWS is used and is supported.
	 *
	 * @since 1.0.2
	 *
	 * @global object $wpdb
	 *
	 * @return string
	 */
	public function rows_found() {
		global $wpdb;
		$query = 'SELECT FOUND_ROWS()';
		// Process
		$query = apply_filters( 'eacciunting_pre_found_rows', $query );
		$query = apply_filters( 'eacciunting_pre_found_rows_' . $this->table, $query );

		return $wpdb->get_var( $query );
	}

	/**
	 * Update or insert.
	 *
	 * @since       1.0.2
	 *
	 * @param $data
	 *
	 * @return array|string
	 */
	public function update_or_insert( $data ) {
		if ( $this->first() ) {
			return $this->update( $data );
		}

		return $this->insert( $data );
	}

	/**
	 * Update
	 *
	 * @since 1.0.2
	 *
	 * @global object $wpdb
	 *
	 * @return bool
	 */
	public function update( $data ) {
		global $wpdb;
		$conditions = '';
		$this->_query_where( $conditions );

		if ( empty( trim( $conditions ) ) ) {
			false;
		}

		$fields = array();
		foreach ( $data as $column => $value ) {

			if ( is_null( $value ) ) {
				$fields[] = "`$column` = NULL";
				continue;
			}
			$fields[] = "`$column` = " . $wpdb->prepare( is_numeric( $value ) ? '%d' : '%s', $value );
		}

		$table  = trim( $this->from );
		$fields = implode( ', ', $fields );

		$query = "UPDATE `$table` SET $fields  $conditions";
		var_dump( $query );

		return $wpdb->query( $query );
	}

	/**
	 * Insert data.
	 *
	 * @since 1.0.2
	 *
	 * @param array $format
	 *
	 * @param       $data
	 *
	 * @return bool|int
	 */
	public function insert( $data, $format = array() ) {
		global $wpdb;

		if ( false !== $wpdb->insert( trim( $this->from ), $data, $format ) ) {
			return $wpdb->insert_id;
		};

		return false;
	}

	/**
	 * Returns flag indicating if delete query has been executed.
	 *
	 * @since 1.0.2
	 *
	 * @global object $wpdb
	 *
	 * @return bool
	 */
	public function delete() {
		global $wpdb;
		do_action( 'eaccounting_pre_delete', $this );
		do_action( 'eaccounting_pre_delete_' . $this->table, $this );

		$query = '';
		$this->_query_delete( $query );
		$this->_query_from( $query );
		$this->_query_join( $query );
		$this->_query_where( $query );

		return $wpdb->query( $query );
	}


	/**
	 * Returns query from builder statements.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function to_sql() {
		$query = '';
		$this->_query_select( $query );
		$this->_query_from( $query );
		$this->_query_join( $query );
		$this->_query_where( $query );
		$this->_query_group( $query );
		$this->_query_having( $query );
		$this->_query_order( $query );
		$this->_query_limit( $query );
		$this->_query_offset( $query );

		return $query;
	}


	// **
	// * Executes when calling any function on an instance of this class.
	// *
	// * @param string $name      The name of the function being called.
	// * @param array  $arguments An array of the arguments to the function call.
	// */
	// public function __call( $name, $arguments ) {
	// return call_user_func_array(
	// array(
	// $this,
	// $name,
	// ),
	// $arguments
	// );
	// }
	//
	// **
	// * Executes when calling any static function on this class.
	// *
	// * @param string $name      The name of the function being called.
	// * @param array  $arguments An array of the arguments to the function call.
	// */
	// public static function __callStatic( $name, $arguments ) {
	// $instance = new self();
	// return call_user_func_array(
	// array(
	// $instance,
	// $name,
	// ),
	// $arguments
	// );
	// }
}
