<?php
/**
 * Item repository.
 *
 * Handle item insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Repositories;

use EverAccounting\Abstracts\ResourceRepository;

defined( 'ABSPATH' ) || exit;

/**
 * Class Accounts
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class Items extends ResourceRepository {
	/**
	 * Table name
	 *
	 * @var string
	 */
	const TABLE = 'ea_items';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	protected $table = self::TABLE;

	/**
	 * A map of database fields to data types.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $data_type = array(
		'id'                => '%d',
		'name'              => '%s',
		'sku'               => '%s',
		'image_id'          => '%d',
		'description'       => '%s',
		'sale_price'        => '%f',
		'purchase_price'    => '%f',
		'quantity'          => '%f',
		'category_id'       => '%d',
		'sales_tax_rate'    => '%f',
		'purchase_tax_rate' => '%f',
		'enabled'           => '%d',
		'creator_id'        => '%d',
		'date_created'      => '%s',
	);




}