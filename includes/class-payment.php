<?php
/**
 * Handle the Payment object.
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
class Payment extends Transaction {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $object_type = 'payment';

	/**
	 * Payment constructor.
	 *
	 * @param int|payment|object|null $payment payment instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $payment = 0 ) {
		$this->core_data['type']       = 'expense';
		parent::__construct( $payment );

		if ( $this->type !== 'expense' ) {
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
	 * Return the vendor id.
	 *
	 * @since  1.1.0
	 *
	 * @return string
	 */
	public function get_vendor_id() {
		return $this->get_prop( 'contact_id' );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/
	/**
	 * set the vendor id.
	 *
	 * @since  1.1.0
	 *
	 * @param int $vendor_id .
	 *
	 */
	public function set_vendor_id( $vendor_id ) {
		$this->set_prop( 'contact_id', absint( $vendor_id ) );
	}

}
