<?php
/**
 * Handle the customer object.
 *
 * @package     EverAccounting\Models
 * @class       Customer
 * @version     1.0.2
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Core\Repositories;
use EverAccounting\Traits\AttachmentTrait;
use EverAccounting\Traits\ContactTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Class Customer
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Customer extends ResourceModel {
	use AttachmentTrait;
	use ContactTrait;

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'customer';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	public $cache_group = 'ea_customers';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $data = array(
		'user_id'       => null,
		'name'          => '',
		'email'         => '',
		'phone'         => '',
		'fax'           => '',
		'birth_date'    => '',
		'address'       => '',
		'country'       => '',
		'website'       => '',
		'tax_number'    => '',
		'currency_code' => '',
		'type'          => '',
		'avatar_id'     => null,
		'enabled'       => 1,
		'creator_id'    => null,
		'date_created'  => null,
	);

	/**
	 * Get the account if ID is passed, otherwise the account is new and empty.
	 *
	 * @since 1.1.0
	 *
	 * @param int|object|Account $data object to read.
	 *
	 */
	public function __construct( $data = 0 ) {
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

		//Load repository
		$this->repository = Repositories::load( 'customers' );

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}

		// If not income then reset to default
		if ( 'customer' !== $this->get_type() ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}

		$this->required_props = array(
			'name'          => __( 'Name', 'wp-ever-accounting' ),
			'currency_code' => __( 'Currency Code', 'wp-ever-accounting' ),
		);
	}

}
