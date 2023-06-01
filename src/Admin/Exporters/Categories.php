<?php
/**
 * Handle category export.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Export
 */

namespace EverAccounting\Admin\Exporters;

defined( 'ABSPATH' ) || exit();

/**
 * Class Categories
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Export
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
			'name',
			'type',
			'description',
			'color',
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
		$args  = array(
			'per_page' => $this->limit,
			'page'     => $this->page,
			'orderby'  => 'id',
			'order'    => 'ASC',
			'number'   => -1,
		);
		$args  = apply_filters( 'ever_accounting_export_categories_args', $args, $this );
		$items = eac_get_categories( $args );
		$rows  = array();

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
					$getter = 'get_' . $column;
					$value  = '';
					if ( method_exists( $item, $getter ) ) {
						$value = $item->$getter( 'edit' );
					}
					$value = apply_filters( 'ever_accounting_account_export_column_' . $column, $value, $item );
			}

			$props[ $column ] = $value;
		}

		return $props;
	}
}
