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
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'tax';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	public $cache_group = 'eaccounting_tax';

	/**
	 * Tax Data array.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $data = array(
		'name'         => '',
		'rate'         => 0.0000,
		'type'         => '',
		'enabled'      => 1,
		'date_created' => null,
	);

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete discounts from the database.
	|
	*/

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Functions for getting item data. Getter methods wont change anything unless
	| just returning from the props.
	|
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
	|
	| Functions for setting item data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
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
	/*
	|--------------------------------------------------------------------------
	| Additional methods
	|--------------------------------------------------------------------------
	|
	| Does extra thing as helper functions.
	|
	*/

	/*
	|--------------------------------------------------------------------------
	| Conditionals
	|--------------------------------------------------------------------------
	|
	| Checks if a condition is true or false.
	|
	*/
}
