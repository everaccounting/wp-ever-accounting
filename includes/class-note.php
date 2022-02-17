<?php
/**
 * Note data handler class.
 *
 * @version     1.0.2
 * @package     Ever_Accounting
 * @class       Account
 */

namespace Ever_Accounting;

defined( 'ABSPATH' ) || exit;

/**
 * Note Class
*/
class Note extends Abstracts\Data  {
	/**
	 * This is the name of this object type.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $object_type = 'note';

	/**
	 * Table name.
	 *
	 * @since 1.1.3
	 * @var string
	 */
	protected $table = 'ea_notes';

	/**
	 * Cache group.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	protected $cache_group = 'ea_notes';

	/**
	 * Item Data array.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	protected $core_data = array(
		'parent_id'    => null,
		'type'         => '',
		'note'         => '',
		'extra'        => '',
		'creator_id'   => '',
		'date_created' => null,
	);

	/**
	 * Note constructor.
	 *
	 * @param int|note|object|null $note  note instance.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $note = 0 ) {
		// Call early so default data is set.
		parent::__construct();

		if ( is_numeric( $note ) && $note > 0 ) {
			$this->set_id( $note );
		} elseif ( $note instanceof self ) {
			$this->set_id( absint( $note->get_id() ) );
		} elseif ( ! empty( $note->ID ) ) {
			$this->set_id( absint( $note->ID ) );
		} else {
			$this->set_object_read( true );
		}

		$this->read();
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD methods
	|--------------------------------------------------------------------------
	|
	| Methods which create, read, update and delete documents from the database.
	| Written in abstract fashion so that the way documents are stored can be
	| changed more easily in the future.
	|
	| A save method is included for convenience (chooses update or create based
	| on if the order exists yet).
	|
	*/

	/**
	 * Saves an object in the database.
	 *
	 * @return \WP_Error|int id on success, WP_Error on failure.
	 * @since 1.1.3
	 */
	public function save() {
		// check if anything missing before save.
		if ( ! $this->is_date_valid( $this->date_created ) ) {
			$this->date_created = current_time( 'mysql' );
		}

		$requires = [ 'parent_id', 'type', 'note' ];
		foreach ( $requires as $required ) {
			if ( empty( $this->$required ) ) {
				return new \WP_Error( 'missing_required_param', sprintf( __( 'Note %s is required.', 'wp-ever-accounting' ), $required ) );
			}
		}

		if ( ! $this->exists() ) {
			$is_error = $this->create();
		} else {
			$is_error = $this->update();
		}

		if ( is_wp_error( $is_error ) ) {
			return $is_error;
		}

		$this->apply_changes();

		// Clear cache.
		wp_cache_delete( $this->get_id(), $this->cache_group );
		wp_cache_set( 'last_changed', microtime(), $this->cache_group );

		/**
		 * Fires immediately after a note is inserted or updated in the database.
		 *
		 * @param int $id Note id.
		 * @param array $data Note data array.
		 * @param Note $note Note object.
		 *
		 * @since 1.0.0
		 */
		do_action( 'ever_accounting_saved_' . $this->object_type, $this->get_id(), $this );

		return $this->get_id();
	}
}


