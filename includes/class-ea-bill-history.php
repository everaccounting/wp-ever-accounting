<?php
/**
 * Handle the bill history object.
 *
 * @since       1.1.0
 *
 * @package     EverAccounting
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Base_Object;

defined( 'ABSPATH' ) || exit();

/**
 * Class Bill History
 *
 * @since 1.1.0
 */
class Bill_History extends Base_Object {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $object_type = 'bill_history';

	/***
	 * Object table name.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $table = 'ea_bill_histories';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $data = array(
		'bill_id'   => '',
		'status'       => null,
		'notify'       => '',
		'description'  => '',
		'date_created' => '',
	);

	/**
	 * Get the bill history if ID is passed, otherwise the bill history  is new and empty.
	 * This class should NOT be instantiated, but the eaccounting_get_bill_history function
	 * should be used. It is possible, but the aforementioned are preferred and are the only
	 * methods that will be maintained going forward.
	 *
	 * @param int|object|Category $data object to read.
	 *
	 * @since 1.1.0
	 *
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
	 * Get bill id
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_bill_id( $context = 'edit' ) {
		return $this->get_prop( 'bill_id', $context );
	}

	/**
	 * Get status
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_status( $context = 'edit' ) {
		return $this->get_prop( 'status', $context );
	}

	/**
	 * Get notify
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_notify( $context = 'edit' ) {
		return $this->get_prop( 'notify', $context );
	}

	/**
	 * Get description
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
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
	 *  Set bill_id
	 *
	 * @param $bill_id
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_bill_id( $bill_id ) {
		$this->set_prop( 'bill_id', absint( $bill_id ) );
	}

	/**
	 *  Set status
	 *
	 * @param $status
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_status( $status ) {
		$this->set_prop( 'status', eaccounting_clean( $status ) );
	}

	/**
	 *  Set notify
	 *
	 * @param $notify
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_notify( $notify ) {
		$this->set_prop( 'notify', absint( $notify ) );
	}

	/**
	 *  Set description
	 *
	 * @param $description
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_description( $description ) {
		$this->set_prop( 'description', sanitize_textarea_field( $description ) );
	}


}
