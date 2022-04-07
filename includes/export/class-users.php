<?php
/**
 * Handle users export.
 *
 * @since   1.1.4
 *
 * @package EverAccounting\Export
 */

namespace EverAccounting\Export;

use EverAccounting\Abstracts\CSV_Exporter;

defined( 'ABSPATH' ) || exit();


/**
 * Class Users
 *
 * @since   1.1.4
 *
 * @package EverAccounting\Export
 */
class Users extends CSV_Exporter {

	/**
	 * Our export type. Used for export-type specific filters/actions.
	 *
	 * @since 1.0.2
	 * @var string
	 */
	public $export_type = 'users';

	/**
	 * Return an array of columns to export.
	 *
	 * @return array
	 * @since  1.0.2
	 */
	public function get_columns() {
		return eaccounting_get_io_headers( 'customer' );
	}

	/**
	 * Get export data.
	 *
	 * @return array
	 * @since 1.0.
	 */
	public function get_rows() {
		$args = array(
			'per_page' => $this->limit,
			'page'     => $this->page,
			'orderby'  => 'name',
			'order'    => 'ASC',
			//'type'     => 'customer',
			'return'   => 'objects',
			'number'   => - 1,
		);

		$args = apply_filters( 'eaccounting_users_export_query_args', $args );

		$items = get_users( $args );

		$rows = array();

		foreach ( $items as $item ) {
			$rows[] = $this->generate_row_data( $item );
		}


		return $rows;
	}


	/**
	 * Take a customer and generate row data from it for export.
	 *
	 * @param \WP_User $item
	 *
	 * @return array
	 */
	protected function generate_row_data( $item ) {
		$props = [];
		$user = $item->ID;
		$user_data = get_userdata( $user );
		foreach ( $this->get_columns() as $column => $label ) {
			switch ( $column ) {
				case 'name':
					$value = $user_data->first_name. ' '. $user_data->last_name;
					break;
				case 'company':
					$value = '';
					break;
				case 'email':
					$value = $user_data->user_email;
					break;
				case 'phone':
					$value = '';
					break;
				case 'birth_date':
					$value = '';
					break;
				case 'street':
					$value = '';
					break;
				case 'city':
					$value = '';
					break;
				case 'state':
					$value = '';
					break;
				case 'postcode':
					$value = '';
					break;
				case 'country':
					$value = '';
					break;
				case 'website':
					$value = $user_data->user_url;
					break;
				case 'vat_number':
					$value = '';
					break;
				case 'currency_code':
					$value = eaccounting_get_default_currency();
					break;
				default:
					$value = apply_filters( 'eaccounting_user_csv_row_item', '', $column, $item, $this );
			}

			$props[ $column ] = $value;
		}

		return $props;
	}
}
