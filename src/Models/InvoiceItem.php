<?php
/**
 * Handle the invoice item object.
 *
 * @package     EverAccounting\Models
 * @class       Currency
 * @version     1.1.0
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Repositories\InvoiceItems;

defined( 'ABSPATH' ) || exit;

/**
 * Class InvoiceItem
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class InvoiceItem extends ResourceModel {

	/**
	 * Get the InvoiceItem if ID is passed, otherwise the invoice item is new and empty.
	 *
	 * @param int|object|InvoiceItems $data object to read.
	 *
	 * @since 1.1.0
	 *
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data, InvoiceItems::instance() );
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
	 * Return the item id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_item_id( $context = 'edit' ) {
		return $this->get_prop( 'item_id', $context );
	}

	/**
	 * Return the name.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * Return the sku.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_sku( $context = 'edit' ) {
		return $this->get_prop( 'sku', $context );
	}

	/**
	 * Return the quantity.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_quantity( $context = 'edit' ) {
		return $this->get_prop( 'quantity', $context );
	}

	/**
	 * Return the invoice number.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_invoice_number( $context = 'edit' ) {
		return $this->get_prop( 'invoice_number', $context );
	}

	/**
	 * Return the price.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_price( $context = 'edit' ) {
		return $this->get_prop( 'price', $context );
	}

	/**
	 * Return the total.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_total( $context = 'edit' ) {
		return $this->get_prop( 'total', $context );
	}

	/**
	 * Return the tax id.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_tax_id( $context = 'edit' ) {
		return $this->get_prop( 'tax_id', $context );
	}

	/**
	 * Return the tax name.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_tax_name( $context = 'edit' ) {
		return $this->get_prop( 'tax_name', $context );
	}

	/**
	 * Return the tax.
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 * @since  1.1.0
	 *
	 */
	public function get_tax( $context = 'edit' ) {
		return $this->get_prop( 'tax', $context );
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
	 * set the item_id.
	 *
	 * @param int $item_id .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_item_id( $item_id ) {
		$this->set_prop( 'item_id', absint( $item_id ) );
	}


	/**
	 * set the name.
	 *
	 * @param string $name .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', eaccounting_clean( $name ) );
	}

	/**
	 * set the sku.
	 *
	 * @param string $sku .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_sku( $sku ) {
		$this->set_prop( 'sku', eaccounting_clean( $sku ) );
	}

	/**
	 * set the quantity.
	 *
	 * @param double $quantity .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_quantity( $quantity ) {
		$this->set_prop( 'quantity', eaccounting_clean( $quantity ) );
	}

	/**
	 * set the price.
	 *
	 * @param double $price .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_price( $price ) {
		$this->set_prop( 'price', eaccounting_sanitize_price( $price ) );
	}

	/**
	 * set the total.
	 *
	 * @param int $total .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_total( $total ) {
		$this->set_prop( 'total', eaccounting_sanitize_price( $total ) );
	}

	/**
	 * set the tax id.
	 *
	 * @param int $tax_id .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_tax_id( $tax_id ) {
		$this->set_prop( 'tax_id', absint( $tax_id ) );
	}

	/**
	 * set the tax_name.
	 *
	 * @param string $tax_name .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_tax_name( $tax_name ) {
		$this->set_prop( 'tax_name', eaccounting_clean( $tax_name ) );
	}

	/**
	 * set the tax.
	 *
	 * @param double $tax .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_tax( $tax ) {
		$this->set_prop( 'tax', eaccounting_sanitize_price( $tax ) );
	}
}
