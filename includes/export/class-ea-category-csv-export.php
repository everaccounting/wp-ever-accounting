<?php

namespace EverAccounting\Export;

defined( 'ABSPATH' ) || exit();

use EverAccounting\Abstracts\CSV_Batch_Exporter;
use EverAccounting\Query_Category;

class Category_CSV_Export extends CSV_Batch_Exporter {

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
	 * @return array
	 * @since  1.0.2
	 */
	public function get_csv_columns() {
		return array(
			'name'  => __( 'Name', 'wp-ever-accounting' ),
			'type'  => __( 'Type', 'wp-ever-accounting' ),
			'color' => __( 'Color', 'wp-ever-accounting' ),
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
		$query             = Query_Category::init()->where( $args );
		$items             = $query->get( OBJECT, 'eaccounting_get_category' );
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
	 * @param \EverAccounting\Category $item
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
				case 'type':
					$value = $item->get_type();
					break;
				case 'color':
					$value = $item->get_color();
					break;

				default:
					$value = apply_filters( 'eaccounting_category_csv_row_item', '', $column, $item, $this );
			}

			$props[ $column ] = $value;
		}

		return $props;
	}
}
