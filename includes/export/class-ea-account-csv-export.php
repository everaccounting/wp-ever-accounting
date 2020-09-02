<?php

namespace EverAccounting\Export;

defined( 'ABSPATH' ) || exit();

use EverAccounting\Abstracts\CSV_Batch_Exporter;
use EverAccounting\Query_Account;

class Account_CSV_Export extends CSV_Batch_Exporter {

	/**
	 * Our export type. Used for export-type specific filters/actions.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $export_type = 'accounts';


	/**
	 * Return an array of columns to export.
	 *
	 * @return array
	 * @since  1.0.2
	 */
	public function get_csv_columns() {
		return array(
			'name'            => __( 'Name', 'wp-ever-accounting' ),
			'number'          => __( 'Number', 'wp-ever-accounting' ),
			'currency_code'   => __( 'Currency Code', 'wp-ever-accounting' ),
			'opening_balance' => __( 'Opening Balance', 'wp-ever-accounting' ),
			'bank_name'       => __( 'Bank Name', 'wp-ever-accounting' ),
			'bank_phone'      => __( 'Bank Phone', 'wp-ever-accounting' ),
			'bank_address'    => __( 'Bank Address', 'wp-ever-accounting' ),
			'enabled'         => __( 'Enabled', 'wp-ever-accounting' ),
		);
	}

	/**
	 *
	 * @since 1.0.2
	 */
	public function set_data() {
		$args              = array(
			'per_page' => $this->get_limit(),
			'page'     => $this->get_page(),
			'orderby'  => 'id',
			'order'    => 'ASC',
			'type'     => 'account',
		);
		$query             = Query_Account::init()->where( $args );
		$items             = $query->get( OBJECT, 'eaccounting_get_contact' );
		$this->total_count = $query->count();
		$this->rows        = array();

		foreach ( $items as $item ) {
			$this->rows[] = $this->generate_row_data( $item );
		}
	}


	/**
	 * Take a product and generate row data from it for export.
	 *
	 *
	 * @param \EverAccounting\Account $item
	 *
	 * @return array
	 */
	protected function generate_row_data( $item ) {
		$props = [];
		foreach ( $this->get_csv_columns() as $column => $label ) {
			$value = null;
			switch ( $column ) {
				case 'name':
					$value = $item->get_name();
					break;
				case 'number':
					$value = $item->get_number();
					break;
				case 'currency_code':
					$value = $item->get_currency_code();
					break;
				case 'opening_balance':
					$value = $item->get_opening_balance();
					break;
				case 'bank_name':
					$value = $item->get_bank_name();
					break;
				case 'bank_phone':
					$value = $item->get_bank_phone();
					break;
				case 'bank_address':
					$value = $item->get_bank_address();
					break;
				case 'enabled':
					$value = $item->get_enabled();
					break;
				default:
					$value = apply_filters( 'eaccounting_account_csv_row_item', '', $column, $item, $this );
			}

			$props[ $column ] = $value;
		}

		return $props;
	}
}
