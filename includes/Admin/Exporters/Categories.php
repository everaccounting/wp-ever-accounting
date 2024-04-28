<?php
/**
 * Handle category export.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Admin\Exporters
 */

namespace EverAccounting\Admin\Exporters;

defined( 'ABSPATH' ) || exit();

/**
 * Class Categories.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Admin\Exporters
 */
class Categories extends CSVExporter {

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
		return array(
			'id',
			'type',
			'name',
			'description',
			'status',
		);
	}

	/**
	 * Get export data.
	 *
	 * @since 1.0.2
	 * @return array
	 */
	public function get_rows() {
		$args = array(
			'per_page' => $this->limit,
			'page'     => $this->page,
			'status'   => eac_get_input_var( 'status', '', 'POST' ),
			'type'     => eac_get_input_var( 'type', '', 'POST' ),
			'orderby'  => 'id',
			'order'    => 'ASC',
			'number'   => - 1,
		);
		$args = apply_filters( 'ever_accounting_export_categories_args', $args, $this );

		$items = eac_get_categories( $args );

		$rows = array();

		foreach ( $items as $item ) {
			$rows[] = $this->generate_row_data( $item );
		}

		return $rows;
	}


	/**
	 * Take a category and generate row data from it for export.
	 *
	 * @param \EverAccounting\Models\Category $item Category object.
	 *
	 * @return array
	 */
	protected function generate_row_data( $item ) {
		$props = [];
		foreach ( $this->get_columns() as $column ) {
			$value = null;
			switch ( $column ) {
				default:
					$value = '';
					if ( $item->$column ) {
						$value = $item->$column;
					}
					$value = apply_filters( 'ever_accounting_export_categories_column_' . $column, $value, $item );
			}

			$props[ $column ] = $value;
		}

		return $props;
	}
}
