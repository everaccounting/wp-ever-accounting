<?php
/**
 * Customers Rest Controller Class.
 *
 * @since       1.1.2
 * @subpackage  Rest
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Abstracts\Entities_Controller;
use EverAccounting\Models\Customer;

defined( 'ABSPATH' ) || die();

/**
 * Class CustomerController
 *
 * @since   1.1.2
 *
 * @package EverAccounting\REST
 */
class Bills_Controller extends Entities_Controller {
	/**
	 * Route base.
	 *
	 * @since 1.1.2
	 *
	 * @var string
	 */
	protected $rest_base = 'bills';

	/**
	 * Entity type.
	 *
	 * @since 1.1.1
	 *
	 * @var string
	 */
	protected $entity_type = 'bill';

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
		return eaccounting_get_bills( $query_args );
	}
}
