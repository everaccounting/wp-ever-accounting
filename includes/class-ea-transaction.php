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
		'attachment'     => '',
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
	 * @return DateTime
	 */
	public function get_paid_at( $context = 'edit' ) {
		return $this->get_prop( 'paid_at', $context );
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
	public function get_invoice_id( $context = 'edit' ) {
		return $this->get_prop( 'invoice_id', $context );
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
	public function get_attachment( $context = 'edit' ) {
		return $this->get_prop( 'attachment', $context );
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
		$this->set_prop( 'type', $value );
	}

	/**
	 * Set transaction paid.
	 *
	 * @since 1.0.2
	 *
	 * @param $value
	 *
	 */
	public function set_paid_at( $value ) {
		$this->set_date_prop( 'paid_at', $value );
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
		$this->set_prop( 'amount', eaccounting_sanitize_price( $value ) );
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
		$this->set_prop( 'currency_rate', (double) $value );
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
	public function set_invoice_id( $value ) {
		$this->set_prop( 'invoice_id', absint( $value ) );
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
	| Crud
	|--------------------------------------------------------------------------
	*/
	/**
	 * Method to create a new record of an EverAccounting object.
	 *
	 * @since 1.0.2
	 * @throws Exception
	 */
	public function create() {
		global $wpdb;

		do_action( 'eaccounting_pre_insert_' . $this->object_type, $this->get_id(), $this );

		$data = wp_unslash( apply_filters( 'eaccounting_new_' . $this->object_type . '_data', $this->get_base_data() ) );
		if ( false === $wpdb->insert( $wpdb->prefix . $this->table, $data ) ) {
			throw new Exception( 'db_error', $wpdb->last_error );
		}

		do_action( 'eaccounting_insert_' . $this->object_type, $this->get_id(), $this );
		do_action( 'eaccounting_insert_transaction_' . $this->get_type( 'raw' ), $this->get_id(), $this );

		$this->set_id( $wpdb->insert_id );
		$this->save_extra_data( 'create' );
		$this->apply_changes();
		$this->set_object_read( true );
	}

	/**
	 * Updates a record in the database.
	 *
	 * @since 1.0.2
	 * @throws Exception
	 */
	public function update() {
		global $wpdb;
		$changes = $this->get_changes();

		foreach ( $changes as $prop => $value ) {
			if ( $value instanceof DateTime ) {
				$changes[ $prop ] = $value->date( 'Y-m-d H:i:s' );
			}
		}

		$changed_data = array_intersect_key( $changes, $this->data );

		if ( ! empty( $changed_data ) ) {
			do_action( 'eaccounting_pre_update_' . $this->object_type, $this->get_id(), $changed_data, $this );
			do_action( 'eaccounting_pre_update_transaction_' . $this->get_type( 'raw' ), $this->get_id(), $changed_data, $this );

			try {
				$wpdb->update( $wpdb->prefix . $this->table, $changed_data, array( 'id' => $this->get_id() ) );
			} catch ( Exception $e ) {
				throw new Exception( 'db_error', __( 'Could not update resource.', 'wp-ever-accounting' ) );
			}

			do_action( 'eaccounting_update_transaction_' . $this->get_type( 'raw' ), $this->get_id(), $changes, $this );
			$this->save_extra_data( 'update' );
			$this->apply_changes();
			$this->set_object_read( true );
			wp_cache_delete( $this->object_type . '-item-' . $this->get_id(), $this->object_type );
		}
	}

	/**
	 * Deletes a record from the database.
	 *
	 * @since 1.0.2
	 * @return bool result
	 */
	public function delete() {
		if ( $this->get_id() && $this->table ) {
			global $wpdb;
			do_action( 'eaccounting_pre_delete_' . $this->object_type, $this->get_id(), $this->get_data(), $this );
			do_action( 'eaccounting_pre_delete_transaction_' . $this->get_type( 'raw' ), $this->get_id(), $this->get_data(), $this );
			$wpdb->delete( $wpdb->prefix . $this->table, array( 'id' => $this->get_id() ) );
			do_action( 'eaccounting_delete_' . $this->object_type, $this->get_id(), $this->get_data(), $this );
			do_action( 'eaccounting_delete_transaction_' . $this->get_type( 'raw' ), $this->get_id(), $this->get_data(), $this );
			$this->delete_extra_data();
			$this->set_id( 0 );

			wp_cache_delete( $this->object_type . '-item-' . $this->get_id(), $this->object_type );

			return true;
		}

		return false;
	}

	/*
	|--------------------------------------------------------------------------
	| Extra
	|--------------------------------------------------------------------------
	*/

	/**
	 * Get formatted transaction amount.
	 *
	 * @since 1.0.2
	 * @return string
	 */
	public function get_formatted_amount() {
		return eaccounting_format_price( $this->get_amount(), $this->get_currency_code() );
	}

	/**
	 * Get transaction account name.
	 *
	 * @since 1.0.2
	 *
	 * @param string $default
	 *
	 * @return string
	 */
	public function get_account_name( $default = 'N/A' ) {
		if ( $this->get_account_id() && $account = eaccounting_get_account( $this->get_account_id() ) ) {
			return $account->get_name();
		}

		return $default;
	}

	/**
	 * Get transaction category name.
	 *
	 * @since 1.0.2
	 *
	 * @param string $default
	 *
	 * @return string
	 */
	public function get_category_name( $default = 'N/A' ) {
		if ( $this->get_category_id() && $category = eaccounting_get_category( $this->get_category_id() ) ) {
			return $category->get_name();
		}

		return $default;
	}

	/**
	 * Get transaction contact name.
	 *
	 * @since 1.0.2
	 *
	 * @param string $default
	 *
	 * @return string
	 */
	public function get_contact_name( $default = 'N/A' ) {
		if ( $this->get_contact_id() && $contact = eaccounting_get_contact( $this->get_contact_id() ) ) {
			return $contact->get_name();
		}

		return $default;
	}
}
