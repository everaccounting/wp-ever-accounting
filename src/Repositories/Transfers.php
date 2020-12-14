<?php
/**
 * Transfer repository.
 *
 * Handle transfer insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Repositories;

use EverAccounting\Abstracts\ResourceRepository;
use EverAccounting\Core\Exception;
use EverAccounting\Models\Expense;
use EverAccounting\Models\Income;
use EverAccounting\Models\Transfer;

defined( 'ABSPATH' ) || exit;

/**
 * Class Transfers
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class Transfers extends ResourceRepository {
	/**
	 * Table name.
	 *
	 * @var string
	 */
	const TABLE = 'ea_transfers';

	/**
	 * Table name.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $table = self::TABLE;

	/**
	 * A map of database fields to data types.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $data_type = array(
		'id'           => '%d',
		'income_id'    => '%d',
		'expense_id'   => '%d',
		'creator_id'   => '%d',
		'date_created' => '%s',
	);

	/*
	|--------------------------------------------------------------------------
	| CRUD Methods
	|--------------------------------------------------------------------------
	*/
	/**
	 * Method to create a new item in the database.
	 *
	 * @param Transfer $transfer Item object.
	 *
	 * @throws \EverAccounting\Core\Exception
	 */
	public function insert( &$transfer ) {
		global $wpdb;
		$from_account = eaccounting_get_account( $transfer->get_from_account_id() );
		$to_account   = eaccounting_get_account( $transfer->get_to_account_id() );
		$update       = $transfer->exists();
		if ( empty( $transfer->get_date() ) ) {
			throw new Exception( 'invalid_prop', __( 'Transfer date is required', 'wp-ever-accounting' ) );
		}
		if ( ! $from_account || ! $to_account ) {
			throw new Exception( 'invalid_account', __( 'From and to accounts are required', 'wp-ever-accounting' ) );
		}
		$amount = eaccounting_sanitize_price( $transfer->get_amount(), $from_account->get_currency_code() );
		if ( empty( $amount ) ) {
			throw new Exception( 'empty_prop', __( 'Transfer amount is required.', 'wp-ever-accounting' ) );
		}
		$cache_key   = md5( 'other' . __( 'Transfer', 'wp-ever-accounting' ) );
		$category_id = wp_cache_get( $cache_key, 'eaccounting_categories' );
		if ( false === $category_id ) {
			$category_id = $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_categories WHERE type=%s AND name=%s", 'other', __( 'Transfer', 'wp-ever-accounting' ) ) );
			wp_cache_add( $cache_key, $category_id, 'eaccounting_categories' );
		}
		if ( empty( $category_id ) ) {
			throw new Exception(
				'empty_prop',
				sprintf(
				/* translators: %s: category name %s: category type */
					__( 'Transfer category is missing please create a category named "%1$s" and type"%2$s".', 'wp-ever-accounting' ),
					__( 'Transfer', 'wp-ever-accounting' ),
					'other'
				)
			);
		}

		$expense_currency = eaccounting_get_currency( $from_account->get_currency_code() );
		$income_currency  = eaccounting_get_currency( $to_account->get_currency_code() );
		if ( empty( $expense_currency ) ) {
			throw new Exception( 'empty_prop', __( 'From account currency is unavailable .', 'wp-ever-accounting' ) );
		}
		if ( empty( $income_currency ) ) {
			throw new Exception( 'empty_prop', __( 'To account currency is unavailable .', 'wp-ever-accounting' ) );
		}

		$expense = new Expense( $transfer->get_expense_id() );
		$expense->set_props(
			array(
				'account_id'     => $from_account->get_id(),
				'paid_at'        => $transfer->get_date(),
				'currency_code'  => $from_account->get_currency_code(),
				'currency_rate'  => $expense_currency->get_rate(),
				'amount'         => $amount,
				'vendor_id'      => 0,
				'description'    => $transfer->get_description(),
				'category_id'    => $category_id,
				'payment_method' => $transfer->get_payment_method(),
				'reference'      => $transfer->get_reference(),
				'parent_id'      => 0,
				'creator_id'     => eaccounting_get_current_user_id(),
			)
		);
		$expense->save();
		if ( empty( $transfer->get_expense_id() ) ) {
			$expense->set_id( $wpdb->insert_id );
			$expense->apply_changes();
		}

		if ( $from_account->get_currency_code() !== $to_account->get_currency_code() ) {
			$expense_currency = eaccounting_get_currency( $from_account->get_currency_code() );
			$income_currency  = eaccounting_get_currency( $to_account->get_currency_code() );
			$amount           = eaccounting_price_convert_to_default( $amount, $from_account->get_currency_code(), $expense_currency->get_rate() );
			$amount           = eaccounting_price_convert_from_default( $amount, $to_account->get_currency_code(), $income_currency->get_rate() );
		}

		try {
			$income = new Income( $transfer->get_income_id() );
			$income->set_props(
				array(
					'account_id'     => $to_account->get_id(),
					'paid_at'        => $transfer->get_date(),
					'currency_code'  => $to_account->get_currency_code(),
					'currency_rate'  => $income_currency->get_rate(),
					'amount'         => $amount,
					'vendor_id'      => 0,
					'description'    => $transfer->get_description(),
					'category_id'    => $category_id,
					'payment_method' => $transfer->get_payment_method(),
					'reference'      => $transfer->get_reference(),
					'parent_id'      => 0,
					'creator_id'     => eaccounting_get_current_user_id(),
				)
			);
			$income->save();
			if ( empty( $transfer->get_income_id() ) ) {
				$income->set_id( $wpdb->insert_id );
				$income->apply_changes();
			}
		} catch ( Exception $e ) {
			$expense->delete();
			throw new Exception( $e->getCode(), $e->getMessage() );
		}

		try {
			if ( ! $update ) {
				if ( false === $wpdb->insert(
						$wpdb->prefix . self::TABLE,
						array(
							'income_id'    => $income->get_id(),
							'expense_id'   => $expense->get_id(),
							'creator_id'   => $transfer->get_creator_id(),
							'date_created' => $transfer->get_date_created(),
						),
						array(
							'%d',
							'%d',
							'%d',
							'%s',
						)
					) ) {
					throw new Exception( 'insert_error', __( 'Could not insert transfer', 'wp-ever-accounting' ) );
				}

				$transfer->set_id( $wpdb->insert_id );
			}

			if ( ! $transfer->exists() ) {
				throw new Exception( 'insert_error', __( 'Could not insert transfer', 'wp-ever-accounting' ) );
			}

			$transfer->set_income_id( $income->get_id() );
			$transfer->set_expense_id( $expense->get_id() );
			$transfer->apply_changes();
			$transfer->clear_cache();
			/**
			 * Let the 3rd party extension to hook when a transfer is created.
			 *
			 * @param array    $data     properties of transfer
			 * @param Transfer $transfer transfer object.
			 */
			do_action( 'eacccounting_insert_' . $transfer->get_object_type(), $transfer->get_data(), $transfer );

			return true;
		} catch ( Exception $e ) {
			$income->delete();
			$expense->delete();
			$transfer->set_defaults();
			throw new Exception( $e->getCode(), $e->getMessage() );
		}
	}


	/**
	 * Method to read a item from the database.
	 *
	 * @param Transfer $item Item object.
	 *
	 * @throws \EverAccounting\Core\Exception
	 */
	public function read( &$item ) {
		global $wpdb;
		$table = $wpdb->prefix . $this->table;

		$item->set_defaults();

		if ( ! $item->get_id() ) {
			$item->set_id( 0 );
			return;
		}

		// Maybe retrieve from the cache.
		$raw_item = wp_cache_get( $item->get_id(), $item->get_cache_group() );
		// If not found, retrieve from the db.
		if ( false === $raw_item ) {
			$raw_item = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT * FROM {$table} WHERE id = %d",
					$item->get_id()
				)
			);

			// Update the cache with our data
			wp_cache_set( $item->get_id(), $raw_item, $item->get_cache_group() );
		}

		if ( ! $raw_item ) {
			$item->set_id( 0 );
			return;
		}

		foreach ( array_keys( $this->data_type ) as $key ) {
			$method = "set_$key";
			$item->$method( $raw_item->$key );
		}

		try {
			$income = new Income( $item->get_income_id() );
			if ( ! $income->exists() ) {
				throw new Exception( 'corrupted_data', __( 'Transfer data corrupted', 'wp-ever-accounting' ) );
			}
			$expense = new Expense( $item->get_expense_id() );
			if ( ! $expense->exists() ) {
				throw new Exception( 'corrupted_data', __( 'Transfer data corrupted', 'wp-ever-accounting' ) );
			}
			$item->set_from_account_id( $expense->get_account_id() );
			$item->set_to_account_id( $income->get_account_id() );
			$item->set_amount( $expense->get_amount() );
			$item->set_date( $expense->get_payment_date() );
			$item->set_payment_method( $expense->get_payment_method() );
			$item->set_description( $expense->get_description() );
			$item->set_reference( $expense->get_reference() );

			$item->set_object_read( true );
			do_action( 'eaccounting_read_' . $item->get_object_type(), $item );
		} catch ( Exception $e ) {
			$item->delete();
			throw new Exception( $e->getCode(), $e->getMessage() );
		}

	}


	/**
	 * Method to update an item in the database.
	 *
	 * @param Transfer $item Subscription object.
	 *
	 * @throws \EverAccounting\Core\Exception
	 */
	public function update( &$item ) {
		return $this->insert( $item );
	}

	/**
	 * Method to delete a subscription from the database.
	 *
	 * @param Transfer $item
	 * @param array    $args Array of args to pass to the delete method.
	 */
	public function delete( &$item, $args = array() ) {
		global $wpdb;
		$wpdb->delete( $wpdb->prefix . 'ea_transactions', array( 'id' => $item->get_income_id() ) );
		$wpdb->delete( $wpdb->prefix . 'ea_transactions', array( 'id' => $item->get_expense_id() ) );

		parent::delete( $item, $args = array() );
	}
}
