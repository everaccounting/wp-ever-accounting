<?php
defined( 'ABSPATH' ) || exit();

class EAccounting_Query_Currency extends EAccounting_Query {
	/**
	 * Static constructor.
	 *
	 *
	 * @since 1.0.0
	 *
	 */
	public static function init( $id = null ) {
		$builder     = new self();
		$builder->id = ! empty( $id ) ? $id : uniqid();
		$builder->from( 'ea_currencies' );

		return $builder;
	}


	public function get_currencies( $args = array() ) {
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

		$builder = $this->copy();

		if ( ! empty( $args['search'] ) ) {
			$builder->search( eaccounting_clean( $args['search'] ), array(
				'name',
				'code',
			) );
		}

		if ( $args['status'] === 'active' ) {
			$builder->where( 'enabled', 1 );
		}

		if ( $args['status'] === 'inactive' ) {
			$builder->where( 'enabled', 0 );
		}

		$builder->offset( absint( $args['offset'] ) );
		$builder->limit( absint( $args['number'] ) );
		$builder->order_by( $args['orderby'], $args['order'] );

		return $builder;
	}
}
