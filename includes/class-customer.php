<?php
/**
 * Contact data handler class.
 *
 * @version     1.0.2
 * @package     EverAccounting
 * @class       Contact
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Contact class.
 */
class Customer extends Contact {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $object_type = 'customer';

	/**
	 * Contact constructor.
	 *
	 * @param int|customer|object|null $customer customer instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $customer = 0 ) {

		$this->core_data['type']       = 'customer';
		$this->meta_data['total_paid'] = 0;
		$this->meta_data['total_due']  = 0;

		parent::__construct( $customer );

		if ( $this->type !== 'customer' ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}

}
