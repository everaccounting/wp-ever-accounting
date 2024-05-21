<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relations\BelongsTo;

/**
 * DocumentItemTax model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int         $id ID of the document_item_tax.
 * @property string      $name Name of the document_item_tax.
 * @property double      $rate Rate of the document_item_tax.
 * @property bool        $is_compound Compound of the document_item_tax.
 * @property double      $amount Amount of the document_item_tax.
 * @property int         $line_id Item ID of the document_item_tax.
 * @property int         $tax_id Tax ID of the document_item_tax.
 * @property int         $document_id Document ID of the document_item_tax.
 * @property string      $date_updated Date updated of the document_item_tax.
 * @property string      $date_created Date created of the document_item_tax.
 *
 * @property-read string $formatted_name Formatted name of the document_item_tax.
 * @property-read DocumentItem $item Item relationship.
 * @property-read Tax $tax Tax relationship.
 * @property-read Document $document Document relationship.
 */
class DocumentLineTax extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_document_line_taxes';

	/**
	 * The table columns of the model.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'name',
		'rate',
		'is_compound',
		'amount',
		'line_id',
		'tax_id',
		'document_id',
	);

	/**
	 * Model's property casts.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array(
		'id'          => 'int',
		'rate'        => 'double',
		'is_compound' => 'bool',
		'amount'      => 'double',
		'line_id'     => 'int',
		'tax_id'      => 'int',
		'document_id' => 'int',
	);

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $appends = array(
		'formatted_name',
	);

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $timestamps = true;

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
	 * Get the formatted name of the document_item_tax.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_name_prop() {
		return $this->name . ' (' . $this->rate . '%)';
	}

	/**
	 * Line relationship.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function line() {
		return $this->belongs_to( DocumentLine::class );
	}

	/**
	 * Tax relationship.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function tax() {
		return $this->belongs_to( Tax::class );
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
		if ( empty( $this->name ) ) {
			return new \WP_Error( 'missing_required', __( 'Tax name is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->rate ) ) {
			return new \WP_Error( 'missing_required', __( 'Tax rate is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->line_id ) ) {
			return new \WP_Error( 'missing_required', __( 'Line ID is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->tax_id ) ) {
			return new \WP_Error( 'missing_required', __( 'Tax ID is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->document_id ) ) {
			return new \WP_Error( 'missing_required', __( 'Document ID is required.', 'wp-ever-accounting' ) );
		}
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
	 * Is the tax similar to another tax?
	 *
	 * @param DocumentLineTax $tax The tax to compare.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_similar( $tax ) {
		return $this->rate === $tax->rate && $this->is_compound === $tax->is_compound;
	}

	/**
	 * Merge this tax with another tax.
	 *
	 * @param static $line_tax The tax to merge with.
	 *
	 * @since 1.1.0
	 */
	public function merge( $line_tax ) {
		if ( ! $this->is_similar( $line_tax ) ) {
			return;
		}

		$this->amount += $line_tax->amount;
	}
}
