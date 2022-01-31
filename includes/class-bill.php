<?php
/**
 * Handle the bill object.
 *
 * @package     EverAccounting
 * @class       Invoice
 * @version     1.1.0
 */

namespace EverAccounting;

defined( 'ABSPATH' ) || exit;

/**
 * Class Bill
 */
class Bill extends Document {
	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'bill';

	/**
	 * Bill constructor.
	 *
	 * @param int|bill|object|null $invoice invoice instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $document = 0 ) {
		$this->core_data['type']       = 'bill';
		parent::__construct( $document );

		if ( $this->type !== 'bill' ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}

	/**
	 * Get supported statues
	 *
	 * @return array
	 * @since 1.1.0
	 */
	public function get_statuses() {
		return Documents::get_bill_statuses();
	}

}
