<?php
/**
 * Handle revenue export.
 *
 * @since   1.0.2
 *
 * @package Ever_Accounting\Export
 */

namespace Ever_Accounting\Export;

use Ever_Accounting\Abstracts\CSV_Exporter;

defined( 'ABSPATH' ) || exit();


/**
 * Class Revenues
 *
 * @since   1.0.2
 *
 * @package Ever_Accounting\Export
 */
class Revenues extends CSV_Exporter {

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
	 * @return array
	 * @since  1.0.2
	 */
	public function get_columns() {
		return ever_accounting_get_io_headers( 'revenue' );
	}

	/**
	 * Get export data.
	 *
	 * @return array
	 * @since 1.0.2
	 */
	public function get_rows() {
		$args  = array(
			'per_page' => $this->limit,
			'page'     => $this->page,
			'orderby'  => 'id',
			'order'    => 'ASC',
			'type'     => 'income',
			'return'   => 'objects',
			'number'   => - 1,
		);
		$args  = apply_filters( 'ever_accounting_revenue_export_query_args', $args );
		$items = \Ever_Accounting\Transactions::query_revenues( $args );

		$rows = array();
		foreach ( $items as $item ) {
			$rows[] = $this->generate_row_data( $item );
		}

		return $rows;
	}


	/**
	 * Take a revenue and generate row data from it for export.
	 *
	 * @param \Ever_Accounting\Revenue $item Revenue Object.
	 *
	 * @return array
	 */
	protected function generate_row_data( $item ) {
		$props = [];
		foreach ( $this->get_columns() as $column => $label ) {
			$value = null;
			switch ( $column ) {
				case 'payment_date':
					$value = \Ever_Accounting\Helpers\Formatting::date( $item->get_payment_date() );
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
					$account = \Ever_Accounting\Accounts::get( $item->get_account_id() );
					$value   = $account ? $account->get_name() : '';
					break;
				case 'customer_name':
					$customer = \Ever_Accounting\Contacts::get_customer( $item->get_contact_id() );
					$value    = $customer ? $customer->get_name() : '';
					break;
				case 'category_name':
					$category = \Ever_Accounting\Categories::get( $item->get_category_id() );
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
					$value = apply_filters( 'ever_accounting_revenue_csv_row_item', '', $column, $item, $this );
			}
			$props[ $column ] = $value;
		}

		return $props;
	}
}
