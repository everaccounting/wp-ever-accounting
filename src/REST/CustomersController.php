<?php
/**
 * Customers Rest Controller Class.
 *
 * @since       1.1.0
 * @subpackage  REST
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Models\Customer;

defined( 'ABSPATH' ) || die();

/**
 * Class CustomerController
 *
 * @since   1.1.0
 *
 * @package EverAccounting\REST
 */
class CustomersController extends ContactsController {
	/**
	 * Route base.
	 *
	 * @since 1.1.0
	 * 
	 * @var string
	 *
	 */
	protected $rest_base = 'customers';

	/**
	 * Entity model class.
	 *
	 * @since 1.1.0
	 * 
	 * @var string
	 */
	protected $entity_model = Customer::class;

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
		return eaccounting_get_customers( $query_args );
	}
}