<?php

use EverAccounting\Models\Invoice;

/**
 * Handle the tax object.
 *
 * @package     EverAccounting\Models
 * @class       Currency
 * @version     1.1.0
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Repositories\Taxes;

defined( 'ABSPATH' ) || exit;

/**
 * Class Tax
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Tax extends ResourceModel {
	/**
	 * Get the Item if ID is passed, otherwise the invoice is new and empty.
	 *
	 * @param int|object|Tax $data object to read.
	 *
	 * @since 1.1.0
	 *
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data, Taxes::instance() );
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return the tax name.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_name( $context = 'edit' ) {
		return $this->get_prop( 'name', $context );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param string $context
	 *
	 * @return float
	 */
	public function get_rate( $context = 'edit' ) {
		return $this->get_prop( 'rate', $context );
	}

	/**
	 * @since 1.1.0
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_type( $context = 'edit' ) {
		return $this->get_prop( 'type', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set tax name.
	 *
	 * @since 1.1.0
	 *
	 * @param string $name Tax name.
	 */
	public function set_name( $name ) {
		$this->set_prop( 'name', eaccounting_clean( $name ) );
	}

	/**
	 * Set tax rate.
	 *
	 * @since 1.1.0
	 *
	 * @param string $rate Tax name.
	 */
	public function set_rate( $rate ) {
		$this->set_prop( 'rate', doubleval( $rate ) );
	}

	/**
	 * Set tax type.
	 *
	 * @since 1.1.0
	 *
	 * @param string $type Tax name.
	 */
	public function set_type( $type ) {
		$tax_types = eaccounting_get_tax_types();
		if ( array_key_exists( $type, $tax_types ) ) {
			$this->set_prop( 'type', eaccounting_clean( $type ) );
		}
	}
}
