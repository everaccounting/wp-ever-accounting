<?php

namespace EverAccounting\Accounts;

use EverAccounting\Exception;
use EverAccounting\Traits\SingletonTrait;

class Controller implements \EverAccounting\Interfaces\Controller {
	use SingletonTrait;

	/**
	 * Controller constructor.
	 */
	public function __construct() {
		add_action( 'eaccounting_delete_account', array( $this, 'delete_account_references' ) );
	}

	/**
	 * When an account is deleted we need to make sure
	 * we do not have any data reference to that.
	 *
	 * @since 1.1.0
	 *
	 * @param $id
	 *
	 * @return bool|false|int
	 */
	public function delete_account_references( $id ) {
		//delete from settings.
		$default_account = eaccounting()->settings->get( 'default_account' );
		if ( intval( $default_account ) === intval( $id ) ) {
			eaccounting()->settings->set( array( array( 'default_account' => '' ) ), true );
		}

		global $wpdb;

		return $wpdb->update( "{$wpdb->prefix}ea_transactions", array( 'account_id' => '' ), array( 'account_id' => absint( $id ) ) );
	}

	/**
	 * Main function for returning account.
	 *
	 * @since 1.1.0
	 *
	 * @param $args
	 *
	 * @return \EverAccounting\Accounts\Account|mixed|\WP_Error
	 */
	public function insert( $args ) {
		try {
			$default_args = array(
				'id' => null,
			);
			$args         = (array) wp_parse_args( $args, $default_args );
			$account      = new Account( $args['id'] );
			$account->set_props( $args );
			//validation
			if ( ! $account->get_date_created() ) {
				$account->set_date_created( time() );
			}
			if ( ! $account->get_creator_id() ) {
				$account->set_creator_id();
			}

			if ( empty( $account->get_name() ) ) {
				throw new Exception( 'empty_props', __( 'Account Name is required', 'wp-ever-accounting' ) );
			}

			if ( empty( $account->get_number( 'edit' ) ) ) {
				throw new Exception( 'empty_props', __( 'Account Number is required', 'wp-ever-accounting' ) );
			}

			if ( empty( $account->get_currency_code( 'edit' ) ) ) {
				throw new Exception( 'empty_props', __( 'Currency code is required', 'wp-ever-accounting' ) );
			}

			$currency = eaccounting_get_currency( $account->get_currency_code() );
			if ( ! $currency || ! $currency->exists() ) {
				throw new Exception( 'invalid_props', __( 'Currency with provided code does not not exist.', 'wp-ever-accounting' ) );
			}

			$existing_id = query()
				->where( 'number', $account->get_number() )
				->value( 0 );

			if ( ! empty( $existing_id ) && absint( $existing_id ) != $account->get_id() ) { // @codingStandardsIgnoreLine
				throw new Exception( 'duplicate_props', __( 'Duplicate account number.', 'wp-ever-accounting' ) );
			}

			$account->save();

		} catch ( Exception $e ) {
			return new \WP_Error( $e->getErrorCode(), $e->getMessage() );
		}

		return $account;
	}

	/**
	 * @since 1.1.0
	 *
	 * @param $account
	 *
	 * @return \EverAccounting\Accounts\Account|null
	 */
	public function get( $account ) {
		if ( empty( $account ) ) {
			return null;
		}

		try {
			if ( $account instanceof Account ) {
				$_account = $account;
			} elseif ( is_object( $account ) && ! empty( $account->id ) ) {
				$_account = new Account( null );
				$_account->populate( $account );
			} else {
				$_account = new Account( absint( $account ) );
			}

			if ( ! $_account->exists() ) {
				throw new Exception( 'invalid_id', __( 'Invalid account.', 'wp-ever-accounting' ) );
			}

			return $_account;
		} catch ( Exception $exception ) {
			return null;
		}
	}

	/**
	 * Deletes an account.
	 *
	 * @since 1.1.0
	 *
	 * @param $account_id
	 *
	 * @return bool
	 */
	public function delete( $account_id ) {
		try {
			$account = new Account( $account_id );
			if ( ! $account->exists() ) {
				throw new Exception( 'invalid_id', __( 'Invalid account.', 'wp-ever-accounting' ) );
			}

			$account->delete();

			return empty( $account->get_id() );

		} catch ( Exception $exception ) {
			return false;
		}
	}


	public function get_items( $args = array()) {
		$args  = wp_parse_args( $args, array( 'count' => false ) );
		$query = new Query();
		$query->parse( $args );
		$result['items'] = $query->get_results( OBJECT, 'eaccounting()->account->get' );
		return $result;
	}
}
