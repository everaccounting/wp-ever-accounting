<?php
/**
 * Handle the Bill object.
 *
 * @package     EverAccounting\Models
 * @class       Currency
 * @version     1.1.0
 */

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Class Bill
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Bill extends Document {
	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'bill';

	/**
	 * @since 1.1.0
	 *
	 * @var string
	 */
	public $cache_group = 'ea_bills';

	/**
	 * Get the bill if ID is passed, otherwise the account is new and empty.
	 *
	 * @since 1.1.0
	 *
	 * @param int|object|Bill $bill object to read.
	 *
	 */
	public function __construct( $bill = 0 ) {
		$this->data = array_merge( $this->data, array( 'type' => 'bill' ) );
		parent::__construct( $bill );

		if ( $bill instanceof self ) {
			$this->set_id( $bill->get_id() );
		} elseif ( is_numeric( $bill ) ) {
			$this->set_id( $bill );
		} elseif ( ! empty( $bill->id ) ) {
			$this->set_id( $bill->id );
		} elseif ( is_array( $bill ) ) {
			$this->set_props( $bill );
		} else {
			$this->set_object_read( true );
		}

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}

		if ( 'bill' !== $this->get_type() ) {
			$this->set_id( 0 );
			$this->set_defaults();
		}

		$this->required_props = array(
			//'line_items'    => __( 'Line Items', 'wp-ever-accounting' ),
			'currency_code' => __( 'Currency', 'wp-ever-accounting' ),
			'category_id'   => __( 'Category', 'wp-ever-accounting' ),
			'contact_id'    => __( 'Vendor', 'wp-ever-accounting' ),
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
	 * All available bill statuses.
	 *
	 * @when  an bill is created status is pending
	 * @when  sent to vendor is sent
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
			'received'  => __( 'Received', 'wp-ever-accounting' ),
			'partial'   => __( 'Partial', 'wp-ever-accounting' ),
			'paid'      => __( 'Paid', 'wp-ever-accounting' ),
			'overdue'   => __( 'Overdue', 'wp-ever-accounting' ),
			'cancelled' => __( 'Cancelled', 'wp-ever-accounting' ),
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
	public function get_bill_number( $context = 'edit' ) {
		return $this->get_prop( 'document_number', $context );
	}

	/**
	 * Return the vendor id.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_vendor_id( $context = 'edit' ) {
		return parent::get_contact_id( $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/
	/**
	 * set the vendor id.
	 *
	 * @since  1.1.0
	 *
	 * @param int $vendor_id .
	 *
	 */
	public function set_vendor_id( $vendor_id ) {
		parent::set_contact_id( $vendor_id );
		if ( $this->get_contact_id() && ( ! $this->exists() || array_key_exists( 'contact_id', $this->changes ) ) ) {
			$contact = eaccounting_get_vendor( $this->get_contact_id() );
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
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_free() {
		return empty( $this->get_total() );
	}

	/**
	 * Checks if the invoice needs payment.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function needs_payment() {
		return ! empty( (int) eaccounting_round_number( $this->get_total_due(), 0 ) );
	}

	/**
	 * Checks if the invoice is due.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_due() {
		$due_date = $this->get_due_date();

		return empty( $due_date ) ? false : current_time( 'timestamp' ) > strtotime( $due_date ); //phpcs:ignore
	}

	/**
	 * Checks if the invoice is draft.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_draft() {
		return $this->is_status( 'draft' );
	}

	/**
	 * Check if an bill is editable.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public function is_editable() {
		return apply_filters( 'eaccounting_bill_is_editable', ! in_array( $this->get_status(), array( 'paid' ), true ), $this );
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
	 * Adds an item to the bill.
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

		//convert the price from default to bill currency.
		$default_currency = eaccounting()->settings->get( 'default_currency', 'USD' );
		$default          = array(
			'item_id'       => $product->get_id(),
			'item_name'     => $product->get_name(),
			'price'         => $product->get_purchase_price(),
			'currency_code' => $this->get_currency_code() ? $this->get_currency_code() : $default_currency,
			'quantity'      => 1,
			'tax_rate'      => eaccounting_tax_enabled() ? $product->get_purchase_tax() : 0,
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
	 * @param false $vendor_note
	 *
	 * @return Note|false|int|\WP_Error
	 */
	public function add_note( $note, $vendor_note = false ) {
		if ( ! $this->exists() ) {
			return false;
		}
		if ( $vendor_note ) {
			do_action( 'eaccounting_bill_vendor_note', $note, $this );
		}

		$creator_id = 0;
		// If this is an admin comment or it has been added by the user.
		if ( is_user_logged_in() ) {
			$creator_id = get_current_user_id();
		}

		return eaccounting_insert_note(
			array(
				'parent_id'  => $this->get_id(),
				'type'       => 'bill',
				'note'       => $note,
				'extra'      => array( 'vendor_note' => $vendor_note ),
				'creator_id' => $creator_id,
			)
		);
	}

	/**
	 * Conditionally change status
	 *
	 * @since 1.1.0
	 */
	public function maybe_change_status() {
//		if ( $this->needs_payment() && ! empty( $this->get_due_date() ) && ( time() > strtotime( $this->get_due_date() ) ) ) {
//			$this->set_status( 'overdue' );
//		}

		if ( $this->is_status( 'paid' ) && empty( $this->get_payment_date() ) ) {
			$this->set_payment_date( time() );
		} else {
			$this->set_payment_date( '' );
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
		$prefix           = eaccounting()->settings->get( 'bill_prefix', 'BILL-' );
		$padd             = (int) eaccounting()->settings->get( 'bill_digit', '5' );
		$formatted_number = zeroise( absint( $number ), $padd );
		$number           = apply_filters( 'eaccounting_generate_bill_number', $prefix . $formatted_number );

		return $number;
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
	 * @return Payment[]
	 */
	public function get_payments() {
		if ( $this->exists() ) {
			return eaccounting_get_payments(
				array(
					'document_id' => $this->get_id(),
					'type'        => 'expense',
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

		//      $total_due = $this->get_total_due();
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
		$expense          = new Payment();
		$expense->set_props(
			array(
				'payment_date'   => $args['date'],
				'document_id'    => $this->get_id(),
				'account_id'     => absint( $args['account_id'] ),
				'amount'         => $converted_amount,
				'category_id'    => $this->get_category_id(),
				'vendor_id'      => $this->get_contact_id(),
				'payment_method' => eaccounting_clean( $args['payment_method'] ),
				'description'    => eaccounting_clean( $args['description'] ),
				'reference'      => sprintf( __( 'Bill Payment #%d', 'wp-ever-accounting' ), $this->get_id() ),//phpcs:ignore
			)
		);

		$expense->save();
		$methods = eaccounting_get_payment_methods();
		$method  = $methods[ $expense->get_payment_method() ];
		/* translators: %s amount */
		$this->add_note( sprintf( __( 'Paid %1$s by %2$s', 'wp-ever-accounting' ), eaccounting_price( $args['amount'], $this->get_currency_code() ), $method ), false );
		wp_cache_flush();

		if ( ( 0 < $this->get_total_paid() ) && ( $this->get_total_paid() < $this->get_total() ) ) {
			$this->set_status( 'partial' );
		} elseif ( $this->get_total_paid() >= $this->get_total() ) { // phpcs:ignore
			$this->set_status( 'paid' );
		}

		return true;
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
				do_action( 'eaccounting_bill_status_' . $status_transition['to'], $this->get_id(), $this );

				if ( $status_transition['from'] !== $status_transition['to'] ) {
					/* translators: 1: old order status 2: new order status */
					$transition_note = sprintf( __( 'Status changed from %1$s to %2$s.', 'wp-ever-accounting' ), $status_transition['from'], $status_transition['to'] );

					// Note the transition occurred.
					$this->add_note( $transition_note, false );

					do_action( 'eaccounting_bill_status_' . $status_transition['from'] . '_to_' . $status_transition['to'], $this->get_id(), $this );
					do_action( 'eaccounting_bill_status_changed', $this->get_id(), $status_transition['from'], $status_transition['to'], $this );

					// Work out if this was for a payment, and trigger a payment_status hook instead.
					if (
						in_array( $status_transition['from'], array( 'cancelled', 'pending', 'viewed', 'approved', 'overdue', 'unpaid' ), true )
						&& in_array( $status_transition['to'], array( 'paid', 'partial' ), true )
					) {
						do_action( 'eaccounting_bill_payment_status_changed', $this, $status_transition );
					}
				}
			} catch ( \Exception $e ) {
				$this->add_note( __( 'Error during status transition.', 'wp-ever-accounting' ) . ' ' . $e->getMessage() );
			}
		}
	}
}