<?php
/**
 * Contacts Rest Controller Class.
 *
 * @since       1.1.2
 * @subpackage  Rest
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Abstracts\Contacts_Controller;
use EverAccounting\Models\Vendor;

defined( 'ABSPATH' ) || die();

/**
 * Class Vendors_Controller
 *
 * @since   1.1.2
 *
 * @package EverAccounting\REST
 */
class Vendors_Controller extends Contacts_Controller {
	/**
	 * entity type.
	 *
	 * @var string
	 */
	protected $entity_type = 'vendor';

	/**
	 * Route base.
	 *
	 * @since 1.1.2
	 *
	 * @var string
	 */
	protected $rest_base = 'vendors';

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
	 * @param array            $query_args Query args.
	 * @param \WP_REST_Request $request Full details about the request.
	 *
	 * @return array|int|\WP_Error
	 * @since  1.1.2
	 */
	protected function get_objects( $query_args, $request ) {
		return eaccounting_get_vendors( $query_args );
	}
}
