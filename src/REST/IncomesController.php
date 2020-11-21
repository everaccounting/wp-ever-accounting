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
		return eaccounting_get_incomes( $query_args );
	}
}
