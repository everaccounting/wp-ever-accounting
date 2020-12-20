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
		'note'         => '',
		'highlight'    => '',
		'author'       => '',
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

		$this->required_props = array(
			'parent_id'   => __( 'Document ID', 'wp-ever-accounting' ),
			'parent_type' => __( 'Document type', 'wp-ever-accounting' ),
			'note'        => __( 'Note content', 'wp-ever-accounting' ),
		);
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
	 * Return the note.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_note( $context = 'edit' ) {
		return $this->get_prop( 'note', $context );
	}

	/**
	 * Return highlight.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_highlight( $context = 'edit' ) {
		return $this->get_prop( 'highlight', $context );
	}

	/**
	 * Return the note.
	 *
	 * @since  1.1.0
	 *
	 * @param string $context What the value is for. Valid values are 'view' and 'edit'.
	 *
	 * @return string
	 */
	public function get_author( $context = 'edit' ) {
		return $this->get_prop( 'author', $context );
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
	 * set the note.
	 *
	 * @since  1.1.0
	 *
	 * @param string $note .
	 *
	 */
	public function set_highlight( $highlight ) {
		$this->set_prop( 'highlight', absint( $highlight ) );
	}

	/**
	 * @since 1.1.0
	 * @return bool
	 */
	public function is_highlighted() {
		return ! empty( $this->get_highlight() );
	}

	/**
	 * set the note.
	 *
	 * @since  1.1.0
	 *
	 * @param string $note .
	 *
	 */
	public function set_note( $note ) {
		$this->set_prop( 'note', eaccounting_sanitize_textarea( $note ) );
	}

	/**
	 * set the note.
	 *
	 * @since  1.1.0
	 *
	 * @param string $author .
	 *
	 */
	public function set_author( $author ) {
		$this->set_prop( 'author', eaccounting_clean( $author ) );
	}


	/**
	 * Save should create or update based on object existence.
	 *
	 * @since  1.1.0
	 * @return \Exception|bool
	 */
	public function save() {
		if ( empty( $this->get_author() ) ) {
			$this->set_author( 'Ever Accounting' );
		}

		return parent::save();
	}

}
