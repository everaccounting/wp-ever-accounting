<?php
/**
 * Handle the Expense object.
 *
 * @package     EverAccounting\Models
 * @class       Payment
 * @version     1.0.2
 */

namespace EverAccounting\Models;


use EverAccounting\Abstracts\ResourceModel;

use EverAccounting\Core\Repositories;
use EverAccounting\Traits\CurrencyTrait;

defined( 'ABSPATH' ) || exit;

/**
 * Class Payment
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Payment extends ResourceModel {
	use CurrencyTrait;

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'payments';

	/**
	 * @since 1.1.0
	 *
	 * @var string
	 */
	public $cache_group = 'ea_payments';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $data = array(
		'type'           => 'expense',
		'payment_date'   => null,
		'amount'         => 0.00,
		'currency_code'  => '', // protected
		'currency_rate'  => 0.00, // protected
		'account_id'     => null,
		'document_id'    => null,
		'contact_id'     => null,
		'category_id'    => null,
		'description'    => '',
		'payment_method' => '',
		'reference'      => '',
		'attachment_id'  => null,
		'parent_id'      => 0,
		'reconciled'     => 0,
		'creator_id'     => null,
		'date_created'   => null,
	);


	/**
	 * Get the account if ID is passed, otherwise the account is new and empty.
	 *
	 * @since 1.1.0
	 *
	 * @param int|object|Account $data object to read.
	 *
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		if ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( is_numeric( $data ) ) {
			$this->set_id( $data );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} elseif ( is_array( $data ) ) {
			$this->set_props( $data );
		} else {
			$this->set_object_read( true );
		}

		//Load repository
		$this->repository = Repositories::load( 'payments' );

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}

		// If not expense then reset to default
		if ( 'expense' !== $this->get_type() ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}

		$this->required_props = array(
			'type'           => __( 'Type', 'wp-ever-accounting' ),
			'payment_date'   => __( 'Payment Date', 'wp-ever-accounting' ),
			'account_id'     => __( 'Account ID', 'wp-ever-accounting' ),
			'category_id'    => __( 'Category ID', 'wp-ever-accounting' ),
			'payment_method' => __( 'Payment Method', 'wp-ever-accounting' ),
		);
	}
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
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_type( $context = 'edit' ) {
		return $this->get_prop( 'type', $context );
	}

	/**
	 * Paid at time.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function get_payment_date( $context = 'edit' ) {
		$payment_date = $this->get_prop( 'payment_date', $context );

		return $payment_date ? eaccounting_format_datetime( $payment_date, 'Y-m-d' ) : $payment_date;
	}

	/**
	 * Transaction Amount.
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
	 * Get formatted amount.
	 *
	 * @since 1.0.2
	 *
	 * @return string
	 */
	public function get_formatted_amount() {
		return eaccounting_format_price( $this->get_amount(), $this->get_currency_code() );
	}

	/**
	 * Currency code.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_currency_code( $context = 'edit' ) {
		return $this->get_prop( 'currency_code', $context );
	}

	/**
	 * Currency rate.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_currency_rate( $context = 'edit' ) {
		return $this->get_prop( 'currency_rate', $context );
	}

	/**
	 * Transaction from account id.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_account_id( $context = 'edit' ) {
		return $this->get_prop( 'account_id', $context );
	}

	/**
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_document_id( $context = 'edit' ) {
		return $this->get_prop( 'document_id', $context );
	}

	/**
	 * Contact id.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_contact_id( $context = 'edit' ) {
		return $this->get_prop( 'contact_id', $context );
	}

	/**
	 * Contact id.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_vendor_id( $context = 'edit' ) {
		return $this->get_contact_id( $context );
	}

	/**
	 * Category ID.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_category_id( $context = 'edit' ) {
		return $this->get_prop( 'category_id', $context );
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
	 * Transaction reference.
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
	 * Get attachment url.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_attachment_id( $context = 'edit' ) {
		return $this->get_prop( 'attachment_id', $context );
	}

	/**
	 * Get associated parent payment id.
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 */
	public function get_parent_id( $context = 'edit' ) {
		return $this->get_prop( 'parent_id', $context );
	}

	/**
	 * Get if reconciled
	 *
	 * @since 1.0.2
	 *
	 * @param string $context
	 *
	 * @return bool
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
	 * @since 1.0.2
	 *
	 * @param string $value Email.
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
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_payment_date( $value ) {
		$this->set_date_prop( 'payment_date', $value );
	}

	/**
	 * Set transaction amount.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_amount( $value ) {
		$this->set_prop( 'amount', (float) eaccounting_sanitize_number( $value, true ) );
	}

	/**
	 * Set currency code.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_currency_code( $value ) {
		$this->set_prop( 'currency_code', eaccounting_clean( $value ) );
	}

	/**
	 * Set currency rate.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_currency_rate( $value ) {
		$this->set_prop( 'currency_rate', (float) $value );
	}

	/**
	 * Set account id.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_account_id( $value ) {
		$this->set_prop( 'account_id', absint( $value ) );
	}

	/**
	 * Set invoice id.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_document_id( $value ) {
		$this->set_prop( 'document_id', absint( $value ) );
	}

	/**
	 * Set contact id.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_contact_id( $value ) {
		$this->set_prop( 'contact_id', absint( $value ) );
	}
	/**
	 * Set contact id.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_vendor_id( $value ) {
		$this->set_contact_id($value);
	}

	/**
	 * Set category id.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_category_id( $value ) {
		$this->set_prop( 'category_id', absint( $value ) );
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

	/**
	 * Set payment method.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
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
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_reference( $value ) {
		$this->set_prop( 'reference', eaccounting_clean( $value ) );
	}

	/**
	 * Set attachment.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_attachment_id( $value ) {
		$this->set_prop( 'attachment_id', intval( $value ) );
	}

	/**
	 * Set parent id.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_parent_id( $value ) {
		$this->set_prop( 'parent_id', absint( $value ) );
	}

	/**
	 * Set if reconciled.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_reconciled( $value ) {
		$this->set_prop( 'reconciled', absint( $value ) );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete discounts from the database.
	|
	*/
	/**
	 * Save should create or update based on object existence.
	 *
	 * @since  1.1.0
	 *
	 * @throws \Exception
	 *
	 * @return bool
	 */
	public function save() {
		//If account id is changing need to update currency.
		if ( array_key_exists( 'account_id', $this->get_changes() ) || ! $this->exists() ) {
			$account = new Account( $this->get_account_id() );
			$this->set_currency_code( $account->get_currency_code() );
		}

		if ( array_key_exists( 'currency_code', $this->get_changes() ) || ! $this->exists() ) {
			$currency = new Currency( $this->get_currency_code() );
			$this->set_currency_rate( $currency->get_rate() );
		}

		//saving same
		return parent::save();
	}
}
