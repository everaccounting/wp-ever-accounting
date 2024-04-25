<?php

namespace EverAccounting\Models;

/**
 * Tax model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int    $id ID of the tax.
 * @property string $name Name of the tax.
 * @property double $rate Rate of the tax.
 * @property bool   $is_compound Whether the tax is compound.
 * @property string $description Description of the tax.
 * @property string $status Status of the tax.
 * @property string $date_created Date the tax was created.
 * @property string $date_updated Date the tax was last updated.
 *
 * @property-read string $formatted_name Formatted name of the tax.
 */
class Tax extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_taxes';

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
		'description',
		'status',
	);

	/**
	 * Model's data container.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
		'status' => 'active',
	);

	/**
	 * Model's casts data.
	 *
	 * @since 1.0.0
	 * @var array
	 * @return bool
	 */
	protected $casts = array(
		'id'          => 'int',
		'rate'        => 'float',
		'is_compound' => 'bool',
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
	 * Searchable properties.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $searchable = array(
		'name',
	);

	/**
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	public $timestamps = true;

	/**
	 * Get formatted name.
	 *
	 * @return string
	 * @since 1.1.6
	 */
	public function get_formatted_name_prop() {
		return sprintf( '%1$s (%2$d%%)', $this->name, $this->rate );
	}

	/**
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 */
	public function save() {
		if ( empty( $this->name ) ) {
			return new \WP_Error( 'missing_required', __( 'Tax name is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->rate ) ) {
			return new \WP_Error( 'missing_required', __( 'Tax rate is required.', 'wp-ever-accounting' ) );
		}

		return parent::save();
	}
}
