<?php
/**
 * Handle the transfer object.
 *
 * @package     EverAccounting\Models
 * @class       Account
 * @version     1.0.2
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Repositories\Transfers;

defined( 'ABSPATH' ) || exit;

/**
 * Class Transfer
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Transfer extends ResourceModel {
	/**
	 * Get an instance of Currency object.
	 *
	 * @since 1.0.2
	 *
	 * @param int|object|array|Transfer $data object to read.
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data, Transfers::instance() );

		if ( $this->get_id() > 0 && ! $this->get_object_read() ) {
			$transfer = Transfers::instance()->get( $this->get_id() );
			if ( $transfer ) {
				$this->set_props( $transfer->get_data() );
				$this->set_object_read( $transfer->exists() );
			} else {
				$this->set_id( 0 );
			}
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
	 * @return \EverAccounting\Core\DateTime
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
