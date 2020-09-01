<?php

namespace EverAccounting\Export;

defined( 'ABSPATH' ) || exit();

use EverAccounting\Abstracts\CSV_Batch_Exporter;
use EverAccounting\Query_Currency;

class Currency_CSV_Export extends CSV_Batch_Exporter {

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
	 * @return array
	 * @since  1.0.2
	 */
	public function get_csv_columns() {
		return array(
			'name'               => __( 'Name', 'wp-ever-accounting' ),
			'code'               => __( 'Code', 'wp-ever-accounting' ),
			'precision'          => __( 'Precision', 'wp-ever-accounting' ),
			'symbol'             => __( 'Symbol', 'wp-ever-accounting' ),
			'position'           => __( 'Position', 'wp-ever-accounting' ),
			'decimal_separator'  => __( 'Decimal Separator', 'wp-ever-accounting' ),
			'thousand_separator' => __( 'Thousand Separator', 'wp-ever-accounting' ),
			'enabled'            => __( 'Enabled', 'wp-ever-accounting' ),
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
		);
		$query             = Query_Currency::init()->where( $args );
		$items             = $query->get( OBJECT, 'eaccounting_get_currency' );
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
	 * @param \EverAccounting\Currency $item
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
