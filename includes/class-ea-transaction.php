<?php
/**
 * Handle the transaction of accounting
 *
 * @class       EAccounting_Transaction
 * @version     1.0.0
 * @package     EverAccounting/Classes
 */

defined( 'ABSPATH' ) || exit();

class EAccounting_Transaction extends EAccounting_Object {
	/**
	 * Transaction Data array.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
		'type'           => '',
		'paid_at'        => null,
		'amount'         => 0,
		'currency_code'  => '',
		'currency_rate'  => 1,
		'account_id'     => null,
		'invoice_id'     => null,
		'contact_id'     => null,
		'category_id'    => null,
		'description'    => '',
		'payment_method' => '',
		'reference'      => '',
		'parent_id'      => '',
		'reconciled'     => 0,
		'creator_id'     => null,
		'company_id'     => 1,
		'created_at'     => null,
	);

	/**
	 * Get the transaction if ID is passed, otherwise the order is new and empty.
	 * This class should NOT be instantiated, but the eaccounting_get_transaction function
	 * should be used. It is possible, but the aforementioned are preferred and are the only
	 * methods that will be maintained going forward.
	 *
	 * @param int|object|EAccounting_Transaction $transaction Order to read.
	 */
	public function __construct( $transaction = 0 ) {

		if ( is_numeric( $transaction ) && $transaction > 0 ) {
			$this->set_id( $transaction );
		} elseif ( $transaction instanceof self ) {
			$this->set_id( $transaction->get_id() );
		} elseif ( ! empty( $transaction->id ) ) {
			$this->set_id( $transaction->id );
		} else {
			$this->set_id( null );
		}


		if ( $this->get_id() > 0 ) {
			$this->load( $this->get_id() );
		}
	}

	/**
	 * @param int $id
	 *
	 * @throws Exception
	 */
	public function load( $id ) {
		$this->set_defaults();
		global $wpdb;

		// Get from cache if available.
		$item = 0 < $this->get_id() ? wp_cache_get( 'transaction-item-' . $this->get_id(), 'transactions' ) : false;
		if ( false === $item ) {
			$item = $wpdb->get_row(
				$wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_transactions WHERE id = %d;", $this->get_id() )
			);

			if ( 0 < $item->id ) {
				wp_cache_set( 'transaction-item-' . $item->id, $item, 'transactions' );
			}
		}

		if ( ! $item || ! $item->id ) {
			throw new Exception( __( 'Invalid transaction.', 'wp-ever-accounting' ) );
		}

		// Gets extra data associated with the order if needed.
		foreach ( $item as $key => $value ) {
			$function = 'set_' . $key;
			if ( is_callable( array( $this, $function ) ) ) {
				$this->{$function}( $value );
			}
		}
	}


	public function create() {

	}


	/**
	 * Update transaction item.
	 * @throws Exception
	 * @since 1.0.0
	 *
	 */
	protected function update() {
		$changes = $this->get_changes();
		global $wpdb;
		do_action( 'eaccounting_pre_transaction_update', $this->get_id(), $changes );
		try {
			$wpdb->update( $wpdb->prefix . 'ea_transactions', $changes, array( 'id' => $this->get_id() ) );
		} catch ( Exception $e ) {
			throw new Exception( __( 'Could not update transaction.', 'wp-ever-accounting' ) );
		}

		do_action( 'eaccounting_contact_update', $this->get_id(), $changes, $this->data );

		$this->apply_changes();
		wp_cache_delete( 'transaction-item-' . $this->get_id(), 'transactions' );
	}


	public function delete( $args = array() ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix } where id=%d", $this->get_id() ) );
	}

	protected function validate_props() {
		// TODO: Implement validate_props() method.
	}

}
