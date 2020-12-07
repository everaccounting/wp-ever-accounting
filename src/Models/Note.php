<?php
/**
 * Handle the history object.
 *
 * @package     EverAccounting\Models
 * @class       Currency
 * @version     1.1.0
 */

namespace EverAccounting\Models;

use EverAccounting\Abstracts\ResourceModel;
use EverAccounting\Core\Exception;
use EverAccounting\Core\Repositories;

defined( 'ABSPATH' ) || exit;

/**
 * Class InvoiceHistory
 *
 * @since   1.1.0
 *
 * @package EverAccounting\Models
 */
class Note extends ResourceModel {
	/**
	 * This is the name of this object type.
	 *
	 * @var string
	 */
	protected $object_type = 'note';

	/**
	 * @since 1.1.0
	 * @var string
	 */
	public $cache_group = 'ea_notes';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	protected $data = array(
		'parent_id'    => null,
		'parent_type'  => '',
		'notify'       => 0,
		'content'      => '',
		'date_created' => null,
	);

	/**
	 * Get the account if ID is passed, otherwise the account is new and empty.
	 *
	 * @since 1.1.0
	 *
	 * @param int|object|Account $data object to read.
	 *
	 */
	public function __construct( $data = 0 ) {
		parent::__construct( $data );

		if ( $data instanceof self ) {
			$this->set_id( $data->get_id() );
		} elseif ( is_numeric( $data ) ) {
			$this->set_id( $data );
		} elseif ( ! empty( $data->id ) ) {
			$this->set_id( $data->id );
		} elseif ( is_array( $data ) ) {
			$this->set_props( $data );
		} else {
			$this->set_object_read( true );
		}

		//Load repository
		$this->repository = Repositories::load( 'notes' );

		if ( $this->get_id() > 0 ) {
			$this->repository->read( $this );
		}
	}
	/*
	|--------------------------------------------------------------------------
	| Getters
	|--------------------------------------------------------------------------
	*/

	/**
	 * Return the id.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_parent_id( $context = 'edit' ) {
		return $this->get_prop( 'parent_id', $context );
	}

	/**
	 * Return the type of parent
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_parent_type( $context = 'edit' ) {
		return $this->get_prop( 'parent_type', $context );
	}

	/**
	 * Return the notify.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_notify( $context = 'edit' ) {
		return $this->get_prop( 'notify', $context );
	}

	/**
	 * Return the content.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_content( $context = 'edit' ) {
		return $this->get_prop( 'content', $context );
	}

	/*
	|--------------------------------------------------------------------------
	| Setters
	|--------------------------------------------------------------------------
	*/

	/**
	 * set the id.
	 *
	 * @since  1.1.0
	 *
	 * @param int $parent_id .
	 *
	 */
	public function set_parent_id( $parent_id ) {
		$this->set_prop( 'parent_id', absint( $parent_id ) );
	}

	/**
	 * set the id.
	 *
	 * @since  1.1.0
	 *
	 * @param int $parent_type .
	 *
	 */
	public function set_parent_type( $parent_type ) {
		$this->set_prop( 'parent_type', eaccounting_clean( $parent_type ) );
	}

	/**
	 * set the notify.
	 *
	 * @since  1.1.0
	 *
	 * @param int $notify .
	 *
	 */
	public function set_notify( $notify ) {
		$this->set_prop( 'notify', absint( $notify ) );
	}

	/**
	 * set the content.
	 *
	 * @since  1.1.0
	 *
	 * @param string $content .
	 *
	 */
	public function set_content( $content ) {
		$this->set_prop( 'content', eaccounting_sanitize_textarea( $content ) );
	}

	/**
	 * Save should create or update based on object existence.
	 *
	 * @since  1.1.0
	 * @throws Exception
	 * @return \Exception|bool
	 */
	public function save() {
		if ( empty( $this->get_parent_id() ) ) {
			throw new Exception( 'empty_parent_id', __( 'Parent ID must be specified', 'wp-ever-accounting' ) );
		}

		if ( empty( $this->get_parent_type() ) ) {
			throw new Exception( 'empty_parent_type', __( 'Parent type must be specified', 'wp-ever-accounting' ) );
		}

		return parent::save();
	}
}
