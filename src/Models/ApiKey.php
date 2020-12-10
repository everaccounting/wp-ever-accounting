<?php
/**
 * Handle the api key object.
 *
 * @package     EverAccounting\Models
 * @class       Category
 * @version     1.1.0
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Core\Repositories;
use EverAccounting\Core\Exception;

defined( 'ABSPATH' ) || exit;

/**
 * Class Category
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class ApiKey extends ResourceModel {
	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'api-key';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	public $cache_group = 'ea_api_keys';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $data = array(
		'user_id'       => '',
		'description'   => '',
		'permission'    => '',
		'api_key'       => '',
		'api_secret'    => '',
		'nonces'        => null,
		'truncated_key' => '',
		'last_access'   => null
	);


	/**
	 * Get the api_key if ID is passed, otherwise the api_key is new and empty.
	 *
	 * @param int|string|object|Item $item Item object to read.
	 */
	public function __construct( $item = 0 ) {
		parent::__construct( $item );

		if ( $item instanceof self ) {
			$this->set_id( $item->get_id() );
		} elseif ( is_numeric( $item ) ) {
			$this->set_id( $item );
		} elseif ( ! empty( $item->id ) ) {
			$this->set_id( $item->id );
		} elseif ( is_array( $item ) ) {
			$this->set_props( $item );
		} else {
			$this->set_object_read( true );
		}

		//Load repository
		$this->repository = Repositories::load( $this->object_type );

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete discounts from the database.
	|
	*/

	/*
	|--------------------------------------------------------------------------
	| Object Specific data methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * @return array
	 * @since 1.1.0
	 */
	public function get_permissions() {
		return array(
			'read'       => __( 'Read', 'wp-ever-accounting' ),
			'write'      => __( 'Write', 'wp-ever-accounting' ),
			'read_write' => __( 'Read/Write', 'wp-ever-accounting' ),

		);
	}
	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	|
	| Functions for getting item data. Getter methods wont change anything unless
	| just returning from the props.
	|
	*/

	/**
	 * Get api user_id.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_user_id( $context = 'edit' ) {
		return $this->get_prop( 'user_id', $context );
	}

	/**
	 * Get api description.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_description( $context = 'edit' ) {
		return $this->get_prop( 'description', $context );
	}

	/**
	 * Get api permission.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_permission( $context = 'edit' ) {
		return $this->get_prop( 'permission', $context );
	}

	/**
	 * Get api-key permission nice name.
	 *
	 * @since 1.1.0
	 * @return mixed|string
	 */
	public function get_permission_nicename() {
		return isset( $this->get_permissions()[ $this->get_permission() ] ) ? $this->get_permissions()[ $this->get_permission() ] : $this->get_permission();
	}

	/**
	 * Get api api_key.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_api_key( $context = 'edit' ) {
		return $this->get_prop( 'api_key', $context );
	}

	/**
	 * Get api api_secret.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_api_secret( $context = 'edit' ) {
		return $this->get_prop( 'api_secret', $context );
	}

	/**
	 * Get api nonces.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_nonces( $context = 'edit' ) {
		return $this->get_prop( 'nonces', $context );
	}

	/**
	 * Get api truncated_key.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_truncated_key( $context = 'edit' ) {
		return $this->get_prop( 'truncated_key', $context );
	}

	/**
	 * Get api last_access.
	 *
	 * @param string $context
	 *
	 * @return mixed|null
	 * @since 1.1.0
	 *
	 */
	public function get_last_access( $context = 'edit' ) {
		return $this->get_prop( 'last_access', $context );
	}
	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete api_keys from the database.
	|
	*/


	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	|
	| Functions for setting item data. These should not update anything in the
	| database itself and should only change what is stored in the class
	| object.
	*/

	/**
	 * Set the api user_id.
	 *
	 * @param $value
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_user_id( $value ) {
		$this->set_prop( 'user_id', eaccounting_clean( $value ) );
	}

	/**
	 * Set the api description.
	 *
	 * @param $value
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_description( $value ) {
		$this->set_prop( 'description', sanitize_text_field( $value ) );
	}

	/**
	 * Set the api permission.
	 *
	 * @param $value
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_permission( $value ) {
		if ( array_key_exists( $value, $this->get_permissions() ) ) {
			$this->set_prop( 'permission', eaccounting_clean( $value ) );
		}
	}

	/**
	 * Set the api api_key.
	 *
	 * @param $value
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_api_key( $value ) {
		$this->set_prop( 'api_key', eaccounting_clean( $value ) );
	}

	/**
	 * Set the api api_secret.
	 *
	 * @param $value
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_api_secret( $value ) {
		$this->set_prop( 'api_secret', eaccounting_clean( $value ) );
	}

	/**
	 * Set the api nonces.
	 *
	 * @param $value
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_nonces( $value ) {
		$this->set_prop( 'nonces', eaccounting_clean( $value ) );
	}

	/**
	 * Set the api truncated_key.
	 *
	 * @param $value
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_truncated_key( $value ) {
		$this->set_prop( 'truncated_key', eaccounting_clean( $value ) );
	}

	/**
	 * Set the api last_access.
	 *
	 * @param $value
	 *
	 * @since 1.1.0
	 *
	 */
	public function set_last_access( $value ) {
		$this->set_prop( 'last_access', eaccounting_clean( $value ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Additional methods
	|--------------------------------------------------------------------------
	|
	| Does extra thing as helper functions.
	|
	*/

	/*
	|--------------------------------------------------------------------------
	| Conditionals
	|--------------------------------------------------------------------------
	|
	| Checks if a condition is true or false.
	|
	*/

	/**
	 * Save should create or update based on object existence.
	 *
	 * @return \Exception|bool
	 * @throws Exception
	 * @since  1.1.0
	 */
	public function save() {
		if ( empty( $this->get_user_id() ) ) {
			throw new Exception( 'empty_user_id', __( 'User ID must be specified.', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_permission() ) ) {
			throw new Exception( 'empty_permission', __( 'Permission can not be blank.', 'wp-ever-accounting' ) );
		}

		return parent::save();
	}

}
