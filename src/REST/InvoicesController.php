<?php
/**
 * Invoice Rest Controller Class.
 *
 * @since       1.1.0
 * @subpackage  REST
 * @package     EverAccounting
 */

namespace EverAccounting\REST;

use EverAccounting\Models\Invoice;

defined( 'ABSPATH' ) || die();

class InvoicesController extends DocumentsController {
	/**
	 * Route base.
	 *
	 * @var string
	 *
	 * @since 1.1.0
	 */
	protected $rest_base = 'invoices';

	/**
	 * Entity type.
	 *
	 * @var string
	 * @since 1.1.1
	 */
	protected $entity_type = 'invoice';

	/**
	 * Entity model class.
	 *
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $entity_model = Invoice::class;

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
		$query_args['category_id']   = $request['category_id'];
		$query_args['contact_id']    = $request['contact_id'];
		$query_args['discount_type'] = $request['discount_type'];
		$query_args['currency_code'] = $request['currency_code'];

		// Set before into date query. Date query must be specified as an array of an array.
		if ( isset( $request['before'] ) ) {
			$args['payment_date'][0]['before'] = $request['before'];
		}

		// Set after into date query. Date query must be specified as an array of an array.
		if ( isset( $request['after'] ) ) {
			$args['payment_date'][0]['after'] = $request['after'];
		}

		return eaccounting_get_invoices( $query_args );
	}
}
