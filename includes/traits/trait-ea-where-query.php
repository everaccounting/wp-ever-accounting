<?php
/**
 * Trait for setting over witting where.
 *
 * @since      1.0.2
 * @subpackage Traits
 * @package    EverAccounting
 */

namespace EverAccounting\Traits;

defined( 'ABSPATH' ) || exit();

/**
 * Trait WP_Query.
 *
 * @since 1.0.2
 */
trait Query_Where {
	/*
	 * @param array|string $column
	 * @param null         $param1
	 * @param null         $param2
	 * @param string       $joint
	 * @since 1.0.2
	 *
	 * @return $this
	 */
	public function where( $column, $param1 = null, $param2 = null, $joint = 'and' ) {
		if ( is_array( $column ) ) {

			foreach ( $column as $key => $value ) {

				if ( empty( $value ) ) {
					continue;
				}

				switch ( $key ) {
					case 's':
					case 'search':
						$this->search( eaccounting_clean( $value ), $this->search_columns );
						unset( $column[ $key ] );
						break;
					case 'status':
					case 'enabled':
						$status = $value == 'active' && ! is_numeric( $value ) ? 1 : 0;
						parent::where( 'enabled', $status );
						unset( $column[ $key ] );
						break;
					case 'limit':
					case 'number':
					case 'offset':
						unset( $column[ $key ] );
						break;
				}
			}

			if ( array_key_exists( 'page', $column ) && array_key_exists( 'per_page', $column ) ) {
				$this->page( absint( $column['page'] ), absint( $column['per_page'] ) );
				unset( $column['page'] );
				unset( $column['per_page'] );
			}

			if ( array_key_exists( 'order', $column ) && array_key_exists( 'orderby', $column ) ) {
				$this->order_by( eaccounting_clean( $column['orderby'] ), eaccounting_clean( $column['order'] ) );
				unset( $column['order'] );
				unset( $column['orderby'] );
			}
		}

		if ( ! empty( $column ) ) {
			return parent::where( $column, $param1, $param2, $joint );
		}

		return $this;
	}

}
