<?php
/**
 * Contacts Rest Controller Class.
 *
 * @since       1.1.2
 * @subpackage  Rest
 * @package     Ever_Accounting
 */

namespace Ever_Accounting\REST;

use Ever_Accounting\Abstracts\Contacts_Controller;
use Ever_Accounting\Models\Vendor;

defined( 'ABSPATH' ) || die();

/**
 * Class Vendors_Controller
 *
 * @since   1.1.2
 *
 * @package Ever_Accounting\REST
 */
class Vendors_Controller extends Contacts_Controller {
	/**
	 * Route base.
	 *
	 * @since 1.1.2
	 *
	 * @var string
	 *
	 */
	protected $rest_base = 'vendors';

	/**
	 * Entity Type.
	 *
	 * @since 1.1.1
	 *
	 * @var string
	 *
	 */
	protected $entity_type = 'vendor';

	/**
	 * Entity model class.
	 *
	 * @since 1.1.2
	 *
	 * @var string
	 */
	protected $entity_model = Vendor::class;

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
		return eaccounting_get_vendors( $query_args );
	}
}
