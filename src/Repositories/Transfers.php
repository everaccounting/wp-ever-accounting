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

use EverAccounting\Models\Transfer;
use EverAccounting\Abstracts\ResourceRepository;

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
	 * @var string
	 */
	const TABLE = 'ea_transfers';

	/**
	 * Accounts constructor.
	 */
	public function __construct() {
		global $wpdb;
		$this->table       = $wpdb->prefix . self::TABLE;
		$this->table_name  = self::TABLE;
		$this->primary_key = 'id';
		$this->object_type = 'transfer';
	}

	/**
	 * Retrieves the list of columns for the database table.
	 *
	 * Sub-classes should define an array of columns here.
	 *
	 * @since 1.1.0
	 * @return array List of columns.
	 */
	public static function get_columns() {
		return array(
			'income_id'    => self::BIGINT,
			'expense_id'   => self::BIGINT,
			'creator_id'   => self::BIGINT,
			'date_created' => self::DATETIME,
		);
	}

	/**
	 * Retrieves column defaults.
	 *
	 * Sub-classes can define default for any/all of columns defined in the get_columns() method.
	 *
	 * @since 1.1.0
	 * @return array All defined column defaults.
	 */
	public static function get_defaults() {
		return array(
			'date'            => null,
			'from_account_id' => null,
			'amount'          => null,
			'to_account_id'   => null,
			'income_id'       => null,
			'expense_id'      => null,
			'payment_method'  => null,
			'reference'       => null,
			'description'     => null,
			'creator_id'      => eaccounting_get_current_user_id(),
			'date_created'    => current_time( 'mysql' ),
		);
	}


	/**
	 * @since 1.1.0
	 *
	 * @param int   $id
	 * @param array $data
	 *
	 * @return array|\WP_Error
	 */
	protected function normalize_resource_data( $data, $id = null ) {
		$data = parent::normalize_resource_data( $data, $id );

		if ( ! empty( $data->errors ) ) {
			return $data;
		}

		$data         = wp_parse_args(
			$data,
			array(
				'income_id'  => null,
				'expense_id' => null,
			)
		);
		$from_account = eaccounting_get_account( $data['from_account_id'] );
		$to_account   = eaccounting_get_account( $data['to_account_id'] );
		$amount       = eaccounting_sanitize_price( $data['amount'], $from_account->get_currency_code() );
		$category_id  = Categories::instance()->get_var(
			'id',
			array(
				'name' => __( 'Transfer', 'wp-ever-accounting' ),
				'type' => 'other',
			)
		);

		$expense = eaccounting_insert_payment(
			array(
				'id'             => $data['expense_id'],
				'account_id'     => $from_account->get_id(),
				'paid_at'        => $data['date'],
				'amount'         => $amount,
				'vendor_id'      => 0,
				'description'    => $data['description'],
				'category_id'    => $category_id,
				'payment_method' => $data['payment_method'],
				'reference'      => $data['reference'],
			)
		);

		if ( is_wp_error( $expense ) ) {
			return $expense;
		}

		$data['expense_id'] = $expense->get_id();

		if ( $from_account->get_currency_code() !== $to_account->get_currency_code() ) {
			$expense_currency = eaccounting_get_currency( $from_account->get_currency_code() );
			$income_currency  = eaccounting_get_currency( $to_account->get_currency_code() );
			$amount           = eaccounting_price_convert_to_default( $amount, $from_account->get_currency_code(), $expense_currency->get_rate() );
			$amount           = eaccounting_price_convert_from_default( $amount, $to_account->get_currency_code(), $income_currency->get_rate() );
		}

		$income = eaccounting_insert_revenue(
			array(
				'id'             => $data['income_id'],
				'account_id'     => $to_account->get_id(),
				'paid_at'        => $data['date'],
				'amount'         => $amount,
				'vendor_id'      => 0,
				'description'    => $data['description'],
				'category_id'    => $category_id,
				'payment_method' => $data['payment_method'],
				'reference'      => $data['reference'],
			)
		);

		if ( is_wp_error( $income ) ) {
			return $income;
		}

		$data['income_id'] = $income->get_id();

		return $data;
	}

}
