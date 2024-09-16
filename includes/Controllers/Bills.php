<?php

namespace EverAccounting\Controllers;

defined( 'ABSPATH' ) || exit;

/**
 * Bills controller.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Controllers
 */
class Bills {

	/**
	 * Get bill columns.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'item'         => esc_html__( 'Item', 'wp-ever-accounting' ),
			'price'        => esc_html__( 'Price', 'wp-ever-accounting' ),
			'quantity'     => esc_html__( 'Quantity', 'wp-ever-accounting' ),
			'subtotal_tax' => esc_html__( 'Tax', 'wp-ever-accounting' ),
			'subtotal'     => esc_html__( 'Subtotal', 'wp-ever-accounting' ),
		);

		return apply_filters( 'eac_bill_columns', $columns );
	}
}
