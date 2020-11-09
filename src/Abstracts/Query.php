<?php

namespace EverAccounting\Abstracts;

use EverAccounting\Cache;

defined( 'ABSPATH' ) || exit();

abstract class Query {
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
	 * @var bool
	 */
	protected $count = false;

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
	 * @param bool    $add_prefix Should DB prefix be added.
	 *
	 * @param string  $from
	 *
	 * @return \EverAccounting\Abstracts\Query
	 */
	public function from( $from, $add_prefix = true ) {
		global $wpdb;
		$this->from = $this->from . ' ' . ( $add_prefix ? $wpdb->prefix : '' ) . $from;

		return $this;
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
	 * @return \EverAccounting\Abstracts\Query
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
			$subquery = new static();
			$column   = array_filter( $column );
			foreach ( $column as $key => $val ) {
				$subquery->where( $key, $val, null, $joint );
			}

			$this->where = array_merge( $this->where, $subquery->where );

			return $this;
		}

		if ( is_object( $column ) && ( $column instanceof \Closure ) ) {
			// create new query object
			$subquery = new static();

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

		//first check if is array if so then make a string out of array
		//if not array but null then set value as null
		//if not null does it contains . it could be column so dont parse as string
		//If not column then use wpdb prepare
		//if contains $prefix
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
	 * Creates a where date between statement
	 *
	 *     ->where_date_between('date', '2014-02-01', '2014-02-28')
	 *
	 * @since       1.0.2
	 *
	 * @param string $column
	 *
	 * @return \EverAccounting\Abstracts\Query
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
	 * @param array|string $table    The table to join. (can contain an alias definition.)
	 *
	 * @param string       $local_key
	 * @param string       $operator The operator (=, !=, <, > etc.)
	 * @param string       $reference_key
	 * @param string       $type     The join type (inner, left, right, outer)
	 * @param string       $joint    The join AND or Or
	 *
	 * @return \EverAccounting\Abstracts\Query The current query builder.
	 */
	public function join( $table, $local_key, $operator = null, $reference_key = null, $type = 'left', $joint = 'AND' ) {
		global $wpdb;
		$type = is_string( $type ) ? strtoupper( trim( $type ) ) : ( $type ? 'LEFT' : '' );
		if ( ! in_array( $type, array( '', 'LEFT', 'RIGHT', 'INNER', 'CROSS', 'LEFT OUTER', 'RIGHT OUTER' ), true ) ) {
			_doing_it_wrong( __METHOD__, 'Invalid join type.', '1.1.0' );
		}

		$join = array(
			'table' => $wpdb->prefix . $table . ' as `' . $table . '`',
			'type'  => $type,
			'on'    => array(),
		);

		// to make nested joins possible you can pass an closure
		// which will create a new query where you can add your nested where
		if ( is_object( $local_key ) && ( $local_key instanceof \Closure ) ) {
			//create new query object
			$subquery = new static();
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
	 * Set to count.
	 *
	 * @since 1.1.0
	 * @return $this
	 */
	public function count_total() {
		$this->count = true;

		return $this;
	}


	/**
	 * Builds query's select statement.
	 *
	 * @since 1.0.2
	 *
	 * @param bool    $calc_rows
	 *
	 * @param string &$query
	 */
	private function _query_select( &$query ) {
		$query = 'SELECT ' . ( $this->count ? 'SQL_CALC_FOUND_ROWS ' : '' );
		$query .= ( is_array( $this->select ) && count( $this->select ) ? implode( ',', $this->select ) : '*' );
	}

	/**
	 * Builds query's from statement.
	 *
	 * @since 1.0.2
	 *
	 * @param string &$query
	 *
	 */
	private function _query_from( &$query ) {
		global $wpdb;
		$query .= ' FROM ' . $wpdb->prefix . $this->table . ' as `' . $this->table . '`';
	}

	/**
	 * Builds query's join statement.
	 *
	 * @since 1.0.2
	 *
	 * @param string &$query
	 *
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
	 *
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
	 *
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
	 *
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
	 *
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
	 *
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
	 *
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
	 *
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
	 * @since 1.1.0
	 *
	 * @param bool $add_limit
	 *
	 * @return string
	 */
	private function _build_query( $add_limit = true ) {
		$query = '';
		$this->_query_select( $query );
		$this->_query_from( $query );
		$this->_query_join( $query );
		$this->_query_where( $query );
		$this->_query_group( $query );
		$this->_query_having( $query );
		$this->_query_order( $query );
		if ( true === $add_limit ) {
			$this->_query_limit( $query );
			$this->_query_offset( $query );
		}

		return $query;
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

		$query = $this->_build_query( false );
		$query .= ' LIMIT 1';
		$this->_query_offset( $query );
		$key    = md5( $query );
		$result = Cache::get( $key, $this->table );
		if ( false === $result ) {
			$result = $wpdb->get_row( $query, $output );
			Cache::set( $key, $result, $this->table );
		}

		return $result;
	}

	/**
	 * Returns results from builder statements.
	 *
	 * @since       1.0.2
	 *
	 * @global object  $wpdb
	 *
	 *
	 * @param callable $row_map Function callable to filter or map results to.
	 *
	 * @param string   $output  wpdb output type.
	 *
	 * @return array
	 */
	public function get_results( $output = OBJECT, $row_map = null ) {
		global $wpdb;
		do_action( 'eaccounting_query_pre_get', $this );
		do_action( 'eaccounting_query_pre_get_' . $this->table, $this );

		$query   = $this->_build_query();
		$key     = md5( $query );
		$results = Cache::get( $key, $this->table );
		if ( false === $results ) {
			$results = $wpdb->get_results( $query, $output );

			if ( $row_map ) {
				$results = array_map(
					function ( $row ) use ( &$row_map ) {
						return call_user_func_array( $row_map, array( $row ) );
					},
					$results
				);
			}
			Cache::set( $key, $results, $this->table );
		}

		return $results;
	}

	/**
	 * Just return the number of results
	 *
	 * @since       1.0.2
	 *
	 * @return int
	 */
	public function count() {
		global $wpdb;
		do_action( 'eaccounting_pre_count', $this );
		do_action( 'eaccounting_pre_count_' . $this->table, $this );
		$query = 'SELECT FOUND_ROWS()';
		if ( ! $this->count ) {
			$query = 'SELECT count(' . $this->primary_column . ') as `count`';
			$this->_query_from( $query );
			$this->_query_join( $query );
			$this->_query_where( $query );
			$this->_query_group( $query );
			$this->_query_having( $query );
		}
		$key   = md5( $query );
		$count = Cache::get( $key, $this->table );
		if ( false === $count ) {
			$count = (int) $wpdb->get_var( $query );
			Cache::set( $key, $count, $this->table );
		}

		return (int) $count;

	}

	/**
	 * Just get a single value from the result
	 *
	 * @since       1.0.2
	 *
	 * @param int $column The index of the column.
	 *
	 * @return mixed The columns value
	 */
	public function get_column( $column = 0 ) {
		global $wpdb;
		do_action( 'eaccounting_pre_column', $this );
		do_action( 'eaccounting_pre_column_' . $this->table, $this );
		$query = $this->_build_query();

		return $wpdb->get_col( $query, $column );
	}

	/**
	 * Returns a value.
	 *
	 * @since       1.0.2
	 *
	 * @global object $wpdb
	 *
	 * @param int     $x Column of value to return. Indexed from 0.
	 * @param int     $y Row of value to return. Indexed from 0.
	 *
	 * @return mixed
	 */
	public function get_var( $x = 0, $y = 0 ) {
		global $wpdb;
		do_action( 'eaccounting_pre_value', $this );
		do_action( 'eaccounting_pre_value_' . $this->table, $this );
		$query = $this->_build_query();

		return $wpdb->get_var( $query, $x, $y );
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
		Cache::delete( $this->table );

		return $wpdb->query( $query );
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
		Cache::delete( $this->table );

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
			Cache::delete( $this->table );

			return $wpdb->insert_id;
		}

		return false;
	}

	/**
	 * Returns query from builder statements.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function to_sql() {
		$query = $this->_build_query();

		return $query;
	}

	/**
	 * Get the default allowed query vars.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	protected function get_query_vars() {
		return array(
			'include'  => array(),
			'exclude'  => array(),
			'page'     => 1,
			'per_page' => 20,
			'order'    => 'DESC',
			'orderby'  => '',
			'search'   => '',
			'fields'   => array(),
		);
	}

	/**
	 * @since 1.0.0
	 *
	 * @param $vars
	 *
	 * @return \EverAccounting\Abstracts\Query
	 */
	public function parse( $vars ) {
		foreach ( $vars as $var => $props ) {
			$alias = "alias_{$var}";
			var_dump(method_exists( $this, $alias ));
			var_dump(is_callable( array( $this, $alias ) ));
			if ( method_exists( $this, $alias ) && is_callable( array( $this, $alias ) ) ) {
				call_user_func( array( $this, $alias ), $props );
			}
		}

		return $this;
	}

	/**
	 * Include specific items.
	 *
	 * @since 1.1.0
	 *
	 * @param $ids
	 *
	 * @return $this
	 */
	public function alias_include( $ids ) {
		var_dump($ids);
		if ( empty( $ids ) ) {
			return $this;
		}

		return $this->where( $this->primary_column, 'in', array_filter( wp_parse_id_list( $ids ) ) );
	}

	/**
	 * Exclude specific items.
	 *
	 * @since 1.1.0
	 *
	 * @param $ids
	 *
	 * @return $this
	 */
	public function alias_exclude( $ids ) {
		if ( empty( $ids ) ) {
			return $this;
		}

		return $this->where( $this->primary_column, 'not in', array_filter( wp_parse_id_list( $ids ) ) );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param $per_page
	 * @param $page
	 *
	 * @return $this
	 */
	public function alias_paginate( $page, $per_page ) {
		return $this->page( intval( $page ), intval( $per_page ) );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param $orderby
	 * @param $order
	 *
	 * @return $this
	 */
	public function alias_orderby( $orderby, $order ) {
		return $this->order_by( $orderby, $order );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param $fields
	 */
	public function alias_fields( $fields ) {
		$this->select( $fields );
	}
}
