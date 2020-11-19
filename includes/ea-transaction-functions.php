<?php
/**
 * EverAccounting Transaction functions.
 *
 * Functions for all kind of transaction of the plugin.
 *
 * @since   1.0.2
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get Transaction Types
 *
 * @since 1.0.2
 * @return array
 */
function eaccounting_get_transaction_types() {
	$types = array(
		'income'  => __( 'Income', 'wp-ever-accounting' ),
		'expense' => __( 'Expense', 'wp-ever-accounting' ),
	);

	return apply_filters( 'eaccounting_transaction_types', $types );
}

/**
 * Get expense.
 *
 * @since 1.0.2
 *
 * @param $expense
 *
 * @return \EverAccounting\Models\Expense|null
 */
function eaccounting_get_expense( $expense ) {
	if ( empty( $expense ) ) {
		return null;
	}

	$result = new EverAccounting\Models\Expense( $expense );

	return $result->exists() ? $result : null;
}


/**
 *  Create new expense programmatically.
 *
 *  Returns a new payment object on success.
 *
 * @since 1.0.2
 *
 * @param array $args payment arguments.
 *
 * @return EverAccounting\Models\Expense|\WP_Error
 */
function eaccounting_insert_expense( $args ) {
	$expense = new EverAccounting\Models\Expense( $args );

	return $expense->save();
}

/**
 * Delete a expense.
 *
 * @since 1.0.2
 *
 * @param $expense_id
 *
 * @return bool
 */
function eaccounting_delete_expense( $expense_id ) {
	$expense = new EverAccounting\Models\Expense( $expense_id );
	if ( ! $expense->exists() ) {
		return false;
	}

	return \EverAccounting\Repositories\Expenses::instance()->delete( $expense->get_id() );
}

/**
 * Get payments items.
 *
 * @since 1.1.0
 *
 * @param array $args
 *
 * @param bool  $callback
 *
 * @return array|int
 */
function eaccounting_get_expenses( $args = array(), $callback = true ) {
	return \EverAccounting\Repositories\Expenses::instance()->get_items(
		$args,
		function ( $item ) use ( $callback ) {
			if ( $callback ) {
				$category = new \EverAccounting\Models\Expense();
				$category->set_props( $item );
				$category->set_object_read( true );

				return $category;
			}

			return $item;
		}
	);
}

/**
 * Get revenue.
 *
 * @since 1.0.2
 *
 * @param $income
 *
 * @return \EverAccounting\Models\Income|null
 */
function eaccounting_get_income( $income ) {
	if ( empty( $income ) ) {
		return null;
	}

	$result = new EverAccounting\Models\Income( $income );

	return $result->exists() ? $result : null;
}


/**
 *  Create new income programmatically.
 *
 *  Returns a new revenue object on success.
 *
 * @since 1.0.2
 *
 * @param array $args           {
 *
 * @type int    $id             Transaction id. If the id is something other than 0 then it will update the transaction.
 * @type string $paid_at        Time of the transaction.
 * @type string $amount         Transaction amount.
 * @type int    $account_id     From/To which account the transaction is.
 * @type int    $contact_id     Contact id related to the transaction.
 * @type int    $invoice_id     Transaction related invoice id(optional).
 * @type int    $category_id    Category of the transaction.
 * @type string $expense_method Payment method used for the transaction.
 * @type string $reference      Reference of the transaction.
 * @type string $description    Description of the transaction.
 *
 * }
 *
 * @return EverAccounting\Models\Income|\WP_Error
 */
function eaccounting_insert_income( $args ) {
	$income = new EverAccounting\Models\Income( $args );

	return $income->save();
}

/**
 * Delete a revenue.
 *
 * @since 1.0.2
 *
 * @param $income_id
 *
 * @return bool
 */
function eaccounting_delete_income( $income_id ) {
	$income = new EverAccounting\Models\Income( $income_id );
	if ( ! $income->exists() ) {
		return false;
	}

	return \EverAccounting\Repositories\Incomes::instance()->delete( $income->get_id() );
}

/**
 * Get revenues items.
 *
 * @since 1.1.0
 *
 * @param array $args           {
 *
 * @type int    $id             Transaction id. If the id is something other than 0 then it will update the transaction.
 * @type string $paid_at        Time of the transaction.
 * @type string $amount         Transaction amount.
 * @type int    $account_id     From/To which account the transaction is.
 * @type int    $contact_id     Contact id related to the transaction.
 * @type int    $invoice_id     Transaction related invoice id(optional).
 * @type int    $category_id    Category of the transaction.
 * @type string $expense_method Payment method used for the transaction.
 * @type string $reference      Reference of the transaction.
 * @type string $description    Description of the transaction.
 *
 * }
 * @param bool  $callback
 *
 * @return array|int
 */
function eaccounting_get_incomes( $args = array(), $callback = true ) {
	return \EverAccounting\Repositories\Incomes::instance()->get_items(
		$args,
		function ( $item ) use ( $callback ) {
			if ( $callback ) {
				$income = new \EverAccounting\Models\Income();
				$income->set_props( $item );
				$income->set_object_read( true );

				return $income;
			}

			return $item;
		}
	);
}

/**
 * Get transfer.
 *
 * @since 1.1.1
 *
 * @param $transfer
 *
 * @return \EverAccounting\Models\Transfer|null
 */
function eaccounting_get_transfer( $transfer ) {
	if ( empty( $transfer ) ) {
		return null;
	}

	$result = new EverAccounting\Models\Transfer( $transfer );

	return $result->exists() ? $result : null;
}

/**
 * Insert transfer.
 *
 * @since 1.1.1
 *
 * @param array $args
 *
 * @return \EverAccounting\Models\Transfer|\WP_Error
 */
function eaccounting_insert_transfer( $args ) {
	$transfer = new EverAccounting\Models\Transfer( $args );

	return $transfer->save();
}

/**
 * Delete a transfer.
 *
 * @since 1.0.2
 *
 * @param $transfer_id
 *
 * @return bool
 */
function eaccounting_delete_transfer( $transfer_id ) {
	$transfer = new EverAccounting\Models\Transfer( $transfer_id );
	if ( ! $transfer->exists() ) {
		return false;
	}

	return \EverAccounting\Repositories\Transfers::instance()->delete( $transfer->get_id() );
}

/**
 * Get transfers.
 *
 * @since 1.1.0
 *
 * @param array $args
 *
 * @param bool  $callback
 *
 * @return array|int
 */
function eaccounting_get_transfers( $args = array(), $callback = true ) {
	return \EverAccounting\Repositories\Transfers::instance()->get_items(
		$args,
		function ( $item ) use ( $callback ) {
			if ( $callback ) {
				$transfer = new \EverAccounting\Models\Transfer();
				$transfer->set_props( $item );
				$transfer->set_object_read( true );

				return $transfer;
			}

			return $item;
		}
	);
}


function eaccounting_get_transactions( $args = array(), $callback = true ) {
	return \EverAccounting\Repositories\Transactions::instance()->get_items(
		$args,
		function ( $item ) use ( $callback ) {
			if ( $callback ) {
				switch ( $callback ) {
					case 'income':
						$transaction = new \EverAccounting\Models\Income();
						$transaction->set_props( $item );
						$transaction->set_object_read( true );

						return $transaction;
						break;
					case 'expense':
						$transaction = new \EverAccounting\Models\Expense();
						$transaction->set_props( $item );
						$transaction->set_object_read( true );

						return $transaction;
						break;
				}

			}

			return $item;
		}
	);
}
