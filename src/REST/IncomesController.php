<?php

/**
 * Transactions Rest Controller Class.
 *
 * @since       1.1.0
 * @subpackage  REST
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Models\Income;

defined( 'ABSPATH' ) || die();

/**
 * Class RevenuesController
 *
 * @since   1.1.0
 *
 * @package EverAccounting\REST
 */
class IncomesController extends TransactionsController {
	/**
	 * Route base.
	 *
	 * @since   1.1.0
	 * @var string
	 *
	 */
	protected $rest_base = 'incomes';
	/**
	 * Entity model class.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $entity_model = Income::class;

	/**
	 * Get objects.
	 *
	 * @since  1.1.0
	 *
	 * @param array            $query_args Query args.
	 * @param \WP_REST_Request $request    Full details about the request.
	 *
	 * @return array|int|\WP_Error
	 */
	protected function get_objects( $query_args, $request ) {
		$query_args['account_id']     = $request['account_id'];
		$query_args['category_id']    = $request['category_id'];
		$query_args['currency_code']  = $request['currency_code'];
		$query_args['vendor_id']      = $request['vendor_id'];
		$query_args['payment_method'] = $request['payment_method'];

		// Set before into date query. Date query must be specified as an array of an array.
		if ( isset( $request['before'] ) ) {
			$args['payment_date'][0]['before'] = $request['before'];
		}

		// Set after into date query. Date query must be specified as an array of an array.
		if ( isset( $request['after'] ) ) {
			$args['payment_date'][0]['after'] = $request['after'];
		}
		return eaccounting_get_incomes( $query_args );
	}
}
