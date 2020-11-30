<?php
/**
 * Handle the transaction object.
 *
 * @package     EverAccounting\Models
 * @class       TransactionModel
 * @version     1.0.2
 */

namespace EverAccounting\Abstracts;


use EverAccounting\Core\Exception;
use EverAccounting\Core\Repositories;
use EverAccounting\Models\Account;
use EverAccounting\Models\Category;
use EverAccounting\Models\Currency;
use EverAccounting\Models\Customer;
use EverAccounting\Models\Vendor;

defined( 'ABSPATH' ) || exit;

/**
 * Class TransactionModel
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Abstracts
 */
abstract class TransactionModel extends ResourceModel {
	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'transaction';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	public $cache_group = 'eaccounting_transaction';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $data = array(
		'type'           => '',
		'paid_at'        => null,
		'amount'         => 0.00,
		'currency_code'  => '', // protected
		'currency_rate'  => 0.00, // protected
		'account_id'     => null,
		'invoice_id'     => null,
		'contact_id'     => null,
		'category_id'    => null,
		'description'    => '',
		'payment_method' => '',
		'reference'      => '',
		'attachment'     => null,
		'parent_id'      => null,
		'reconciled'     => 0,
		'creator_id'     => null,
		'date_created'   => null,
	);

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
	 * @return string
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
	 * Get attachment url.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.0.2
	 *
	 */
	public function get_attachment( $context = 'edit' ) {
		return $this->get_prop( 'attachment', $context );
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
	|
	| Functions for setting item data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
	*/

	/**
	 * Set contact's email.
	 *
	 * @param string $value Email.
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_type( $value ) {
		if ( array_key_exists( $value, eaccounting_get_transaction_types() ) ) {
			$this->set_prop( 'type', $value );
		}
	}

	/**
	 * Set transaction paid.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 *
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
	 *
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
	 *
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
	 *
	 */
	public function set_currency_rate( $value ) {
		$this->set_prop( 'currency_rate', (float) $value );
	}

	/**
	 * Set account id.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 *
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
	 *
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
	 *
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
	 *
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
	 *
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
	 *
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
	 *
	 */
	public function set_reference( $value ) {
		$this->set_prop( 'reference', eaccounting_clean( $value ) );
	}

	/**
	 * Set attachment.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 *
	 */
	public function set_attachment( $value ) {
		if ( ! empty( $value ) ) {
			$value = esc_url_raw( $value );
		}
		$this->set_prop( 'attachment', $value );
	}

	/**
	 * Set parent id.
	 *
	 * @param $value
	 *
	 * @since 1.0.2
	 *
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
	 *
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
	 * Get currency object.
	 *
	 * @return array|null
	 * @since 1.1.0
	 */
	public function get_currency() {
		$currency = new Currency( $this->get_currency_code() );

		return $currency->exists() ? $currency->get_data() : null;
	}

	/**
	 * Get account object.
	 *
	 * @return array|null
	 * @since 1.1.0
	 */
	public function get_account() {
		$account = new Account( $this->get_account_id() );

		return $account->exists() ? $account->get_data() : null;
	}

	/**
	 * Get category object.
	 *
	 * @return array|null
	 * @since 1.1.0
	 */
	public function get_category() {
		$category = new Category( $this->get_category_id() );

		return $category->exists() ? $category->get_data() : null;
	}

	/**
	 * Get category object.
	 *
	 * @return array|null
	 * @since 1.1.0
	 */
	public function get_customer() {
		$customer = new Customer( $this->get_contact_id() );

		return $customer->exists() ? $customer->get_data() : null;
	}

	/**
	 * Get category object.
	 *
	 * @return array|null
	 * @since 1.1.0
	 */
	public function get_vendor() {
		$vendor = new Vendor( $this->get_contact_id() );

		return $vendor->exists() ? $vendor->get_data() : null;
	}

	/**
	 * Get formatted transaction amount.
	 *
	 * @return string
	 * @since 1.0.2
	 */
	public function get_formatted_amount() {
		return eaccounting_format_price( $this->get_amount(), $this->get_currency_code() );
	}

	/**
	 * Save should create or update based on object existence.
	 *
	 * @since  1.1.0
	 * @throws Exception
	 * @return \Exception|bool
	 */
	public function save() {
		$account = eaccounting_get_account( $this->get_account_id() );
		if ( ! empty( $this->get_account_id() ) && $account ) {
			$this->set_currency_code( $account->get_currency_code() );
			$currency = eaccounting_get_currency( $account->get_currency_code() );
			if ( $currency->exists() && empty( $this->get_currency_rate() ) ) {
				$this->set_currency_rate( $currency->get_rate() );
			}
			if ( $currency->exists() && empty( $this->get_currency_code() ) ) {
				$this->set_currency_code( $currency->get_code() );
			}

			$this->set_amount( eaccounting_sanitize_price( $this->get_amount(), $this->get_currency_code() ) );
		}

		return parent::save();
	}
}
