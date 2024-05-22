<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relations\BelongsTo;
use ByteKit\Models\Relations\HasMany;

/**
 * DocumentLine model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int                    $id ID of the document_item.
 * @property string                 $type Type of the document_item.
 * @property string                 $name Name of the document_item.
 * @property double                 $price Price of the document_item.
 * @property double                 $quantity Quantity of the document_item.
 * @property double                 $subtotal Subtotal of the document_item.
 * @property double                 $subtotal_tax Subtotal_tax of the document_item.
 * @property double                 $discount Discount of the document_item.
 * @property double                 $discount_tax Discount Tax of the document_item.
 * @property double                 $tax_total Tax total of the document_item.
 * @property double                 $total Total of the document_item.
 * @property int                    $taxable Taxable of the document_item.
 * @property string                 $description Description of the document_item.
 * @property string                 $unit Unit of the document_item.
 * @property int                    $item_id Item ID of the document_item.
 * @property int                    $document_id Document ID of the document_item.
 * @property string                 $date_updated Date updated of the document_item.
 * @property string                 $date_created Date created of the document_item.
 *
 * @property-read double            $discounted_subtotal Discounted subtotal of the document_item.
 * @property-read DocumentLineTax[] $taxes Taxes of the document_item.
 */
class DocumentLine extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_document_lines';

	/**
	 * The table columns of the model.
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
	 * The model's data properties.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $props = array(
		'type' => 'standard',
	);

	/**
	 * The properties that should be cast.
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
	 * The accessors to append to the model's array form.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $appends = array();

	/**
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $timestamps = true;

	/**
	 * Create a new model instance.
	 *
	 * @param string|array|object $props The model attributes.
	 *
	 * @throws \InvalidArgumentException If table name or object type is not set.
	 */
	public function __construct( $props = array() ) {
		$this->props['taxable'] = filter_var( eac_tax_enabled(), FILTER_VALIDATE_BOOLEAN );
		parent::__construct( $props );
	}

	/*
	|--------------------------------------------------------------------------
	| Prop Definition Methods
	|--------------------------------------------------------------------------
	| This section contains methods that define and provide specific prop values
	| related to the model, such as statuses or types. These methods can be accessed
	| without instantiating the model.
	|--------------------------------------------------------------------------
	*/

	/*
	|--------------------------------------------------------------------------
	| Accessors, Mutators, Relationship and Validation Methods
	|--------------------------------------------------------------------------
	| This section contains methods for getting and setting properties (accessors
	| and mutators) as well as defining relationships between models. It also includes
	| a data validation method that ensures data integrity before saving.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Set name prop.
	 *
	 * @param string $value Name of the document_item.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function set_name_prop( $value ) {
		$this->props['name'] = sanitize_text_field( wp_strip_all_tags( $value ) );
	}

	/**
	 * Set description prop.
	 *
	 * @param string $value Description of the document_item.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function set_description_prop( $value ) {
		$this->props['description'] = sanitize_text_field( wp_trim_words( wp_strip_all_tags( $value ), 10 ) );
	}

	/**
	 * Get discounted subtotal.
	 *
	 * @since 1.0.0
	 * @return double
	 */
	protected function get_discounted_subtotal_prop() {
		return (float) $this->subtotal - (float) $this->discount;
	}

	/**
	 * Set tax ids.
	 *
	 * @param string $tax_ids Tax ids.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_tax_ids_props( $tax_ids ) {
		$tax_ids = wp_parse_id_list( $tax_ids );
		$taxes   = array();
		foreach ( $tax_ids as $tax_id ) {
			$taxes[] = array( 'tax_id' => $tax_id );
		}
		$this->set_taxes( $taxes );
	}

	/**
	 * Tax relationship.
	 *
	 * @since 1.0.0
	 * @return HasMany
	 */
	public function taxes() {
		return $this->has_many( DocumentLineTax::class, 'line_id' );
	}

	/**
	 * Item relationship.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function item() {
		return $this->belongs_to( Item::class );
	}

	/**
	 * Document relationship.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function document() {
		return $this->belongs_to( Document::class );
	}

	/**
	 * Validate data before saving.
	 *
	 * @since 1.0.0
	 * @return void|\WP_Error Return WP_Error if data is not valid or void.
	 */
	protected function validate_save_data() {
		if ( empty( $this->type ) ) {
			return new \WP_Error( 'required_missing', __( 'Product type is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->name ) ) {
			return new \WP_Error( 'required_missing', __( 'Product name is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->quantity ) ) {
			return new \WP_Error( 'required_missing', __( 'Product quantity is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->item_id ) ) {
			return new \WP_Error( 'required_missing', __( 'Item ID is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->document_id ) ) {
			return new \WP_Error( 'required_missing', __( 'Document ID is required.', 'wp-ever-accounting' ) );
		}
		// If the item is not taxable, then shipping and fee should not be taxable.
		if ( ! $this->taxable ) {
			$this->taxes()->delete();
		}
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD Methods
	|--------------------------------------------------------------------------
	| This section contains methods for creating, reading, updating, and deleting
	| objects in the database.
	|--------------------------------------------------------------------------
	*/
	/**
	 * Delete the object from the database.
	 *
	 * @since 1.0.0
	 * @return array|false true on success, false on failure.
	 */
	public function delete() {
		$this->taxes()->delete();

		return parent::delete();
	}

	/*
	|--------------------------------------------------------------------------
	| Helper Methods
	|--------------------------------------------------------------------------
	| This section contains utility methods that are not directly related to this
	| object but can be used to support its functionality.
	|--------------------------------------------------------------------------
	*/
	/**
	 * Set taxes.
	 *
	 * @param array $taxes Taxes.
	 *
	 * @since 1.1.6
	 * @return void
	 */
	public function set_taxes( $taxes ) {
		if ( ! is_array( $taxes ) ) {
			return;
		}
		$old_taxes = $this->taxes()->get_items();
		$this->set_relation( 'taxes', array() );
		foreach ( $taxes as $tax_data ) {
			// Load items before call this method. Otherwise, it will cause mis calculation.
			$default = array(
				'id'          => 0,
				'tax_id'      => 0,
				'item_id'     => $this->id,
				'document_id' => $this->document_id,
				'is_compound' => 'no',
			);

			if ( is_object( $tax_data ) ) {
				$tax_data = is_callable( array( $tax_data, 'to_array' ) ) ? $tax_data->to_array() : (array) $tax_data;
			}

			if ( empty( $tax_data['id'] ) && empty( $tax_data['tax_id'] ) ) {
				return;
			}

			if ( empty( $tax_data['id'] ) && ! empty( $tax_data['tax_id'] ) ) {
				$tax      = Tax::make( $tax_data['tax_id'] );
				$tax_data = wp_array_slice_assoc( $tax->to_array(), array( 'name', 'rate', 'is_compound' ) );
				$default  = wp_parse_args( $tax_data, $default );
			}

			$tax_data = wp_parse_args( $tax_data, $default );
			$line_tax = DocumentLineTax::make( $tax_data );

			if ( ! $line_tax->tax_id ) {
				continue;
			}

			foreach ( $old_taxes as $key => $old_tax ) {
				if ( $old_tax->is_similar( $line_tax ) ) {
					unset( $old_taxes[ $key ] );
					break;
				}
			}

			// skip if the tax is already added.
			foreach ( $this->taxes as $old_line_tax ) {
				if ( $old_line_tax->tax_id === $line_tax->tax_id ) {
					continue;
				}
			}

			$this->set_relation(
				'taxes',
				function ( $relation ) use ( $line_tax ) {
					$relation   = is_array( $relation ) ? $relation : array( $relation );
					$relation[] = $line_tax;

					return $relation;
				}
			);
		}

		foreach ( $old_taxes as $old_tax ) {
			$old_tax->delete();
		}
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
			$tax_rates    = eac_calculate_taxes( $subtotal, $this->taxes, $tax_inclusive );
			$subtotal_tax = array_sum( wp_list_pluck( $tax_rates, 'amount' ) );
			$subtotal    -= $subtotal_tax;
		}

		$this->subtotal = $subtotal;
	}

	/**
	 * Is similar.
	 *
	 * @param DocumentLine $line Item to compare.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_similar( $line ) {
		return $this->item_id === $line->item_id &&
				$this->type === $line->type &&
				$this->name === $line->name &&
				$this->unit === $line->unit &&
				$this->price === $line->price &&
				$this->taxable === $line->taxable &&
				wp_list_pluck( $this->taxes, 'id' ) === wp_list_pluck( $this->taxes, 'id' );
	}

	/**
	 * Merge two items.
	 *
	 * @param DocumentLine $line Item to merge.
	 *
	 * @since 1.1.0
	 * @return static|false Merged item or false on failure.
	 */
	public function merge( $line ) {
		// If the item's properties are same, then merge and increase the quantity.

		if ( $this->is_similar( $line ) ) {
			$this->quantity += $line->quantity;
			$this->calculate_subtotal( $this->tax_inclusive );

			return $this;
		}

		return false;
	}
}
