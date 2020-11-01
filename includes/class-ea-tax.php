<?php
/**
 * Handle the tax rate object.
 *
 * @since       1.1.0
 *
 * @package     EverAccounting
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Base_Object;

defined( 'ABSPATH' ) || exit();

class Tax extends Base_Object {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $object_type = 'tax';

	/***
	 * Object table name.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $table = 'ea_taxes';

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

	/**
	 * Get the tax if ID is passed, otherwise the tax is new and empty.
	 * This class should NOT be instantiated, but the eaccounting_get_tax function
	 * should be used. It is possible, but the aforementioned are preferred and are the only
	 * methods that will be maintained going forward.
	 *
	 * @since 1.1.0
	 *
	 * @param int|object|Tax $data object to read.
	 *
	 * @throws \EverAccounting\Exception
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		if ( is_numeric( $data ) && $data > 0 ) {
			$this->set_id( $data );
		} elseif ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} else {
			$this->set_id( 0 );
		}

		if ( $this->get_id() > 0 && ! $this->object_read ) {
			$this->read();
		}
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
	 *
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
	 *
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
	 *
	 */
	public function set_type( $type ) {
		$tax_types = eaccounting_get_tax_types();
		if ( array_key_exists( $type, $tax_types ) ) {
			$this->set_prop( 'type', eaccounting_clean( $type ) );
		}
	}
}
