<?php
/**
 * InvoiceItem repository.
 *
 * Handle Invoice Item insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Repositories;

use EverAccounting\Abstracts\ResourceRepository;
use EverAccounting\Models\LineItem;

defined( 'ABSPATH' ) || exit;

/**
 * Class InvoiceItems
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class LineItems extends ResourceRepository {
	/**
	 * Table name
	 *
	 * @var string
	 */
	const TABLE = 'ea_line_items';

	/**
	 * @since 1.1.0
	 * 
	 * @var string
	 */
	protected $table = self::TABLE;

	/**
	 * A map of database fields to data types.
	 *
	 * @since 1.1.0
	 * 
	 * @var array
	 */
	protected $data_type = array(
		'id'           => '%d',
		'parent_id'    => '%d',
		'parent_type'  => '%s',
		'item_id'      => '%d',
		'item_name'    => '%s',
		'unit_price'   => '%f',
		'quantity'     => '%f',
		'tax_rate'     => '%f',
		'discount'     => '%f',
		'total'        => '%f',
		'extra'        => '%s',
		'date_created' => '%s',
	);

	/**
	 * Read order items of a specific type from the database for this order.
	 *
	 * @param LineItem $line_item Order object.
	 *
	 * @return array
	 */
	public function read_line_taxes( $line_item ) {
		global $wpdb;

		// Get from cache if available.
		$items = 0 < $line_item->get_id() ? wp_cache_get( 'line-tax-' . $line_item->get_id(), 'ea-line-taxes' ) : false;

		if ( false === $items ) {
			$items = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$wpdb->prefix}ea_line_taxes WHERE item_id = %d AND parent_id=%d AND parent_type = %s ORDER BY id;",
					$line_item->get_id(),
					$line_item->get_parent_id(),
					$line_item->get_parent_type()
				)
			);
			foreach ( $items as $item ) {
				wp_cache_set( 'line-tax-' . $item->id, $item, 'ea-line-taxes' );
			}
		}

		return array_map(
			function ( $item ) {
				return new LineTax( $item );
			},
			$items
		);

	}
}
