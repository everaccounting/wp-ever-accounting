<?php
/**
 * Contacts Rest Controller Class.
 *
 * @package     EverAccounting
 * @subpackage  Api
 * @since       1.0.2
 */

namespace EverAccounting\API;

defined( 'ABSPATH' ) || exit();

class Contacts_Controller extends Controller {

	/**
	 * Specify the type of contact will be used.
	 *
	 * @since 1.1.0
	 * @return \WP_Error
	 */
	protected function get_type() {
		// translators: %s: Class method name.
		return new \WP_Error( 'invalid-method', sprintf( __( "Method '%s' not implemented. Must be overridden in subclass.", 'wp-ever-accounting' ), __METHOD__ ), array( 'status' => 405 ) );
	}

	public function get_items( $request ) {
		$args = array(
			'type'         => $request['type'],
			'city'         => $request['city'],
			'state'        => $request['state'],
			'postcode'     => $request['postcode'],
			'country'      => $request['country'],
			'date_created' => $request['date_created'],
			'include'      => $request['include'],
			'exclude'      => $request['exclude'],
			'search'       => $request['search'],
			'orderby'      => $request['orderby'],
			'order'        => $request['order'],
			'per_page'     => $request['per_page'],
			'page'         => $request['page'],
			'offset'       => $request['offset'],
		);
	}
}
