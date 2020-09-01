<?php

namespace EverAccounting\Export;


use EverAccounting\Abstracts\CSV_Batch_Exporter;

class Revenue_CSV_Export extends CSV_Batch_Exporter {

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
	public function get_csv_columns() {
		return array(
			'id'    => __( 'id', 'wp-ever-accounting' ),
			'name'  => __( 'Name', 'wp-ever-accounting' ),
			'email' => __( 'Email', 'wp-ever-accounting' ),
			'phone' => __( 'Phone', 'wp-ever-accounting' ),
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
			'type'     => 'customer',
		);
		$query             = Query_Contact::init()->where( $args );
		$items             = $query->get();
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
	 * @param $item
	 *
	 * @return array
	 */
	protected function generate_row_data( $item ) {
		switch ($key)
		return get_object_vars( $item );
	}
}
