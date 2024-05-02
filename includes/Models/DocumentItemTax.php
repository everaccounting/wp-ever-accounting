<?php

namespace EverAccounting\Models;

/**
 * DocumentItemTax model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int    $id ID of the document_item_tax.
 * @property string $name Name of the document_item_tax.
 * @property double $rate Rate of the document_item_tax.
 * @property bool   $is_compound Compound of the document_item_tax.
 * @property double $amount Amount of the document_item_tax.
 * @property int    $item_id Item ID of the document_item_tax.
 * @property int    $tax_id Tax ID of the document_item_tax.
 * @property int    $document_id Document ID of the document_item_tax.
 * @property string $date_updated Date updated of the document_item_tax.
 * @property string $date_created Date created of the document_item_tax.
 *
 * @property-read string $formatted_name Formatted name of the document_item_tax.
 */
class DocumentItemTax extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_document_item_taxes';

	/**
	 * Table columns.
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
		'item_id',
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
		'item_id'     => 'int',
		'tax_id'      => 'int',
		'document_id' => 'int',
	);

	/**
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $timestamps = true;

	/*
	|--------------------------------------------------------------------------
	| Attributes & Relations
	|--------------------------------------------------------------------------
	| Define the attributes and relations of the model.
	*/

	/**
	 * Get the formatted name of the document_item_tax.
	 *
	 * @return string
	 * @since 1.0.0
	 */
	protected function get_formatted_name_attribute() {
		return $this->name . ' (' . $this->rate . '%)';
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
	 * @return true|\WP_Error True on success, WP_Error on failure.
	 * @since 1.0.0
	 */
	public function save() {
		// Required fields check.
		if ( empty( $this->name ) ) {
			return new \WP_Error( 'missing_required', __( 'Tax name is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->rate ) ) {
			return new \WP_Error( 'missing_required', __( 'Tax rate is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->item_id ) ) {
			return new \WP_Error( 'missing_required', __( 'Item ID is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->tax_id ) ) {
			return new \WP_Error( 'missing_required', __( 'Tax ID is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->document_id ) ) {
			return new \WP_Error( 'missing_required', __( 'Document ID is required.', 'wp-ever-accounting' ) );
		}

		return parent::save();
	}


	/*
	|--------------------------------------------------------------------------
	| Helper methods.
	|--------------------------------------------------------------------------
	| Utility methods which don't directly relate to this object but may be
	| used by this object.
	*/
	/**
	 * Is the tax similar to another tax?
	 *
	 * @param DocumentItemTax $tax The tax to compare.
	 *
	 * @return bool
	 * @since 1.1.0
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
