<?php
/**
 * Handle accounts export.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Export
 */

namespace EverAccounting\Export;

defined( 'ABSPATH' ) || exit();

use EverAccounting\Abstracts\CSV_Exporter;
use EverAccounting\Query_Account;

/**
 * Class Export_Accounts
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Export
 */
class Export_Accounts extends CSV_Exporter {

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
	 * @since  1.0.2
	 * @return array
	 */
	public function get_columns() {
		return eaccounting_get_io_headers( 'account' );
	}

	/**
	 *
	 * @since 1.0.2
	 */
	public function get_rows() {
		$args              = array(
			'per_page' => $this->limit,
			'page'     => $this->page,
			'orderby'  => 'id',
			'order'    => 'ASC',
		);
		$query             = Query_Account::init()->where( $args );
		$items             = $query->get( OBJECT, 'eaccounting_get_account' );
		$this->total_count = $query->count();
		$rows              = array();

		foreach ( $items as $item ) {
			$rows[] = $this->generate_row_data( $item );
		}

		return $rows;
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
		foreach ( $this->get_columns() as $column => $label ) {
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
