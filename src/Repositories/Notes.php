<?php
/**
 * Notes repository.
 *
 * Handle Notes insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */

namespace EverAccounting\Repositories;

use EverAccounting\Abstracts\ResourceRepository;

defined( 'ABSPATH' ) || exit;

/**
 * Class InvoiceHistories
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Repositories
 */
class Notes extends ResourceRepository {
	/**
	 * @var string
	 */
	const TABLE = 'ea_notes';

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
		'id'           => '%d',
		'parent_id'    => '%d',
		'parent_type'  => '%s',
		'notify'       => '%s',
		'content'      => '%s',
		'date_created' => '%s',
	);

}
