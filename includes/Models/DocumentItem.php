<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relation;

/**
 * DocumentItem model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int    $id ID of the document_item.
 * @property string $type Type of the document_item.
 * @property string $name Name of the document_item.
 * @property double $price Price of the document_item.
 * @property double $quantity Quantity of the document_item.
 * @property double $subtotal Subtotal of the document_item.
 * @property double $subtotal_tax Subtotal_tax of the document_item.
 * @property double $discount Discount of the document_item.
 * @property double $discount_tax Discount Tax of the document_item.
 * @property double $tax_total Tax total of the document_item.
 * @property double $total Total of the document_item.
 * @property int    $taxable Taxable of the document_item.
 * @property string $description Description of the document_item.
 * @property string $unit Unit of the document_item.
 * @property int    $item_id Item ID of the document_item.
 * @property int    $document_id Document ID of the document_item.
 * @property string $date_updated Date updated of the document_item.
 * @property string $date_created Date created of the document_item.
 */
class DocumentItem extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_document_items';

	/**
	 * Table columns.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'type',
		'name',
		'price',
		'quantity',
		'subtotal',
		'subtotal_tax',
		'discount',
		'discount_tax',
		'tax_total',
		'total',
		'taxable',
		'description',
		'unit',
		'item_id',
		'document_id',
	);

	/**
	 * The model's attributes.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $attributes = array(
		'type' => 'standard',
	);

	/**
	 * Model's property casts.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array(
		'id'           => 'int',
		'price'        => 'double',
		'quantity'     => 'double',
		'subtotal'     => 'double',
		'subtotal_tax' => 'double',
		'discount'     => 'double',
		'discount_tax' => 'double',
		'tax_total'    => 'double',
		'total'        => 'double',
		'taxable'      => 'int',
		'item_id'      => 'int',
		'document_id'  => 'int',
	);

	/**
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $timestamps = true;

	/**
	 * document taxes will be stored here, sometimes before they persist in the DB.
	 *
	 * @since 1.1.0
	 *
	 * @var DocumentItemTax[]
	 */
	protected $taxes = null;

	/**
	 * Taxes to delete.
	 *
	 * @since 1.0.0
	 *
	 * @var DocumentItemTax[]
	 */
	protected $deletable = array();

	/**
	 * Create a new model instance.
	 *
	 * @param string|int|array $attributes Attributes.
	 *
	 * @throws \InvalidArgumentException If table name or object type is not set.
	 * @return void
	 */
	public function __construct( $attributes = 0 ) {
		$this->attributes['taxable'] = filter_var( eac_tax_enabled(), FILTER_VALIDATE_BOOLEAN );
		parent::__construct( $attributes );
	}

	/**
	 * Convert the model instance to an array.
	 *
	 * @return array
	 */
	public function to_array() {
		$data          = parent::to_array();
		$data['taxes'] = array();
		foreach ( $this->get_taxes() as $tax ) {
			$data['taxes'][] = $tax->to_array();
		}

		return $data;
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	| Methods for saving, updating, and deleting objects.
	*/
	/**
	 * Saves an object in the database.
	 *
	 * @throws \Exception When the document item is invalid.
	 * @since 1.0.0
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 */
	public function save() {
		// Required fields check.
		if ( empty( $this->document_id ) ) {
			return new \WP_Error( 'required_missing', __( 'Document ID is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->item_id ) ) {
			return new \WP_Error( 'required_missing', __( 'Item ID is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->name ) ) {
			return new \WP_Error( 'required_missing', __( 'Product name is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->quantity ) ) {
			return new \WP_Error( 'required_missing', __( 'Product quantity is required.', 'wp-ever-accounting' ) );
		}

		// If the item is not taxable, then shipping and fee should not be taxable.
		if ( ! $this->taxable ) {
			$this->taxes = array();
		}

		try {
			$this->wpdb()->query( 'START TRANSACTION' );
			$saved = parent::save();
			if ( is_wp_error( $saved ) ) {
				throw new \Exception( $saved->get_error_message() );
			}

			foreach ( $this->get_taxes() as $doc_tax ) {
				$doc_tax->document_id = $this->document_id;
				$doc_tax->item_id     = $this->id;

				// check if tax is 0, set it to delete.
				// if ( empty( $doc_tax->subtotal ) && empty( $doc_tax->get_discount() ) && empty( $doc_tax->get_shipping() ) && empty( $doc_tax->get_fee() ) && empty( $doc_tax->get_total() ) ) {
				// $this->deletable[] = $doc_tax;
				// continue;
				// }

				$saved = $doc_tax->save();
				if ( is_wp_error( $saved ) ) {
					throw new \Exception( $saved->get_error_message() );
				}
			}

			// Delete taxes which are not in the current list.
			foreach ( $this->deletable as $tax ) {
				if ( $tax->exists() && ! $tax->delete() ) {
					// translators: %s: error message.
					throw new \Exception( sprintf( __( 'Error while deleting unused tax. error: %s', 'wp-ever-accounting' ), $this->wpdb()->last_error ) );
				}
			}

			$this->deletable = array();

			$this->wpdb()->query( 'COMMIT' );

			return true;
		} catch ( \Exception $e ) {
			$this->wpdb()->query( 'ROLLBACK' );

			return new \WP_Error( 'db_error', $e->getMessage() );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/

	/**
	 * Get discounted price.
	 *
	 * @return float
	 * @since 1.1.0
	 */
	public function get_discounted_price() {
		return (float) $this->subtotal - (float) $this->discount;
	}

	/**
	 * Get taxes.
	 *
	 * @since 1.0.0
	 * @return DocumentItemTax[]
	 */
	public function get_taxes() {
		if ( is_null( $this->taxes ) ) {
			$this->taxes = array();
			if ( $this->exists() ) {
				$this->taxes = DocumentItemTax::query(
					array(
						'item_id'     => $this->id,
						'document_id' => $this->document_id,
						'orderby'     => 'id',
						'order'       => 'ASC',
						'limit'       => - 1,
						'no_count'    => true,
					)
				);
			}
		}

		return $this->taxes;
	}

	/**
	 * Set taxes.
	 *
	 * @param array $taxes Taxes.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function set_taxes( $taxes ) {
		$old_taxes       = array_merge( $this->get_taxes(), $this->deletable );
		$this->taxes     = array();
		$this->deletable = array_filter(
			$old_taxes,
			function ( $old_tax ) {
				return $old_tax->exists();
			}
		);

		if ( ! is_array( $taxes ) ) {
			$taxes = wp_parse_id_list( $taxes );
		}

		foreach ( $taxes as $tax ) {
			$this->add_tax( $tax );
		}

		// Go through deletable items and if they are in the new items list, remove them from the deletable list.
		foreach ( $this->deletable as $key => $deletable ) {
			foreach ( $this->taxes as $new_tax ) {
				if ( $deletable->id === $new_tax->id ) {
					unset( $this->deletable[ $key ] );
				}
			}
		}
	}

	/**
	 * Add tax.
	 *
	 * @param int|array|DocumentItemTax $data Tax data.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function add_tax( $data ) {
		// Load items before call this method. Otherwise, it will cause mis calculation.
		$default = array(
			'id'          => 0,
			'tax_id'      => 0,
			'item_id'     => $this->id,
			'document_id' => $this->document_id,
			'name'        => '',
			'rate'        => 0,
			'is_compound' => 'no',
		);

		if ( is_object( $data ) ) {
			$data = $data instanceof \stdClass ? get_object_vars( $data ) : $data->to_array();
		} elseif ( is_numeric( $data ) ) {
			$data = array( 'tax_id' => $data );
		}

		if ( empty( $data['id'] ) && empty( $data['tax_id'] ) ) {
			return;
		}

		if ( ! empty( $data['tax_id'] ) ) {
			$tax           = Tax::find( $data['tax_id'] );
			$tax_data      = $tax ? $tax->to_array() : array();
			$accepted_keys = array( 'name', 'rate', 'is_compound' );
			$tax_data      = wp_array_slice_assoc( $tax_data, $accepted_keys );
			$data          = wp_parse_args( $data, $tax_data );
		}

		$data     = wp_parse_args( $data, $default );
		$item_tax = new DocumentItemTax( $data );

		// If tax id is not set, we will ignore the tax.
		if ( empty( $item_tax->tax_id ) ) {
			return;
		}

		// Check if the item is set to be deleted and all the data matches. If so, remove it from the deletable list and add it to the items list.
		foreach ( $this->deletable as $key => $deletable_item ) {
			if ( $deletable_item->is_similar( $item_tax ) ) {
				unset( $this->deletable[ $key ] );
				$deletable_item->fill( $data );
				$this->taxes[] = $deletable_item;

				return;
			}
		}

		// if the tax_id with same tax_id already exists, we will ignore the tax.
		foreach ( $this->taxes as $tax ) {
			if ( $tax->tax_id === $item_tax->tax_id ) {
				return;
			}
		}
		$this->taxes[] = $item_tax;
	}

	/**
	 * Get tax ids.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_tax_ids() {
		$tax_ids = array();
		foreach ( $this->get_taxes() as $tax ) {
			$tax_ids[] = $tax->id;
		}

		return implode( ',', $tax_ids );
	}

	/**
	 * Set tax ids.
	 *
	 * @param string|array $tax_ids Tax ids.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_taxes_by_ids( $tax_ids ) {
		$tax_ids = wp_parse_id_list( $tax_ids );
		$taxes   = array();
		foreach ( $tax_ids as $tax_id ) {
			$taxes[] = array( 'tax_id' => $tax_id );
		}
		$this->set_taxes( $taxes );
	}

	/**
	 * Calculate subtotal.
	 *
	 * @param bool $tax_inclusive Tax inclusive.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function calculate_subtotal( $tax_inclusive = false ) {
		$subtotal = $this->quantity * $this->price;
		if ( $tax_inclusive ) {
			$tax_rates    = eac_calculate_taxes( $subtotal, $this->get_taxes(), $tax_inclusive );
			$subtotal_tax = array_sum( wp_list_pluck( $tax_rates, 'amount' ) );
			$subtotal    -= $subtotal_tax;
		}

		$this->subtotal = $subtotal;
	}

	/**
	 * Is similar.
	 *
	 * @param DocumentItem $item Item to compare.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_similar( $item ) {
		return $this->item_id === $item->item_id &&
				$this->type === $item->type &&
				$this->name === $item->name &&
				$this->unit === $item->unit &&
				$this->price === $item->price &&
				$this->taxable === $item->taxable &&
				wp_list_pluck( $this->get_taxes(), 'id' ) === wp_list_pluck( $item->get_taxes(), 'id' );
	}

	/**
	 * Merge two items.
	 *
	 * @param DocumentItem $item Item to merge.
	 *
	 * @since 1.1.0
	 * @return static|false Merged item or false on failure.
	 */
	public function merge( $item ) {
		// If the item's properties are same, then merge and increase the quantity.

		if ( $this->is_similar( $item ) ) {
			$this->quantity += $item->quantity;
			$this->calculate_subtotal( $this->tax_inclusive );

			return $this;
		}

		return false;
	}
}
