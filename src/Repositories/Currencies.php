<?php
/**
 * Currency repository.
 *
 * Handle currency insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Repositories;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Abstracts\ResourceRepository;
use EverAccounting\Models\Currency;

defined( 'ABSPATH' ) || exit;

/**
 * Class Accounts
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class Currencies extends ResourceRepository {
	/**
	 * Table name
	 *
	 * @var string
	 */
	const OPTION = 'eaccounting_currencies';

	/**
	 * @since 1.1.0
	 *
	 * @var string
	 */
	protected $option = self::OPTION;

	/*
	|--------------------------------------------------------------------------
	| CRUD Methods
	|--------------------------------------------------------------------------
	*/
	/**
	 * Method to create a new item in the database.
	 *
	 * @param ResourceModel $item Item object.
	 *
	 * @throws \Exception | @return bool
	 */
	public function insert( &$item ) {
		$data                            = array(
			'name'               => $item->get_name(),
			'code'               => $item->get_code(),
			'rate'               => $item->get_rate(),
			'number'             => $item->get_number(),
			'precision'          => $item->get_precision(),
			'subunit'            => $item->get_subunit(),
			'symbol'             => $item->get_symbol(),
			'position'           => $item->get_position(),
			'decimal_separator'  => $item->get_decimal_separator(),
			'thousand_separator' => $item->get_thousand_separator(),
		);
		$currencies                      = $this->get_raw_currencies();
		$currencies[ $item->get_code() ] = $data;
		update_option( self::OPTION, $currencies );
		wp_cache_delete( 'currency', 'ea_currencies' );
		$item->set_id( $item->get_code() );
		$item->apply_changes();
		$item->clear_cache();
		do_action( 'eacccounting_insert_' . $item->get_object_type(), $item, $data );

		return true;
	}

	/**
	 * Method to read a item from the database.
	 *
	 * @param Currency $item Item object.
	 *
	 * @throws \Exception
	 */
	public function read( &$item ) {

		if ( empty( $item->get_code() ) ) {
			$item->set_defaults();
			$item->set_object_read( false );

			return;
		}

		$codes    = eaccounting_get_currency_codes();
		$saved    = eaccounting_collect( $this->get_raw_currencies() )->get( $item->get_code() );
		$currency = (array) eaccounting_collect( $codes )->merge( $this->get_raw_currencies() )->get( $item->get_code() );
		$item->set_props( $currency );
		if ( ! empty( $saved ) ) {
			$item->set_id( $item->get_code() );
			$item->set_object_read( $saved );
			do_action( 'eaccounting_read_' . $item->get_object_type(), $item );
		}
	}

	/**
	 * Method to update an item in the database.
	 *
	 * @param ResourceModel $item Subscription object.
	 *
	 * @throws \Exception
	 */
	public function update( &$item ) {
		$changes = $item->get_changes();
		if ( empty( $changes ) ) {
			return;
		}
		$code                = $item->get_id();
		$currencies          = $this->get_raw_currencies();
		$changed_item        = eaccounting_collect( $currencies )->get( $code );
		$changed_item        = eaccounting_collect( $changed_item )->merge( $changes )->all();
		$currencies[ $code ] = $changed_item;
		update_option( self::OPTION, $currencies );
		wp_cache_delete( 'currency', 'ea_currencies' );
		// Apply the changes.
		$item->apply_changes();
		// Fire a hook.
		do_action( 'eaccounting_update_' . $item->get_object_type(), $changes, $item );

	}

	/**
	 * Method to delete a subscription from the database.
	 *
	 * @param ResourceModel $item
	 * @param array         $args Array of args to pass to the delete method.
	 */
	public function delete( &$item, $args = array() ) {
		$code       = $item->get_id();
		$currencies = $this->get_raw_currencies();
		$currencies = eaccounting_collect( $currencies )->reject(
			function ( $item ) use ( $code ) {
				return $item['code'] === $code;
			}
		);

		update_option( self::OPTION, $currencies );
		wp_cache_delete( 'currency', 'ea_currencies' );
		// Delete cache.
		$item->clear_cache();
		// Fire a hook.
		do_action( 'eaccounting_delete_' . $item->get_object_type(), $item->get_id(), $item->get_data(), $item );
		$item->set_id( 0 );
	}

	/**
	 * Get raw currencies.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_raw_currencies() {
		$currencies = wp_cache_get( 'currency', 'ea_currencies' );
		if ( false === $currencies ) {
			$currencies = get_option( self::OPTION, array() );
			wp_cache_set( 'currency', $currencies, 'ea_currencies' );
		}

		return $currencies;
	}


	/**
	 * @since 1.1.0
	 *
	 * @param array $args
	 *
	 * @return false|mixed|void
	 */
	public function get_currencies( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'search'      => '',
				'orderby'     => 'name',
				'order'       => 'ASC',
				'number'      => - 1,
				'offset'      => 0,
				'paged'       => 1,
				'return'      => 'objects',
				'count_total' => false,
			)
		);

		$qv = apply_filters( 'eaccounting_get_currencies_args', $args );

		$collection    = eaccounting_collect( $this->get_raw_currencies() );
		$qv['order']   = isset( $qv['order'] ) ? strtoupper( $qv['order'] ) : 'ASC';
		$qv['orderby'] = in_array( $qv['orderby'], array( 'code', 'name', 'rate', 'symbol' ), true ) ? $qv['orderby'] : 'name';
		$qv['number']  = isset( $qv['number'] ) && $qv['number'] > 0 ? $qv['number'] : - 1;
		$qv['offset']  = isset( $qv['offset'] ) ? $qv['offset'] : ( $qv['number'] * ( $qv['paged'] - 1 ) );
		$count_total   = true === $qv['count_total'];

		$collection = $collection->sort(
			function ( $a, $b ) use ( $qv ) {
				if ( 'ASC' === $qv['orderby'] ) {
					return $a[ $qv['orderby'] ] < $b[ $qv['orderby'] ];
				}

				return $a[ $qv['orderby'] ] > $b[ $qv['orderby'] ];
			}
		);

		if ( ! empty( $qv['search'] ) ) {
			$collection = $collection->filter(
				function ( $item ) use ( $qv ) {
					$search = implode( ' ', array( $item['name'], $item['code'], $item['symbol'] ) );
					if ( false !== strpos( $search, $qv['search'] ) ) {
						return $item;
					}

					return false;
				}
			);
		}

		if ( $count_total ) {
			return $collection->count();
		}

		if ( $qv['number'] > 1 ) {
			$collection = $collection->splice( $qv['offset'], $qv['number'] );
		}

		$results = $collection->values()->all();

		if ( 'objects' === $qv['return'] ) {
			$results = array_map( 'eaccounting_get_currency', $results );
		}

		return $results;
	}

}
