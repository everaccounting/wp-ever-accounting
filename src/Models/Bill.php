<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Bill.
 *
 * @since   1.0.0
 * @package EverAccounting\Models
 */
class Bill extends Document {

	/**
	 * Object type.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $object_type = 'bill';

	/**
	 * Constructor.
	 *
	 * @param int|object|array $data Object ID, post object, or array of data.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $data = 0 ) {
		$this->core_data = array_merge( $this->core_data, array( 'type' => 'bill' ) );
		parent::__construct( $data );

		// after reading check if the contact is a customer.
		if ( $this->exists() && 'bill' !== $this->get_type() ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}
	}
}
