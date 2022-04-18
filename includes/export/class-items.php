<?php
/**
 * Handle items export.
 *
 * @since   1.1.0
 *
 * @package Ever_Accounting\Export
 */

namespace Ever_Accounting\Export;

use Ever_Accounting\Abstracts\CSV_Exporter;

defined( 'ABSPATH' ) || exit();


/**
 * Class Items
 *
 * @since   1.1.0
 *
 * @package Ever_Accounting\Export
 */
class Items extends CSV_Exporter {

	/**
	 * Our export type. Used for export-type specific filters/actions.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $export_type = 'items';


	/**
	 * Return an array of columns to export.
	 *
	 * @return array
	 * @since  1.0.2
	 */
	public function get_columns() {
		return ever_accounting_get_io_headers( 'item' );
	}

	/**
	 *
	 * @since 1.0.2
	 */
	public function get_rows() {
		$args  = array(
			'per_page' => $this->limit,
			'paged'     => $this->page,
			'orderby'  => 'id',
			'order'    => 'ASC',
			'return'   => 'objects',
			'number'   => - 1,
		);
		$args  = apply_filters( 'ever_accounting_item_export_query_args', $args );
		$items = \Ever_Accounting\Items::query( $args );
		$rows  = array();

		foreach ( $items as $item ) {
			$rows[] = $this->generate_row_data( $item );
		}

		return $rows;
	}


	/**
	 * Take a item and generate row data from it for export.
	 *
	 * @param \Ever_Accounting\Item $item Item object.
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
				case 'category_name':
					$category = \Ever_Accounting\Categories::get( $item->get_category_id() );
					$value    = $category ? $category->get_name() : '';
					break;
				case 'sale_price':
					$value = $item->get_sale_price();
					break;
				case 'purchase_price':
					$value = $item->get_purchase_price();
					break;
				case 'sales_tax':
					$value = $item->get_sales_tax();
					break;
				case 'purchase_tax':
					$value = $item->get_purchase_tax();
					break;
				default:
					$value = apply_filters( 'ever_accounting_item_csv_row_item', '', $column, $item, $this );
			}

			$props[ $column ] = $value;
		}

		return $props;
	}
}
