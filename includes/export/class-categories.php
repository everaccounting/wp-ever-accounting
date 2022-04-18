<?php
/**
 * Handle category export.
 *
 * @since   1.0.2
 *
 * @package Ever_Accounting\Export
 */

namespace Ever_Accounting\Export;

use Ever_Accounting\Abstracts\CSV_Exporter;

defined( 'ABSPATH' ) || exit();

/**
 * Class Categories
 *
 * @since   1.0.2
 *
 * @package Ever_Accounting\Export
 */
class Categories extends CSV_Exporter {

	/**
	 * Our export type. Used for export-type specific filters/actions.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $export_type = 'categories';


	/**
	 * Return an array of columns to export.
	 *
	 * @since  1.0.2
	 * @return array
	 */
	public function get_columns() {
		return ever_accounting_get_io_headers( 'category' );
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
			'paged'     => $this->page,
			'orderby'  => 'id',
			'order'    => 'ASC',
			'return'   => 'objects',
			'number'      => -1,
		);
		$args = apply_filters( 'ever_accounting_category_export_query_args', $args );
		$items = \Ever_Accounting\Categories::query( $args );
		$rows              = array();

		foreach ( $items as $item ) {
			$rows[] = $this->generate_row_data( $item );
		}

		return $rows;
	}


	/**
	 * Take a category and generate row data from it for export.
	 *
	 * @param \Ever_Accounting\Category $item Category object.
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
				case 'type':
					$value = $item->get_type();
					break;
				case 'color':
					$value = $item->get_color();
					break;

				default:
					$value = apply_filters( 'ever_accounting_category_csv_row_item', '', $column, $item, $this );
			}

			$props[ $column ] = $value;
		}

		return $props;
	}
}
