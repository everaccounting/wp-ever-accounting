<?php
/**
 * Handle the invoice object.
 *
 * @package     EverAccounting\Models
 * @class       Currency
 * @version     1.1.0
 */

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Invoice
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Invoice extends Document {

	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'invoice';

	/**
	 * @since 1.1.0
	 *
	 * @var string
	 */
	public $cache_group = 'ea_invoices';

	/**
	 * Get the invoice if ID is passed, otherwise the account is new and empty.
	 *
	 * @since 1.1.0
	 *
	 * @param int|object|Invoice $invoice object to read.
	 *
	 */
	public function __construct( $invoice = 0 ) {
		$this->data = array_merge( $this->data, array( 'type' => 'invoice' ) );
		parent::__construct( $invoice );

		if ( $invoice instanceof self ) {
			$this->set_id( $invoice->get_id() );
		} elseif ( is_numeric( $invoice ) ) {
			$this->set_id( $invoice );
		} elseif ( ! empty( $invoice->id ) ) {
			$this->set_id( $invoice->id );
		} elseif ( is_array( $invoice ) ) {
			$this->set_props( $invoice );
		} else {
			$this->set_object_read( true );
		}

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}

		$this->required_props = array(
			//'line_items'    => __( 'Line Items', 'wp-ever-accounting' ),
			'currency_code' => __( 'Currency', 'wp-ever-accounting' ),
			'category_id'   => __( 'Category', 'wp-ever-accounting' ),
			'customer_id'   => __( 'Customer', 'wp-ever-accounting' ),
			'issue_date'    => __( 'Issue date', 'wp-ever-accounting' ),
			'due_date'      => __( 'Due date', 'wp-ever-accounting' ),
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Object Specific data methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * All available invoice statuses.
	 *
	 * @when  an invoice is created status is pending
	 * @when  sent to customer is sent
	 * @when  partially paid is partial
	 * @when  Full amount paid is paid
	 * @when  due date passed but not paid is overdue.
	 *
	 * @since 1.0.1
	 *
	 * @return array
	 */
	public static function get_statuses() {
		return array(
			'draft'     => __( 'Draft', 'wp-ever-accounting' ),
			'pending'   => __( 'Pending', 'wp-ever-accounting' ),
			'sent'      => __( 'Sent', 'wp-ever-accounting' ),
			'partial'   => __( 'Partial', 'wp-ever-accounting' ),
			'paid'      => __( 'Paid', 'wp-ever-accounting' ),
			'overdue'   => __( 'Overdue', 'wp-ever-accounting' ),
			'cancelled' => __( 'Cancelled', 'wp-ever-accounting' ),
			'refunded'  => __( 'Refunded', 'wp-ever-accounting' ),
		);
	}

	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/
	/**
	 * Return the document number.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_invoice_number( $context = 'edit' ) {
		return $this->get_prop( 'document_number', $context );
	}

	/**
	 * Get internal type.
	 *
	 * @return string
	 */
	public function get_type( $context = 'edit' ) {
		return 'invoice';
	}

	/**
	 * Return the customer id.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_customer_id( $context = 'edit' ) {
		return $this->get_prop( 'contact_id', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/
	/**
	 * set the customer id.
	 *
	 * @since  1.1.0
	 *
	 * @param int $customer_id .
	 *
	 */
	public function set_customer_id( $customer_id ) {
		parent::set_contact_id( $customer_id );
		if ( $this->get_contact_id() && ( ! $this->exists() || array_key_exists( 'contact_id', $this->changes ) ) ) {
			$contact = eaccounting_get_customer( $this->get_contact_id() );
			$address = $this->data['address'];
			foreach ( $address as $prop => $value ) {
				$getter = "get_{$prop}";
				$setter = "set_{$prop}";
				if ( is_callable( array( $contact, $getter ) )
				     && is_callable( array( $this, $setter ) )
				     && is_callable( array( $this, $getter ) ) ) {
					$this->$setter( $contact->$getter() );
				}
			}
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Boolean methods
	|--------------------------------------------------------------------------
	|
	| Return true or false.
	|
	*/

	/**
	 * Checks to see if the invoice requires payment.
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_free() {
		return empty( $this->get_total() );
	}

	/**
	 * Checks if the invoice needs payment.
	 * @since 1.1.0
	 * @return bool
	 */
	public function needs_payment() {
		return ! $this->is_status( 'paid' ) && ! $this->is_status( 'refunded' ) && ! $this->is_free();
	}

	/**
	 * Checks if the invoice is refunded.
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_refunded() {
		return $this->is_status( 'refunded' );
	}

	/**
	 * Checks if the invoice is due.
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_due() {
		$due_date = $this->get_due_date();
		return empty( $due_date ) ? false : current_time( 'timestamp' ) > strtotime( $due_date ); //phpcs:ignore
	}

	/**
	 * Checks if the invoice is draft.
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_draft() {
		return $this->is_status( 'draft' );
	}

	/*
	|--------------------------------------------------------------------------
	| Additional methods
	|--------------------------------------------------------------------------
	|
	| Used for various reasons.
	|
	*/
	/**
	 * Get payments.
	 *
	 * @since 1.1.0
	 *
	 * @return Revenue[]
	 */
	public function get_payments() {
		if ( $this->exists() ) {
			return eaccounting_get_revenues(
				array(
					'document_id' => $this->get_id(),
					'type'        => 'income',
				)
			);
		}

		return array();
	}

	/**
	 * Get total due.
	 *
	 * @since 1.1.0
	 *
	 * @return float|int
	 */
	public function get_total_due() {
		$due = $this->get_total() - $this->get_total_paid();
		if ( $due < 0 ) {
			$due = 0;
		}

		return $due;
	}

	/**
	 * Get total paid
	 *
	 * @since 1.1.0
	 *
	 * @return float|int|string
	 */
	public function get_total_paid() {
		$total_paid = 0;
		foreach ( $this->get_payments() as $payment ) {
			$total_paid += (float) eaccounting_price_convert_between( $payment->get_amount(), $payment->get_currency_code(), $payment->get_currency_rate(), $this->get_currency_code(), $this->get_currency_rate() );
		}

		return $total_paid;
	}

	/**
	 * @since 1.1.0
	 *
	 * @param array $args
	 *
	 * @throws \Exception
	 * @return false
	 */
	public function add_payment( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'date'           => '',
				'amount'         => '',
				'account_id'     => '',
				'payment_method' => '',
				'description'    => '',
			)
		);
		if ( ! $this->exists() ) {
			return false;
		}

		if ( empty( $args['date'] ) ) {
			$args['date'] = current_time( 'mysql' );
		}

		if ( empty( $args['amount'] ) ) {
			throw new \Exception(
				__( 'Payment amount is required', 'wp-ever-accounting' )
			);
		}

		if ( empty( $args['account_id'] ) ) {
			throw new \Exception(
				__( 'Payment account is required', 'wp-ever-accounting' )
			);
		}

		if ( empty( $args['payment_method'] ) ) {
			throw new \Exception(
				__( 'Payment method is required', 'wp-ever-accounting' )
			);
		}

		//$total_due = $this->get_total_due();
		//      if ( $amount  $total_due ) {
		//          throw new \Exception(
		//              sprintf(
		//              /* translators: %s paying amount %s due amount */
		//                  __( 'Amount is larger than due amount, input total: %1$s & due: %2$s', 'wp-ever-accounting' ),
		//                  eaccounting_format_price( $amount, $this->get_currency_code() ),
		//                  eaccounting_format_price( $this->get_total_due(), $this->get_currency_code() )
		//              )
		//          );
		//      }
		$amount           = (float) eaccounting_sanitize_number( $args['amount'], true );
		$account          = eaccounting_get_account( $args['account_id'] );
		$currency         = eaccounting_get_currency( $account->get_currency_code() );
		$converted_amount = eaccounting_price_convert_between( $amount, $this->get_currency_code(), $this->get_currency_rate(), $currency->get_code(), $currency->get_rate() );
		$income           = new Revenue();
		$income->set_props(
			array(
				'payment_date'   => $args['date'],
				'document_id'    => $this->get_id(),
				'account_id'     => absint( $args['account_id'] ),
				'amount'         => $converted_amount,
				'category_id'    => $this->get_category_id(),
				'customer_id'    => $this->get_contact_id(),
				'payment_method' => eaccounting_clean( $args['payment_method'] ),
				'description'    => eaccounting_clean( $args['description'] ),
				'reference'      => sprintf( __( 'Invoice Payment #%d', 'wp-ever-accounting' ), $this->get_id() ),//phpcs:ignore
			)
		);

		$income->save();
		/* translators: %s amount */
		$this->add_note( sprintf( __( 'Received payment %s', 'wp-ever-accounting' ), eaccounting_price( $args['amount'], $this->get_currency_code() ) ), false );
		wp_cache_flush();

		return true;
	}

	/**
	 * Mark paid.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function set_paid() {
		try {
			$default_account = eaccounting()->settings->get( 'default_account' );
			$payment_method  = eaccounting()->settings->get( 'default_payment_method', 'cash' );
			if ( empty( $default_account ) ) {
				return false;
			}
			$due = $this->get_total_due();
			$this->add_payment(
				array(
					'payment_date'   => time(),
					'account_id'     => absint( $default_account ),
					'amount'         => $due,
					'category_id'    => $this->get_category_id(),
					'customer_id'    => $this->get_contact_id(),
					'payment_method' => $payment_method,
				)
			);
			return $this->save();
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Refund.
	 * @since 1.1.0
	 */
	public function set_refunded() {
		$payments = $this->get_payments();
		foreach ( $payments as $payment ) {
			$payment->delete();
		}
		$this->set_status('refunded');
		wp_cache_flush();
		$this->save();
	}

	/**
	 * Handle the status transition.
	 */
	protected function status_transition() {
		$status_transition = $this->status_transition;

		// Reset status transition variable.
		$this->status_transition = false;
		if ( $status_transition ) {
			try {
				do_action( 'eaccounting_invoice_status_' . $status_transition['to'], $this->get_id(), $this );

				if ( ! empty( $status_transition['from'] ) ) {
					/* translators: 1: old order status 2: new order status */
					$transition_note = sprintf( __( 'Status changed from %1$s to %2$s.', 'wp-ever-accounting' ), $status_transition['from'], $status_transition['to'] );

					// Note the transition occurred.
					$this->add_note( $transition_note, false );

					do_action( 'eaccounting_invoice_status_' . $status_transition['from'] . '_to_' . $status_transition['to'], $this->get_id(), $this );
					do_action( 'eaccounting_invoice_status_changed', $this->get_id(), $status_transition['from'], $status_transition['to'], $this );

					// Work out if this was for a payment, and trigger a payment_status hook instead.
					if (
						in_array( $status_transition['from'], array( 'cancelled', 'pending', 'viewed', 'approved', 'overdue', 'unpaid' ), true )
						&& in_array( $status_transition['to'], array( 'paid', 'partial' ), true )
					) {
						do_action( 'eaccounting_invoice_payment_status_changed', $this, $status_transition );
					}
				} else {
					/* translators: %s: new invoice status */
					$transition_note = sprintf( __( 'Status set to %s.', 'wp-ever-accounting' ), $status_transition['to'], $this );

					// Note the transition occurred.
					$this->add_note( trim( $status_transition['note'] . ' ' . $transition_note ), false );
				}
			} catch ( \Exception $e ) {
				$this->add_note( __( 'Error during status transition.', 'wp-ever-accounting' ) . ' ' . $e->getMessage() );
			}
		}
	}

	/**
	 *  Conditionally change status
	 * @since 1.1.0
	 */
	public function maybe_change_status() {
		if ( $this->needs_payment() && ( time() > strtotime( $this->get_due_date() ) ) ) {
			$this->set_status( 'overdue' );
		}
		if ( $this->is_status( 'paid' ) && empty( $this->get_payment_date() ) ) {
			$this->set_payment_date( time() );
		}
	}


	/**
	 * Save object.
	 *
	 * @since 1.1.0
	 * @return bool|\Exception|int
	 */
	public function save() {
		$this->check_required_items();
		$this->calculate_totals();
		$this->maybe_set_document_number();
		$this->maybe_set_key();
		$this->maybe_change_status();
		$saved = parent::save();
		$this->save_items();
		$this->status_transition();
		return $saved;
	}


	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Used for database transactions.
	|
	*/

	/**
	 * Adds an item to the invoice.
	 *
	 * @param array $args
	 *
	 * @return \WP_Error|Bool
	 */
	public function add_item( $args ) {
		$args = wp_parse_args( $args, array( 'item_id' => null ) );
		if ( empty( $args['item_id'] ) ) {
			return false;
		}
		$product = new Item( $args['item_id'] );
		if ( ! $product->exists() ) {
			return false;
		}

		//convert the price from default to invoice currency.
		$default_currency = eaccounting()->settings->get( 'default_currency', 'USD' );
		$default          = array(
			'item_id'       => $product->get_id(),
			'item_name'     => $product->get_name(),
			'price'         => $product->get_sale_price(),
			'currency_code' => $this->get_currency_code() ? $this->get_currency_code() : $default_currency,
			'quantity'      => 1,
			'tax_rate'      => eaccounting_tax_enabled() ? $product->get_sales_tax() : 0,
		);
		$item             = $this->get_item( $product->get_id() );
		if ( ! $item ) {
			$item = new DocumentItem();
		}
		$args = wp_parse_args( $args, $default );
		$item->set_props( $args );

		//Now prepare
		$this->items[ $item->get_item_id() ] = $item;

		return $item->get_item_id();
	}


	/**
	 * Add note.
	 *
	 * @since 1.1.0
	 *
	 * @param       $note
	 * @param false $customer_note
	 *
	 * @return Note|false|int|\WP_Error
	 */
	public function add_note( $note, $customer_note = false ) {
		if ( ! $this->exists() ) {
			return false;
		}
		if ( $customer_note ) {
			do_action( 'eaccounting_invoice_customer_note', $note, $this );
		}

		$creator_id = 0;
		// If this is an admin comment or it has been added by the user.
		if ( is_user_logged_in() ) {
			$creator_id = get_current_user_id();
		}

		return eaccounting_insert_note(
			array(
				'parent_id'  => $this->get_id(),
				'type'       => 'invoice',
				'note'       => $note,
				'extra'      => array( 'customer_note' => $customer_note ),
				'creator_id' => $creator_id,
			)
		);
	}

	/**
	 * Conditionally set complete
	 *
	 * @since 1.1.0
	 */
	public function maybe_set_complete() {
		if ( $this->is_status( 'paid' ) && empty( $this->get_payment_date() ) ) {
			$this->set_payment_date( time() );
		}
	}

	/**
	 * Generate number.
	 *
	 * @since 1.1.0
	 *
	 * @param $number
	 *
	 * @return string
	 */
	public function generate_number( $number ) {
		$prefix           = eaccounting()->settings->get( 'invoice_prefix', 'INV-' );
		$padd             = (int) eaccounting()->settings->get( 'invoice_digit', '5' );
		$formatted_number = zeroise( absint( $number ), $padd );
		$number           = apply_filters( 'eaccounting_generate_' . sanitize_key( $this->get_type() ) . '_number', $prefix . $formatted_number );

		return $number;
	}
}
