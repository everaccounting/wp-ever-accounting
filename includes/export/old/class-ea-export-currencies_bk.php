<?php
/**
 * Handle currency export.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Export
 */

namespace EverAccounting\Export;

defined( 'ABSPATH' ) || exit();

use EverAccounting\Abstracts\CSV_Exporter;
use EverAccounting\Query_Currency;

/**
 * Class Export_Currencies
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Export
 */
class Export_Currencies extends CSV_Exporter {

	/**
	 * Our export type. Used for export-type specific filters/actions.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $export_type = 'currencies';


	/**
	 * Return an array of columns to export.
	 *
	 * @since  1.0.2
	 * @return array
	 */
	public function get_columns() {
		return eaccounting_get_io_headers( 'currency' );
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
		);
		$query             = Query_Currency::init()->where( $args );
		$items             = $query->get( OBJECT, 'eaccounting_get_currency' );
		$this->total_count = $query->count();
		$rows              = array();

		foreach ( $items as $item ) {
			$rows[] = $this->generate_row_data( $item );
		}

		return $rows;
	}


	/**
	 * Take a currency and generate row data from it for export.
	 *
	 * @param \EverAccounting\Currency $item
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
				case 'code':
					$value = $item->get_code();
					break;
				case 'rate':
					$value = $item->get_rate();
					break;
				case 'precision':
					$value = $item->get_precision();
					break;
				case 'symbol':
					$value = $item->get_symbol();
					break;
				case 'position':
					$value = $item->get_position();
					break;
				case 'decimal_separator':
					$value = $item->get_decimal_separator();
					break;
				case 'thousand_separator':
					$value = $item->get_thousand_separator();
					break;
				case 'enabled':
					$value = $item->get_enabled();
					break;
				default:
					$value = apply_filters( 'eaccounting_currency_csv_row_item', '', $column, $item, $this );
			}

			$props[ $column ] = $value;
		}

		return $props;
	}
}