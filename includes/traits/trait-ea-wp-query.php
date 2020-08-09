<?php
/**
 * Trait for setting wp style query arguments
 *
 * @package EverAccounting/Traits
 * @version 1.0.2
 */

namespace EverAccounting\Traits;

defined( 'ABSPATH' ) || exit();

/**
 * Trait WP_Query.
 *
 * @since 1.0.2
 */
trait WP_Query {

	/**
	 * Searchable columns for the current table.
	 *
	 * @return array Table columns.
	 * @since 1.0.2
	 *
	 */
	abstract protected function get_search_columns();

	/**
	 * Implement WP style query.
	 *
	 * @param $args
	 *
	 * @return
	 * @since 1.0.2
	 *
	 */
	public function wp_query( $args ) {
		$defaults = array(
			'number'  => 20,
			'offset'  => 0,
			'include' => array(),
			'exclude' => array(),
			'status'  => '',
			'order'   => 'DESC',
			'orderby' => 'id',
			'search'  => '',
		);

		$args = (array) wp_parse_args( $args, $defaults );


		if ( ! empty( $args['search'] ) ) {
			$this->search( eaccounting_clean( $args['search'] ), $this->get_search_columns() );
		}

		if ( $args['status'] === 'active' ) {
			$this->where( 'enabled', 1 );
		}

		if ( $args['status'] === 'inactive' ) {
			$this->where( 'enabled', 0 );
		}

		//for category and transactions
		if ( ! empty( $args['type'] ) ) {
			$this->where( 'type', eaccounting_clean( $args['type'] ) );
		}

		$this->offset( absint( $args['offset'] ) );
		$this->limit( absint( $args['number'] ) );
		$this->order_by( $args['orderby'], $args['order'] );

		return $this;
	}
}
