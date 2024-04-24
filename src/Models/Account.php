<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relation;

defined( 'ABSPATH' ) || exit;

/**
 * Account model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int    $id ID of the category.
 * @property string $name Name of the category.
 * @property string $type Type of the account.
 * @property string $number Account number.
 * @property float  $opening_balance Opening balance.
 * @property string $bank_name Bank name.
 * @property string $bank_phone Bank phone.
 * @property string $bank_address Bank address.
 * @property string $currency_code Currency code.
 * @property int    $author_id Author ID.
 * @property int    $thumbnail_id Thumbnail ID.
 * @property string $status Status of the account.
 * @property string $uuid UUID of the account.
 * @property string $date_created Date created.
 * @property string $date_updated Date updated.
 */
class Account extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_accounts';

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
		'number',
		'opening_balance',
		'bank_name',
		'bank_phone',
		'bank_address',
		'currency_code',
		'thumbnail_id',
		'author_id',
		'status',
		'uuid'
	);

	/**
	 * Model's data container.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
		'balance' => null,
	);

	/**
	 * Model's casts data.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	protected $casts = array(
		'id'              => 'int',
		'opening_balance' => 'float',
		'thumbnail_id'    => 'int',
		'author_id'       => 'int',
	);

	/**
	 * The properties that should be hidden from array/json.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $hidden = array(
		'opening_balance',
	);

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $appends = array(
		'balance',
	);

	/**
	 * Searchable properties.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $searchable = array(
		'name',
		'bank_name'
	);

	/**
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	public $timestamps = true;

	/**
	 * Get balance.
	 *
	 * @since 1.1.0
	 * @since 1.0.2
	 *
	 * @return float|string
	 */
	public function get_balance_prop() {
		if ( null !== $this->get_prop_value( 'balance' ) ) {
			return $this->get_prop_value( 'balance' );
		}
		global $wpdb;
		$transaction_total = (float) $wpdb->get_var(
			$wpdb->prepare( "SELECT SUM(CASE WHEN type='income' then amount WHEN type='expense' then - amount END) as total from {$wpdb->prefix}ea_transactions WHERE account_id=%d", $this->id )
		);
		$balance           = $this->opening_balance + $transaction_total;
		$this->set_prop_value( 'balance', $balance );

		return $balance;
	}

	/**
	 * Get the currency.
	 *
	 * @since 1.0.0
	 * @return Relation
	 */
	public function currency() {
		return $this->has_one( Currency::class, 'code', 'currency_code' );
	}

	/**
	 * Transaction relation.
	 *
	 * @since 1.0.0
	 * @return Relation
	 */
	public function transactions() {
		return $this->has_many( Transaction::class, 'account_id' );
	}
}
