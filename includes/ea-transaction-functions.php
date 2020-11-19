<?php
/**
 * EverAccounting Transaction functions.
 *
 * Functions for all kind of transaction of the plugin.
 *
 * @since   1.1.0
 * @package EverAccounting
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get Transaction Types
 *
 * @return array
 * @since 1.1.0
 */
function eaccounting_get_transaction_types() {
	$types = array(
		'income'  => __( 'Income', 'wp-ever-accounting' ),
		'expense' => __( 'Expense', 'wp-ever-accounting' ),
	);

	return apply_filters( 'eaccounting_transaction_types', $types );
}

/**
 * Get a single expense.
 *
 * @param $expense
 *
 * @return \EverAccounting\Models\Expense|null
 * @since 1.1.0
 *
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
 * @param array $args {
 * An array of elements that make up an expense to update or insert.
 *
 * @type int $id Transaction id. If the id is something other than 0 then it will update the transaction.
 * @type string $paid_at Time of the transaction. Default null.
 * @type string $amount Transaction amount. Default null.
 * @type int $account_id From/To which account the transaction is. Default empty.
 * @type int $contact_id Contact id related to the transaction. Default empty.
 * @type int $invoice_id Transaction related invoice id(optional). Default empty.
 * @type int $category_id Category of the transaction. Default empty.
 * @type string $payment_method Payment method used for the transaction. Default empty.
 * @type string $reference Reference of the transaction. Default empty.
 * @type string $description Description of the transaction. Default empty.
 *
 * }
 *
 * @return EverAccounting\Models\Expense|\WP_Error
 * @since 1.1.0
 *
 */
function eaccounting_insert_expense( $args ) {
	$expense = new EverAccounting\Models\Expense( $args );

	return $expense->save();
}

/**
 * Delete a expense.
 *
 * @param $expense_id
 *
 * @return bool
 * @since 1.1.0
 *
 */
function eaccounting_delete_expense( $expense_id ) {
	$expense = new EverAccounting\Models\Expense( $expense_id );
	if ( ! $expense->exists() ) {
		return false;
	}

	return \EverAccounting\Repositories\Expenses::instance()->delete( $expense->get_id() );
}

/**
 * Get expense items.
 *
 * @param array $args {
 *
 * @type int $id Transaction id.
 * @type string $paid_at Time of the transaction.
 * @type string $amount Transaction amount.
 * @type int $account_id From/To which account the transaction is.
 * @type int $contact_id Contact id related to the transaction.
 * @type int $invoice_id Transaction related invoice id(optional).
 * @type int $category_id Category of the transaction.
 * @type string $payment_method Payment method used for the transaction.
 * @type string $reference Reference of the transaction.
 * @type string $description Description of the transaction.
 *
 * }
 *
 * @param bool $callback
 *
 * @return array|int
 * @since 1.1.0
 *
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
 * @param $income
 *
 * @return \EverAccounting\Models\Income|null
 * @since 1.1.0
 *
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
 * @param array $args {
 * An array of elements that make up an expense to update or insert.
 *
 * @type int $id Transaction id. If the id is something other than 0 then it will update the transaction.
 * @type string $paid_at Time of the transaction. Default null.
 * @type string $amount Transaction amount. Default null.
 * @type int $account_id From/To which account the transaction is. Default empty.
 * @type int $contact_id Contact id related to the transaction. Default empty.
 * @type int $invoice_id Transaction related invoice id(optional). Default empty.
 * @type int $category_id Category of the transaction. Default empty.
 * @type string $payment_method Payment method used for the transaction. Default empty.
 * @type string $reference Reference of the transaction. Default empty.
 * @type string $description Description of the transaction. Default empty.
 *
 * }
 *
 * @return EverAccounting\Models\Income|\WP_Error
 * @since 1.1.0
 *
 */
function eaccounting_insert_income( $args ) {
	$income = new EverAccounting\Models\Income( $args );

	return $income->save();
}

/**
 * Delete a income.
 *
 * @param $income_id
 *
 * @return bool
 * @since 1.1.0
 *
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
 * @param array $args {
 *
 * @type int $id Transaction id.
 * @type string $paid_at Time of the transaction.
 * @type string $amount Transaction amount.
 * @type int $account_id From/To which account the transaction is.
 * @type int $contact_id Contact id related to the transaction.
 * @type int $invoice_id Transaction related invoice id(optional).
 * @type int $category_id Category of the transaction.
 * @type string $expense_method Payment method used for the transaction.
 * @type string $reference Reference of the transaction.
 * @type string $description Description of the transaction.
 *
 * }
 * @param bool $callback
 *
 * @return array|int
 * @since 1.1.0
 *
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
 * @param $transfer
 *
 * @return \EverAccounting\Models\Transfer|null
 * @since 1.1.0
 *
 */
function eaccounting_get_transfer( $transfer ) {
	if ( empty( $transfer ) ) {
		return null;
	}

	$result = new EverAccounting\Models\Transfer( $transfer );

	return $result->exists() ? $result : null;
}

/**
 * Create new transfer programmatically.
 *
 * Returns a new transfer object on success.
 *
 * @param array $args {
 * An array of elements that make up an transfer to update or insert.
 *
 * @type int $id ID of the transfer. If equal to something other than 0,
 *                               the post with that ID will be updated. Default 0.
 * @type int $from_account_id ID of the source account from where transfer is initiating.
 *                               default null.
 * @type int $to_account_id ID of the target account where the transferred amount will be
 *                               deposited. default null.
 * @type string $amount Amount of the money that will be transferred. default 0.
 * @type string $date Date of the transfer. default null.
 * @type string $payment_method Payment method used in transfer. default null.
 * @type string $reference Reference used in transfer. Default empty.
 * @type string $description Description of the transfer. Default empty.
 *
 * }
 *
 * @return \EverAccounting\Models\Transfer|\WP_Error
 * @since 1.1.0
 *
 */
function eaccounting_insert_transfer( $args ) {
	$transfer = new EverAccounting\Models\Transfer( $args );

	return $transfer->save();
}

/**
 * Delete a transfer.
 *
 * @param $transfer_id
 *
 * @return bool
 * @since 1.1.0
 *
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
 * @param array $args {
 *
 * @type int $id ID of the transfer.
 * @type int $from_account_id ID of the source account from where transfer is initiating.
 * @type int $to_account_id ID of the target account where the transferred amount will be deposited.
 * @type string $amount Amount of the money that will be transferred.
 * @type string $date Date of the transfer.
 * @type string $payment_method Payment method used in transfer.
 * @type string $reference Reference used in transfer.
 * @type string $description Description of the transfer.
 *
 * }
 *
 * @param bool $callback
 *
 * @return array|int
 * @since 1.1.0
 *
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
