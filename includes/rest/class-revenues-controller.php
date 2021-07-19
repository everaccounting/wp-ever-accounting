<?php
/**
 * Revenues Rest Controller Class.
 *
 * @since       1.1.2
 * @subpackage  Rest
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Abstracts\Transactions_Controller;
use EverAccounting\Models\Revenue;

defined( 'ABSPATH' ) || die();

/**
 * Class RevenuesController
 *
 * @since   1.1.2
 *
 * @package EverAccounting\REST
 */
class Revenues_Controller extends Transactions_Controller {
	/**
	 * Route base.
	 *
	 * @since   1.1.2
	 *
	 * @var string
	 *
	 */
	protected $rest_base = 'revenues';
	/**
	 * Entity model class.
	 *
	 * @since 1.1.2
	 *
	 * @var string
	 */
	protected $entity_model = Revenue::class;

	/**
	 * Entity type.
	 *
	 * @since   1.1.2
	 *
	 * @var string
	 *
	 */
	protected $entity_type = 'revenue';

	/**
	 * Get objects.
	 *
	 * @param array $query_args Query args.
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return array|int|\WP_Error
	 * @since  1.1.2
	 *
	 */
	protected function get_objects( $query_args, $request ) {
		$query_args['account_id']      = $request['account_id'];
		$query_args['category_id']     = $request['category_id'];
		$query_args['currency_code']   = $request['currency_code'];
		$query_args['customer_id']     = $request['customer_id'];
		$query_args['payment_method']  = $request['payment_method'];
		$query_args['amount_min']  = (float) $request['amount_min'];
		$query_args['amount_max']  = (float) $request['amount_max'];
		$query_args['amount_between']  = array_map('floatval', $request['amount_between']);
		$query_args['account__in']     = wp_parse_id_list( $request['account__in'] );
		$query_args['account__not_in'] = wp_parse_id_list( $request['account__not_in'] );
		$query_args['payment_date']    = [
			[
				'before' => '',
				'after'  => ''
			],
		];

		// Set before into date query. Date query must be specified as an array of an array.
		if ( ! empty( $request['payment_date_before'] ) ) {
			$query_args['payment_date'][0]['before'] = eaccounting_date( $request['payment_date_before'], 'Y-m-d' );
		}

		// Set after into date query. Date query must be specified as an array of an array.
		if ( ! empty( $request['payment_date_after'] ) ) {
			$query_args['payment_date'][0]['after'] = eaccounting_date( $request['payment_date_after'], 'Y-m-d' );
		}
		// Set after into date query. Date query must be specified as an array of an array.
		if ( ! empty( $request['payment_date_between'] ) && is_array( $request['payment_date_between'] )) {
			$min = min( $request['payment_date_between'] );
			$max = max( $request['payment_date_between'] );
			if( $min && $max ){
				$query_args['payment_date']    = [
					[
						'before' => eaccounting_date($max, 'Y-m-d'),
						'after'  => eaccounting_date($min, 'Y-m-d'),
					],
				];
			}
		}

		return eaccounting_get_revenues( $query_args );
	}
}
