<?php
/**
 * Handle vendors export.
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Export
 */
namespace EverAccounting\Export;

defined( 'ABSPATH' ) || exit();

use EverAccounting\Abstracts\CSV_Exporter;

/**
 * Class Export_Vendors
 *
 * @since   1.0.2
 *
 * @package EverAccounting\Export
 */
class Export_Vendors extends CSV_Exporter {
	/**
	 * Our export type. Used for export-type specific filters/actions.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $export_type = 'vendors';

	/**
	 * Return an array of columns to export.
	 *
	 * @since  1.0.2
	 * @return array
	 */
	public function get_columns() {
		return eaccounting_get_io_headers( 'vendor' );
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
			'type'     => 'vendor',
		);
		$args = apply_filters( 'eaccounting_vendor_export_query_args', $args );

		$items = eaccounting_get_vendors( $args );

		$rows = array();

		foreach ( $items as $item ) {
			$rows[] = $this->generate_row_data( $item );
		}

		return $rows;
	}


	/**
	 * Take a vendor and generate row data from it for export.
	 *
	 * @param \EverAccounting\Models\Vendor $item
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
				case 'email':
					$value = $item->get_email();
					break;
				case 'phone':
					$value = $item->get_phone();
					break;
				case 'birth_date':
					$value = $item->get_birth_date();
					break;
				case 'address':
					$value = eaccounting_format_address( array( 'street' => $item->get_street(), 'city' => $item->get_city(), 'state' => $item->get_state(), 'postcode' => $item->get_postcode(), 'country' => $item->get_country_nicename() ),',' );
					break;
				case 'country':
					$value = $item->get_country_nicename();
					break;
				case 'website':
					$value = $item->get_website();
					break;
				case 'vat_number':
					$value = $item->get_vat_number();
					break;
				case 'currency_code':
					$value = $item->get_currency_code();
					break;
				case 'thumbnail_id':
					$value = $item->get_attachment_url();
					break;
				default:
					$value = apply_filters( 'eaccounting_vendor_csv_row_item', '', $column, $item, $this );
			}

			$props[ $column ] = $value;
		}

		return $props;
	}
}
