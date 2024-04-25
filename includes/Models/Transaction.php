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
 * @property int    $id ID of the item.
 * @property string $type Type of the transaction.
 * @property string $number Number of the transaction.
 * @property string $date Date of the transaction.
 * @property double $amount Amount of the transaction.
 * @property string $currency_code Currency code of the transaction.
 * @property double $exchange_rate Exchange rate of the transaction.
 * @property string $reference Reference of the transaction.
 * @property string $note Note of the transaction.
 * @property string $payment_method Payment method of the transaction.
 * @property int    $account_id Account ID of the transaction.
 * @property int    $document_id Document ID of the transaction.
 * @property int    $contact_id Contact ID of the transaction.
 * @property int    $category_id Category ID of the transaction.
 * @property int    $transfer_id Transfer ID of the transaction.
 * @property int    $attachment_id Attachment ID of the transaction.
 * @property int    $parent_id Parent ID of the transaction.
 * @property bool   $reconciled Whether the transaction is reconciled.
 * @property string $created_via Created via of the transaction.
 * @property int    $author_id Author ID of the transaction.
 * @property string $status Status of the transaction.
 * @property string $uuid UUID of the transaction.
 * @property string $date_created Date the transaction was created.
 * @property string $date_updated Date the transaction was last updated.
 *
 * @property string $formatted_amount Formatted amount of the transaction.
 * @property \EverAccounting\Models\Currency $currency Related currency.
 * @property \EverAccounting\Models\Account $account Related account.
 * @property \EverAccounting\Models\Category $category Related category.
 * @property \EverAccounting\Models\Contact $customer Related customer.
 * @property \EverAccounting\Models\Vendor $vendor Related vendor.
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
		'transfer_id',
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
	 * Model's data container.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $data = array(
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
		'creator_id'    => 'int',
		'date_created'  => 'datetime',
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
	 * Searchable properties.
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
	public $timestamps = true;

	/**
	 * Create a new Eloquent model instance.
	 *
	 * @param string|int|array $data Data properties.
	 *
	 * @throws \InvalidArgumentException If table name or object type is not set.
	 * @return void
	 */
	public function __construct( $data = null ) {
		$this->data['type']       = $this->get_object_type();
		$this->query_args['type'] = $this->get_object_type();
		parent::__construct( $data );
	}

	/**
	 * Returns the formatted amount.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_formatted_amount_prop() {
		return eac_format_money( $this->amount, $this->currency_code );
	}

	/**
	 * Related currency.
	 *
	 * @since 1.2.1
	 * @return \ByteKit\Models\Relation
	 */
	protected function currency() {
		return $this->has_one( Currency::class, 'code', 'currency_code' );
	}

	/**
	 * Transaction related account.
	 *
	 * @since 1.0.0
	 * @return \ByteKit\Models\Relation
	 */
	protected function account() {
		return $this->has_one( Account::class, 'account_id' );
	}

	/**
	 * Transaction related category.
	 *
	 * @since 1.0.0
	 * @return \ByteKit\Models\Relation
	 */
	public function category() {
		return $this->has_one( Category::class, 'category_id' );
	}

	/**
	 * Transaction related contact.
	 *
	 * @since 1.0.0
	 * @return \ByteKit\Models\Relation
	 */
	public function customer() {
		return $this->belongs_to( Contact::class, 'contact_id' );
	}

	/**
	 * Transaction related contact.
	 *
	 * @since 1.0.0
	 * @return \ByteKit\Models\Relation
	 */
	public function vendor() {
		return $this->belongs_to( Vendor::class, 'contact_id' );
	}

	/**
	 * Load the object from the database.
	 *
	 * @param string|int $id ID of the object.
	 *
	 * @since 1.0.0
	 * @return $this
	 */
	protected function load( $id ) {
		parent::load( $id );
		if ( $this->get_object_type() !== $this->data['type'] ) {
			$this->apply_defaults();
		}

		return $this;
	}

	/**
	 * Save the object to the database.
	 *
	 * @since 1.0.0
	 * @return \WP_Error|true True on success, WP_Error on failure.
	 */
	public function save() {
		// If the account_id is changed, update the currency code.
		if ( $this->is_prop_changed( 'account_id' ) || ! $this->exists() ) {
			$account = Account::find( $this->account_id );
			$this->set_prop_value( 'currency_code', $account ? $account->currency_code : 'USD' );
		}
		// If currency code is changed, update the currency rate.
		if ( $this->is_prop_changed( 'currency_code' ) || ! $this->exists() ) {
			$currency = Currency::find( array( 'code' => $this->currency_code ) );
			$this->set_prop_value( 'currency_rate', $currency ? $currency->exchange_rate : 1 );
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
