<?php
/**
 * Handle the transaction object.
 *
 * @package     EverAccounting
 * @class       Transaction
 * @version     1.0.2
 */

namespace EverAccounting;

use EverAccounting\DateTime;
use EverAccounting\Abstracts\Base_Object;

defined( 'ABSPATH' ) || exit();

/**
 * Class Transaction
 *
 * @since   1.0.2
 * @package EverAccounting
 */
class Transaction extends Base_Object {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $object_type = 'transaction';

	/***
	 * Object table name.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $table = 'ea_transactions';

	/**
	 * Transaction Data array.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	protected $data = array(
		'type'           => '',
		'paid_at'        => null,
		'amount'         => null,
		'currency_code'  => '', //protected
		'currency_rate'  => 1, //protected
		'account_id'     => null,
		'invoice_id'     => null,
		'contact_id'     => null,
		'category_id'    => null,
		'description'    => '',
		'payment_method' => '',
		'reference'      => '',
		'parent_id'      => 0,
		'reconciled'     => 0,
		'creator_id'     => '',
		'date_created'   => '',
	);

	/**
	 * Get the transaction if ID is passed, otherwise the transaction is new and empty.
	 * This class should NOT be instantiated, but the eaccounting_get_transaction function
	 * should be used. It is possible, but the aforementioned are preferred and are the only
	 * methods that will be maintained going forward.
	 *
	 * @param int|object|Category $data object to read.
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
	 * Transaction type.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_type( $context = 'edit' ) {
		return $this->get_prop( 'type', $context );
	}

	/**
	 * Paid at time.
	 *
	 * @param string $context
	 *
	 * @return DateTime
	 * @since 1.0.2
	 *
	 */
	public function get_paid_at( $context = 'edit' ) {
		return $this->get_prop( 'paid_at', $context );
	}

	/**
	 * Transaction Amount.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_amount( $context = 'edit' ) {
		return $this->get_prop( 'amount', $context );
	}

	/**
	 * Currency code.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_currency_code( $context = 'edit' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/**
	 * Currency rate.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_currency_rate( $context = 'edit' ) {
		return $this->get_prop( 'currency_rate', $context );
	}

	/**
	 * Transaction from account id.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_account_id( $context = 'edit' ) {
		return $this->get_prop( 'account_id', $context );
	}

	/**
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_invoice_id( $context = 'edit' ) {
		return $this->get_prop( 'invoice_id', $context );
	}

	/**
	 * Contact id.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_contact_id( $context = 'edit' ) {
		return $this->get_prop( 'contact_id', $context );
	}

	/**
	 * Category ID.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_category_id( $context = 'edit' ) {
		return $this->get_prop( 'category_id', $context );
	}

	/**
	 * Description.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_description( $context = 'edit' ) {
		return $this->get_prop( 'description', $context );
	}

	/**
	 * Transaction payment methods.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_payment_method( $context = 'edit' ) {
		return $this->get_prop( 'payment_method', $context );
	}

	/**
	 * Transaction reference.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_reference( $context = 'edit' ) {
		return $this->get_prop( 'reference', $context );
	}

	/**
	 * Get associated parent payment id.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_parent_id( $context = 'edit' ) {
		return $this->get_prop( 'parent_id', $context );
	}

	/**
	 * Get if reconciled
	 *
	 * @param string $context
	 *
	 * @return bool
	 * @since 1.0.2
	 *
	 */
	public function get_reconciled( $context = 'edit' ) {
		return (bool) $this->get_prop( 'reconciled', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set contact's email.
	 *
	 * @param string $value Email.
	 *
	 * @since 1.0.2
	 */
	public function set_type( $value ) {
		$this->set_prop( 'type', $value );
	}

	/**
	 * Set transaction paid.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_paid_at( $value ) {
		$this->set_date_prop( 'paid_at', $value );
	}

	/**
	 * Set transaction amount.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_amount( $value ) {
		$this->set_prop( 'amount', eaccounting_sanitize_price( $value ) );
	}

	/**
	 * Set currency code.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_currency_code( $value ) {
		$this->set_prop( 'currency_code', eaccounting_clean( $value ) );
	}

	/**
	 * Set currency rate.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_currency_rate( $value ) {
		$this->set_prop( 'currency_rate', (double) $value );
	}

	/**
	 * Set account id.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_account_id( $value ) {
		$this->set_prop( 'account_id', absint( $value ) );
	}

	/**
	 * Set invoice id.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_invoice_id( $value ) {
		$this->set_prop( 'invoice_id', absint( $value ) );
	}

	/**
	 * Set contact id.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_contact_id( $value ) {
		$this->set_prop( 'contact_id', absint( $value ) );
	}

	/**
	 * Set category id.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_category_id( $value ) {
		$this->set_prop( 'category_id', absint( $value ) );
	}

	/**
	 * Set description.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_description( $value ) {
		$this->set_prop( 'description', eaccounting_clean( $value ) );
	}

	/**
	 * Set payment method.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_payment_method( $value ) {
		if ( array_key_exists( $value, eaccounting_get_payment_methods() ) ) {
			$this->set_prop( 'payment_method', $value );
		}
	}

	/**
	 * Set reference.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_reference( $value ) {
		$this->set_prop( 'reference', eaccounting_clean( $value ) );
	}

	public function set_file_id( $value ) {
		$this->set_prop( 'file_id', absint( $value ) );
	}

	/**
	 * Set parent id.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_parent_id( $value ) {
		$this->set_prop( 'parent_id', absint( $value ) );
	}

	/**
	 * Set if reconciled.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 */
	public function set_reconciled( $value ) {
		$this->set_prop( 'reconciled', absint( $value ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Extra
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get formatted transaction amount.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_formatted_amount() {
		return eaccounting_format_price( $this->get_amount(), $this->get_currency_code() );
	}
}
