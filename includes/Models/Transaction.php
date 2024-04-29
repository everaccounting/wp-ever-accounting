<?php

namespace EverAccounting\Models;

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
 * @property int      $author_id Author ID of the transaction.
 * @property string   $status Status of the transaction.
 * @property string   $uuid UUID of the transaction.
 * @property string   $date_created Date the transaction was created.
 * @property string   $date_updated Date the transaction was last updated.
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
	 * Meta type declaration for the object.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $meta_type = 'ea_transaction';

	/**
	 * The table associated with the model.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'ea_transactions';

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
		'author_id',
	);

	/**
	 * Model's data that aren't mass assignable.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $guarded = array(
		'currency_code',
		'type',
	);

	/**
	 * The model's attributes.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $attributes = array(
		'status'        => 'draft',
		'type'          => 'income',
		'exchange_rate' => 1,
		'created_via'   => 'manual',
	);

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @var array
	 */
	protected $casts = array(
		'id'            => 'int',
		'amount'        => 'float',
		'currency_rate' => 'float',
		'account_id'    => 'int',
		'document_id'   => 'int',
		'contact_id'    => 'int',
		'category_id'   => 'int',
		'attachment_id' => 'int',
		'parent_id'     => 'int',
		'reconciled'    => 'bool',
		'author_id'     => 'int',
	);

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $appends = array(
		'formatted_amount',
	);

	/**
	 * Searchable attributes.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $searchable = array(
		'number',
		'note',
	);

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
	 * @param string|int|array $attributes Attributes.
	 *
	 * @throws \InvalidArgumentException If table name or object type is not set.
	 * @return void
	 */
	public function __construct( $attributes = null ) {
		$this->attributes['uid']           = wp_generate_uuid4();
		$this->attributes['currency_code'] = eac_get_base_currency();
		parent::__construct( $attributes );
	}

	/*
	|--------------------------------------------------------------------------
	| Attributes & Relations
	|--------------------------------------------------------------------------
	| Define the attributes and relations of the model.
	*/

	/**
	 * Returns the formatted amount.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_amount_attribute() {
		return eac_format_money( $this->amount, $this->currency_code );
	}

	/**
	 * Get formatted address.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	protected function get_formatted_address_attribute() {
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
	 * @return \ByteKit\Models\Relation
	 */
	protected function currency() {
		return $this->belongs_to( Currency::class, 'currency_code', 'code' );
	}

	/**
	 * Transaction related account.
	 *
	 * @since 1.0.0
	 * @return \ByteKit\Models\Relation
	 */
	public function account() {
		return $this->belongs_to( Account::class );
	}

	/**
	 * Transaction related category.
	 *
	 * @since 1.0.0
	 * @return \ByteKit\Models\Relation
	 */
	protected function category() {
		return $this->belongs_to( Category::class );
	}

	/**
	 * Transaction related contact.
	 *
	 * @since 1.0.0
	 * @return \ByteKit\Models\Relation
	 */
	protected function customer() {
		return $this->belongs_to( Customer::class, 'contact_id' );
	}

	/**
	 * Transaction related contact.
	 *
	 * @since 1.0.0
	 * @return \ByteKit\Models\Relation
	 */
	protected function vendor() {
		return $this->belongs_to( Vendor::class, 'contact_id' );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	| The following methods are used to create, read, update and delete the object.
	*/

	/**
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 */
	public function save() {
		if ( empty( $this->account_id ) ) {
			return new \WP_Error( 'missing_required', __( 'Account ID is required.', 'wp-ever-accounting' ) );
		}

		// If the account_id is changed, update the currency code.
		if ( $this->is_attribute_changed( 'account_id' ) || ! $this->exists() ) {
			$account = Account::find( $this->account_id );
			$this->set_attribute_value( 'currency_code', $account ? $account->currency_code : 'USD' );
		}

		// If currency code is changed, update the currency rate.
		if ( $this->is_attribute_changed( 'currency_code' ) || ! $this->exists() ) {
			$currency = Currency::find( array( 'code' => $this->currency_code ) );
			$this->set_attribute_value( 'currency_rate', $currency ? $currency->exchange_rate : 1 );
		}

		if ( empty( $this->number ) ) {
			$this->number = $this->get_next_number();
		}

		if ( empty( $this->uuid ) ) {
			$this->uuid = wp_generate_uuid4();
		}

		if ( empty( $this->author_id ) && is_user_logged_in() ) {
			$this->author_id = get_current_user_id();
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
	 * Get max voucher number.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_max_number() {
		return (int) $this->wpdb()->get_var(
			$this->wpdb()->prepare(
				"SELECT MAX(REGEXP_REPLACE(number, '[^0-9]', '')) FROM {$this->wpdb()->prefix}{$this->get_table()} WHERE type = %s",
				$this->type
			)
		);
	}

	/**
	 * Set next transaction number.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_next_number() {
		$max    = $this->get_max_number();
		$prefix = strtoupper( substr( $this->type, 0, 3 ) ) . '-';
		$next   = str_pad( $max + 1, 4, '0', STR_PAD_LEFT );

		return $prefix . $next;
	}
}
