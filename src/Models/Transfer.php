<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Transfer model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int    $id ID of the item.
 * @property int    $payment_id Payment ID of the item.
 * @property int    $expense_id Expense ID of the transfer.
 * @property double $amount Amount of the transfer.
 * @property string $uuid UUID of the transfer.
 * @property int    $creator_id Creator ID of the transfer.
 * @property string $date_created Date the transfer was created.
 * @property string $date_updated Date the transfer was last updated.
 *
 * @property int    $from_account_id From account ID of the transfer.
 * @property int    $to_account_id To account ID of the transfer.
 * @property string $currency_code Currency code of the transfer.
 * @property float  $exchange_rate Exchange rate of the transfer.
 * @property string $date Date of the transfer.
 * @property string $payment_method Payment method of the transfer.
 */
class Transfer extends Model {
	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_transfers';

	/**
	 * Table columns.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'income_id',
		'expense_id',
		'author_id',
		'uuid',
	);

	/**
	 * Model's data container.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
		'from_account_id' => null,
		'to_account_id'   => null,
		'amount'          => 0.00,
		'currency_code'   => null,
		'exchange_rate'   => 1,
		'date'            => null,
		'payment_method'  => '',
	);

	/**
	 * Model's casts data.
	 *
	 * @since 1.0.0
	 * @return bool
	 */
	protected $casts = array(
		'from_account_id' => 'int',
		'to_account_id'   => 'int',
		'amount'          => 'float',
		'exchange_rate'   => 'float',
		'date'            => 'datetime',
	);

	/**
	 * Searchable properties.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $searchable = array();

	/**
	 * Whether the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	public $timestamps = true;

	/**
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 */
	public function save() {
		if ( empty( $this->from_account_id ) ) {
			return new \WP_Error( 'missing_required', __( 'From account is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->to_account_id ) ) {
			return new \WP_Error( 'missing_required', __( 'To account is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->amount ) ) {
			return new \WP_Error( 'missing_required', __( 'Transfer amount is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->date ) ) {
			return new \WP_Error( 'missing_required', __( 'Transfer date is required.', 'wp-ever-accounting' ) );
		}
		if ( empty( $this->payment_method ) ) {
			return new \WP_Error( 'missing_required', __( 'Payment method is required.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->uuid ) ) {
			$this->uuid = wp_generate_uuid4();
		}

		if ( empty( $this->author_id ) && is_user_logged_in() ) {
			$this->author_id = get_current_user_id();
		}

		return parent::save();
	}

}
