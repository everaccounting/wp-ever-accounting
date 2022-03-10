<?php
/**
 * Handle the Revenue object.
 *
 * @package     EverAccounting
 * @class       Revenue
 * @version     1.0.2
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class revenue
*/
class Revenue extends Transaction {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $object_type = 'revenue';

	/**
	 * Revenue constructor.
	 *
	 * @param int|revenue|object|null $revenue revenue instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $revenue = 0 ) {
		$this->core_data['type']       = 'income';
		parent::__construct( $revenue );

		if ( $this->type !== 'income' ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return the customer id.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_customer_id() {
		return $this->get_prop( 'contact_id' );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/
	/**
	 * set the customer id.
	 *
	 * @since  1.1.0
	 *
	 * @param int $customer_id .
	 *
	 */
	public function set_customer_id( $customer_id ) {
		$this->set_prop( 'contact_id', absint( $customer_id ) );
	}

}
