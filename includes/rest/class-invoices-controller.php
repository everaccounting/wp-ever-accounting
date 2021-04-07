<?php
/**
 * Invoice Rest Controller Class.
 *
 * @since       1.1.4
 * @subpackage  Rest
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Abstracts\Documents_Controller;
use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || die();

/**
 * Class Invoices_Controller
 *
 * @package EverAccounting\REST
 */
class Invoices_Controller extends Documents_Controller {
	/**
	 * Entity type.
	 *
	 * @var string
	 * @since 1.1.4
	 */
	protected $entity_type = 'invoice';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'invoices';

	/**
	 * Entity model class.
	 *
	 * @since 1.1.4
	 *
	 * @var string
	 */
	protected $entity_model = Invoice::class;

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
		return eaccounting_get_invoices( $query_args );
	}

}
