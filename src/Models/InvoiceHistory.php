<?php
/**
 * Handle the invoice history object.
 *
 * @package     EverAccounting\Models
 * @class       Currency
 * @version     1.1.0
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Core\Repositories;
use EverAccounting\Repositories\InvoiceHistories;

defined( 'ABSPATH' ) || exit;

/**
 * Class InvoiceHistory
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class InvoiceHistory extends ResourceModel {
	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'invoice_history';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	public $cache_group = 'eaccounting_invoice_history';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $data = array(
		'invoice_id'   => null,
		'status'       => '',
		'notify'       => 0,
		'description'  => '',
		'date_created' => null,
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
		$this->repository = Repositories::load( 'invoice-history' );

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}
	}
	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return the invoice id.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_invoice_id( $context = 'edit' ) {
		return $this->get_prop( 'invoice_id', $context );
	}

	/**
	 * Return the status.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_status( $context = 'edit' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Return the notify.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_notify( $context = 'edit' ) {
		return $this->get_prop( 'notify', $context );
	}

	/**
	 * Return the description.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_description( $context = 'edit' ) {
		return $this->get_prop( 'description', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * set the invoice id.
	 *
	 * @since  1.1.0
	 *
	 * @param int $invoice_id .
	 *
	 */
	public function set_invoice_id( $invoice_id ) {
		$this->set_prop( 'invoice_id', absint( $invoice_id ) );
	}

	/**
	 * set the status.
	 *
	 * @since  1.1.0
	 *
	 * @param string $status .
	 *
	 */
	public function set_status( $status ) {
		$this->set_prop( 'status', eaccounting_clean( $status ) );
	}

	/**
	 * set the notify.
	 *
	 * @since  1.1.0
	 *
	 * @param int $notify .
	 *
	 */
	public function set_notify( $notify ) {
		$this->set_prop( 'notify', absint( $notify ) );
	}

	/**
	 * set the description.
	 *
	 * @since  1.1.0
	 *
	 * @param string $description .
	 *
	 */
	public function set_description( $description ) {
		$this->set_prop( 'description', eaccounting_sanitize_textarea( $description ) );
	}

}
