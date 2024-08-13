<?php

namespace EverAccounting\Models;

use ByteKit\Models\Relations\BelongsTo;

defined( 'ABSPATH' ) || exit;

/**
 * Transaction model.
 *
 * @since 1.0.0
 * @author  Sultan Nasir Uddin <manikdrmc@gmail.com>
 * @package EverAccounting
 * @subpackage Models
 *
 * @property int      $id ID of the item.
 * @property string   $type Type of the transaction.
 * @property string   $number Number of the transaction.
 * @property string   $date Date of the transaction.
 * @property double   $amount Amount of the transaction.
 * @property string   $currency_code Currency code of the transaction.
 * @property double   $exchange_rate Exchange rate of the transaction.
 * @property string   $reference Reference of the transaction.
 * @property string   $note Note of the transaction.
 * @property string   $payment_method Payment method of the transaction.
 * @property int      $account_id Account ID of the transaction.
 * @property int      $document_id Document ID of the transaction.
 * @property int      $contact_id Contact ID of the transaction.
 * @property int      $category_id Category ID of the transaction.
 * @property int      $attachment_id Attachment ID of the transaction.
 * @property int      $parent_id Parent ID of the transaction.
 * @property bool     $reconciled Whether the transaction is reconciled.
 * @property string   $created_via Created via of the transaction.
 * @property int      $creator_id Author ID of the transaction.
 * @property string   $status Status of the transaction.
 * @property string   $uuid UUID of the transaction.
 * @property string   $created_at Date the transaction was created.
 * @property string   $updated_at Date the transaction was last updated.
 *
 * @property string   $formatted_amount Formatted amount of the transaction.
 * @property Currency $currency Related currency.
 * @property Account  $account Related account.
 * @property Category $category Related category.
 * @property Contact  $contact Related contact.
 * @property Customer $customer Related customer.
 * @property Vendor   $vendor Related vendor.
 */
class Transaction extends Model {
	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_transactions';

	/**
	 * Meta type declaration for the object.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $meta_type = 'ea_transaction';

	/**
	 * Table columns.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'type',
		'number',
		'date',
		'amount',
		'currency_code',
		'exchange_rate',
		'reference',
		'note',
		'payment_method',
		'account_id',
		'document_id',
		'contact_id',
		'category_id',
		'attachment_id',
		'parent_id',
		'reconciled',
		'status',
		'uuid',
		'created_via',
		'creator_id',
	);

	/**
	 * The attributes of the model.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $attributes = array(
		'status'        => '',
		'exchange_rate' => 1,
		'created_via'   => 'manual',
		'reconciled'    => false,
	);

	/**
	 * The attributes that should be cast.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array(
		'id'            => 'int',
		'amount'        => 'float',
		'exchange_rate' => 'double',
		'account_id'    => 'int',
		'document_id'   => 'int',
		'contact_id'    => 'int',
		'category_id'   => 'int',
		'attachment_id' => 'int',
		'parent_id'     => 'int',
		'reconciled'    => 'bool',
		'creator_id'    => 'int',
	);

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $appends = array(
		'formatted_number',
		'formatted_amount',
	);

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $has_timestamps = true;

	/**
	 * The attributes that are searchable.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $searchable = array(
		'number',
		'note',
	);

	/**
	 * Create a new model instance.
	 *
	 * @param string|array|object $attributes The model attributes.
	 */
	public function __construct( $attributes = array() ) {
		$this->attributes['uuid']          = wp_generate_uuid4();
		$this->attributes['currency_code'] = eac_get_base_currency();
		parent::__construct( $attributes );
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

	/**
	 * Get transaction types.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_types() {
		return apply_filters(
			'ever_accounting_transaction_types',
			array(
				'payment' => esc_html__( 'Payment', 'wp-ever-accounting' ),
				'expense' => esc_html__( 'Expense', 'wp-ever-accounting' ),
			)
		);
	}

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
	 * Returns the formatted number.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_number() {
		$number = empty( $this->number ) ? $this->get_next_number() : $this->number;
		$prefix = strtoupper( substr( $this->type, 0, 3 ) ) . '-';
		$next   = str_pad( $number, 4, '0', STR_PAD_LEFT );

		return $prefix . $next;
	}

	/**
	 * Returns the formatted amount.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_amount() {
		return eac_format_amount( $this->amount, $this->currency_code );
	}

	/**
	 * Get formatted address.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_address() {
		if ( empty( $this->contact ) ) {
			return '';
		}

		// return eac_get_formatted_address(
		// array(
		// 'name'      => $contact->get_name(),
		// 'company'   => $contact->get_company(),
		// 'address_1' => $contact->get_address_1(),
		// 'address_2' => $contact->get_address_2(),
		// 'city'      => $contact->get_city(),
		// 'state'     => $contact->get_state(),
		// 'postcode'  => $contact->get_postcode(),
		// 'country'   => $contact->get_country(),
		// )
		// );
	}

	/**
	 * Related currency.
	 *
	 * @since 1.2.1
	 * @return BelongsTo
	 */
	protected function currency() {
		return $this->belongs_to( Currency::class, 'currency_code', 'code' );
	}

	/**
	 * Transaction related account.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function account() {
		return $this->belongs_to( Account::class );
	}

	/**
	 * Transaction related category.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function category() {
		return $this->belongs_to( Category::class );
	}

	/**
	 * Transaction related contact.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function customer() {
		return $this->belongs_to( Customer::class, 'contact_id' );
	}

	/**
	 * Transaction related contact.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function vendor() {
		return $this->belongs_to( Vendor::class, 'contact_id' );
	}

	/**
	 * Document relation.
	 *
	 * @since 1.0.0
	 * @return BelongsTo
	 */
	public function document() {
		return $this->belongs_to( Document::class );
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
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|static WP_Error on failure, or the object on success.
	 */
	public function save() {
		if ( empty( $this->account_id ) ) {
			return new \WP_Error( 'missing_required', __( 'Account ID is required.', 'wp-ever-accounting' ) );
		}

		// If the account_id is changed, update the currency code.
		if ( $this->is_dirty( 'account_id' ) || ! $this->exists() ) {
			$account = Account::find( $this->account_id );

			$this->attributes['currency_code'] = $account ? $account->currency_code : eac_get_base_currency();
		}

		// If currency code is changed, update the currency rate.
		if ( $this->is_dirty( 'currency_code' ) || ! $this->exists() ) {
			$currency = Currency::find( $this->currency_code );

			$this->attributes['exchange_rate'] = $currency ? $currency->exchange_rate : 1;
		}

		// check if the number is empty.
		if ( empty( $this->number ) ) {
			$this->number = $this->get_next_number();
		}

		if ( empty( $this->uuid ) ) {
			$this->uuid = wp_generate_uuid4();
		}

		if ( empty( $this->creator_id ) && is_user_logged_in() ) {
			$this->creator_id = get_current_user_id();
		}

		return parent::save();
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
	 * Get max voucher number.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_max_number() {
		global $wpdb;
		$max = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT `number` FROM {$wpdb->prefix}{$this->table} WHERE `type` = %s ORDER BY `number` DESC",
				$this->type
			)
		);

		// Get the number part of the max number using regex.
		return (int) preg_replace( '/[^0-9]/', '', $max );
	}

	/**
	 * Set next transaction number.
	 *
	 * @since 1.0.0
	 * @return int
	 */
	public function get_next_number() {
		$max = (int) $this->get_max_number();
		return $max + 1;
	}
}
