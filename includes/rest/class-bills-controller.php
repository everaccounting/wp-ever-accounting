<?php
/**
 * Bill Rest Controller Class.
 *
 * @since       1.1.4
 * @subpackage  Rest
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Abstracts\Documents_Controller;
use EverAccounting\Models\Bill;

defined( 'ABSPATH' ) || die();

/**
 * Class Bills_Controller
 *
 * @package EverAccounting\REST
 */
class Bills_Controller extends Documents_Controller {
	/**
	 * Entity type.
	 *
	 * @var string
	 * @since 1.1.4
	 */
	protected $entity_type = 'bill';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'bills';

	/**
	 * Entity model class.
	 *
	 * @since 1.1.4
	 *
	 * @var string
	 */
	protected $entity_model = Bill::class;

	/**
	 * Get objects.
	 *
	 * @param array            $query_args Query args.
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return array|int|\WP_Error
	 * @since  1.1.4
	 */
	protected function get_objects( $query_args, $request ) {
		return eaccounting_get_bills( $query_args );

	}

}
