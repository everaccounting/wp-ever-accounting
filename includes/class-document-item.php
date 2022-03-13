<?php
/**
 * Document Item data handler class.
 *
 * @version     1.0.2
 * @package     Ever_Accounting
 * @class       Account
 */

namespace Ever_Accounting;

defined( 'ABSPATH' ) || exit;


/**
 * Document Item class.
 */
class Document_Item extends Abstracts\Data {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $object_type = 'line_item';

	/**
	 * Table name.
	 *
	 * @since 1.1.3
	 * @var string
	 */
	protected $table = 'ea_document_items';

	/**
	 * Cache group.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $cache_group = 'ea_document_items';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $core_data = array(
		'document_id'   => null,
		'item_id'       => null,
		'item_name'     => '',
		'price'         => 0.00,
		'quantity'      => 1,
		'subtotal'      => 0.00,
		'tax_rate'      => 0.00,
		'discount'      => 0.00,
		'tax'           => 0.00,
		'total'         => 0.00,
		'currency_code' => '',
		'extra'         => array(
			'shipping'     => 0.00,
			'shipping_tax' => 0.00,
			'fees'         => 0.00,
			'fees_tax'     => 0.00,
		),
		'date_created'  => null,
	);

	/**
	 * Account constructor.
	 *
	 * @param int|document_item|object|null $document_item  document_item instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $document_item = 0 ) {
		// Call early so default data is set.
		parent::__construct();

		if ( is_numeric( $document_item ) && $document_item > 0 ) {
			$this->set_id( $document_item );
		} elseif ( $document_item instanceof self ) {
			$this->set_id( absint( $document_item->get_id() ) );
		} elseif ( ! empty( $document_item->ID ) ) {
			$this->set_id( absint( $document_item->ID ) );
		} else {
			$this->set_object_read( true );
		}

		$this->read();
	}

	/**
	 * Increment quantity.
	 *
	 * @param $increment
	 *
	 * @since 1.1.0
	 */
	public function increment_quantity( $increment ) {
		$this->set_quantity( $this->get_quantity() + $increment );
	}

	/**
	 * Calculate total.
	 *
	 * @since 1.1.0
	 */
	public function calculate_total() {
		$subtotal         = $this->get_price() * $this->get_quantity();
		$discount         = $this->get_discount();
		$subtotal_for_tax = $subtotal - $discount;
		$tax_rate         = ( $this->get_tax_rate() / 100 );
		$total_tax        = \Ever_Accounting\Helpers\Tax::calculate_tax( $subtotal_for_tax, $tax_rate );

		if ( 'tax_subtotal_rounding' !== eaccounting()->settings->get( 'tax_subtotal_rounding', 'tax_subtotal_rounding' ) ) {
			$total_tax = \Ever_Accounting\Helpers\Formatting::format_decimal( $total_tax, 2 );
		}
		if ( \Ever_Accounting\Helpers\Tax::prices_include_tax() ) {
			$subtotal -= $total_tax;
		}
		$total = $subtotal - $discount + $total_tax;
		if ( $total < 0 ) {
			$total = 0;
		}

		$this->set_subtotal( $subtotal );
		$this->set_tax( $total_tax );
		$this->set_total( $total );

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

		$requires = [ 'document_id', 'item_id', 'item_name', 'document_id', 'currency_code' ];

		foreach ( $requires as $required ) {
			if ( empty( $this->$required ) ) {
				return new \WP_Error( 'missing_required_param', sprintf( __( '%s is required', 'wp-ever-accounting' ), $required ) );
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

		// Clear cache.
		wp_cache_delete( $this->get_id(), $this->cache_group );
		wp_cache_set( 'last_changed', microtime(), $this->cache_group );

		/**
		 * Fires immediately after a document item is inserted or updated in the database.
		 *
		 * @param int $id Document Item id.
		 * @param array $data Document Item data array.
		 * @param Document_Item $document_item Document Item object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'ever_accounting_saved_' . $this->object_type, $this->get_id(), $this );

		return $this->get_id();
	}
}
