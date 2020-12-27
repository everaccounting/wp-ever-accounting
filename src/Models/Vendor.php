<?php
/**
 * Handle the vendor object.
 *
 * @package     EverAccounting\Models
 * @class       Customer
 * @version     1.0.2
 */

namespace EverAccounting\Models;

use EverAccounting\Traits\AttachmentTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Class Vendor
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Vendor extends Contact {
	use AttachmentTrait;
	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'vendor';

	/**
	 * Get the customer if ID is passed, otherwise the customer is new and empty.
	 *
	 * @since 1.1.0
	 *
	 * @param int|object|Customer $data object to read.
	 *
	 * @throws \Exception
	 */
	public function __construct( $data = 0 ) {
		$this->data = array_merge( $this->data, array( 'type' => 'vendor' ) );
		parent::__construct( $data );

		if ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( is_numeric( $data ) ) {
			$this->set_id( $data );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} elseif ( is_array( $data ) ) {
			$this->set_props( $data );
		} else {
			$this->set_object_read( true );
		}

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}

		$this->required_props = array(
			'name'          => __( 'Name', 'wp-ever-accounting' ),
			'currency_code' => __( 'Currency Code', 'wp-ever-accounting' ),
		);
	}
}
