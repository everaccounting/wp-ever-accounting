<?php
/**
 * Notes repository.
 *
 * Handle Notes insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EAccounting\Repositories
 */

namespace EAccounting\Repositories;

use EAccounting\Abstracts\Resource_Repository;
use EAccounting\Models\Note;

defined( 'ABSPATH' ) || exit;

/**
 * Class InvoiceHistories
 *
 * @since   1.1.0
 *
 * @package EAccounting\Repositories
 */
class Notes extends Resource_Repository {
	/**
	 * The table name.
	 *
	 * @var string
	 */
	const TABLE = 'ea_notes';

	/**
	 * The table name.
	 *
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
		'type'         => '%s',
		'note'         => '%s',
		'extra'        => '%s',
		'creator_id'   => '%d',
		'date_created' => '%s',
	);
}
