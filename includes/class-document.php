<?php
/**
 * Document data handler class.
 *
 * @version     1.0.2
 * @package     EverAccounting
 * @class       Document
 */

namespace EverAccounting;

use EverAccounting\Models\Document_Item;

defined( 'ABSPATH' ) || exit;

/**
 * Document class.
 */
class Document extends Data {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $object_type = 'document';

	/**
	 * Table name.
	 *
	 * @since 1.1.3
	 * @var string
	 */
	protected $table = 'ea_documents';

	/**
	 * Meta type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $meta_type = false;

	/**
	 * Cache group.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $cache_group = 'ea_documents';


	/**
	 * Core data for this object. Name value pairs (name + default value).
	 *
	 * @since 1.1.3
	 * @var array
	 */
	protected $core_data = [
		'document_number' => '',
		'type'            => '',
		'order_number'    => '',
		'status'          => 'draft',
		'issue_date'      => null,
		'due_date'        => null,
		'payment_date'    => null,
		'category_id'     => null,
		'contact_id'      => null,
		'address'         => array(
			'name'       => '',
			'company'    => '',
			'street'     => '',
			'city'       => '',
			'state'      => '',
			'postcode'   => '',
			'country'    => '',
			'email'      => '',
			'phone'      => '',
			'vat_number' => '',
		),
		'discount'        => 0.00,
		'discount_type'   => 'percentage',
		'subtotal'        => 0.00,
		'total_tax'       => 0.00,
		'total_discount'  => 0.00,
		'total_fees'      => 0.00,
		'total_shipping'  => 0.00,
		'total'           => 0.00,
		'tax_inclusive'   => 1,
		'note'            => '',
		'terms'           => '',
		'attachment_id'   => null,
		'currency_code'   => null,
		'currency_rate'   => 1,
		'key'             => null,
		'parent_id'       => null,
		'creator_id'      => null,
		'date_created'    => null,
	];

	/**
	 * document items will be stored here, sometimes before they persist in the DB.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * document items that need deleting are stored here.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $items_to_delete = array();

	/**
	 * Document constructor.
	 *
	 * @param int|document|object|null $document document instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $document = 0 ) {
		// Call early so default data is set.
		parent::__construct();

		if ( is_numeric( $document ) && $document > 0 ) {
			$this->set_id( $document );
		} elseif ( $document instanceof self ) {
			$this->set_id( absint( $document->get_id() ) );
		} elseif ( ! empty( $document->ID ) ) {
			$this->set_id( absint( $document->ID ) );
		} else {
			$this->set_object_read( true );
		}

		$this->read();
	}

	/**
	 * Get all class data in array format.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function get_data() {
		return $this->to_array( array_merge(
				parent::get_data(),
				array(
					'items' => $this->get_items(),
				)
			)

		);
	}

	/**
	 * Get supported statuses
	 *
	 * @return array
	 * @since 1.1.0
	*/
	public function get_statuses() {
		return array();
	}


	/*
	|--------------------------------------------------------------------------
	| Non CRUD getter & Setter
	|--------------------------------------------------------------------------
	|
	*/

	/**
	 * Get invoice status nice name.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed|string
	 */
	public function get_status_nicename() {
		return isset( $this->get_statuses()[ $this->get_status() ] ) ? $this->get_statuses()[ $this->get_status() ] : $this->get_status();
	}

	public function get_formatted_address() {

	}

	/**
	 * Get item ids.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function get_item_ids() {
		$ids = array();
		foreach ( $this->get_items() as $item ) {
			$ids[] = $item->get_id();
		}

		return array_filter( $ids );
	}

	/**
	 * Get the invoice items.
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	public function get_taxes() {
		$taxes = array();
		if ( ! empty( $this->get_items() ) ) {
			foreach ( $this->get_items() as $item ) {
				$taxes[] = array(
					'line_id' => $item->get_item_id(),
					'rate'    => $item->get_tax_rate(),
					'amount'  => $item->get_tax(),
				);
			}
		}

		return $taxes;
	}


	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete documents from the database.
	| Written in abstract fashion so that the way documents are stored can be
	| changed more easily in the future.
	|
	| A save method is included for convenience (chooses update or create based
	| on if the order exists yet).
	|
	*/
	public function delete( $args = array() ) {
		Documents::delete_notes(  $this );
		Documents::delete_items( $this );
		Documents::delete_transactions( $this );
		parent::delete( $args );
	}

	/**
	 * Saves an object in the database.
	 *
	 * @return \WP_Error|int id on success, WP_Error on failure.
	 * @since 1.1.3
	 */
	public function save() {
		// check if anything missing before save.
		if ( ! $this->is_date_valid( $this->date_created ) ) {
			$this->date_created = current_time( 'mysql' );
		}

		$requires = [ 'currency_code', 'category_id', 'contact_id', 'issue_date', 'due_date' ];
		foreach ( $requires as $required ) {
			if ( empty( $this->$required ) ) {
				return new \WP_Error( 'missing_required_prop', sprintf( __( '%s is required', 'wp-ever-accounting' ), $required ) );
			}
		}

		if ( ! $this->exists() ) {
			$is_error = $this->create();
		} else {
			$is_error = $this->update();
		}

		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		$this->apply_changes();

		$this->save_items();

		// Clear cache.
		wp_cache_delete( $this->get_id(), $this->cache_group );
		wp_cache_set( 'last_changed', microtime(), $this->cache_group );

		/**
		 * Fires immediately after a contact is inserted or updated in the database.
		 *
		 * @param int $id Document id.
		 * @param array $data Document data array.
		 * @param Contact $document Document object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'eaccounting_saved_' . $this->object_type, $this->get_id(), $this );

		return $this->get_id();
	}

	/**
	 * Save all document items which are part of this order
	 *
	 * @since 1.1.0
	*/
	protected function save_items() {
		foreach ( $this->items_to_delete as $item ) {
			if( $item->exists() ) {
				$item->delete();
			}
		}

		$this->items_to_delete = array();

		$items = array_filter( $this->items );

		// Add/save items
		foreach ( $items as $item ) {
			$item->set_document_id( $this->get_id() );
			$item->set_currency_code( $this->get_currency_code() );
			$item->save();
		}
	}

	/**
	 * Delete notes.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function delete_notes() {
		if ( $this->exists() ) {
			Documents::delete_notes( $this );
		}
	}

	/**
	 * Delete all transactions.
	 *
	 * @since 1.1.0
	 */
	public function delete_payments() {
		if ( $this->exists() ) {
			Documents::delete_transactions( $this );
		}
	}

	/**
	 * Get tax inclusive or not.
	 *
	 * @param string $context
	 *
	 * @since 1.1.0
	 *
	 * @return mixed|null
	 */
	public function get_tax_inclusive() {
		if ( ! $this->exists() ) {
			return eaccounting_prices_include_tax();
		}

		return $this->get_prop( 'tax_inclusive' );
	}

	/**
	 * set the completed at.
	 *
	 * @param string $payment_date .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_payment_date( $payment_date ) {
		if ( $payment_date && $this->is_paid() ) {
			$this->set_date_prop( 'payment_date', $payment_date );
		}
	}

	/**
	 * set the status.
	 *
	 * @param string $status .
	 *
	 * @since  1.1.0
	 *
	 * @return string[]
	 */
	public function set_status( $status ) {
		$old_status = $this->get_status();
		// If setting the status, ensure it's set to a valid status.
		if ( true === $this->object_read ) {
			// Only allow valid new status.
			if ( ! array_key_exists( $status, $this->get_statuses() ) ) {
				$status = 'draft';
			}

			// If the old status is set but unknown (e.g. draft) assume its pending for action usage.
			if ( $old_status && ! array_key_exists( $old_status, $this->get_statuses() ) ) {
				$old_status = 'draft';
			}
		}

		$this->set_prop( 'status', $status );

		return array(
			'from' => $old_status,
			'to'   => $status,
		);
	}

	/**
	 * set the discount type.
	 *
	 * @param float $discount_type .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_discount_type( $discount_type ) {
		if ( in_array( $discount_type, array( 'percentage', 'fixed' ), true ) ) {
			$this->set_prop( 'discount_type', $discount_type );
		}
	}

	/**
	 * set the subtotal.
	 *
	 * @param float $subtotal .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_subtotal( $subtotal ) {
		$this->set_prop( 'subtotal', eaccounting_format_decimal( $subtotal, 4 ) );
	}

	/**
	 * set the tax.
	 *
	 * @param float $tax .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_total_tax( $tax ) {
		$this->set_prop( 'total_tax', eaccounting_format_decimal( $tax, 4 ) );
	}

	/**
	 * set the tax.
	 *
	 * @param float $discount .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_total_discount( $discount ) {
		$this->set_prop( 'total_discount', eaccounting_format_decimal( $discount, 4 ) );
	}

	/**
	 * set the fees.
	 *
	 * @param float $fees .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_total_fees( $fees ) {
		$this->set_prop( 'total_fees', eaccounting_format_decimal( $fees, 4 ) );
	}

	/**
	 * set the shipping.
	 *
	 * @param float $shipping .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_total_shipping( $shipping ) {
		$this->set_prop( 'total_shipping', eaccounting_format_decimal( $shipping, 4 ) );
	}

	/**
	 * set the total.
	 *
	 * @param float $total .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_total( $total ) {
		$this->set_prop( 'total', eaccounting_format_decimal( $total, 4 ) );
	}

	/**
	 * set the currency code.
	 *
	 * @param string $currency_code .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_currency_code( $currency_code ) {
		if ( eaccounting_sanitize_currency_code( $currency_code ) ) {
			$this->set_prop( 'currency_code', eaccounting_clean( $currency_code ) );
		}

		if ( $this->get_currency_code() && ( ! $this->exists() || array_key_exists( 'currency_code', $this->changes ) ) ) {
			$currency = eaccounting_get_currency( $this->get_currency_code() );
			$this->set_currency_rate( $currency->get_rate() );
		}
	}

	/**
	 * set the currency rate.
	 *
	 * @param double $currency_rate .
	 *
	 * @since  1.1.0
	 *
	 */
	public function set_currency_rate( $currency_rate ) {
		if ( ! empty( $currency_rate ) ) {
			$this->set_prop( 'currency_rate', eaccounting_format_decimal( $currency_rate, 7 ) );
		}
	}

	/**
	 * set the address.
	 *
	 * @param int $address .
	 *
	 * @since  1.1.0
	 */
	public function set_address( $address ) {
		$this->set_prop( 'address', maybe_unserialize( $address ) );
	}

	/**
	 * Delete items.
	 * @since 1.1.3
	 */
	public function delete_items() {
		if ( $this->exists() ) {
			$this->repository->delete_items( $this );
			$this->items = array();
		}
	}

	/**
	 * Get the invoice items.
	 *
	 * @since 1.1.0
	 *
	 *
	 * @return Document_Item[]
	 */
	public function get_items() {
		if ( $this->exists() && empty( $this->items ) ) {
			$items      = Documents::get_items( $this );
			$removables = array_keys( $this->items_to_delete );
			foreach ( $items as $line_id => $item ) {
				if ( ! in_array( $item->get_id(), $removables, true ) ) {
					$this->items[ $line_id ] = $item;
				}
			}
		}

		return $this->items;
	}

	/**
	 * @param      $item_id
	 *
	 * @since 1.1.0
	 *
	 * @return Document_Item|int
	 */
	public function get_item( $item_id ) {
		$items = $this->get_items();
		if ( empty( absint( $item_id ) ) ) {
			return false;
		}

		foreach ( $items as $item ) {
			if ( $item->get_id() === absint( $item_id ) ) {
				return $item;
			}
		}

		return false;
	}

	/**
	 * Set the document items.
	 *
	 * @param array|Document_Item[] $items items.
	 * @param bool $append
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_items( $items, $append = false ) {
		// Ensure that we have an array.
		if ( ! is_array( $items ) ) {
			return;
		}
		// Remove existing items.
		$old_items = $this->get_items();
		$new_ids   = array();
		foreach ( $items as $item ) {
			$new_ids[] = $this->add_item( $item );
		}

		if ( ! $append ) {
			$new_ids         = array_values( array_filter( $new_ids ) );
			$old_item_ids    = array_keys( $old_items );
			$remove_item_ids = array_diff( $old_item_ids, $new_ids );
			foreach ( $remove_item_ids as $remove_item_id ) {
				$this->items_to_delete[] = $old_items[ $remove_item_id ];
				unset( $this->items[ $remove_item_id ] );
			}
		}
	}

	/**
	 * Remove item from the order.
	 *
	 * @param int $item_id Item ID to delete.
	 *
	 * @param bool $by_line_id
	 *
	 * @return false|void
	 */
	public function remove_item( $item_id ) {
		if ( empty( $item_id ) ) {
			return false;
		}

		$item = $this->get_item( $item_id );

		if ( ! $item ) {
			return false;
		}

		// Unset and remove later.
		$this->items_to_delete[] = $item;
		unset( $this->items[ $item_id ] );
	}

	/*
	|--------------------------------------------------------------------------
	| Conditionals
	|--------------------------------------------------------------------------
	|
	| Checks if a condition is true or false.
	|
	*/
	/**
	 * Checks if the invoice has a given status.
	 *
	 * @param $status
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public function is_status( $status ) {
		return $this->get_status() === eaccounting_clean( $status );
	}

	/**
	 * Checks if an order can be edited, specifically for use on the Edit Order screen.
	 *
	 * @return bool
	 */
	public function is_editable() {
		return ! in_array( $this->get_status(), array( 'partial', 'paid' ), true );
	}

	/**
	 * Returns if an order has been paid for based on the order status.
	 *
	 * @since 1.10
	 * @return bool
	 */
	public function is_paid() {
		return $this->is_status( 'paid' );
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
	 * Checks if the invoice is due.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_due() {
		$due_date = $this->get_due_date();

		return empty( $due_date ) || $this->is_paid() ? false : strtotime( date_i18n( 'Y-m-d 23:59:00' ) ) > strtotime( date_i18n( 'Y-m-d 23:59:00', strtotime( $due_date ) ) ); //phpcs:ignore
	}

	/**
	 * Check if tax inclusive or not.
	 *
	 * @since 1.1.0
	 *
	 * @return mixed|null
	 */
	public function is_tax_inclusive() {
		return ! empty( $this->get_tax_inclusive() );
	}

	/**
	 * Get the type of discount.
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	public function is_fixed_discount() {
		return 'percentage' !== $this->get_discount_type();
	}

	/**
	 * Check if an key is valid.
	 *
	 * @param string $key Order key.
	 *
	 * @return bool
	 */
	public function is_key_valid( $key ) {
		return $key === $this->get_key( 'edit' );
	}

	/**
	 * Checks if an order needs payment, based on status and order total.
	 *
	 * @return bool
	 */
	public function needs_payment() {
		return ! $this->is_status( 'paid' ) && $this->get_total() > 0;
	}
}
