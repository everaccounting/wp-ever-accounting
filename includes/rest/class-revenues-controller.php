<?php
/**
 * Revenues Rest Controller Class.
 *
 * @since       1.1.2
 * @subpackage  Rest
 * @package     Ever_Accounting
 */

namespace Ever_Accounting\REST;

use Ever_Accounting\Abstracts\Transactions_Controller;
use Ever_Accounting\Models\Revenue;

defined( 'ABSPATH' ) || die();

/**
 * Class RevenuesController
 *
 * @since   1.1.2
 *
 * @package Ever_Accounting\REST
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
		$query_args['account_id']     = $request['account_id'];
		$query_args['category_id']    = $request['category_id'];
		$query_args['currency_code']  = $request['currency_code'];
		$query_args['customer_id']    = $request['customer_id'];
		$query_args['payment_method'] = $request['payment_method'];

		// Set before into date query. Date query must be specified as an array of an array.
		if ( isset( $request['before'] ) ) {
			$args['payment_date'][0]['before'] = $request['before'];
		}

		// Set after into date query. Date query must be specified as an array of an array.
		if ( isset( $request['after'] ) ) {
			$args['payment_date'][0]['after'] = $request['after'];
		}

		return eaccounting_get_revenues( $query_args );
	}
}
