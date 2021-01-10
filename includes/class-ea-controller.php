<?php
/**
 * Controller various actions of the plugin.
 *
 * @package     EverAccounting
 * @subpackage  Classes
 * @version     1.1.0
 */

use EverAccounting\Models\Account;
use EverAccounting\Models\Category;

/**
 * Class EverAccounting_Controller
 * @since 1.1.0
 */
class EverAccounting_Controller {

	/**
	 * EverAccounting_Controller constructor.
	 */
	public function __construct() {
		//accounts
		add_action( 'eaccounting_pre_save_account', array( $this, 'validate_account_data' ), 10, 2 );
		add_action( 'eaccounting_delete_account', array( $this, 'delete_default_account' ) );
		add_action( 'eaccounting_delete_account', array( $this, 'update_transaction_account' ) );
		//customers

		//vendors

		//payments
		add_action( 'eaccounting_validate_payment_data', array( $this, 'validate_payment_data' ), 10, 2 );
		//revenues
		add_action( 'eaccounting_validate_revenue_data', array( $this, 'validate_revenue_data' ), 10, 2 );
		//transactions
		add_action( 'eaccounting_pre_save_transaction', array( $this, 'validate_transaction_data' ), 10, 2 );

		//category
		add_action( 'eaccounting_pre_save_category', array( $this, 'validate_category_data' ), 10, 2 );
		add_action( 'eaccounting_delete_category', array( $this, 'update_transaction_category' ) );
		//currency
		add_action( 'update_option_eaccounting_settings', array( $this, 'update_default_currency' ), 10, 2 );
		add_action( 'eaccounting_delete_currency', array( $this, 'delete_default_currency' ), 10, 2 );
		//bill
		add_action( 'eaccounting_delete_payment', array( $this, 'update_bill' ), 10, 2 );
		add_action( 'eaccounting_update_payment', array( $this, 'update_bill' ), 10, 2 );
	}

	/**
	 * Validate account data.
	 *
	 * @param array $data
	 * @param int $id
	 * @param Account $account
	 *
	 * @throws \Exception
	 * @since 1.1.0
	 *
	 */
	public static function validate_account_data( $data, $id ) {
		global $wpdb;
		if ( $id != (int) $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_accounts WHERE number='%s'", eaccounting_clean( $data['number'] ) ) ) ) { // @codingStandardsIgnoreLine
			throw new \Exception( __( 'Duplicate account.', 'wp-ever-accounting' ) );
		}

	}

	/**
	 * When an account is deleted check if
	 * default account need to be updated or not.
	 *
	 * @param $id
	 *
	 * @since 1.1.0
	 *
	 */
	public static function delete_default_account( $id ) {
		$default_account = eaccounting()->settings->get( 'default_account' );
		if ( intval( $default_account ) === intval( $id ) ) {
			eaccounting()->settings->set( array( array( 'default_account' => '' ) ), true );
		}
	}

	/**
	 * Delete account id from transactions.
	 *
	 * @param $id
	 *
	 * @return bool
	 *
	 * @since 1.0.2
	 *
	 */
	public static function update_transaction_account( $id ) {
		global $wpdb;
		$id = absint( $id );
		if ( empty( $id ) ) {
			return false;
		}

		return $wpdb->update( $wpdb->prefix . 'ea_transactions', array( 'account_id' => '' ), array( 'account_id' => absint( $id ) ) );
	}

	public static function update_bill( $payment_id, $payment ) {
		if ( ! empty( $payment->get_document_id() ) && $bill = eaccounting_get_bill( $payment->get_document_id() ) ) {
			$bill->save();
		}
	}


	/**
	 * Validate payment data.
	 *
	 * @param array $data
	 * @param null $id
	 * @param \WP_Error $errors
	 *
	 * @throws \Exception
	 * @since 1.1.0
	 *
	 */
	public static function validate_payment_data( $data, $id = null ) {
		if ( empty( $data['payment_date'] ) ) {
			throw new \Exception( 'empty_prop', __( 'Payment date is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['payment_method'] ) ) {
			throw new \Exception( 'empty_prop', __( 'Payment method is required.', 'wp-ever-accounting' ) );
		}

		$category = eaccounting_get_category( $data['category_id'] );
		if ( empty( $category ) || ! in_array( $category->get_type(), array( 'expense', 'other' ), true ) ) {
			throw new \Exception( __( 'A valid payment category is required.', 'wp-ever-accounting' ) );
		}

		$vendor = eaccounting_get_vendor( $data['contact_id'] );
		if ( ! empty( $data['contact_id'] ) && empty( $vendor ) ) {
			throw new \Exception( __( 'Vendor is not valid.', 'wp-ever-accounting' ) );
		}

		$account = eaccounting_get_account( $data['account_id'] );
		if ( ! empty( $data['account_id'] ) && empty( $account ) ) {
			throw new \Exception( __( 'Account is not valid.', 'wp-ever-accounting' ) );
		}

		if ( empty( eaccounting_sanitize_number( $data['amount'] ) ) ) {
			throw new \Exception( 'empty_prop', __( 'Payment amount is required.', 'wp-ever-accounting' ) );
		}
	}

	/**
	 * Validate expense data.
	 *
	 * @param array $data
	 * @param null $id
	 * @param \WP_Error $errors
	 *
	 * @throws \Exception
	 * @since 1.1.0
	 *
	 */
	public static function validate_revenue_data( $data, $id = null ) {
		if ( empty( $data['payment_date'] ) ) {
			throw new \Exception( 'empty_prop', __( 'Revenue date is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['payment_method'] ) ) {
			throw new \Exception( 'empty_prop', __( 'Payment method is required.', 'wp-ever-accounting' ) );
		}

		$category = eaccounting_get_category( $data['category_id'] );
		if ( empty( $category ) || ! in_array( $category->get_type(), array( 'income', 'other' ), true ) ) {
			throw new \Exception( 'empty_prop', __( 'A valid income category is required.', 'wp-ever-accounting' ) );
		}

		$account = eaccounting_get_account( $data['account_id'] );
		if ( empty( $account ) ) {
			throw new \Exception( 'empty_prop', __( 'Account is required.', 'wp-ever-accounting' ) );
		}

		$customer = eaccounting_get_customer( $data['contact_id'] );
		if ( ! empty( $data['contact_id'] ) && empty( $customer ) ) {
			throw new \Exception( 'empty_prop', __( 'Customer is not valid.', 'wp-ever-accounting' ) );
		}

		if ( empty( eaccounting_sanitize_number( $data['amount'] ) ) ) {
			throw new \Exception( 'empty_prop', __( 'Revenue amount is required.', 'wp-ever-accounting' ) );
		}
	}

	/**
	 * Validate transaction data.
	 *
	 * @param array $data
	 * @param null $id
	 *
	 * @throws \Exception
	 *
	 * @since 1.1.0
	 *
	 */
	public static function validate_transaction_data( $data, $id ) {
		if ( empty( $data['payment_date'] ) ) {
			throw new \Exception( 'empty_prop', __( 'Transaction date is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['type'] ) ) {
			throw new \Exception( 'empty_prop', __( 'Transaction type is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $data['payment_method'] ) ) {
			throw new \Exception( 'empty_prop', __( 'Payment method is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $data['category_id'] ) ) {
			throw new \Exception( 'empty_prop', __( 'Category id is required.', 'wp-ever-accounting' ) );
		}
		$category = eaccounting_get_category( $data['category_id'] );
		if ( empty( $category ) ) {
			throw new \Exception( 'empty_prop', __( 'A valid transaction category is required.', 'wp-ever-accounting' ) );
		}

		$account = eaccounting_get_account( $data['account_id'] );
		if ( empty( $account ) ) {
			throw new \Exception( 'empty_prop', __( 'Account is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( eaccounting_sanitize_number( $data['amount'] ) ) ) {
			throw new \Exception( 'empty_prop', __( 'Transaction amount is required.', 'wp-ever-accounting' ) );
		}
	}

	/**
	 * Validate category data.
	 *
	 * @param array $data
	 * @param null $id
	 * @param Category $category
	 *
	 * @throws \Exception
	 *
	 * @since 1.1.0
	 *
	 */
	public static function validate_category_data( $data, $id ) {
		global $wpdb;

		if ( $id != (int) $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_categories WHERE type=%s AND name='%s'", eaccounting_clean( $data['type'] ), eaccounting_clean( $data['name'] ) ) ) ) { // @codingStandardsIgnoreLine
			throw new \Exception( __( 'Duplicate category.', 'wp-ever-accounting' ) );
		}
	}

	/**
	 * Delete category id from transactions.
	 *
	 * @param $id
	 *
	 * @return bool
	 *
	 * @since 1.1.0
	 *
	 */
	public static function update_transaction_category( $id ) {
		global $wpdb;
		$id = absint( $id );
		if ( empty( $id ) ) {
			return false;
		}

		return $wpdb->update( $wpdb->prefix . 'ea_transactions', array( 'category_id' => '' ), array( 'category_id' => absint( $id ) ) );
	}

	/**
	 * Update default currency.
	 *
	 * @param $id
	 *
	 * @return bool
	 *
	 * @since 1.1.0
	 *
	 */
	public static function update_default_currency( $value, $old_value ) {
		if ( ! array_key_exists( 'default_currency', $value ) || $value['default_currency'] === $old_value['default_currency'] ) {
			return;
		}

		do_action( 'eaccounting_pre_change_default_currency', $value['default_currency'], $old_value['default_currency'] );
		$new_currency          = eaccounting_get_currency( $old_value['default_currency'] );
		$new_currency_old_rate = $new_currency->get_rate();
		$conversion_rate       = (float) ( 1 / $new_currency_old_rate );
		$currencies            = eaccounting_collect( get_option( 'eaccounting_currencies', array() ) );
		$currencies            = $currencies->each(
			function ( $currency ) use ( $conversion_rate ) {
				$currency['rate'] = eaccounting_format_decimal( $currency['rate'] * $conversion_rate, 4 );

				return $currency;
			}
		)->all();
		update_option( 'eaccounting_currencies', $currencies );
	}

	/**
	 * Delete currency id from settings.
	 *
	 * @param $id
	 * @param $data
	 *
	 * @since 1.1.0
	 *
	 */
	public static function delete_default_currency( $id, $data ) {
		$default_currency = eaccounting()->settings->get( 'default_currency' );
		if ( $default_currency === $data['code'] ) {
			eaccounting()->settings->set( array( array( 'default_currency' => '' ) ), true );
		}
	}

}

new EverAccounting_Controller();
