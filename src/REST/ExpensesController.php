<?php

/**
 * Transactions Rest Controller Class.
 *
 * @since       1.1.0
 * @subpackage  REST
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Models\Expense;

defined( 'ABSPATH' ) || die();

/**
 * Class PaymentsController
 *
 * @since   1.1.0
 *
 * @package EverAccounting\REST
 */
class ExpensesController extends TransactionsController {
	/**
	 * Route base.
	 *
	 * @since 1.1.0
	 * @var string
	 *
	 */
	protected $rest_base = 'expenses';

	/**
	 * Entity model class.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $entity_model = Expense::class;

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
		return eaccounting_get_expenses( $query_args );
	}
}
