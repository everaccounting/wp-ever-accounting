<?php

namespace EverAccounting\Abstracts;

use EverAccounting\Inflector;

abstract class ResourceRepository extends Singleton implements \EverAccounting\Interfaces\ResourceRepository {
	/**
	 * @since 1.1.0
	 * @var string
	 */
	const INT = '%d';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	const TINYINT = '%d';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	const BIGINT = '%d';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	const VARCHAR = '%s';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	const LONGTEXT = '%s';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	const DATETIME = '%s';
	/**
	 * @since 1.1.0
	 * @var string
	 */
	const DOUBLE = '%f';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	protected $table_name = '';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	protected $table;

	/**
	 * @since 1.1.0
	 * @var string
	 */
	protected $primary_key;

	/**
	 * Used to caching and hook.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $object_type = 'ea_unset';

	/**
	 * Truncate all entries.
	 *
	 * @since 1.1.0
	 * @return mixed
	 */
	public function truncate() {
		global $wpdb;

		return $wpdb->query( "TRUNCATE TABLE {$this->table};" );
	}

	/**
	 * Name of the table.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_table() {
		return $this->table;
	}

	/**
	 * Primary key of the table.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_primary_key() {
		return $this->primary_key;
	}

	/**
	 * Retrieve any item for database.
	 *
	 * @since 1.1.0
	 *
	 * @param $id
	 *
	 * @return object|null
	 */
	public function get( $id ) {
		global $wpdb;
		$key          = md5( $id );
		$last_changed = $this->get_last_changed();

		$cache_key = "query:$key:$last_changed";
		$result    = wp_cache_get( $cache_key, $this->object_type );

		if ( false === $result ) {
			$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table WHERE $this->primary_key = %s LIMIT 1;", $id ) );
			wp_cache_add( $cache_key, $result, $this->table );
		}

		return $result;
	}

	/**
	 * Retrieves a row based on column and row ID.
	 *
	 * @since 1.1.0
	 *
	 * @param string $column Column name. See get_columns().
	 * @param string $value
	 *
	 * @return object|null Database query result object, null if nothing was found, or false on failure.
	 */
	public function get_by( $column, $value ) {
		global $wpdb;

		if ( ! array_key_exists( $column, array_merge( static::get_columns(), array( 'id' => '' ) ) ) || empty( $value ) ) {
			return null;
		}

		if ( empty( $column ) || empty( $value ) ) {
			return null;
		}

		if ( $column === $this->primary_key ) {
			return $this->get( $value );
		}

		$key          = md5( serialize( array( $column, $value ) ) );
		$last_changed = $this->get_last_changed();

		$cache_key = "query:$key:$last_changed";
		$result    = wp_cache_get( $cache_key, $this->object_type );
		if ( false === $result ) {
			$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table WHERE $column = '%s' LIMIT 1;", $value ) );
			$result = apply_filters( 'eaccounting_raw_' . $this->object_type . '_data', $result );
			wp_cache_add( $cache_key, $result, $this->table );
		}

		return $result;
	}

	/**
	 * Retrieves a value based on column name and row ID.
	 *
	 * @since 1.1.0
	 *
	 * @param       $select
	 * @param array $where
	 *
	 * @return string|null         Database query result (as string), or null on failure
	 */
	public function get_var( $select, $where = array() ) {
		global $wpdb;

		// take only native columns
		$where = array_intersect_key( $where, $this->get_columns() );

		if ( empty( $where ) ) {
			return null;
		}

		//Builds query's where statement.
		$where_sql = '';
		$keys      = array_keys( $where );
		for ( $i = 0, $max = count( $keys ); $i < $max; $i ++ ) {
			$where_sql .= ( 0 === $i ? ' WHERE ' : ' AND ' );
			$where_sql .= $wpdb->prepare( sprintf( "({$keys[$i]} = %s )", is_numeric( $where[ $keys[ $i ] ] ) ? '%d' : '%s' ), $where[ $keys[ $i ] ] ); // @codingStandardsIgnoreLine
		}

		$key          = md5( serialize( array( $select, $where ) ) );
		$last_changed = $this->get_last_changed();

		$cache_key = "query:$key:$last_changed";
		$result    = wp_cache_get( $cache_key, $this->table );
		if ( false === $result ) {
			$result = $wpdb->get_var( "SELECT $select FROM $this->table $where_sql LIMIT 1;" ); // @codingStandardsIgnoreLine
			$result = apply_filters( 'eaccounting_raw_' . $this->object_type . '_data', $result );
			wp_cache_add( $cache_key, $result, $this->table );
		}

		return $result;
	}


	/**
	 * Insert item.
	 *
	 * @since 1.1.0
	 *
	 * @param $data
	 *
	 * @return \WP_Error|int
	 */
	public function insert( $data ) {
		global $wpdb;

		// Set default values
		$defaults = $this->get_defaults();
		$data     = apply_filters( 'eaccounting_insert_' . $this->object_type, wp_parse_args( $data, $defaults ) );

		do_action( 'eaccounting_pre_insert_' . $this->object_type, $data );

		$prepared_data = $this->normalize_resource_data( $data );

		if ( is_wp_error( $prepared_data ) ) {
			return $prepared_data;
		}

		// Initialise column format array
		$column_formats = $this->get_columns();
		// Force fields to lower case
		$prepared_data = array_change_key_case( $prepared_data );
		// White list columns
		$prepared_data = array_intersect_key( $prepared_data, $column_formats );

		$data_keys      = array_keys( (array) $prepared_data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );
		// Unslash data.
		$prepared_data = wp_unslash( $prepared_data );
		if ( false === $wpdb->insert( $this->table, $prepared_data, $column_formats ) ) {
			return new \WP_Error( 'db_error', $wpdb->last_error );
		}

		$id = $wpdb->insert_id;
		do_action( 'eaccounting_insert_' . $this->object_type, $id, $data, $prepared_data );
		$this->update_last_changed();

		return $id;
	}

	/**
	 * Update item.
	 *
	 * @since 1.1.0
	 *
	 * @param        $id
	 * @param array  $data
	 * @param string $where
	 *
	 * @return mixed
	 */
	public function update( $id, array $data ) {
		global $wpdb;

		// id must be present & positive.
		$id = absint( $id );
		if ( empty( $id ) ) {
			return new \WP_Error( 'empty_id', __( 'ID must not be empty.' ) );
		}

		$old_item = $this->get( $id );
		if ( ! $old_item ) {
			return new \WP_Error( 'not_exist', __( 'Item does not exist.' ) );
		}

		$old_item = (array) $old_item;

		do_action( 'eaccounting_pre_update_' . $this->object_type, $id, $data, $old_item );

		$prepared_data = $this->normalize_resource_data( array_merge( $old_item, $data ), $id );

		if ( is_wp_error( $prepared_data ) ) {
			return $prepared_data;
		}

		// Initialise column format array
		$column_formats = $this->get_columns();
		// Force fields to lower case
		$prepared_data = array_change_key_case( $prepared_data );
		// White list columns
		$prepared_data = array_intersect_key( $prepared_data, $column_formats );

		$data_keys      = array_keys( (array) $prepared_data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );
		// Unslash data.
		$prepared_data = wp_unslash( $prepared_data );
		if ( false === $wpdb->update( $this->table, $prepared_data, array( $this->primary_key => $id ), $column_formats ) ) {
			return false;
		}

		do_action( 'eaccounting_update_' . $this->object_type, $id, $prepared_data );
		$this->update_last_changed();

		return true;
	}

	/**
	 * Duplicate item.
	 *
	 * @since 1.1.0
	 *
	 * @param $id
	 *
	 * @return int|bool
	 */
	public function duplicate( $id ) {
		_doing_it_wrong(
			'WP_REST_Controller::register_routes',
			/* translators: %s: register_routes() */
			sprintf( __( "Method '%s' must be overridden." ), __METHOD__ ),
			'4.7'
		);

		return false;
	}

	/**
	 * Delete's the entry.
	 *
	 * @since 1.1.0
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function delete( $id ) {
		global $wpdb;

		// ID must be positive integer
		$id = absint( $id );
		if ( empty( $id ) ) {
			return false;
		}

		$item = $this->get( $id );
		if ( ! $item ) {
			return false;
		}
		$item = (array) $item;

		$deletable = true;

		do_action( 'eaccounting_pre_delete_' . $this->object_type, $deletable, $id, $item );

		if ( ! $deletable ) {
			return false;
		}

		if ( false === $wpdb->query( $wpdb->prepare( "DELETE FROM $this->table WHERE $this->primary_key = %d", $id ) ) ) {
			return false;
		}

		do_action( 'eaccounting_delete_' . $this->hook, $id, $item );

		$this->update_last_changed();

		return true;
	}

	/**
	 * Retrieves items from the database.
	 *
	 * @since 1.1.0
	 *
	 * @param array       $args
	 *
	 * @param bool|string $callback
	 *
	 * @return array
	 */
	public function get_items( $args = array(), $callback = false ) {
		global $wpdb;
		$defaults = array(
			'number'      => 20,
			'offset'      => 0,
			'paged'       => '1',
			'include'     => '',
			'search'      => '',
			'exclude'     => '',
			'orderby'     => $this->primary_key,
			'count'       => false,
			'order'       => 'ASC',
			'fields'      => '*',
			'search_cols' => array(),
			'where'       => array(),
			'join'        => array(),
			'groupby'     => array(),
			'having'      => array(),
		);
		$args     = wp_parse_args( $args, $defaults );
		$args     = apply_filters( 'eaccounting_pre_get_' . $this->object_type, $args );
		$columns  = static::get_columns();

		if ( ! empty( $args['include'] ) ) {
			$includes        = implode( ',', wp_parse_id_list( $args['include'] ) );
			$args['where'][] = array(
				'joint'     => 'AND',
				'condition' => "{$this->table_name}.{$this->primary_key} IN( {$includes} ) ",
			);
		}

		if ( ! empty( $args['exclude'] ) ) {
			$excludes        = implode( ',', wp_parse_id_list( $args['exclude'] ) );
			$args['where'][] = array(
				'joint'     => 'AND',
				'condition' => "{$this->table_name}.{$this->primary_key} IN( {$excludes} ) ",
			);
		}

		//Status
		if ( isset( $args['status'] ) && in_array( $args['status'], array( 'active', 'inactive' ), true ) ) {
			$args['enabled'] = 'active' === $args['status'] ? '1' : '0';
		}
		if ( isset( $args['enabled'] ) && '' !== $args['enabled'] && array_key_exists( 'enabled', $columns ) ) {
			$args['where'][] = array(
				'joint'     => 'AND',
				'condition' => $wpdb->prepare( "{$this->table_name}.enabled = %d ", $args['enabled'] ),
			);
		}

		//search
		//Search
		if ( ! empty( $args['search'] ) ) {
			$searches = array();
			$words    = array_unique( array_filter( explode( ' ', $args['search'] ) ) );
			$cols     = empty( $args['search_cols'] ) ? array_keys( $columns ) : $args['search_cols'];
			$cols     = $this->aliased_fields( $cols );

			if ( ! empty( $words ) || ! empty( $cols ) ) {
				foreach ( $words as $word ) {
					$like = '%' . $wpdb->esc_like( $word ) . '%';
					foreach ( $cols as $col ) {
						$searches[] = $wpdb->prepare( "$col LIKE %s", $like );
					}
				}
			}
			if ( ! empty( $searches ) ) {
				$args['where'][] = array(
					'joint'     => 'AND',
					'condition' => '(' . implode( ' OR ', $searches ) . ')',
				);
			}
		}

		$query = '';
		$query = $this->parse_select( $args, $query );
		$query = $this->parse_from( $args, $query );
		$query = $this->parse_where( $args, $query );
		$query = $this->parse_groupby( $args, $query );
		$query = $this->parse_having( $args, $query );
		$query = $this->parse_orderby( $args, $query );
		$query = $this->parse_limit( $args, $query );

		$key          = serialize( $args );
		$last_changed = $this->get_last_changed();

		$cache_key = "query:$key:$last_changed";
		$result    = wp_cache_get( $cache_key, $this->table );
		if ( false === $result ) {
			if ( true === $args['count'] ) {
				$result = (int) $wpdb->get_var( $query );
			} else {
				$result = $wpdb->get_results( $query );
				if ( $callback ) {
					$result = array_map( $callback, $result );
				}
			}

			wp_cache_add( $cache_key, $result, $this->table );
		}

		return $result;
	}

	/**
	 * Count items.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function count( $args = array() ) {
		return $this->get_items( array_merge( $args, array( 'count' => true ) ) );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param int   $id
	 * @param array $data
	 *
	 * @return array|\WP_Error
	 */
	protected function normalize_resource_data( $data, $id = null ) {

		$data = apply_filters( 'eaccounting_prepare_' . $this->object_type . '_data', $data, $id );

		$errors = new \WP_Error();
		/**
		 * Fires when data should be validated for a site prior to inserting or updating in the database.
		 *
		 * Plugins should amend the `$errors` object via its `WP_Error::add()` method.
		 *
		 * @since 1.1.0
		 *
		 * @param \WP_Error $errors Error object to add validation errors to.
		 * @param array     $data   Associative array of complete item data.
		 * @param int|null  $id     The ID of the entry.
		 */
		do_action( 'eaccounting_validate_' . $this->object_type . '_data', $errors, $data, $id );

		if ( ! empty( $errors->errors ) ) {
			return $errors;
		}

		//if updating do not overwrite date created.
		if ( $id && array_key_exists( 'date_created', $data ) ) {
			unset( $data['date_created'] );
		}

		//if updating do not overwrite creator.
		if ( $id && array_key_exists( 'creator_id', $data ) ) {
			unset( $data['creator_id'] );
		}

		return $data;
	}

	protected function parse_select( $args, &$query = '' ) {
		$args = wp_parse_args(
			$args,
			array(
				'fields' => '*',
				'count'  => false,
			)
		);

		if ( true === $args['count'] ) {
			$query .= "SELECT count({$this->table_name}.{$this->primary_key})";

			return $query;
		}

		$fields = apply_filters( 'eaccounting_parse_select_' . $this->object_type, $args['fields'], $args );

		if ( ! is_array( $fields ) ) {
			$fields = preg_split( '/[,\s]+/', $fields );
		}

		$whitelist       = static::get_columns();
		$whitelist['id'] = '%d';

		foreach ( $fields as $key => $field ) {
			if ( false === strpos( $field, '.' ) && array_key_exists( $field, $whitelist ) ) {
				$fields[ $key ] = "{$this->table_name}.$field";
			}
		}

		$query .= 'SELECT ' . implode( ',', $fields );

		return $query;
	}

	protected function parse_from( $args = array(), &$query = '' ) {
		$query .= " FROM `{$this->table}` as {$this->table_name}";

		return $query;
	}

	protected function parse_where( $args = array(), &$query = '' ) {
		global $wpdb;
		$args   = wp_parse_args( $args, array( 'where' => array() ) );
		$wheres = apply_filters( 'eaccounting_parse_where_' . $this->object_type, $args['where'], $args );

		foreach ( $wheres as $key => $where ) {
			$where = wp_parse_args(
				$where,
				array(
					'joint'     => 'AND',
					'condition' => '',
					'field'     => '',
					'value'     => 'NULL',
					'operator'  => '=',
				)
			);

			if ( ! empty( $where['condition'] ) || empty( $where['field'] ) ) {
				continue;
			}

			$condition = '( 1 = 1 )';
			$joint     = $where['joint'];
			$value     = $where['value'];
			$operator  = $where['operator'];
			$fields    = $this->aliased_fields( $where['field'] );
			$field     = array_pop( $fields );

			switch ( $operator ) {
				default:
					if ( is_array( $value ) ) {
						$value = '("' . implode( '","', $value ) . '")';
					} elseif ( false !== strpos( $value, $wpdb->prefix ) ) {

					} elseif ( is_numeric( $value ) ) {
						$value = $wpdb->prepare( '%d', $value );
					} elseif ( null === $value ) {
						$value = 'null';
					} else {
						$value = $wpdb->prepare( '%s', $value );
					}
					$condition = implode( ' ', array( $field, $operator, $value ) );
					break;
			}

			$wheres[ $key ] = array(
				'joint'     => $joint,
				'condition' => $condition,
			);
		}

		for ( $i = 0, $max = count( $wheres ); $i < $max; $i ++ ) {
			$query .= ( 0 === $i ? ' WHERE ' : ' ' . $wheres[ $i ]['joint'] . ' ' );
			$query .= $wheres[ $i ]['condition'];
		}

		return $query;
	}


	protected function parse_join( $args = array(), &$query = '' ) {
		$args = wp_parse_args( $args, array( 'join' => array() ) );
		foreach ( $args['join'] as $join ) {
			$query .= ( ! empty( $join['type'] ) ? ' ' . $join['type'] . ' JOIN ' : ' JOIN ' ) . $join['table'];
			for ( $i = 0, $max = count( $join['on'] ); $i < $max; ++ $i ) {
				$query .= ( 0 === $i ? ' ON ' : ' ' . $join['on'][ $i ]['joint'] . ' ' ) . $join['on'][ $i ]['condition'];
			}
		}

		return $query;
	}


	protected function parse_groupby( $args = array(), &$query = '' ) {
		if ( ! empty( $args['groupby'] ) ) {
			$query .= ' GROUP BY ' . implode( ',', $this->aliased_fields( $args['groupby'] ) );
		}

		return $query;
	}

	protected function parse_having( $args = array(), &$query = '' ) {
		if ( ! empty( $args['having'] ) ) {
			$query .= ' HAVING ' . implode( ' AND ', $args['having'] );
		}

		return $query;
	}

	protected function parse_orderby( $args = array(), &$query = '' ) {
		if ( ! empty( $args['orderby'] ) ) {
			$query .= ' ORDER BY ' . implode( ',', $this->aliased_fields( $args['orderby'] ) );
			$query .= ! empty( $args['order'] ) && in_array( strtoupper( $args['order'] ), array( 'ASC', 'DESC', 'RAND' ), true ) ? ' ' . $args['order'] : '';
		}

		return $query;
	}

	protected function parse_limit( $args = array(), &$query = '' ) {
		$args = wp_parse_args(
			$args,
			array(
				'number' => '20',
				'count'  => false,
			)
		);
		global $wpdb;
		if ( false === $args['count'] && isset( $args['number'] ) && $args['number'] > 0 ) {
			if ( $args['offset'] ) {
				$query .= $wpdb->prepare( ' LIMIT %d, %d', $args['offset'], $args['number'] );
			} else {
				$query .= $wpdb->prepare( ' LIMIT %d, %d', $args['number'] * ( $args['paged'] - 1 ), $args['number'] );
			}
		}

		return $query;
	}


	/**
	 * Add table alias to the fields.
	 *
	 * @since 1.1.0
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	protected function aliased_fields( $fields ) {
		$whitelist   = array_keys( static::get_columns() );
		$whitelist[] = 'id';

		if ( ! is_array( $fields ) ) {
			$fields = preg_split( '/[,\s]+/', $fields );
		}

		foreach ( $fields as $key => $field ) {
			if ( false === strpos( $field, '.' ) && in_array( $field, $whitelist, true ) ) {
				$fields[ $key ] = "{$this->table_name}.$field";
			}
		}

		return $fields;
	}


	/**
	 * Sets the last_changed cache key for items.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	public function update_last_changed() {
		wp_cache_set( 'last_changed', microtime(), $this->object_type );
	}

	/**
	 * Retrieves the value of the last_changed cache key for items.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_last_changed() {
		if ( function_exists( 'wp_cache_get_last_changed' ) ) {
			return wp_cache_get_last_changed( $this->object_type );
		}

		$last_changed = wp_cache_get( 'last_changed', $this->object_type );
		if ( ! $last_changed ) {
			$last_changed = microtime();
			wp_cache_set( 'last_changed', $last_changed, $this->object_type );
		}

		return $last_changed;
	}
}
