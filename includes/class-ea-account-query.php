<?php
/**
 * Class for Account querying
 *
 * @package  EverAccounting/Classes
 * @since    1.0.2
 */

defined( 'ABSPATH' ) || exit;

class EAccounting_Account_Query extends EAccounting_Query {
	/**
	 * Table name.
	 *
	 * @var string
	 * @since 1.0.2
	 */
	protected static $table = 'ea_accounts';

	/**
	 * Static constructor.
	 *
	 *
	 * @param string $id
	 *
	 * @return EAccounting_Account_Query
	 * @since 1.0.0
	 */
	public static function init( $id = 'account_query' ) {
		global $wpdb;
		$builder       = new self();
		$builder->id   = ! empty( $id ) ? $id : uniqid( '', true );
		$builder->from = $wpdb->prefix . self::$table;

		return $builder;
	}


	public function get_accounts( $args = array() ) {
		$defaults = array(
			'number'  => 20,
			'offset'  => 0,
			'include' => array(),
			'exclude' => array(),
			'status'  => '',
			'order'   => 'DESC',
			'orderby' => 'id',
			'search'       => '',
		);

		$args = (array) wp_parse_args( $args, $defaults );

		$builder = $this->copy();

		if ( ! empty( $args['search'] ) ) {
			$builder->search( eaccounting_clean( $args['search'] ), array(
				'name',
				'number',
				'bank_name',
				'bank_phone',
				'bank_address'
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
