<?php
/**
 * Handle revenue export.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Export
 */

namespace EverAccounting\Export;

defined( 'ABSPATH' ) || exit();

use EverAccounting\Abstracts\CSV_Exporter;
use EverAccounting\Query_Transaction;

/**
 * Class Export_Revenues
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Export
 */
class Export_Revenues extends CSV_Exporter {

	/**
	 * Our export type. Used for export-type specific filters/actions.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $export_type = 'revenues';


	/**
	 * Return an array of columns to export.
	 *
	 * @since  1.0.2
	 * @return array
	 */
	public function get_columns() {
		return eaccounting_get_io_headers( 'revenue' );
	}

	/**
	 * Get export data.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public function get_rows() {
		$args              = array(
			'per_page' => $this->limit,
			'page'     => $this->page,
			'orderby'  => 'id',
			'order'    => 'ASC',
			'type'     => 'income',
		);
		$query             = Query_Transaction::init()->where( $args )->notTransfer();
		$items             = $query->get( OBJECT, 'eaccounting_get_transaction' );
		$this->total_count = $query->count();
		$rows              = array();
		foreach ( $items as $item ) {
			$rows[] = $this->generate_row_data( $item );
		}

		return $rows;
	}


	/**
	 * Take a revenue and generate row data from it for export.
	 *
	 * @param \EverAccounting\Transaction $item
	 *
	 * @return array
	 */
	protected function generate_row_data( $item ) {
		$props = [];
		foreach ( $this->get_columns() as $column => $label ) {
			$value = null;
			switch ( $column ) {
				case 'payment_date':
					$value = eaccounting_format_datetime( $item->get_payment_date() );
					break;
				case 'amount':
					$value = $item->get_amount();
					break;
				case 'currency_code':
					$value = $item->get_currency_code();
					break;
				case 'currency_rate':
					$value = $item->get_currency_rate();
					break;
				case 'account_name':
					$account = eaccounting_get_account( $item->get_account_id() );
					$value   = $account ? $account->get_name() : '';
					break;
				case 'customer_name':
					$customer = eaccounting_get_contact( $item->get_contact_id() );
					$value    = $customer ? $customer->get_name() : '';
					break;
				case 'category_name':
					$category = eaccounting_get_category( $item->get_category_id() );
					$value    = $category ? $category->get_name() : '';
					break;
				case 'description':
					$value = $item->get_description();
					break;
				case 'payment_method':
					$value = $item->get_payment_method();
					break;
				case 'reference':
					$value = $item->get_reference();
					break;
				case 'reconciled':
					$value = $item->get_reconciled();
					break;
				default:
					$value = apply_filters( 'eaccounting_revenue_csv_row_item', '', $column, $item, $this );
			}
			$props[ $column ] = $value;
		}

		return $props;
	}
}