<?php
/**
 * Customers Rest Controller Class.
 *
 * @since       1.1.2
 * @subpackage  Rest
 * @package     Ever_Accounting
 */

namespace Ever_Accounting\REST;

use Ever_Accounting\Abstracts\Contacts_Controller;
use Ever_Accounting\Models\Customer;

defined( 'ABSPATH' ) || die();

/**
 * Class CustomerController
 *
 * @since   1.1.2
 *
 * @package Ever_Accounting\REST
 */
class Customers_Controller extends Contacts_Controller {
	/**
	 * Route base.
	 *
	 * @since 1.1.2
	 *
	 * @var string
	 */
	protected $rest_base = 'customers';

	/**
	 * Entity type.
	 *
	 * @since 1.1.1
	 *
	 * @var string
	 *
	 */
	protected $entity_type = 'customer';

	/**
	 * Entity model class.
	 *
	 * @since 1.1.2
	 *
	 * @var string
	 */
	protected $entity_model = Customer::class;

	/**
	 * Get objects.
	 *
	 * @since  1.1.2
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
