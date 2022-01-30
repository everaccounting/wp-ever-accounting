<?php
/**
 * Document data handler class.
 *
 * @version     1.0.2
 * @package     EverAccounting
 * @class       Document
 */

namespace EverAccounting;

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
}
