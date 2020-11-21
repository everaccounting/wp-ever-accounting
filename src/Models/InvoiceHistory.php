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
	 * Get the InvoiceHistory if ID is passed, otherwise the invoice item is new and empty.
	 *
	 * @param int|object|InvoiceHistory $data object to read.
	 *
	 * @since 1.1.0
	 *
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data, InvoiceHistories::instance() );
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return the invoice id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_invoice_id( $context = 'edit' ) {
		return $this->get_prop( 'invoice_id', $context );
	}

	/**
	 * Return the status.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_status( $context = 'edit' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Return the notify.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_notify( $context = 'edit' ) {
		return $this->get_prop( 'notify', $context );
	}

	/**
	 * Return the description.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
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
	 * @param int $invoice_id .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_invoice_id( $invoice_id ) {
		$this->set_prop( 'invoice_id', absint( $invoice_id ) );
	}

	/**
	 * set the status.
	 *
	 * @param string $status .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_status( $status ) {
		$this->set_prop( 'status', eaccounting_clean( $status ) );
	}

	/**
	 * set the notify.
	 *
	 * @param int $notify .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_notify( $notify ) {
		$this->set_prop( 'notify', absint( $notify ) );
	}

	/**
	 * set the description.
	 *
	 * @param string $description .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_description( $description ) {
		$this->set_prop( 'description', eaccounting_sanitize_textarea( $description ) );
	}

}
