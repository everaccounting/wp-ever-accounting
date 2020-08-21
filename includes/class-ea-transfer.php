<?php
/**
 * Handle the Transfer object.
 *
 * @package     EverAccounting
 * @class       Transfer
 * @version     1.0.2
 */

namespace EverAccounting;

use EAccounting\DateTime;
use EverAccounting\Abstracts\Base_Object;

defined( 'ABSPATH' ) || exit();

class Transfer extends Base_Object {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $object_type = 'transfer';

	/***
	 * Object table name.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $table = 'ea_transfers';

	/**
	 * Transfers Data array.
	 *
	 * @since 1.0.2
	 * @var array
	 */
	protected $data = array(
		'income_id'    => '',
		'expense_id'   => '',
		'creator_id'   => '',
		'company_id'   => 1,
		'date_created' => '',
	);

	/**
	 * Extra data.
	 *
	 * @since 1.0.2
	 * @var null[]
	 */
	protected $extra_data = array(
		'from_account_id' => null,
		'to_account_id'   => null,
		'date'            => null,
		'amount'          => null,
		'payment_method'  => null,
		'reference'       => null,
		'description'     => null,
		'income'          => null,
		'expense'         => null,
	);

	/**
	 * @since 1.0.2
	 * @var \EverAccounting\Transaction
	 */
	protected $income;

	/**
	 * @since 1.0.2
	 * @var \EverAccounting\Transaction
	 */
	protected $expense;

	/**
	 * Get the transfer if ID is passed, otherwise the transfer is new and empty.
	 * This class should NOT be instantiated, but the eaccounting_get_transfer function
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
	 * Income ID.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_income_id( $context = 'edit' ) {
		return $this->get_prop( 'income_id', $context );
	}

	/**
	 * Expense ID.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_expense_id( $context = 'edit' ) {
		return $this->get_prop( 'expense_id', $context );
	}

	/**
	 * Transaction payment methods.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_from_account_id( $context = 'edit' ) {
		return $this->get_prop( 'from_account_id', $context );
	}


	/**
	 * Transaction payment methods.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_to_account_id( $context = 'edit' ) {
		return $this->get_prop( 'to_account_id', $context );
	}

	/**
	 * Transaction payment methods.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_amount( $context = 'edit' ) {
		return $this->get_prop( 'amount', $context );
	}

	/**
	 * Transaction payment methods.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return DateTime
	 */
	public function get_date( $context = 'edit' ) {
		return $this->get_prop( 'date', $context );
	}

	/**
	 * Transaction payment methods.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_payment_method( $context = 'edit' ) {
		return $this->get_prop( 'payment_method', $context );
	}

	/**
	 * Description.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_reference( $context = 'edit' ) {
		return $this->get_prop( 'reference', $context );
	}

	/**
	 * Description.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_description( $context = 'edit' ) {
		return $this->get_prop( 'description', $context );
	}

	/**
	 * get currency_code.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_currency_code( $context = 'edit' ) {
		return $this->get_prop( 'currency_code', $context );
	}


	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set income id.
	 *
	 * @since 1.0.2
	 *
	 * @param int $value income_id.
	 *
	 */
	public function set_income_id( $value ) {
		$this->set_prop( 'income_id', absint( $value ) );
	}

	/**
	 * Set expense id.
	 *
	 * @since 1.0.2
	 *
	 * @param int $value expense_id.
	 *
	 */
	public function set_expense_id( $value ) {
		$this->set_prop( 'expense_id', absint( $value ) );
	}

	/**
	 * Set from account id.
	 *
	 * @since 1.0.2
	 *
	 * @param $account_id
	 */
	public function set_from_account_id( $account_id ) {
		$this->set_prop( 'from_account_id', absint( $account_id ) );
	}

	/**
	 * Set to account id.
	 *
	 * @since 1.0.2
	 *
	 * @param int $account_id
	 */
	public function set_to_account_id( $account_id ) {
		$this->set_prop( 'to_account_id', absint( $account_id ) );
	}

	/**
	 * Set date.
	 *
	 * @since 1.0.2
	 *
	 * @param string $date
	 */
	public function set_date( $date ) {
		$this->set_date_prop( 'date', eaccounting_clean( $date ) );
	}

	/**
	 * Set amount.
	 *
	 * @since 1.0.2
	 *
	 * @param string $amount
	 */
	public function set_amount( $amount ) {
		$this->set_prop( 'amount', eaccounting_sanitize_price( $amount ) );
	}

	/**
	 * Set payment method.
	 *
	 * @since 1.0.2
	 *
	 * @param string $payment_method
	 */
	public function set_payment_method( $payment_method ) {
		$this->set_prop( 'payment_method', eaccounting_clean( $payment_method ) );
	}

	/**
	 * Set reference.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_reference( $value ) {
		$this->set_prop( 'reference', eaccounting_clean( $value ) );
	}

	/**
	 * Set description.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_description( $value ) {
		$this->set_prop( 'description', eaccounting_clean( $value ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Overwrite
	|--------------------------------------------------------------------------
	*/

	/**
	 * Populate extra data.
	 *
	 * @since 1.0.2
	 */
	public function populate_extra_data() {
		if ( ! empty( $this->get_expense_id() ) && $expense = eaccounting_get_transaction( $this->get_expense_id() ) ) {
			$this->set_from_account_id( $expense->get_account_id() );
			$this->set_amount( $expense->get_amount() );
			$this->set_date( $expense->get_paid_at()->date_mysql() );
			$this->set_payment_method( $expense->get_payment_method() );
			$this->set_reference( $expense->get_reference() );
			$this->set_description( $expense->get_description() );
			$this->set_prop( 'currency_code', $expense->get_currency_code() );
		}
		if ( ! empty( $this->get_income_id() ) && $income = eaccounting_get_transaction( $this->get_income_id() ) ) {
			$this->set_to_account_id( $income->get_account_id() );
		}
	}



	/**
	 * Delete extra data.
	 *
	 * @since 1.0.2
	 */
	public function delete_extra_data() {
		if ( ! empty( $this->get_income_id() ) ) {
			eaccounting_delete_transaction( $this->get_income_id() );
		}
		if ( ! empty( $this->get_expense_id() ) ) {
			eaccounting_delete_transaction( $this->get_expense_id() );
		}
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
