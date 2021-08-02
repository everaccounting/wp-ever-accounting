<?php
/**
 * Handle the Note object.
 *
 * @package     EverAccounting
 * @class       Note
 * @version     1.2.1
 */

namespace EverAccounting;

use EverAccounting\Abstracts\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Core class used to implement the Transfer object.
 *
 * @package EverAccounting
 *
 * @since 1.2.1
 *
 * @property string $date
 * @property int $from_account_id
 * @property float $amount
 * @property int $to_account_id
 * @property int $income_id
 * @property int $expense_id
 * @property string $payment_method
 * @property string $reference
 * @property string $description
 * @property int $creator_id
 * @property string $date_created
 */
class Transfer extends Data {
	/**
	 * Transfer id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	public $id = null;

	/**
	 * Transfer data container.
	 *
	 * @since 1.2.1
	 * @var array
	 */
	public $data = array(
		'date'            => null,
		'from_account_id' => null,
		'amount'          => 0.0000,
		'to_account_id'   => null,
		'income_id'       => null,
		'expense_id'      => null,
		'payment_method'  => '',
		'reference'       => '',
		'description'     => '',
		'creator_id'      => null,
		'date_created'    => null,
	);

	/**
	 * Category id.
	 *
	 * @since 1.2.1
	 * @var int
	 */
	protected $category_id = null;

	/**
	 * Stores the transfer object's sanitization level.
	 *
	 * Does not correspond to a DB field.
	 *
	 * @since 1.2.1
	 * @var string
	 */
	public $filter;

	/**
	 * Retrieve Transfer instance.
	 *
	 * @param int $transfer_id Transfer id.
	 *
	 * @return Transfer|false Transfer object, false otherwise.
	 * @since 1.2.1
	 *
	 * @global \wpdb $wpdb WordPress database abstraction object.
	 */
	public static function get_instance( $transfer_id ) {
		global $wpdb;

		$transfer_id = (int) $transfer_id;
		if ( ! $transfer_id ) {
			return false;
		}

		$_item = wp_cache_get( $transfer_id, 'ea_transfers' );

		if ( ! $_item ) {
			$_item = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}ea_transfers WHERE id = %d LIMIT 1", $transfer_id ) );

			if ( ! $_item ) {
				return false;
			}

			$_item = eaccounting_sanitize_transfer( $_item, 'raw' );
			wp_cache_add( $_item->id, $_item, 'ea_transfers' );
		} elseif ( empty( $_item->filter ) ) {
			$_item = eaccounting_sanitize_transfer( $_item, 'raw' );
		}

		return new Transfer( $_item );
	}

	/**
	 * Transfer constructor.
	 *
	 * @param Transfer $transfer Transfer object
	 *
	 * @since 1.2.1
	 */
	public function __construct( $transfer ) {
		foreach ( get_object_vars( $transfer ) as $key => $value ) {
			$this->$key = $value;
		}
	}

	/**
	 * Filter transfer object based on context.
	 *
	 * @param string $filter Filter.
	 *
	 * @return Transfer|Object
	 * @since 1.2.1
	 */
	public function filter( $filter ) {
		if ( $this->filter === $filter ) {
			return $this;
		}

		if ( 'raw' === $filter ) {
			return self::get_instance( $this->id );
		}

		return new self( eaccounting_sanitize_transfer( (object) $this->to_array(), $filter ) );
	}

	/**
	 * Set transfer category.
	 *
	 * @throws \Exception If any error happens
	 * @since 1.1.0
	 */
	protected function set_transfer_category() {
		global $wpdb;
		$cache_key   = md5( 'other' . __( 'Transfer', 'wp-ever-accounting' ) );
		$category_id = wp_cache_get( $cache_key, 'ea_categories' );
		if ( false === $category_id ) {
			$category_id = $wpdb->get_var( $wpdb->prepare( "SELECT id from {$wpdb->prefix}ea_categories WHERE type=%s AND name=%s", 'other', __( 'Transfer', 'wp-ever-accounting' ) ) );
			wp_cache_add( $cache_key, $category_id, 'ea_categories' );
		}
		if ( empty( $category_id ) ) {
			throw new \Exception(
				sprintf(
				/* translators: %s: category name %s: category type */
					__( 'Transfer category is missing please create a category named "%1$s" and type"%2$s".', 'wp-ever-accounting' ),
					__( 'Transfer', 'wp-ever-accounting' ),
					'other'
				)
			);
		}

		$this->category_id = $category_id;
	}

}
