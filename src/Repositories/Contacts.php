<?php
/**
 * Contact repository.
 *
 * Handle contact insert, update, delete & retrieve from database.
 *
 * @version   1.1.0
 * @package   EverAccounting\Repositories
 */
namespace EverAccounting\Repositories;

use EverAccounting\Abstracts\ResourceRepository;

defined( 'ABSPATH' ) || exit;

/**
 * Class ContactsRepository
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Abstracts
 */
class Contacts extends ResourceRepository {

	/**
	 * Name of the table.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	const TABLE = 'ea_contacts';

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
		'id'            => '%d',
		'currency_code' => '%s',
		'user_id'       => '%d',
		'name'          => '%s',
		'email'         => '%s',
		'phone'         => '%s',
		'fax'           => '%s',
		'birth_date'    => '%s',
		'address'       => '%s',
		'country'       => '%s',
		'website'       => '%s',
		'tax_number'    => '%s',
		'type'          => '%s',
		'note'          => '%s',
		'enabled'       => '%d',
		'creator_id'    => '%d',
		'date_created'  => '%s',
	);


}
