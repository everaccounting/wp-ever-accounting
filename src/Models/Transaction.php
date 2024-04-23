<?php

namespace EverAccounting\Models;

defined( 'ABSPATH' ) || exit;

/**
 * Transaction model.
 *
 * @since 1.0.0
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
	 * Table columns.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $columns = array(
		'id',
		'type',
		'payment_date',
		'amount',
		'currency_code',
		'currency_rate',
		'account_id',
		'document_id',
		'contact_id',
		'category_id',
		'description',
		'payment_method',
		'reference',
		'attachment_id',
		'parent_id',
		'reconciled',
		'creator_id',
		'date_created',
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
		'type' => 'income',
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
	 * Field aliases.
	 *
	 * @var array
	 */
	protected $aliases = array();


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
	 * Returns the formatted amount.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_formatted_amount_prop(){
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
	public function account() {
		return $this->belongs_to( Account::class );
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
			$currency = Currency::find(array('code' => $this->currency_code));
			$this->set_prop_value( 'currency_rate', $currency ? $currency->rate : 1 );
		}


		if ( empty( $this->date_created ) ) {
			$this->date_created = current_time( 'mysql' );
		}

		return parent::save();
	}
}
