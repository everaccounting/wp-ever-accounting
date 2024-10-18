<?php

namespace EverAccounting\Models;

/**
 * Abstract class Model.
 *
 * @since 1.2.0
 * @package EverAccounting
 * @subpackage Models
 */
abstract class Model extends \ByteKit\Models\Model {
	/**
	 * The name of the "date created" column.
	 *
	 * This constant defines the name of the column used to store the creation date of the model.
	 * It is typically set to 'date_created'.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const CREATED_AT = 'date_created';

	/**
	 * The name of the "date updated" column.
	 *
	 * This constant defines the name of the column used to store the last update date of the model.
	 * It is typically set to 'date_updated'.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const UPDATED_AT = 'date_updated';

	/**
	 * The name of the "creator_id" column.
	 *
	 * This constant defines the name of the column used to store the ID of the user who created the model instance.
	 * It is typically set to 'creator_id'.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const CREATOR_ID = 'author_id';

	/**
	 * Get hook prefix. Default is the object type.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_hook_prefix() {
		return 'eac_' . $this->get_object_type();
	}
}
