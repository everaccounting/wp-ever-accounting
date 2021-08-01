<?php
/**
 * Contact Query class.
 * @since   1.2.1
 * @package   EverAccounting
 */

namespace EverAccounting;

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
	const TABLE_NAME = 'ea_contacts';

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
		if ( is_null( $query ) ) {
			$query = $this->query_vars;
		}

		$query = (array) wp_parse_args( $query, $this->query_var_defaults );

		// Parse args.
		$query['number']        = absint( $query['number'] );
		$query['offset']        = absint( $query['offset'] );
		$query['no_found_rows'] = (bool) $query['no_found_rows'];
	}
}
